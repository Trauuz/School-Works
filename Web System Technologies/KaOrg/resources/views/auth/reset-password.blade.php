@extends('layouts.app')
@section('title', 'Choose a new password · KaOrg')
@section('mobile-title', 'New password')
@section('content')
<div class="login-wrap"><section class="login-panel">
    <h1>Choose a new password</h1>
    <p>Use at least 8 characters.</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <div class="field"><label for="password">New password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required></div>
        <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div>
        <button class="submit-button">Save password</button>
    </form>
    <p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p>
</section></div>
@endsection
