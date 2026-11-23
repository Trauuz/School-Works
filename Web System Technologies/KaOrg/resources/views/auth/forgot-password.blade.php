@extends('layouts.app')
@section('content')
<div class="login-wrap"><section class="login-panel">
    <h1>Reset your password</h1>
    <p>Enter your email and we’ll send a link to choose a new password.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email') }}" required></div>
        <button class="submit-button">Send reset link</button>
    </form>
    <p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p>
</section></div>
@endsection
