@extends('layouts.app')
@section('content')
<header class="page-header">
<div>
<h1>{{ $member->full_name }}</h1>
<p>{{ $member->member_number }}</p>
</div>
<a class="primary-action" href="{{ route('members.edit',$member) }}">Edit member</a>
</header>
<div class="member-details">
<section class="profile-panel">
<dl class="detail-list">
<div>
<dt>Email</dt>
<dd>{{ $member->email }}</dd>
</div>
<div>
<dt>Phone</dt>
<dd>{{ $member->phone ?: 'Not provided' }}</dd>
</div>
<div>
<dt>Category / level</dt>
<dd>{{ $member->category->name }} / {{ $member->level->name }}</dd>
</div>
<div>
<dt>Status</dt>
<dd>{{ $member->deactivated_at ? 'Deactivated' : ($member->is_active ? 'Active' : 'No current membership') }}</dd>
</div>
<div>
<dt>Membership expires</dt>
<dd>{{ $member->membership_expires_at?->format('M j, Y H:i') ?? 'Awaiting approved application' }}{{ $member->legacy_validity ? ' (legacy membership)' : '' }}</dd>
</div>
</dl>
<div class="form-actions">
@if(!$member->deactivated_at)
<a class="primary-action" href="{{ route('transactions.create',['member'=>$member->id]) }}">Create transaction</a>
<form data-confirm="Deactivate this member? Their history will be preserved." method="POST" action="{{ route('members.deactivate',$member) }}">@csrf @method('PATCH')<button class="danger-action">Deactivate member</button>
</form>
@endif
<form data-confirm="Delete this member? They will be removed from the directory. Payment and transaction history will be preserved." method="POST" action="{{ route('members.destroy', $member) }}">
@csrf @method('DELETE')<button class="danger-action" type="submit">Delete member</button>
</form>
</div></section>
<h2>Transaction history</h2>@include('transactions._table')<div class="pagination">{{ $transactions->links() }}</div>
</div>
@endsection
