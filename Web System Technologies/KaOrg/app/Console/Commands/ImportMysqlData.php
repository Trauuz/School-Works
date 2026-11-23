<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ImportMysqlData extends Command
{
    protected $signature = 'app:import-mysql {--source=mysql : Existing MySQL connection} {--target=supabase : Empty migrated PostgreSQL connection} {--dry-run : Inspect row counts without writing} {--force : Confirm the prepared empty target for non-interactive import}';

    protected $description = 'Copy membership and legacy business data to an empty PostgreSQL target without modifying MySQL';

    /** @var list<string> */
    private const TABLES = ['users', 'tasks', 'membership_categories', 'membership_levels', 'members', 'subscriptions', 'invoices', 'corporate_seats', 'membership_transactions', 'payments'];

    public function handle(): int
    {
        $sourceName = (string) $this->option('source');
        $targetName = (string) $this->option('target');
        $source = DB::connection($sourceName);
        $target = DB::connection($targetName);
        if (! in_array($source->getDriverName(), ['mysql', 'mariadb'], true) || $target->getDriverName() !== 'pgsql') {
            $this->error('Source must be MySQL/MariaDB and target must be PostgreSQL.');

            return self::FAILURE;
        }
        try {
            $this->validateTarget($source, $target);
            foreach (['users', 'members'] as $table) {
                if ($source->table($table)->selectRaw('LOWER(email) AS email')->groupByRaw('LOWER(email)')->havingRaw('COUNT(*) > 1')->exists()) {
                    throw new RuntimeException('Resolve duplicate emails in '.$table.' before importing.');
                }
            }
            $this->table(['Table', 'Source rows'], array_map(fn (string $table): array => [$table, $source->table($table)->count()], self::TABLES));
            if ($this->option('dry-run')) {
                return self::SUCCESS;
            }
            if (! $this->option('force') && ! $this->confirm('Back up MySQL and pause application writes before continuing. Import into the empty PostgreSQL target?', false)) {
                return self::FAILURE;
            }
            $target->transaction(function () use ($source, $target): void {
                $target->table('membership_levels')->delete();
                $target->table('membership_categories')->delete();
                foreach (self::TABLES as $table) {
                    $columns = $target->getSchemaBuilder()->getColumns($table);
                    $names = array_column($columns, 'name');
                    $booleans = array_column(array_filter($columns, fn (array $column): bool => str_contains($column['type'], 'bool')), 'name');
                    foreach ($source->table($table)->orderBy('id')->cursor() as $record) {
                        $row = array_intersect_key((array) $record, array_flip($names));
                        foreach ($booleans as $column) {
                            if (isset($row[$column])) {
                                $row[$column] = (bool) $row[$column];
                            }
                        }
                        if (isset($row['email'])) {
                            $row['email'] = mb_strtolower($row['email']);
                        }
                        $target->table($table)->insert($row);
                    }
                    $count = $source->table($table)->count();
                    if ($target->table($table)->count() !== $count) {
                        throw new RuntimeException('Row count mismatch for '.$table);
                    }
                    $maximum = (int) ($target->table($table)->max('id') ?? 0);
                    $target->select("SELECT setval(pg_get_serial_sequence(?, 'id'), ?, ?)", [$table, max(1, $maximum), $maximum > 0]);
                }
                $this->verify($source, $target);
            });
        } catch (Throwable $exception) {
            $this->error('Import stopped: '.$exception->getMessage());

            return self::FAILURE;
        }
        $this->info('Import verified. MySQL was not modified. Sessions and caches were not copied. Set DB_CONNECTION=supabase only after the auth configuration and smoke checks are ready.');
        $this->warn('In the Supabase dashboard, disable the Data API before exposing this project. Laravel connects directly to PostgreSQL.');

        return self::SUCCESS;
    }

    private function validateTarget(Connection $source, Connection $target): void
    {
        foreach (self::TABLES as $table) {
            if (! $source->getSchemaBuilder()->hasTable($table) || ! $target->getSchemaBuilder()->hasTable($table)) {
                throw new RuntimeException('Missing table '.$table.'. Run migrations on the target and upgrade the source schema first.');
            }
            if (! in_array($table, ['membership_categories', 'membership_levels'], true) && $target->table($table)->exists()) {
                throw new RuntimeException('Target '.$table.' is not empty. Refusing to overwrite data.');
            }
        }
        if (! $target->getSchemaBuilder()->hasColumn('users', 'supabase_id')) {
            throw new RuntimeException('Run all target migrations first.');
        }
        $categoryDefaults = ['Student', 'Regular', 'Senior Citizen', 'Corporate'];
        foreach ($target->table('membership_categories')->get() as $category) {
            if (! isset($categoryDefaults[$category->id - 1]) || $category->name !== $categoryDefaults[$category->id - 1]) {
                throw new RuntimeException('Target categories contain custom data. Use a fresh target database.');
            }
        }
        $levelDefaults = ['Basic' => 29900, 'Silver' => 59900, 'Gold' => 99900, 'Platinum' => 149900];
        foreach ($target->table('membership_levels')->get() as $level) {
            if (! isset($levelDefaults[$level->name]) || (int) $level->fee !== $levelDefaults[$level->name] || (int) $level->validity_days !== 30) {
                throw new RuntimeException('Target levels contain custom data. Use a fresh target database.');
            }
        }
    }

    private function verify(Connection $source, Connection $target): void
    {
        foreach (['membership_levels' => 'fee', 'membership_transactions' => 'fee', 'payments' => 'amount'] as $table => $column) {
            if ((string) $source->table($table)->sum($column) !== (string) $target->table($table)->sum($column)) {
                throw new RuntimeException('Financial total mismatch for '.$table);
            }
        }
        foreach ($source->table('members')->orderBy('id')->cursor() as $member) {
            $copy = $target->table('members')->find($member->id);
            foreach (['member_number', 'membership_starts_at', 'membership_expires_at'] as $field) {
                if (($member->$field ?? null) != ($copy->$field ?? null)) {
                    throw new RuntimeException('Member validity mismatch for ID '.$member->id);
                }
            }
        }
    }
}
