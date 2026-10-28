<?php

namespace App\Models;

use App\MemberCategory;
use App\MemberLevel;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subscription_id', 'member_id', 'kind', 'category', 'level', 'currency', 'unit_price_snapshot', 'seat_count', 'total_price_snapshot', 'period_starts_at', 'period_expires_at'])]
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => MemberCategory::class,
            'level' => MemberLevel::class,
            'period_starts_at' => 'datetime',
            'period_expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** @return BelongsTo<Subscription, $this> */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
