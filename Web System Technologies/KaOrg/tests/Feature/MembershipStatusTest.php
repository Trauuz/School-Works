<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\Member;
use App\Models\Payment;
use App\Models\User;
use App\Services\MembershipManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(EnsureSupabaseSession::class);
});

test('membership status pages and updates require an administrator', function () {
    $member = Member::factory()->create();
    $this->get(route('membership-status.index'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create())->get(route('membership-status.index'))->assertForbidden();
    $this->patch(route('membership-status.update', $member), ['payment_status' => 'paid', 'membership_status' => 'active'])->assertForbidden();
});

test('administrator can pay activate deactivate and reverse a member payment without duplicates', function () {
    $member = Member::factory()->create();
    $this->actingAs(User::factory()->create(['can_manage_members' => true]));
    $update = route('membership-status.update', $member);
    $this->patch($update, ['payment_status' => 'paid', 'membership_status' => 'active'])->assertSessionHasNoErrors();
    $transaction = $member->transactions()->firstOrFail();
    expect($member->fresh()->is_active)->toBeTrue()->and($transaction->status)->toBe('Approved');
    $paymentId = $transaction->payment->id;
    $this->get(route('dashboard'))->assertViewHas('collected', $transaction->fee)->assertViewHas('active', 1);
    $this->patch($update, ['payment_status' => 'paid', 'membership_status' => 'active'])->assertSessionHasNoErrors();
    $this->assertDatabaseCount('payments', 1);
    $this->assertDatabaseCount('membership_transactions', 1);
    $this->patch($update, ['payment_status' => 'paid', 'membership_status' => 'inactive'])->assertSessionHasNoErrors();
    expect($member->fresh()->is_active)->toBeFalse();
    $this->patch($update, ['payment_status' => 'unpaid', 'membership_status' => 'inactive'])->assertSessionHasNoErrors();
    expect($transaction->fresh()->status)->toBe('Pending')->and($transaction->fresh()->payment)->toBeNull()->and(Payment::findOrFail($paymentId)->voided_at)->not->toBeNull();
    $this->get(route('dashboard'))->assertViewHas('collected', 0)->assertViewHas('active', 0);
    $this->get(route('payments.show', $paymentId))->assertOk()->assertSee('excluded from collected payments');
    $this->patch($update, ['payment_status' => 'paid', 'membership_status' => 'active'])->assertSessionHasNoErrors();
    expect($member->fresh()->is_active)->toBeTrue()->and($transaction->fresh()->payment->id)->toBe($paymentId);
    $this->assertDatabaseCount('payments', 1);
});

test('unpaid active memberships invalid statuses and deleted members cannot be updated', function () {
    $member = Member::factory()->create();
    $this->actingAs(User::factory()->create(['can_manage_members' => true]));
    $this->patch(route('membership-status.update', $member), ['payment_status' => 'unpaid', 'membership_status' => 'active'])->assertSessionHasErrors('transaction');
    $this->patch(route('membership-status.update', $member), ['payment_status' => 'other', 'membership_status' => 'other'])->assertSessionHasErrors(['payment_status', 'membership_status']);
    $this->assertDatabaseCount('payments', 0);
    $member->delete();
    $this->patch(route('membership-status.update', $member), ['payment_status' => 'paid', 'membership_status' => 'active'])->assertNotFound();
});

test('status page searches members and includes current saved statuses', function () {
    $member = Member::factory()->create(['first_name' => 'Mara', 'last_name' => 'Santos']);
    Member::factory()->create(['first_name' => 'Other', 'last_name' => 'Person']);
    app(MembershipManager::class)->updateStatus($member, true, true);
    $this->actingAs(User::factory()->create(['can_manage_members' => true]))->get(route('membership-status.index', ['q' => 'Mara']))->assertOk()->assertSee('Mara Santos')->assertDontSee('Other Person')->assertSee('Membership status');
});
