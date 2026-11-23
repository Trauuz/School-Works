@extends('layouts.auth')
@section('title', 'Email link unavailable Â· KaOrg')
@section('mobile-title', 'Email link unavailable')
@section('content')
<div class="login-wrap"><section class="login-panel">
    <h1>We couldnâ€™t open this link</h1>
    <p role="alert">{{ $message }}</p>
    <p><a class="text-link" href="{{ route('verification.notice') }}">Resend confirmation email</a></p>
    <p><a class="text-link" href="{{ route('password.request') }}">Request a password reset</a></p>
    <p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p>
</section></div>
@endsection
