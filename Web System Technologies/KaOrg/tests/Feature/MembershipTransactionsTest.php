<?php

use App\Http\Middleware\EnsureSupabaseSession;
use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Models\User;
use App\Services\MembershipManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->withoutMiddleware(EnsureSupabaseSession::class);
});
function applicationTransaction(): MembershipTransaction
{
    $member = Member::factory()->create();

    return app(MembershipManager::class)->create($member, 'application');
}
test('full payment then approval activates membership and prints receipt', function () {
    $transaction = applicationTransaction();
    $this->actingAs(memberManager())->patch(route('transactions.approve', $transaction))->assertSessionHasErrors('transaction');
    $this->post(route('payments.store'), ['membership_transaction_id' => $transaction->id, 'amount' => number_format($transaction->fee / 100, 2, '.', '')])->assertRedirect();
    expect($transaction->fresh()->status)->toBe('Paid')->and($transaction->member->fresh()->is_active)->toBeFalse();
    $this->patch(route('transactions.approve', $transaction))->assertSessionHasNoErrors();
    expect($transaction->fresh()->status)->toBe('Approved')->and($transaction->member->fresh()->is_active)->toBeTrue();
    $payment = Payment::firstOrFail();
    $this->get(route('payments.show', $payment))->assertOk()->assertSee($transaction->reference);
    $this->get(route('members.show', $transaction->member))->assertOk()->assertSee($transaction->reference);
    $this->get(route('dashboard'))->assertOk()->assertSee('Collected payments');
    foreach (['transactions.index', 'transactions.create'] as $route) {
        $this->get(route($route))->assertOk();
    } $this->get(route('transactions.show', $transaction))->assertOk();
});
test('invalid amounts and duplicate payments do not change financial records', function () {
    $transaction = applicationTransaction();
    $this->actingAs(memberManager());
    foreach (['0', '-1', '1', '1.001', '1e3', '999999999999999999'] as $amount) {
        $this->post(route('payments.store'), ['membership_transaction_id' => $transaction->id, 'amount' => $amount])->assertSessionHasErrors();
    } $this->assertDatabaseCount('payments', 0);
    $manager = app(MembershipManager::class);
    $manager->pay($transaction, $transaction->fee);
    $this->post(route('payments.store'), ['membership_transaction_id' => $transaction->id, 'amount' => (string) ($transaction->fee / 100)])->assertSessionHasErrors('transaction');
    $this->patch(route('transactions.cancel', $transaction))->assertSessionHasErrors('transaction');
    $this->assertDatabaseCount('payments', 1);
    $this->post(route('payments.store'), ['membership_transaction_id' => 99999, 'amount' => '299.00'])->assertSessionHasErrors('membership_transaction_id');
});
test('pending cancellation allows a replacement and blocks subsequent payment', function () {
    $transaction = applicationTransaction();
    $this->actingAs(memberManager())->post(route('transactions.store'), ['member_id' => $transaction->member_id, 'type' => 'application'])->assertSessionHasErrors('transaction');
    $this->patch(route('transactions.cancel', $transaction))->assertSessionHasNoErrors();
    expect($transaction->fresh()->status)->toBe('Cancelled');
    $this->post(route('payments.store'), ['membership_transaction_id' => $transaction->id, 'amount' => (string) ($transaction->fee / 100)])->assertSessionHasErrors('transaction');
    $this->post(route('transactions.store'), ['member_id' => $transaction->member_id, 'type' => 'application'])->assertSessionHasNoErrors();
    $this->assertDatabaseCount('membership_transactions', 2);
});
test('deactivation prevents transactions payments and approval', function () {
    $transaction = applicationTransaction();
    $this->actingAs(memberManager())->delete(route('members.destroy', $transaction->member));
    $this->post(route('payments.store'), ['membership_transaction_id' => $transaction->id, 'amount' => (string) ($transaction->fee / 100)])->assertSessionHasErrors('transaction');
    $this->post(route('transactions.store'), ['member_id' => $transaction->member_id, 'type' => 'application'])->assertSessionHasErrors('transaction');
    $paid = applicationTransaction();
    app(MembershipManager::class)->pay($paid, $paid->fee);
    $this->delete(route('members.destroy', $paid->member));
    $this->patch(route('transactions.approve', $paid))->assertSessionHasErrors('transaction');
});
test('all financial actions reject non administrators', function () {
    $transaction = applicationTransaction();
    $this->post(route('payments.store'), [])->assertRedirect('/login');
    $this->patch(route('transactions.approve', $transaction))->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->post(route('payments.store'), [])->assertForbidden();
    $this->patch(route('transactions.approve', $transaction))->assertForbidden();
});
