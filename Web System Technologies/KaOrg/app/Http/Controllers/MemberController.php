<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Services\MembershipManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $members = Member::query()
            ->with(['category', 'level'])
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $query->where(function (Builder $query) use ($request): void {
                    foreach (['first_name', 'last_name', 'email', 'member_number'] as $field) {
                        $query->orWhereRaw('LOWER('.$field.') LIKE ?', [mb_strtolower('%'.$request->string('q').'%')]);
                    }
                });
            })
            ->when($request->filled('category'), fn (Builder $query): Builder => $query->where('membership_category_id', $request->query('category')))
            ->when($request->filled('level'), fn (Builder $query): Builder => $query->where('membership_level_id', $request->query('level')))
            ->when($request->query('status') === 'active', fn (Builder $query): Builder => $query->active())
            ->when($request->query('status') === 'inactive', fn (Builder $query): Builder => $query->whereNotNull('deactivated_at'))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', ['members' => $members, 'categories' => MembershipCategory::all(), 'levels' => MembershipLevel::all()]);
    }

    public function create(): View
    {
        return view('members.create', ['categories' => MembershipCategory::all(), 'levels' => MembershipLevel::all()]);
    }

    public function store(StoreMemberRequest $request, MembershipManager $manager): RedirectResponse
    {
        $member = DB::transaction(function () use ($request, $manager): Member {
            $member = Member::create($request->validated());
            $transaction = $manager->create($member, 'application');
            $manager->pay($transaction, $transaction->fee);

            return $member;
        });

        return redirect()->route('members.show', $member)->with('success', 'Member registered. Membership fee recorded as paid.');
    }

    public function show(Member $member): View
    {
        return view('members.show', [
            'member' => $member->load(['category', 'level']),
            'transactions' => $member->transactions()->with('payment')->latest()->paginate(10),
        ]);
    }

    public function edit(Member $member): View
    {
        return view('members.edit', ['member' => $member, 'categories' => MembershipCategory::all(), 'levels' => MembershipLevel::all()]);
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validated());

        return redirect()->route('members.show', $member)->with('success', 'Member updated.');
    }

    public function deactivate(Member $member): RedirectResponse
    {
        $member->forceFill(['deactivated_at' => $member->deactivated_at ?? now()])->save();

        return redirect()->route('members.show', $member)->with('success', 'Member deactivated. History preserved.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        DB::transaction(function () use ($member): void {
            $member = Member::query()->lockForUpdate()->findOrFail($member->id);
            $member->forceFill(['deactivated_at' => $member->deactivated_at ?? now()])->save();
            $member->transactions()->where('status', 'Pending')->update(['status' => 'Cancelled']);
            $member->delete();
        });

        return redirect()->route('members.index')->with('success', 'Member deleted. Payment and transaction history preserved.');
    }
}
