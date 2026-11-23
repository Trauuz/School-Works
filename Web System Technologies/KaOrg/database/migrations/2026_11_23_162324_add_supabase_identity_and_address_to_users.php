<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['users', 'members'] as $table) {
            if (DB::table($table)->selectRaw('LOWER(email) AS normalized_email')->groupByRaw('LOWER(email)')->havingRaw('COUNT(*) > 1')->exists()) {
                throw new RuntimeException('Resolve case-insensitive duplicate emails in '.$table.' before migrating.');
            }
        }
        Schema::table('users', function (Blueprint $table): void {
            $table->uuid('supabase_id')->nullable()->unique();
            $table->timestamp('profile_completed_at')->nullable();
            $table->string('password')->nullable()->change();
            $table->string('phone', 30)->nullable();
            $table->string('address_line')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('address_latitude', 10, 7)->nullable();
            $table->decimal('address_longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('weather_location_id')->nullable();
            $table->string('weather_city', 100)->nullable();
            $table->decimal('weather_latitude', 10, 7)->nullable();
            $table->decimal('weather_longitude', 10, 7)->nullable();
            $table->string('weather_timezone', 100)->nullable();
        });
        $addMemberLink = function (): void {
            Schema::table('members', function (Blueprint $table): void {
                $table->foreignId('user_id')->nullable()->unique()->constrained()->restrictOnDelete();
            });
        };
        if (DB::getDriverName() === 'sqlite') {
            Schema::withoutForeignKeyConstraints($addMemberLink);
        } else {
            $addMemberLink();
        }
        foreach (['users', 'members'] as $table) {
            DB::table($table)->update(['email' => DB::raw('LOWER(email)')]);
            if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
                DB::statement('CREATE UNIQUE INDEX '.$table.'_email_lower_unique ON '.$table.' (LOWER(email))');
            }
        }
        if (DB::getDriverName() === 'pgsql') {
            foreach (['users', 'members', 'membership_categories', 'membership_levels', 'membership_transactions', 'payments', 'subscriptions', 'invoices', 'corporate_seats', 'tasks', 'sessions', 'password_reset_tokens', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'] as $table) {
                DB::statement('ALTER TABLE '.$table.' ENABLE ROW LEVEL SECURITY');
            }
            DB::statement('ALTER TABLE membership_levels ADD CONSTRAINT membership_levels_positive_fee CHECK (fee > 0), ADD CONSTRAINT membership_levels_positive_validity CHECK (validity_days > 0)');
            DB::statement('ALTER TABLE membership_transactions ADD CONSTRAINT membership_transactions_positive_fee CHECK (fee > 0), ADD CONSTRAINT membership_transactions_positive_validity CHECK (validity_days > 0)');
            DB::statement('ALTER TABLE payments ADD CONSTRAINT payments_positive_amount CHECK (amount > 0)');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE membership_levels DROP CONSTRAINT membership_levels_positive_fee, DROP CONSTRAINT membership_levels_positive_validity');
            DB::statement('ALTER TABLE membership_transactions DROP CONSTRAINT membership_transactions_positive_fee, DROP CONSTRAINT membership_transactions_positive_validity');
            DB::statement('ALTER TABLE payments DROP CONSTRAINT payments_positive_amount');
        }
        foreach (['users', 'members'] as $table) {
            if (DB::getDriverName() !== 'mysql' && DB::getDriverName() !== 'mariadb') {
                DB::statement('DROP INDEX IF EXISTS '.$table.'_email_lower_unique');
            }
        }
        Schema::withoutForeignKeyConstraints(function (): void {
            Schema::table('members', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('user_id');
            });
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['supabase_id']);
                $table->dropColumn(['supabase_id', 'profile_completed_at', 'phone', 'address_line', 'city', 'region', 'country_code', 'postal_code', 'address_latitude', 'address_longitude', 'weather_location_id', 'weather_city', 'weather_latitude', 'weather_longitude', 'weather_timezone']);
            });
        });
    }
};
