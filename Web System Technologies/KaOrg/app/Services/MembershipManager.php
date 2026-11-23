<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MembershipManager
{
    public function create(Member $member, string $type): MembershipTransaction
    {
        return DB::transaction(function () use ($member, $type): MembershipTransaction {
            $member = Member::query()->lockForUpdate()->findOrFail($member->id);
            $this->require(! $member->deactivated_at, 'This member is deactivated.');
            $this->require(! $member->transactions()->whereIn('status', ['Pending', 'Paid'])->exists(), 'This member already has an outstanding transaction.');
            $hasMembership = $member->membership_expires_at !== null;
            $this->require($type === ($hasMembership ? 'renewal' : 'application'), 'Choose an application for a new member or a renewal for an existing membership.');
            $level = MembershipLevel::query()->findOrFail($member->membership_level_id);
            $category = MembershipCategory::query()->findOrFail($member->membership_category_id);

            return $member->transactions()->create([
                'reference' => 'TXN-'.Str::ulid(),
                'type' => $type,
                'status' => 'Pending',
                'membership_category_id' => $category->id,
                'membership_level_id' => $level->id,
                'category_name' => $category->name,
                'level_name' => $level->name,
                'fee' => $level->fee,
                'validity_days' => $level->validity_days,
            ]);
        });
    }

    public function pay(MembershipTransaction $transaction, int $amount): Payment
    {
        return DB::transaction(function () use ($transaction, $amount): Payment {
            $member = Member::query()->lockForUpdate()->findOrFail($transaction->member_id);
            $transaction = MembershipTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            $this->require(! $member->deactivated_at, 'This member is deactivated.');
            $this->require($transaction->status === 'Pending' && ! $transaction->payment()->exists(), 'Only an unpaid Pending transaction can receive payment.');
            $this->require($amount > 0 && $amount === $transaction->fee, 'Payment must equal the full transaction fee.');
            $payment = $transaction->payment()->create(['amount' => $amount, 'paid_at' => now()]);
            $transaction->update(['status' => 'Paid']);

            return $payment;
        });
    }

    public function approve(MembershipTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $member = Member::query()->lockForUpdate()->findOrFail($transaction->member_id);
            $transaction = MembershipTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            $this->require(! $member->deactivated_at, 'This member is deactivated.');
            $this->require($transaction->status === 'Paid' && $transaction->payment?->amount === $transaction->fee, 'Only fully paid transactions can be approved.');
            $starts = now();
            if ($transaction->type === 'renewal' && $member->membership_expires_at?->gt($starts)) {
                $starts = $member->membership_expires_at->copy();
            }
            $expires = $starts->copy()->addDays($transaction->validity_days);
            $transaction->update(['status' => 'Approved', 'starts_at' => $starts, 'expires_at' => $expires]);
            $member->forceFill([
                'membership_starts_at' => $member->is_active ? $member->membership_starts_at : $starts,
                'membership_expires_at' => $expires,
                'legacy_validity' => false,
            ])->save();
        });
    }

    public function cancel(MembershipTransaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $transaction = MembershipTransaction::query()->lockForUpdate()->findOrFail($transaction->id);
            $this->require($transaction->status === 'Pending', 'Only Pending transactions can be cancelled.');
            $transaction->update(['status' => 'Cancelled']);
        });
    }

    private function require(bool $condition, string $message): void
    {
        if (! $condition) {
            throw ValidationException::withMessages(['transaction' => $message]);
        }
    }

    public static function cents(string $amount): int
    {
        $parts = explode('.', $amount);

        return (int) $parts[0] * 100 + (int) str_pad($parts[1] ?? '', 2, '0');
    }
}
