<?php

namespace App\Services;

use App\MemberCategory;
use App\MemberLevel;

class MembershipEntitlements
{
    /** @return array<string, bool|int|string> */
    public function for(MemberLevel $level, ?MemberCategory $category = null): array
    {
        $entitlements = [];

        foreach (MemberLevel::cases() as $candidate) {
            if ($candidate->rank() > $level->rank()) {
                break;
            }

            $entitlements = array_replace($entitlements, config("memberships.entitlements.{$candidate->value}", []));
        }

        if ($category === MemberCategory::Student) {
            $entitlements['student_learning_paths'] = true;
            $entitlements['student_only_events'] = true;
        }

        if ($category === MemberCategory::SeniorCitizen) {
            $entitlements['guided_onboarding'] = true;
            $entitlements['digital_skills_orientation'] = true;
        }

        if ($category === MemberCategory::Corporate) {
            $entitlements['centralized_billing'] = true;
            $entitlements['seat_administration'] = true;
            $entitlements['team_progress_reports'] = $level->rank() >= MemberLevel::Silver->rank();
            $entitlements['assignable_learning_paths'] = $level->rank() >= MemberLevel::Gold->rank();
            $entitlements['organization_reports'] = $level === MemberLevel::Platinum;
        }

        return $entitlements;
    }

    /** @return array<string, int> */
    public function quotas(MemberLevel $level): array
    {
        return array_filter(
            $this->for($level),
            fn (mixed $value, string $key): bool => is_int($value) && str_ends_with($key, '_per_cycle'),
            ARRAY_FILTER_USE_BOTH,
        );
    }
}
