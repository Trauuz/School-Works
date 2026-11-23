<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Services\MembershipManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        return view('transactions.index', ['transactions' => MembershipTransaction::with(['member', 'payment'])->latest()->paginate(15)]);
    }

    public function create(): View
    {
        return view('transactions.create', ['members' => Member::whereNull('deactivated_at')->orderBy('last_name')->get()]);
    }

    public function store(TransactionRequest $request, MembershipManager $manager): RedirectResponse
    {
        $transaction = $manager->create(Member::findOrFail($request->validated('member_id')), $request->validated('type'));

        return redirect()->route('transactions.show', $transaction);
    }

    public function show(MembershipTransaction $transaction): View
    {
        Gate::authorize('view-transaction', $transaction);

        return view('transactions.show', ['transaction' => $transaction->load(['member', 'payment'])]);
    }

    public function approve(MembershipTransaction $transaction, MembershipManager $manager): RedirectResponse
    {
        $manager->approve($transaction);

        return back()->with('success', 'Transaction approved.');
    }

    public function cancel(MembershipTransaction $transaction, MembershipManager $manager): RedirectResponse
    {
        $manager->cancel($transaction);

        return back()->with('success', 'Transaction cancelled.');
    }
}
