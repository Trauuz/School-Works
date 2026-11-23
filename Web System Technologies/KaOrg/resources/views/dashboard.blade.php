@extends('layouts.app')
@section('content')
@include('account._greeting')
<header class="page-header">
<h2>Membership dashboard</h2>
<a class="primary-action" href="{{ route('members.create') }}">Register member</a>
</header>
<section class="summary-grid" aria-label="Membership totals">@foreach(['Total members'=>$total,'Active members'=>$active,'Pending transactions'=>$pending,'Collected payments'=>'PHP '.number_format($collected/100,2)] as $label=>$value)<div class="profile-panel">
<h2>{{ $label }}</h2>
<strong class="summary-value">{{ $value }}</strong>
</div>@endforeach</section>
<div class="form-actions">
<a href="{{ route('members.index') }}">View members</a>
<a href="{{ route('transactions.index') }}">View transactions</a>
</div>
@endsection
