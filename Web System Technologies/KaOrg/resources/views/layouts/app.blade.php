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
<body>
    <div class="app-shell">
        <aside class="sidebar" id="app-navigation">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="KaOrg home">
                <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
                <span>KaOrg</span>
            </a>

            <nav class="primary-nav" aria-label="Primary navigation">
                @auth
                    @if(auth()->user()->profile_completed_at)
                        <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>Dashboard</a>
                        @can('manage-membership')
                            @foreach(['members' => 'Members', 'categories' => 'Categories', 'levels' => 'Levels', 'transactions' => 'Transactions'] as $section => $label)
                                <a href="{{ route($section.'.index') }}" @if(request()->routeIs($section.'.*')) aria-current="page" @endif>{{ $label }}</a>
                            @endforeach
                        @endcan
                        <a href="{{ route('account.profile') }}" @if(request()->routeIs('account.profile')) aria-current="page" @endif>Your profile</a>
                    @else
                        <a href="{{ route('account.setup') }}" aria-current="page">Complete profile</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" @if(request()->routeIs('login')) aria-current="page" @endif>Sign in</a>
                    <a href="{{ route('register') }}" @if(request()->routeIs('register')) aria-current="page" @endif>Create account</a>
                @endauth
            </nav>

            <div class="sidebar-note">
                @auth
                    <p>Signed in as</p>
                    <strong>{{ auth()->user()->name }}</strong>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="sidebar-action" type="submit">Sign out</button>
                    </form>
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
