<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="referrer" content="no-referrer"><title>@yield('title', 'Verify email · KaOrg')</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
<body data-auth-callback-url="{{ route('auth.confirm') }}"><main class="verification-shell"><a class="verification-brand" href="{{ route('login') }}" aria-label="KaOrg home"><img class="brand-logo" src="{{ asset('kaorg-logo.png') }}" alt="KaOrg" width="180" height="120"></a>
@if(session('success'))<p class="verification-notice" role="status">{{ session('success') }}</p>@endif
@if($errors->any())<div class="verification-notice" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@yield('content')</main></body></html>