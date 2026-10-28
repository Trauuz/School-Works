<?php

namespace App\Models;

use App\EligibilityStatus;
use App\MemberCategory;
use App\MemberLevel;
use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['member_number', 'first_name', 'last_name', 'email', 'phone', 'category', 'level', 'eligibility_status', 'eligibility_verified_at', 'corporate_organization', 'seat_count', 'deactivated_at'])]
class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => MemberCategory::class,
            'level' => MemberLevel::class,
            'eligibility_status' => EligibilityStatus::class,
            'eligibility_verified_at' => 'datetime',
            'deactivated_at' => 'datetime',
        ];
    }

    /** @return HasMany<Subscription, $this> */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()->where('expires_at', '>', now())->latest('starts_at')->first();
    }

    public function hasValidEligibility(): bool
    {
        return ! $this->category->requiresEligibility() || $this->eligibility_status === EligibilityStatus::Verified;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deactivated_at');
    }

    public function scopeDeactivated(Builder $query): Builder
    {
        return $query->whereNotNull('deactivated_at');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search): void {
            $query->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('member_number', 'like', "%{$search}%");
        });
    }

    public function scopeCategory(Builder $query, MemberCategory $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeLevel(Builder $query, MemberLevel $level): Builder
    {
        return $query->where('level', $level);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
