<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'membership_category_id', 'membership_level_id'];

    protected static function booted(): void
    {
        static::creating(function (Member $member): void {
            $member->member_number ??= 'MEM-'.Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'deactivated_at' => 'datetime',
            'membership_starts_at' => 'datetime',
            'membership_expires_at' => 'datetime',
            'legacy_validity' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MembershipCategory::class, 'membership_category_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function latestTransaction(): HasOne
    {
        return $this->hasOne(MembershipTransaction::class)->latestOfMany();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deactivated_at')->where('membership_starts_at', '<=', now())->where('membership_expires_at', '>', now());
    }

    public function getIsActiveAttribute(): bool
    {
        return ! $this->deactivated_at && $this->membership_starts_at?->lte(now()) && $this->membership_expires_at?->gt(now());
    }

    public function getFullNameAttribute(): string
    {
        return "$this->first_name $this->last_name";
    }
}
