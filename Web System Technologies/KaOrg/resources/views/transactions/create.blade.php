@extends('layouts.app')
@section('content')
<header class="page-header">
<h1>Create membership transaction</h1>
</header>
<form class="profile-panel" method="POST" action="{{ route('transactions.store') }}">
@csrf
<div class="form-grid">
<div class="field">
<label for="member_id">Member</label>
<select id="member_id" name="member_id" required>@foreach($members as $member)<option value="{{ $member->id }}" @selected(old('member_id',request('member'))==$member->id)>{{ $member->full_name }} · {{ $member->member_number }}</option>
@endforeach
</select>
</div>
<div class="field">
<label for="type">Transaction type</label>
<select id="type" name="type">
<option value="renewal" @selected(old('type')==='renewal')>Renewal</option>
<option value="application" @selected(old('type')==='application')>Application</option>
</select>
</div>
</div>
<p>Uses the member's selected category and level. An application is created automatically during registration.</p>
<div class="form-actions">
<button class="primary-action">Create transaction</button>
</div>
</form>

@endsection

