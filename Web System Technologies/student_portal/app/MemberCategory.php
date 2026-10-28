<?php

namespace App;

enum MemberCategory: string
{
    case Student = 'student';
    case Regular = 'regular';
    case SeniorCitizen = 'senior_citizen';
    case Corporate = 'corporate';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Regular => 'Regular',
            self::SeniorCitizen => 'Senior Citizen',
            self::Corporate => 'Corporate',
        };
    }

    public function requiresEligibility(): bool
    {
        return $this !== self::Regular;
    }
}
