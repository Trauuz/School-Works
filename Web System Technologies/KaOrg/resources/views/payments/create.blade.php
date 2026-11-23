@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>Record payment</h1>
</header>
<form class="profile-panel" method="POST" action="{{ route('payments.store') }}">
@csrf
<input type="hidden" name="membership_transaction_id" value="{{ $transaction->id }}">
<p>{{ $transaction->reference }} · {{ $transaction->member->full_name }}</p>
<p>Full amount due: PHP {{ number_format($transaction->fee/100,2) }}</p>
<div class="field">
<label for="amount">Payment amount (PHP)</label>
<input id="amount" name="amount" type="number" step="0.01" min="0.01" required value="{{ old('amount',number_format($transaction->fee/100,2,'.','')) }}">
</div>
<div class="form-actions">
<a href="{{ route('transactions.show',$transaction) }}">Back</a>
<button class="primary-action">Record full payment</button>
</div>
</form>

@endsection

