@extends('layouts.auth')
@section('title', 'Check your email · KaOrg')
@section('content')
<section class="login-panel" data-verification-status="{{ route('verification.status') }}">
<h1>Check your email</h1>
@if($pending)<p>Open the email from KaOrg sent to <strong>{{ $pending['email'] }}</strong> and select Confirm email to continue.</p>@else<p>Enter your email to request another confirmation link.</p>@endif
<form method="POST" action="{{ route('verification.resend') }}">@csrf
@unless($pending)<div class="field"><label for="email">Email</label><input id="email" type="email" name="email" autocomplete="email" value="{{ old('email') }}" required></div>@endunless
<button class="submit-button" data-resend-countdown="{{ $retryAfter }}">Resend confirmation email</button>
<noscript><p>Please wait at least 60 seconds between email requests.</p></noscript></form>
@if($pending)<p><button type="button" class="text-link" data-check-verification>I have confirmed my email</button></p><p data-verification-message role="status"></p><p><a class="text-link" href="{{ route('verification.correction') }}">Wrong email address?</a></p>@endif
<details><summary>Cannot find the email?</summary><p>Check your spam or junk folder. If it is not there, request another email after the cooldown.</p></details>
<p class="auth-footer"><a class="text-link" href="{{ route('login') }}">Back to sign in</a></p></section>
@endsection