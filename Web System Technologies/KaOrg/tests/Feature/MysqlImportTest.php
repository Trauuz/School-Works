<?php

use App\Models\Member;
use App\Services\MembershipManager;
use Illuminate\Support\Facades\DB;

test('import command rejects non MySQL source without writing', function () {
    $this->artisan('app:import-mysql', ['--source' => 'sqlite', '--target' => 'sqlite', '--dry-run' => true, '--no-interaction' => true])->assertFailed();
});

test('mysql to postgres rehearsal preserves source records and resets target sequences', function () {
    if (! env('RUN_DATABASE_IMPORT_TESTS')) {
        $this->markTestSkipped('Requires explicitly configured disposable MySQL source and PostgreSQL target.');
    }
    $sourceName = 'mysql';
    $targetName = 'supabase';
    $source = DB::connection($sourceName);
    $target = DB::connection($targetName);
    foreach ([$source, $target] as $connection) {
        expect($connection->getDatabaseName())->toContain('test');
    }
    $this->artisan('migrate:fresh', ['--database' => $sourceName, '--force' => true, '--no-interaction' => true])->assertSuccessful();
    $this->artisan('migrate:fresh', ['--database' => $targetName, '--force' => true, '--no-interaction' => true])->assertSuccessful();
    $original = config('database.default');
    try {
        DB::setDefaultConnection($sourceName);
        $member = Member::factory()->create(['membership_starts_at' => now()->subDay()->startOfSecond(), 'membership_expires_at' => now()->addDays(29)->startOfSecond()]);
        $transaction = app(MembershipManager::class)->create($member, 'renewal');
        app(MembershipManager::class)->pay($transaction, $transaction->fee);
        $sourceCount = $source->table('members')->count();
        $this->artisan('app:import-mysql', ['--force' => true, '--no-interaction' => true])->assertSuccessful();
        expect($target->table('members')->find($member->id)->member_number)->toBe($member->member_number)
            ->and((int) $target->table('payments')->sum('amount'))->toBe($transaction->fee)
            ->and($source->table('members')->count())->toBe($sourceCount);
        $nextId = $target->table('members')->insertGetId(['member_number' => 'MEM-POST-IMPORT', 'first_name' => 'New', 'last_name' => 'Member', 'email' => 'new-import@example.com']);
        expect($nextId)->toBeGreaterThan($member->id);
        $this->artisan('app:import-mysql', ['--force' => true, '--no-interaction' => true])->assertFailed();
    } finally {
        DB::setDefaultConnection($original);
    }
});
