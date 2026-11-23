@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>Membership {{ $kind }}</h1>
<a class="primary-action" href="{{ route($kind.'.create') }}">Add {{ $kind==='levels'?'level':'category' }}</a>
</header>
<section class="member-panel">
<div class="member-table-wrap">
<table class="member-table">
<thead>
<tr>
<th>Name</th>@if($kind==='levels')<th>Fee (PHP)</th>
<th>Validity</th>
@endif
<th>Actions</th>
</tr>
</thead>
<tbody>@forelse($items as $item)<tr>
<td>
<a href="{{ route($kind.'.show',$item) }}">{{ $item->name }}</a>
</td>@if($kind==='levels')<td>{{ number_format($item->fee/100,2) }}</td>
<td>{{ $item->validity_days }} days</td>
@endif
<td>
<a href="{{ route($kind.'.edit',$item) }}">Edit</a>
<form data-confirm="Delete this entry? Referenced entries cannot be deleted." method="POST" action="{{ route($kind.'.destroy',$item) }}">
@csrf
 @method('DELETE')<button class="row-action row-action-danger">Delete</button>
</form>
</td>
</tr>
@empty
<tr>
<td>No entries yet.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>

@endsection

