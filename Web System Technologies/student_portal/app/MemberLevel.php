<?php

namespace App;

enum MemberLevel: string
{
    case Basic = 'basic';
    case Silver = 'silver';
    case Gold = 'gold';
    case Platinum = 'platinum';

    public function label(): string
    {
        return match ($this) {
            self::Basic => 'Basic',
            self::Silver => 'Silver',
            self::Gold => 'Gold',
            self::Platinum => 'Platinum',
        };
    }

    public function rank(): int
    {
        return match ($this) {
            self::Basic => 1,
            self::Silver => 2,
            self::Gold => 3,
            self::Platinum => 4,
        };
    }
}
