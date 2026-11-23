@extends('layouts.app')
@section('content')
<header class="page-header">
<div>
<h1>{{ ucfirst($transaction->type) }} transaction</h1>
<p>{{ $transaction->reference }}</p>
</div>
<a href="{{ auth()->user()->can_manage_members ? route('members.show',$transaction->member) : route('dashboard') }}">{{ $transaction->member->full_name }}</a>
</header>
<section class="profile-panel">
<dl class="detail-list">
<div>
<dt>Status</dt>
<dd>{{ $transaction->display_status }}</dd>
</div>
<div>
<dt>Membership</dt>
<dd>{{ $transaction->category_name }} / {{ $transaction->level_name }}</dd>
</div>
<div>
<dt>Fee</dt>
<dd>PHP {{ number_format($transaction->fee/100,2) }}</dd>
</div>
<div>
<dt>Validity</dt>
<dd>{{ $transaction->validity_days }} days</dd>
</div>
<div>
<dt>Approved period</dt>
<dd>{{ $transaction->starts_at?->format('M j, Y H:i') ?? 'Not approved' }} — {{ $transaction->expires_at?->format('M j, Y H:i') ?? '—' }}</dd>
</div>
<div>
<dt>Payment</dt>
<dd>@if($transaction->payment)PHP {{ number_format($transaction->payment->amount/100,2) }} · {{ $transaction->payment->paid_at->format('M j, Y H:i') }} <a href="{{ route('payments.show',$transaction->payment) }}">View receipt</a>
@else
 Unpaid
@endif
</dd>
</div>
</dl>
@can('manage-membership')
<div class="form-actions">@if($transaction->status==='Pending')@if(!$transaction->member->deactivated_at)<a class="primary-action" href="{{ route('payments.create',$transaction) }}">Record payment</a>
@endif
<form method="POST" action="{{ route('transactions.cancel',$transaction) }}">
@csrf
 @method('PATCH')<button class="danger-action">Cancel transaction</button>
</form>@elseif($transaction->status==='Paid' && !$transaction->member->deactivated_at)<form method="POST" action="{{ route('transactions.approve',$transaction) }}">
@csrf
 @method('PATCH')<button class="primary-action">Approve membership</button>
</form>
@endif
</div>
@endcan
</section>

@endsection

