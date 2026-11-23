<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountProfileRequest;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Services\AccountSetup;
use App\Services\MembershipManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function setup(Request $request): View|RedirectResponse
    {
        if ($request->user()->profile_completed_at) {
            return redirect()->route('dashboard');
        }

        return view('account.setup', ['user' => $request->user(), 'categories' => MembershipCategory::orderBy('name')->get(), 'levels' => MembershipLevel::orderBy('fee')->get()]);
    }

    public function complete(AccountProfileRequest $request, AccountSetup $setup): RedirectResponse
    {
        $setup->complete($request->user(), $request->validated());

        return redirect()->route('dashboard')->with('success', 'Your profile is ready.');
    }

    public function edit(Request $request): View
    {
        return view('account.profile', ['user' => $request->user()]);
    }

    public function update(AccountProfileRequest $request, AccountSetup $setup): RedirectResponse
    {
        $setup->update($request->user(), $request->validated());

        return redirect()->route('account.profile')->with('success', 'Contact and address details updated.');
    }

    public function renew(Request $request, MembershipManager $manager): RedirectResponse
    {
        $member = $request->user()->member;
        abort_unless($member && ! $request->user()->can_manage_members, 403);
        $transaction = $manager->create($member, 'renewal');

        return redirect()->route('transactions.show', $transaction)->with('success', 'Renewal requested. Contact an administrator to record payment.');
    }
}
