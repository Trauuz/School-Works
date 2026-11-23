@extends('layouts.auth')
@section('title', 'Email confirmed Â· KaOrg')
@section('mobile-title', 'Email confirmed')
@section('content')
<div class="login-wrap"><section class="login-panel">
    <h1>Youâ€™re confirmed!</h1>
    <p>Your email is verified, {{ auth()->user()->username ?? auth()->user()->name }}.</p>
    @if(auth()->user()->profile_completed_at)
        <p>Your KaOrg account is ready. Continue to your dashboard.</p>
        <a class="submit-button" href="{{ route('dashboard') }}">Open dashboard</a>
    @else
        <p>Complete your profile to finish setting up your KaOrg account.</p>
        <a class="submit-button" href="{{ route('account.setup') }}">Complete your profile</a>
    @endif
</section></div>
@endsection
