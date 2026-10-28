@extends('layouts.app', ['activeNavigation' => 'members'])
@section('title', $member->full_name.' · Student Portal')
@section('mobile-title', 'Member profile')
@section('content')
<header class="page-header">
    <div>
        <h1>{{ $member->full_name }}</h1>
        <p>{{ $member->member_number }} · {{ $member->category->label() }} · {{ $effectiveLevel->label() }}</p>
    </div>
    <div class="page-header-actions">
        <a class="secondary-action" href="{{ route('dashboard') }}">Back to dashboard</a>
        <a class="primary-action" href="{{ route('members.edit', $member) }}">Edit member</a>
    </div>
</header>
<div class="profile-grid membership-profile-grid">
    <section class="profile-panel">
        <div class="profile-status"><span
                class="status-pill {{ $member->deactivated_at ? 'is-inactive' : '' }}">{{ $member->deactivated_at ? 'Inactive' : 'Active' }}</span>
        </div>
        <dl class="detail-list">
            <div>
                <dt>Email</dt>
                <dd><a href="mailto:{{ $member->email }}">{{ $member->email }}</a></dd>
            </div>
            <div>
                <dt>Phone</dt>
                <dd>{{ $member->phone ?: 'Not provided' }}</dd>
            </div>
            <div>
                <dt>Category</dt>
                <dd>{{ $member->category->label() }}</dd>
            </div>
            <div>
                <dt>Level</dt>
                <dd>{{ $member->level->label() }}</dd>
            </div>
            <div>
                <dt>Eligibility</dt>
                <dd>{{ $member->eligibility_status->label() }}</dd>
            </div>
            @if ($member->category === App\MemberCategory::Corporate)
            <div>
                <dt>Organization</dt>
                <dd>{{ $member->corporate_organization }}</dd>
            </div>
            <div>
                <dt>Active seats</dt>
                <dd>{{ $member->seat_count }}</dd>
            </div>
            @endif
            <div>
                <dt>Joined directory</dt>
                <dd>{{ $member->created_at->format('M j, Y') }}</dd>
            </div>
        </dl>
    </section>
    <div class="profile-rail">
        <aside class="profile-actions membership-summary">
            <h2>Membership</h2>
            @if ($subscription)
            <strong class="membership-price">₱{{ number_format($subscription->total_price_snapshot) }}<span>/30
                    days</span></strong>
            <p>{{ $subscription->category->label() }} ·
                {{ $subscription->level->label() }}{{ $subscription->seat_count > 1 ? ' · '.$subscription->seat_count.' seats' : '' }}
            </p>
            <dl class="compact-details">
                <div>
                    <dt>Started</dt>
                    <dd>{{ $subscription->starts_at->format('M j, Y') }}</dd>
                </div>
                <div>
                    <dt>Renews</dt>
                    <dd>{{ $subscription->expires_at->format('M j, Y') }}</dd>
                </div>
                <div>
                    <dt>Renewal</dt>
                    <dd>{{ $subscription->auto_renews ? 'Automatic' : 'Ends this cycle' }}</dd>
                </div>
            </dl>
            @else
            <p>Membership activation is waiting for valid category eligibility.</p>
            @endif
        </aside>
        <aside class="profile-actions">
            <h2>Access status</h2>
            @if ($member->deactivated_at)
            <p>This member was deactivated {{ $member->deactivated_at->diffForHumans() }}. Their record is preserved.
            </p>
            <form method="POST" action="{{ route('members.reactivate', $member) }}">@csrf @method('PATCH')<button
                    class="primary-action" type="submit">Reactivate member</button></form>
            @else
            <p>Deactivation removes this member from the active directory but keeps their record available.</p>
            <button class="danger-action" type="button" data-dialog-open="deactivate-member">Deactivate member</button>
            @endif
        </aside>
    </div>

</div>
@endsection

@if (! $member->deactivated_at)
@push('dialogs')
<dialog class="confirmation-dialog" id="deactivate-member">
    <form method="dialog"><button class="dialog-close" aria-label="Close">×</button></form>
    <h2>Deactivate {{ $member->full_name }}?</h2>
    <p>They will leave the active directory. This is reversible and their record will not be deleted.</p>
    <div class="dialog-actions">
        <button class="secondary-action" type="button" data-dialog-close>Cancel</button>
        <form method="POST" action="{{ route('members.destroy', $member) }}">@csrf @method('DELETE')<button
                class="danger-action" type="submit">Deactivate</button></form>
    </div>
</dialog>
@endpush
@endif