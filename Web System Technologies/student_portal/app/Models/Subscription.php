<?php

namespace App\Models;

use App\MemberCategory;
use App\MemberLevel;
use App\RenewalStatus;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['member_id', 'category', 'level', 'scheduled_level', 'renewal_status', 'auto_renews', 'starts_at', 'expires_at', 'quota_period_started_at', 'quota_usage', 'corporate_organization', 'seat_count', 'unit_price_snapshot', 'total_price_snapshot'])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => MemberCategory::class,
            'level' => MemberLevel::class,
            'scheduled_level' => MemberLevel::class,
            'renewal_status' => RenewalStatus::class,
            'auto_renews' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'quota_period_started_at' => 'datetime',
            'quota_usage' => 'array',
        ];
    }

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** @return HasMany<Invoice, $this> */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** @return HasMany<CorporateSeat, $this> */
    public function corporateSeats(): HasMany
    {
        return $this->hasMany(CorporateSeat::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
