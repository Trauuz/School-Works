<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Services\MembershipManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(MembershipTransaction $transaction): View
    {
        return view('payments.create', compact('transaction'));
    }

    public function store(PaymentRequest $request, MembershipManager $manager): RedirectResponse
    {
        $payment = $manager->pay(MembershipTransaction::findOrFail($request->validated('membership_transaction_id')), MembershipManager::cents((string) $request->input('amount')));

        return redirect()->route('transactions.show', $payment->membership_transaction_id)->with('success', 'Payment recorded. Ready for approval.');
    }

    public function show(Payment $payment): View
    {
        Gate::authorize('view-transaction', $payment->transaction);

        return view('payments.receipt', ['payment' => $payment->load('transaction.member')]);
    }
}
