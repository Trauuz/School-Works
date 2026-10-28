<?php

namespace App\Services;

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use App\Models\CorporateSeat;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Subscription;
use App\RenewalStatus;
use Carbon\CarbonInterface;
use DomainException;

class SubscriptionManager
{
    public function __construct(
        public MembershipPricing $pricing,
    ) {}

    public function activate(Member $member, ?CarbonInterface $startsAt = null): Subscription
    {
        $this->validateEligibility($member);
        $startsAt ??= now();
        $expiresAt = $startsAt->copy()->addDays((int) config('memberships.validity_days'));
        $seatCount = $member->category === MemberCategory::Corporate ? $member->seat_count : 1;
        $unitPrice = $this->pricing->unitPrice($member->category, $member->level);

        $subscription = $member->subscriptions()->create([
            'category' => $member->category,
            'level' => $member->level,
            'renewal_status' => RenewalStatus::Active,
            'auto_renews' => true,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'quota_period_started_at' => $startsAt,
            'quota_usage' => [],
            'corporate_organization' => $member->corporate_organization,
            'seat_count' => $seatCount,
            'unit_price_snapshot' => $unitPrice,
            'total_price_snapshot' => $unitPrice * $seatCount,
        ]);

        $this->snapshotInvoice($subscription, 'activation');

        return $subscription;
    }

    public function renew(Subscription $subscription): Subscription
    {
        $startsAt = $subscription->expires_at->copy();
        $level = $subscription->scheduled_level ?? $subscription->level;
        $unitPrice = $this->pricing->unitPrice($subscription->category, $level);

        $subscription->update([
            'level' => $level,
            'scheduled_level' => null,
            'renewal_status' => RenewalStatus::Active,
            'starts_at' => $startsAt,
            'expires_at' => $startsAt->copy()->addDays((int) config('memberships.validity_days')),
            'quota_period_started_at' => $startsAt,
            'quota_usage' => [],
            'unit_price_snapshot' => $unitPrice,
            'total_price_snapshot' => $unitPrice * $subscription->seat_count,
        ]);

        $this->snapshotInvoice($subscription, 'renewal');

        return $subscription->refresh();
    }

    public function upgrade(Subscription $subscription, MemberLevel $newLevel, ?CarbonInterface $at = null): int
    {
        if ($newLevel->rank() <= $subscription->level->rank()) {
            throw new DomainException('The new level must be higher than the current level.');
        }

        $at ??= now();
        $remainingSeconds = max(0, $at->diffInSeconds($subscription->expires_at, false));
        $cycleSeconds = max(1, $subscription->starts_at->diffInSeconds($subscription->expires_at));
        $newUnitPrice = $this->pricing->unitPrice($subscription->category, $newLevel);
        $difference = ($newUnitPrice - $subscription->unit_price_snapshot) * $subscription->seat_count;
        $proratedDifference = (int) round($difference * $remainingSeconds / $cycleSeconds);

        $subscription->update([
            'level' => $newLevel,
            'unit_price_snapshot' => $newUnitPrice,
            'total_price_snapshot' => $newUnitPrice * $subscription->seat_count,
        ]);

        $this->snapshotInvoice($subscription, 'upgrade', $proratedDifference);

        return $proratedDifference;
    }

    public function scheduleDowngrade(Subscription $subscription, MemberLevel $newLevel): void
    {
        if ($newLevel->rank() >= $subscription->level->rank()) {
            throw new DomainException('The new level must be lower than the current level.');
        }

        $subscription->update(['scheduled_level' => $newLevel, 'renewal_status' => RenewalStatus::PendingDowngrade]);
    }

    public function scheduleCancellation(Subscription $subscription): void
    {
        $subscription->update(['auto_renews' => false, 'renewal_status' => RenewalStatus::CancelAtPeriodEnd]);
    }

    public function canAccessPaidContent(Subscription $subscription): bool
    {
        return ! $subscription->isExpired() && ! in_array($subscription->renewal_status, [RenewalStatus::Cancelled, RenewalStatus::Expired], true);
    }

    public function assignCorporateSeat(Subscription $subscription, string $name, string $email): CorporateSeat
    {
        if ($subscription->category !== MemberCategory::Corporate) {
            throw new DomainException('Seats can only be assigned to corporate subscriptions.');
        }

        if ($subscription->corporateSeats()->whereNull('released_at')->count() >= $subscription->seat_count) {
            throw new DomainException('All corporate seats are already assigned.');
        }

        return $subscription->corporateSeats()->create([
            'assignee_name' => $name,
            'assignee_email' => $email,
            'assigned_at' => now(),
        ]);
    }

    public function reassignCorporateSeat(CorporateSeat $seat, string $name, string $email, CarbonInterface $at): CorporateSeat
    {
        if (! $at->equalTo($seat->subscription->starts_at)) {
            throw new DomainException('Corporate seats may only be reassigned at renewal.');
        }

        $seat->update(['assignee_name' => $name, 'assignee_email' => $email, 'assigned_at' => $at, 'released_at' => null]);

        return $seat->refresh();
    }

    private function validateEligibility(Member $member): void
    {
        if ($member->category->requiresEligibility() && $member->eligibility_status !== EligibilityStatus::Verified) {
            throw new DomainException('This category requires verified eligibility before activation.');
        }

        if ($member->category === MemberCategory::Corporate) {
            if (! $member->corporate_organization || $member->seat_count < (int) config('memberships.minimum_corporate_seats')) {
                throw new DomainException('Corporate memberships require a verified organization and at least 10 seats.');
            }
        }
    }

    private function snapshotInvoice(Subscription $subscription, string $kind, ?int $total = null): Invoice
    {
        return $subscription->invoices()->create([
            'member_id' => $subscription->member_id,
            'kind' => $kind,
            'category' => $subscription->category,
            'level' => $subscription->level,
            'currency' => config('memberships.currency'),
            'unit_price_snapshot' => $subscription->unit_price_snapshot,
            'seat_count' => $subscription->seat_count,
            'total_price_snapshot' => $total ?? $subscription->total_price_snapshot,
            'period_starts_at' => $subscription->starts_at,
            'period_expires_at' => $subscription->expires_at,
        ]);
    }
}
