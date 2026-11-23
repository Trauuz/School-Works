@extends('layouts.app')
@section('content')
<div class="login-wrap"><section class="login-panel">
    <h1>Check your email</h1>
    <p>Follow the confirmation link to verify your email and complete your profile.</p>
    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" value="{{ old('email', session('email')) }}" required></div>
        <button class="submit-button">Resend confirmation</button>
    </form>
    <p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p>
</section></div>
@endsection
