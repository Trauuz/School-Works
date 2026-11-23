@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>{{ $item->exists?'Edit':'Add' }} {{ $kind==='levels'?'level':'category' }}</h1>
</header>
<form class="profile-panel" method="POST" action="{{ $item->exists?route($kind.'.update',$item):route($kind.'.store') }}">
@csrf
 @if($item->exists) @method('PUT')
@endif
<div class="form-grid">
<div class="field">
<label for="name">Name</label>
<input id="name" name="name" value="{{ old('name',$item->name) }}" required maxlength="100">
</div>@if($kind==='levels')<div class="field">
<label for="fee">Fee (PHP)</label>
<input id="fee" name="fee" type="number" min="0.01" step="0.01" value="{{ old('fee',$item->exists?number_format($item->fee/100,2,'.',''):'') }}" required>
</div>
<div class="field">
<label for="validity_days">Validity (days)</label>
<input id="validity_days" name="validity_days" type="number" min="1" max="36500" value="{{ old('validity_days',$item->validity_days) }}" required>
</div>
<div class="field">
<label for="benefits">Benefits (optional)</label>
<textarea id="benefits" name="benefits" rows="6">{{ old('benefits',$item->benefits) }}</textarea>
</div>
@endif
</div>
<div class="form-actions">
<a href="{{ route($kind.'.index') }}">Cancel</a>
<button class="primary-action">Save</button>
</div>
</form>

@endsection

