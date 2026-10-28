<?php

namespace App;

enum RenewalStatus: string
{
    case Active = 'active';
    case CancelAtPeriodEnd = 'cancel_at_period_end';
    case PendingDowngrade = 'pending_downgrade';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}
