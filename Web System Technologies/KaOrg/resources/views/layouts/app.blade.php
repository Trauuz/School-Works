<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'KaOrg')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-auth-callback-url="{{ route('auth.confirm') }}">
    <div class="app-shell {{ request()->routeIs('login', 'register') ? 'app-shell-auth' : '' }}">
        @unless(request()->routeIs('login', 'register'))
        <aside class="sidebar" id="app-navigation">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="KaOrg home">
                <img class="brand-logo" src="{{ asset('kaorg-logo.png') }}" alt="KaOrg" width="180" height="120">
            </a>

            <nav class="primary-nav" aria-label="Primary navigation">
                @auth
                    @if(auth()->user()->profile_completed_at)
                        <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                        @can('manage-membership')
                            @foreach(['members' => 'Members', 'membership-status' => 'Membership status', 'categories' => 'Categories', 'levels' => 'Levels', 'transactions' => 'Transactions'] as $section => $label)
                                <a href="{{ route($section.'.index') }}" @if(request()->routeIs($section.'.*')) aria-current="page" @endif>{{ $label }}</a>
                            @endforeach
                        @endcan
                    @else
                        <a href="{{ route('account.setup') }}" aria-current="page">Complete profile</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Sign in</a>
                    <a href="{{ route('register') }}" @if(request()->routeIs('register')) aria-current="page" @endif>Sign up</a>
                @endauth
            </nav>

            <div class="sidebar-note">
                @auth
                    <div class="sidebar-account">
                        <a class="profile-avatar" href="{{ route(auth()->user()->profile_completed_at ? 'account.profile' : 'account.setup') }}" aria-label="Your profile" title="Your profile" @if(request()->routeIs('account.profile', 'account.setup')) aria-current="page" @endif>
                            <span aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->username ?? auth()->user()->name, 0, 1)) }}</span>
                        </a>
                        <div class="sidebar-account-details">
                            <strong title="{{ auth()->user()->username ?? auth()->user()->name }}">{{ auth()->user()->username ?? auth()->user()->name }}</strong>
                            <p>{{ auth()->user()->can_manage_members ? 'Administrator' : 'Member' }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="sidebar-action" type="submit">Sign out</button>
                        </form>
                    </div>
                @else
                    <p>Current term</p>
                    <strong>{{ now()->format('F Y') }}</strong>
                @endauth
            </div>
        </aside>

        <header class="mobile-bar">
            <button class="menu-button" type="button" aria-controls="app-navigation" aria-expanded="false">
                <span></span><span></span>
                <span class="sr-only">Open navigation</span>
            </button>
            <span>@yield('mobile-title', 'KaOrg')</span>
            @yield('mobile-action')
        </header>

        @endunless

        <main class="workspace">
            @if (session('success'))
                <div class="flash-message" role="status">
                    <span>{{ session('success') }}</span>
                    <button type="button" aria-label="Dismiss message">Dismiss</button>
                </div>
            @endif
            @if ($errors->any())<div class="flash-message" role="alert"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </main>
    </div>

    @stack('dialogs')
</body>
</html>
