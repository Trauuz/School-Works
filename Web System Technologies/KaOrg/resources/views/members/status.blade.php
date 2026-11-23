@extends('layouts.app')
@section('title', 'Membership status · KaOrg')
@section('mobile-title', 'Membership status')
@section('content')
<header class="page-header"><div><h1>Membership status</h1><p>Manage payment and membership access for each member.</p></div></header>
<form class="filter-panel" method="GET" action="{{ route('membership-status.index') }}">
    <div class="field"><label for="q">Search members</label><input id="q" name="q" value="{{ request('q') }}" placeholder="Name or email"></div>
    <button class="filter-submit" type="submit">Search</button>
    <a class="filter-clear" href="{{ route('membership-status.index') }}">Clear</a>
</form>
<p class="membership-status-help">An active membership must be paid. Marking a payment unpaid removes its amount from collected payments.</p>
<section class="member-panel">
    <div class="member-table-wrap">
        <table class="member-table membership-status-table">
            <thead><tr><th>Member</th><th>Membership / fee</th><th>Payment</th><th>Membership status</th><th>Action</th></tr></thead>
            <tbody>
            @forelse($members as $member)
                @php($transaction = $member->latestTransaction)
                <tr>
                    <td><a class="member-name" href="{{ route('members.show', $member) }}">{{ $member->full_name }}</a><span>{{ $member->email }}</span></td>
                    <td>{{ $transaction?->level_name ?? $member->level->name }}<span>PHP {{ number_format(($transaction?->fee ?? $member->level->fee) / 100, 2) }}</span></td>
                    <td><label class="sr-only" for="payment-{{ $member->id }}">Payment for {{ $member->full_name }}</label><select id="payment-{{ $member->id }}" name="payment_status" form="status-{{ $member->id }}"><option value="paid" @selected($transaction?->payment)>Paid</option><option value="unpaid" @selected(!$transaction?->payment)>Unpaid</option></select></td>
                    <td><label class="sr-only" for="membership-{{ $member->id }}">Membership status for {{ $member->full_name }}</label><select id="membership-{{ $member->id }}" name="membership_status" form="status-{{ $member->id }}"><option value="active" @selected($member->is_active)>Active</option><option value="inactive" @selected(!$member->is_active)>Inactive</option></select></td>
                    <td><form id="status-{{ $member->id }}" method="POST" action="{{ route('membership-status.update', $member) }}">@csrf @method('PATCH')<button class="primary-action" type="submit" aria-label="Save status for {{ $member->full_name }}">Save</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5">No members found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $members->links() }}</div>
</section>
@endsection
