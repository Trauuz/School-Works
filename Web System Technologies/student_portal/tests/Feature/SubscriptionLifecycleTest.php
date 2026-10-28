<?php

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use App\Models\Member;
use App\RenewalStatus;
use App\Services\SubscriptionManager;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('activation and renewal each provide exactly 30 calendar days', function () {
    $startsAt = CarbonImmutable::parse('2026-01-15 10:30:00');
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]), $startsAt);

    expect($subscription->expires_at->equalTo($startsAt->addDays(30)))->toBeTrue();

    $renewed = app(SubscriptionManager::class)->renew($subscription);

    expect($renewed->starts_at->equalTo($startsAt->addDays(30)))->toBeTrue()
        ->and($renewed->expires_at->equalTo($startsAt->addDays(60)))->toBeTrue();
});

test('discounted categories require verified eligibility', function (MemberCategory $category) {
    $member = Member::factory()->create([
        'category' => $category,
        'eligibility_status' => EligibilityStatus::Pending,
    ]);

    expect(fn () => app(SubscriptionManager::class)->activate($member))->toThrow(DomainException::class);
})->with([MemberCategory::Student, MemberCategory::SeniorCitizen, MemberCategory::Corporate]);

test('corporate activation requires an organization and at least ten seats', function () {
    $member = Member::factory()->create([
        'category' => MemberCategory::Corporate,
        'eligibility_status' => EligibilityStatus::Verified,
        'corporate_organization' => 'Learning Co.',
        'seat_count' => 9,
    ]);

    expect(fn () => app(SubscriptionManager::class)->activate($member))->toThrow(DomainException::class);
});

test('corporate seats can only be reassigned at renewal', function () {
    $startsAt = CarbonImmutable::parse('2026-03-01 08:00:00');
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->corporate()->create([
        'level' => MemberLevel::Silver,
    ]), $startsAt);
    $manager = app(SubscriptionManager::class);
    $seat = $manager->assignCorporateSeat($subscription, 'First Learner', 'first@example.com');

    expect(fn () => $manager->reassignCorporateSeat($seat, 'Second Learner', 'second@example.com', $startsAt->addDay()))
        ->toThrow(DomainException::class);

    $reassigned = $manager->reassignCorporateSeat($seat, 'Second Learner', 'second@example.com', $startsAt);
    expect($reassigned->assignee_email)->toBe('second@example.com');
});

test('renewal resets monthly quotas without carrying usage forward', function () {
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Gold,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]));
    $subscription->update(['quota_usage' => ['live_workshops_per_cycle' => 3]]);

    $renewed = app(SubscriptionManager::class)->renew($subscription);

    expect($renewed->quota_usage)->toBe([])
        ->and($renewed->quota_period_started_at->equalTo($renewed->starts_at))->toBeTrue();
});

test('upgrades apply immediately and charge the prorated difference', function () {
    $startsAt = CarbonImmutable::parse('2026-01-01 00:00:00');
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]), $startsAt);

    $charge = app(SubscriptionManager::class)->upgrade($subscription, MemberLevel::Silver, $startsAt->addDays(15));

    expect($charge)->toBe(150)
        ->and($subscription->fresh()->level)->toBe(MemberLevel::Silver)
        ->and($subscription->invoices()->latest('id')->first()->total_price_snapshot)->toBe(150);
});

test('downgrades and cancellations preserve benefits until expiration', function () {
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Gold,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]));
    $manager = app(SubscriptionManager::class);

    $manager->scheduleDowngrade($subscription, MemberLevel::Basic);
    expect($subscription->fresh()->level)->toBe(MemberLevel::Gold)
        ->and($subscription->fresh()->renewal_status)->toBe(RenewalStatus::PendingDowngrade);

    $manager->scheduleCancellation($subscription);
    expect($subscription->fresh()->level)->toBe(MemberLevel::Gold)
        ->and($subscription->fresh()->auto_renews)->toBeFalse()
        ->and($manager->canAccessPaidContent($subscription->fresh()))->toBeTrue();
});

test('expired memberships block paid content without deleting the member record', function () {
    $startsAt = CarbonImmutable::parse('2025-01-01 00:00:00');
    $member = Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]);
    $subscription = app(SubscriptionManager::class)->activate($member, $startsAt);

    expect(app(SubscriptionManager::class)->canAccessPaidContent($subscription))->toBeFalse();
    $this->assertDatabaseHas('members', ['id' => $member->id]);
});

test('invoice snapshots retain historical prices after configuration changes', function () {
    $subscription = app(SubscriptionManager::class)->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]));
    $invoice = $subscription->invoices()->firstOrFail();

    config()->set('memberships.base_fees.basic', 9999);

    expect($invoice->fresh()->unit_price_snapshot)->toBe(299)
        ->and($invoice->fresh()->total_price_snapshot)->toBe(299);
});

test('the scheduler command renews due subscriptions and closes cancellations', function () {
    $startsAt = CarbonImmutable::parse('2026-01-01 00:00:00');
    $manager = app(SubscriptionManager::class);
    $renewing = $manager->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]), $startsAt);
    $cancelling = $manager->activate(Member::factory()->create([
        'category' => MemberCategory::Regular,
        'level' => MemberLevel::Basic,
        'eligibility_status' => EligibilityStatus::Exempt,
    ]), $startsAt);
    $manager->scheduleCancellation($cancelling);
    $this->travelTo($startsAt->addDays(30));

    $this->artisan('subscriptions:renew-due')->assertSuccessful();

    expect($renewing->fresh()->expires_at->equalTo($startsAt->addDays(60)))->toBeTrue()
        ->and($cancelling->fresh()->renewal_status)->toBe(RenewalStatus::Cancelled);
});
