<?php

namespace Database\Factories;

use App\MemberCategory;
use App\MemberLevel;
use App\Models\Member;
use App\Models\Subscription;
use App\RenewalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'category' => MemberCategory::Regular,
            'level' => MemberLevel::Basic,
            'scheduled_level' => null,
            'renewal_status' => RenewalStatus::Active,
            'auto_renews' => true,
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
            'quota_period_started_at' => now(),
            'quota_usage' => [],
            'corporate_organization' => null,
            'seat_count' => 1,
            'unit_price_snapshot' => 299,
            'total_price_snapshot' => 299,
        ];
    }
}
