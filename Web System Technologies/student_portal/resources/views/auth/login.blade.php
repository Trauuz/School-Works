@extends('layouts.app', ['activeNavigation' => 'login'])
@section('title', 'Staff login · Student Portal')
@section('mobile-title', 'Staff login')
@section('content')
    <div class="login-wrap">
        <section class="login-panel">
            <h1>Staff login</h1>
            <p>Sign in to manage the member directory.</p>
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<p class="field-error">{{ $message }}</p>@enderror</div>
                <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required></div>
                <label class="checkbox-row"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
                <button class="submit-button" type="submit">Sign in</button>
            </form>
        </section>
    </div>
@endsection
