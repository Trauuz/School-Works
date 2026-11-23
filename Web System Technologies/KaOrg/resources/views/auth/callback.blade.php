@extends('layouts.auth')
@section('title', 'Verify your email link Â· KaOrg')
@section('mobile-title', 'Verify email link')
@section('content')
<div class="login-wrap"><section class="login-panel" data-auth-callback data-flow="{{ $flow ?? request('flow', 'signup') }}">
    <h1 data-callback-title>Checking your email link</h1>
    <p data-callback-message role="status">Please wait while KaOrg verifies your link.</p>
    <form method="POST" action="{{ route('auth.confirm') }}" data-callback-form hidden>
        @csrf
        <input type="hidden" name="access_token">
        <input type="hidden" name="refresh_token">
        <input type="hidden" name="type">
    </form>
    <noscript><p>Enable JavaScript to open this email link, then reload this page.</p></noscript>
    <p class="auth-footer"><a class="text-link" href="{{ route('verification.notice') }}">Resend confirmation</a></p>
    <p class="auth-footer"><a class="text-link" href="{{ route('password.request') }}">Request a password reset</a></p>
    <p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p>
</section></div>
@endsection
