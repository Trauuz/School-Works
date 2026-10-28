<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\RenewalStatus;
use App\Services\SubscriptionManager;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:renew-due')]
#[Description('Renew active subscriptions that have reached the end of their 30-day cycle')]
class RenewDueSubscriptions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SubscriptionManager $subscriptions): int
    {
        $renewed = 0;
        $closed = 0;

        Subscription::query()
            ->where('expires_at', '<=', now())
            ->whereIn('renewal_status', [
                RenewalStatus::Active->value,
                RenewalStatus::PendingDowngrade->value,
                RenewalStatus::CancelAtPeriodEnd->value,
            ])
            ->each(function (Subscription $subscription) use ($subscriptions, &$renewed, &$closed): void {
                if ($subscription->auto_renews) {
                    $subscriptions->renew($subscription);
                    $renewed++;

                    return;
                }

                $subscription->update(['renewal_status' => RenewalStatus::Cancelled]);
                $closed++;
            });

        $this->info("Renewed {$renewed} subscription(s); closed {$closed} subscription(s).");

        return self::SUCCESS;
    }
}
