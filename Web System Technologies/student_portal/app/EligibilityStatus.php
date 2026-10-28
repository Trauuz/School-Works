<?php

namespace App;

enum EligibilityStatus: string
{
    case Exempt = 'exempt';
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Exempt => 'Not required',
            self::Pending => 'Pending review',
            self::Verified => 'Verified',
            self::Rejected => 'Rejected',
        };
    }
}
