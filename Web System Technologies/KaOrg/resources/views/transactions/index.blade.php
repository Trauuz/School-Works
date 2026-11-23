@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>Transactions</h1>
<a class="primary-action" href="{{ route('transactions.create') }}">Create transaction</a>
</header>@include('transactions._table')<div class="pagination workspace-pagination">{{ $transactions->links() }}</div>

@endsection

