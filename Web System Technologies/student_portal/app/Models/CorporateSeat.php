<?php

namespace App\Models;

use Database\Factories\CorporateSeatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subscription_id', 'assignee_name', 'assignee_email', 'assigned_at', 'released_at'])]
class CorporateSeat extends Model
{
    /** @use HasFactory<CorporateSeatFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['assigned_at' => 'datetime', 'released_at' => 'datetime'];
    }

    /** @return BelongsTo<Subscription, $this> */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
