<?php

namespace App\Services;

use App\MemberCategory;
use App\MemberLevel;

class MembershipPricing
{
    public function unitPrice(MemberCategory $category, MemberLevel $level): int
    {
        $baseFee = (int) config("memberships.base_fees.{$level->value}");
        $discount = (int) config("memberships.discounts.{$category->value}");

        return (int) round($baseFee * (100 - $discount) / 100);
    }

    public function totalPrice(MemberCategory $category, MemberLevel $level, int $seatCount = 1): int
    {
        return $this->unitPrice($category, $level) * ($category === MemberCategory::Corporate ? $seatCount : 1);
    }

    public function formatted(int $amount): string
    {
        return '₱'.number_format($amount);
    }
}
