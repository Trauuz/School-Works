<?php

namespace Database\Factories;

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = fake()->randomElement(MemberCategory::cases());

        return [
            'member_number' => fake()->unique()->numerify('M-#####'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'category' => $category,
            'level' => fake()->randomElement(MemberLevel::cases()),
            'eligibility_status' => EligibilityStatus::Verified,
            'eligibility_verified_at' => now(),
            'corporate_organization' => $category === MemberCategory::Corporate ? fake()->company() : null,
            'seat_count' => $category === MemberCategory::Corporate ? 10 : 1,
            'deactivated_at' => null,
        ];
    }

    public function student(): static
    {
        return $this->state(fn (): array => ['category' => MemberCategory::Student]);
    }

    public function corporate(int $seatCount = 10): static
    {
        return $this->state(fn (): array => [
            'category' => MemberCategory::Corporate,
            'eligibility_status' => EligibilityStatus::Verified,
            'eligibility_verified_at' => now(),
            'corporate_organization' => fake()->company(),
            'seat_count' => $seatCount,
        ]);
    }

    public function level(MemberLevel $level): static
    {
        return $this->state(fn (): array => ['level' => $level]);
    }

    public function deactivated(): static
    {
        return $this->state(fn (): array => ['deactivated_at' => now()]);
    }
}
