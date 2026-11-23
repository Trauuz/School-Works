@extends('layouts.app')
@section('title', 'Your membership · KaOrg')
@section('content')
@include('account._greeting')
<header class="page-header"><h2>Your membership</h2><a class="secondary-action" href="{{ route('account.profile') }}">Edit contact details</a></header>
@if($member)
<section class="profile-panel">
    <dl class="detail-list">
        <div><dt>Membership number</dt><dd>{{ $member->member_number }}</dd></div>
        <div><dt>Category / level</dt><dd>{{ $member->category->name }} / {{ $member->level->name }}</dd></div>
        <div><dt>Status</dt><dd>{{ $member->deactivated_at ? 'Deactivated' : ($member->is_active ? 'Active' : 'No current membership') }}</dd></div>
        <div><dt>Membership expires</dt><dd>{{ $member->membership_expires_at?->format('M j, Y H:i') ?? 'Awaiting an approved application' }}</dd></div>
    </dl>
    @if(!$member->deactivated_at && $member->membership_expires_at)
        <form class="form-actions" method="POST" action="{{ route('account.renew') }}">@csrf<button class="primary-action">Request renewal</button></form>
    @endif
    <p class="field-help">An administrator records your payment and approves your membership. Only one unpaid or unapproved request can be open at a time.</p>
</section>
<h2 class="section-title">Your transaction history</h2>
@include('transactions._table')
<div class="pagination">{{ $transactions->links() }}</div>
@else
<section class="profile-panel"><p>No membership is linked to this account. Contact an administrator for help.</p></section>
@endif
@endsection
