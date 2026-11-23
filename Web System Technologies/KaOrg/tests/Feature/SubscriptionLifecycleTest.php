<?php

use App\Models\Member;
use App\Services\MembershipManager;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->artisan('migrate:fresh', ['--force' => true])->assertSuccessful();
});
test('renewal snapshots fees and extends current validity without losing access', function () {
    $this->travelTo(now()->startOfSecond());
    $member = Member::factory()->create(['membership_starts_at' => now()->subDays(10), 'membership_expires_at' => now()->addDays(20)]);
    $manager = app(MembershipManager::class);
    $transaction = $manager->create($member, 'renewal');
    $fee = $transaction->fee;
    $member->level->update(['fee' => 99999, 'validity_days' => 7, 'name' => 'Changed']);
    $manager->pay($transaction, $fee);
    $manager->approve($transaction);
    expect($transaction->fresh()->fee)->toBe($fee)->and($transaction->fresh()->validity_days)->toBe(30)->and($transaction->fresh()->level_name)->not->toBe('Changed')->and($member->fresh()->is_active)->toBeTrue()->and($member->fresh()->membership_expires_at->equalTo(now()->addDays(50)))->toBeTrue();
});
test('expired renewal starts at approval and expiry boundary is exact', function () {
    $this->travelTo(now()->startOfSecond());
    $member = Member::factory()->create(['membership_starts_at' => now()->subDays(40), 'membership_expires_at' => now()->subDays(10)]);
    $manager = app(MembershipManager::class);
    $transaction = $manager->create($member, 'renewal');
    $manager->pay($transaction, $transaction->fee);
    $manager->approve($transaction);
    expect($transaction->fresh()->starts_at->equalTo(now()))->toBeTrue();
    $this->travel(30)->days();
    expect($transaction->fresh()->display_status)->toBe('Expired')->and($member->fresh()->is_active)->toBeFalse()->and(Member::active()->count())->toBe(0);
});
test('legacy migration preserves records and dates without fabricating payments', function () {
    $migration = require database_path('migrations/2026_11_23_154136_create_membership_catalog_and_transactions.php');
    $legacyMigrationPaths = collect(glob(database_path('migrations/*.php')))
        ->filter(fn (string $path): bool => basename($path) < '2026_11_23_154136_create_membership_catalog_and_transactions.php')
        ->values()
        ->all();
    $this->artisan('migrate:fresh', ['--force' => true, '--path' => $legacyMigrationPaths, '--realpath' => true])->assertSuccessful();
    $id = DB::table('members')->insertGetId(['member_number' => 'M-LEGACY', 'first_name' => 'Legacy', 'last_name' => 'Member', 'email' => 'legacy@example.com', 'category' => 'student', 'level' => 'gold']);
    $starts = now()->subDays(5)->startOfSecond();
    $expires = now()->addDays(25)->startOfSecond();
    DB::table('subscriptions')->insert(['member_id' => $id, 'category' => 'student', 'level' => 'gold', 'starts_at' => $starts, 'expires_at' => $expires, 'quota_period_started_at' => $starts, 'unit_price_snapshot' => 699, 'total_price_snapshot' => 699]);
    $migration->up();
    (require database_path('migrations/2026_11_23_211711_add_soft_deletes_to_members_table.php'))->up();
    $member = Member::findOrFail($id);
    expect($member->getAttributes())->toMatchArray(['membership_expires_at' => $expires->format('Y-m-d H:i:s')]);
    expect($member->member_number)->toBe('M-LEGACY')->and($member->category->name)->toBe('Student')->and($member->level->name)->toBe('Gold')->and($member->membership_expires_at->equalTo($expires))->toBeTrue()->and($member->legacy_validity)->toBeTrue()->and($member->is_active)->toBeTrue();
    $this->assertDatabaseCount('payments', 0);
    $this->assertDatabaseCount('membership_transactions', 0);
    $this->assertDatabaseCount('subscriptions', 1);
});
