<?php

namespace Database\Factories;

use App\MemberCategory;
use App\MemberLevel;
use App\Models\Invoice;
use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'member_id' => Member::factory(),
            'kind' => 'activation',
            'category' => MemberCategory::Regular,
            'level' => MemberLevel::Basic,
            'currency' => 'PHP',
            'unit_price_snapshot' => 299,
            'seat_count' => 1,
            'total_price_snapshot' => 299,
            'period_starts_at' => now(),
            'period_expires_at' => now()->addDays(30),
        ];
    }
}
