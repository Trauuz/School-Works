@extends('layouts.app')
@section('title', 'Sign up · KaOrg')
@section('content')
<div class="login-wrap">
    <section class="login-panel">
        <h1>Sign up</h1>
        <p>Verify your email, then complete your administrator profile.</p>
        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" value="{{ old('username') }}" minlength="3" maxlength="30" pattern="[a-zA-Z0-9_]+" required>
            </div>
            <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required></div>
            <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required><p class="field-help">Use at least 8 characters.</p></div>
            <div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div>
            <button class="submit-button">Sign up</button>
        </form>
        <p class="auth-footer">Already registered? <a class="text-link" href="{{ route('login') }}">Sign in</a></p>
    </section>
</div>
@endsection
