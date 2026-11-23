<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMembershipStatusRequest;
use App\Models\Member;
use App\Services\MembershipManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MembershipStatusController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::with(['level', 'latestTransaction.payment'])
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = '%'.mb_strtolower(trim($request->string('q'))).'%';
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereRaw('LOWER(first_name) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(last_name) LIKE ?', [$search])
                        ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
                });
            })
            ->orderBy('last_name')->orderBy('first_name')->paginate(10)->withQueryString();

        return view('members.status', compact('members'));
    }

    public function update(UpdateMembershipStatusRequest $request, Member $member, MembershipManager $manager): RedirectResponse
    {
        $manager->updateStatus($member, $request->validated('payment_status') === 'paid', $request->validated('membership_status') === 'active');

        return back()->with('success', 'Payment and membership status updated.');
    }
}
