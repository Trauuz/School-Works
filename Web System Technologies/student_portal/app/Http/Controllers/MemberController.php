<?php

namespace App\Http\Controllers;

use App\EligibilityStatus;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\MemberCategory;
use App\MemberLevel;
use App\Models\Member;
use App\Services\MembershipEntitlements;
use App\Services\MembershipPricing;
use App\Services\SubscriptionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(
        public SubscriptionManager $subscriptions,
        public MembershipPricing $pricing,
        public MembershipEntitlements $entitlements,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $category = MemberCategory::tryFrom((string) $request->query('category'));
        $level = MemberLevel::tryFrom((string) $request->query('level'));
        $defaultStatus = $request->routeIs('dashboard') ? 'all' : 'active';
        $status = in_array($request->query('status'), ['active', 'inactive', 'all'], true)
            ? $request->query('status')
            : $defaultStatus;
        $search = trim((string) $request->query('q'));

        $members = Member::query()
            ->when($status === 'active', fn ($query) => $query->active())
            ->when($status === 'inactive', fn ($query) => $query->deactivated())
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($category, fn ($query) => $query->category($category))
            ->when($level, fn ($query) => $query->level($level))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('members.index', compact('members', 'category', 'level', 'status', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('members.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $attributes['eligibility_verified_at'] = $attributes['eligibility_status'] === EligibilityStatus::Verified->value ? now() : null;
        $member = Member::create($attributes);

        if ($member->hasValidEligibility()) {
            $this->subscriptions->activate($member);
        }

        return redirect()->route('members.show', $member)->with('success', 'Member added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member): View
    {
        $subscription = $member->currentSubscription();
        $effectiveLevel = $subscription?->level ?? $member->level;
        $entitlements = $this->entitlements->for($effectiveLevel, $member->category);

        return view('members.show', compact('member', 'subscription', 'effectiveLevel', 'entitlements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member): View
    {
        return view('members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $subscription = $member->currentSubscription();
        $attributes = $request->validated();
        $attributes['eligibility_verified_at'] = $attributes['eligibility_status'] === EligibilityStatus::Verified->value ? ($member->eligibility_verified_at ?? now()) : null;
        $member->update($attributes);

        if (! $member->currentSubscription() && $member->hasValidEligibility()) {
            $this->subscriptions->activate($member);
        } elseif ($subscription && $member->level !== $subscription->level) {
            if ($member->level->rank() > $subscription->level->rank()) {
                $this->subscriptions->upgrade($subscription, $member->level);
            } else {
                $this->subscriptions->scheduleDowngrade($subscription, $member->level);
            }
        }

        return redirect()->route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $member->update(['deactivated_at' => now()]);

        return redirect()->route('members.show', $member)->with('success', 'Member deactivated.');
    }

    public function reactivate(Member $member): RedirectResponse
    {
        $member->update(['deactivated_at' => null]);

        return redirect()->route('members.show', $member)->with('success', 'Member reactivated.');
    }
}
