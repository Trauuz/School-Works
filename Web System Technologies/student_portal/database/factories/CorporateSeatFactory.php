<?php

namespace Database\Factories;

use App\Models\CorporateSeat;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CorporateSeat>
 */
class CorporateSeatFactory extends Factory
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
            'assignee_name' => fake()->name(),
            'assignee_email' => fake()->unique()->safeEmail(),
            'assigned_at' => now(),
            'released_at' => null,
        ];
    }
}
