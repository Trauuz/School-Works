<?php

use App\MemberCategory;
use App\MemberLevel;
use App\Services\MembershipEntitlements;
use App\Services\MembershipPricing;

test('every category and level produces the published monthly fee', function () {
    $pricing = app(MembershipPricing::class);
    $matrix = [
        MemberCategory::Regular->value => [299, 599, 999, 1499],
        MemberCategory::Student->value => [209, 419, 699, 1049],
        MemberCategory::SeniorCitizen->value => [239, 479, 799, 1199],
        MemberCategory::Corporate->value => [254, 509, 849, 1274],
    ];

    foreach (MemberCategory::cases() as $category) {
        foreach (MemberLevel::cases() as $index => $level) {
            expect($pricing->unitPrice($category, $level))->toBe($matrix[$category->value][$index]);
        }
    }
});

test('corporate pricing is calculated per seat', function () {
    $pricing = app(MembershipPricing::class);

    expect($pricing->totalPrice(MemberCategory::Corporate, MemberLevel::Basic, 10))->toBe(2540)
        ->and($pricing->totalPrice(MemberCategory::Corporate, MemberLevel::Silver, 10))->toBe(5090)
        ->and($pricing->totalPrice(MemberCategory::Corporate, MemberLevel::Gold, 10))->toBe(8490)
        ->and($pricing->totalPrice(MemberCategory::Corporate, MemberLevel::Platinum, 10))->toBe(12740);
});

test('higher levels inherit lower level benefits', function () {
    $entitlements = app(MembershipEntitlements::class);
    $basic = $entitlements->for(MemberLevel::Basic);
    $platinum = $entitlements->for(MemberLevel::Platinum);

    foreach (array_keys($basic) as $benefit) {
        expect($platinum)->toHaveKey($benefit);
    }

    expect($platinum)
        ->toHaveKey('downloadable_materials')
        ->toHaveKey('offline_access')
        ->toHaveKey('private_mentoring_minutes_per_cycle');
});
