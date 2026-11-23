@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>{{ $item->name }}</h1>
<a href="{{ route($kind.'.edit',$item) }}">Edit</a>
</header>
<section class="profile-panel">@if($kind==='levels')<p>PHP {{ number_format($item->fee/100,2) }} · {{ $item->validity_days }} days</p>
<p class="preserve-lines">{{ $item->benefits ?: 'No benefits specified.' }}</p>
@endif
<a href="{{ route($kind.'.index') }}">Back to {{ $kind }}</a>
</section>

@endsection

