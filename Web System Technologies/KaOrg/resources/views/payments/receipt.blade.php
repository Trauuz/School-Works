@extends('layouts.app')
@section('content')
<header class="page-header no-print">
<h1>Membership receipt</h1>
<button class="primary-action" type="button" data-print>Print receipt</button>
</header>
<section class="profile-panel receipt">
<h2>KaOrg · Payment receipt</h2>
<dl class="detail-list">
<div>
<dt>Transaction reference</dt>
<dd>{{ $payment->transaction->reference }}</dd>
</div>
<div>
<dt>Member</dt>
<dd>{{ $payment->transaction->member->full_name }} · {{ $payment->transaction->member->member_number }}</dd>
</div>
<div>
<dt>Membership</dt>
<dd>{{ $payment->transaction->category_name }} / {{ $payment->transaction->level_name }}</dd>
</div>
<div>
<dt>Paid on</dt>
<dd>{{ $payment->paid_at->format('M j, Y H:i') }}</dd>
</div>
<div>
<dt>Amount received</dt>
<dd>PHP {{ number_format($payment->amount/100,2) }}</dd>
</div>
<div>
<dt>Transaction status</dt>
<dd>{{ $payment->transaction->display_status }}</dd>
</div>
</dl>
<a class="no-print" href="{{ route('transactions.show',$payment->transaction) }}">Back to transaction</a>
</section>

@endsection

