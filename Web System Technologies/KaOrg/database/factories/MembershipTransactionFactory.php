<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<MembershipTransaction> */
class MembershipTransactionFactory extends Factory
{
    /** @return array<string,mixed> */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'reference' => 'TXN-'.Str::ulid(),
            'membership_category_id' => MembershipCategory::factory(),
            'membership_level_id' => MembershipLevel::factory(),
            'category_name' => 'Regular',
            'level_name' => 'Basic',
            'type' => 'application',
            'status' => 'Pending',
            'fee' => 29900,
            'validity_days' => 30,
        ];
    }
}
