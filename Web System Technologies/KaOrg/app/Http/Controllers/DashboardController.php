<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        if (! $request->user()->can_manage_members) {
            $member = $request->user()->member?->load(['category', 'level']);

            return view('account.dashboard', ['member' => $member, 'transactions' => $member?->transactions()->with('payment')->latest()->paginate(10)]);
        }

        return view('dashboard', ['total' => Member::count(), 'active' => Member::active()->count(), 'pending' => MembershipTransaction::where('status', 'Pending')->count(), 'collected' => Payment::sum('amount')]);
    }
}
