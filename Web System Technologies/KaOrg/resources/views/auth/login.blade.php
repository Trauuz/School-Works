@extends('layouts.app')
@section('title', 'Sign in · KaOrg')
@section('mobile-title', 'Sign in')
@section('content')
<div class="login-wrap">
    <section class="login-panel">
        <h1>Sign in</h1>
        <p>Access your membership or manage the portal.</p>
        <form method="POST" action="{{ route('login.store') }}">
            @csrf
            <div class="field"><label for="username">Username</label><input type="text" id="username" name="username" autocomplete="username" maxlength="30" value="{{ old('username') }}" required autofocus></div>
            <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" autocomplete="current-password" required></div>
            <a class="text-link" href="{{ route('password.request') }}">Forgot password?</a>
            <button class="submit-button" type="submit">Sign in</button>
        </form>
        <p class="auth-footer">New here? <a class="text-link" href="{{ route('register') }}">Sign up</a></p>
    </section>
</div>
@endsection
