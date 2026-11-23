@extends('layouts.app')
@section('title', 'Your profile · KaOrg')
@section('content')
<header class="page-header"><div><h1>Your contact details</h1><p>{{ $user->name }} · {{ $user->email }}</p></div></header>
<form class="form-panel account-form" method="POST" action="{{ route('account.profile.update') }}">
    @csrf @method('PUT')
    <div class="field"><label for="phone">Phone <span>Optional</span></label><input id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone', $user->phone) }}" maxlength="30"></div>
    @include('account._address')
    <div class="form-actions"><a class="secondary-action" href="{{ route('dashboard') }}">Back to dashboard</a><button class="primary-action">Save contact details</button></div>
</form>
@endsection
