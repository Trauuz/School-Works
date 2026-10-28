<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light">
    <title>@yield('title', 'Student Portal')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="app-navigation">
            <a class="brand" href="{{ route('tasks.index') }}" aria-label="Student Portal home">
                <span class="brand-mark" aria-hidden="true"><span></span><span></span><span></span></span>
                <span>Student Portal</span>
            </a>

            <nav class="primary-nav" aria-label="Primary navigation">
                <p class="nav-heading">Workspace</p>
                <a href="{{ route('tasks.index') }}" @if(($activeNavigation ?? '') === 'tasks') aria-current="page" @endif>Tasks</a>
                <a href="{{ route('dashboard') }}" @if(($activeNavigation ?? '') === 'members') aria-current="page" @endif>Member dashboard</a>
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
            <span>@yield('mobile-title', 'Student Portal')</span>
            @yield('mobile-action')
        </header>

        <main class="workspace">
            @if (session('success'))
                <div class="flash-message" role="status">
                    <span>{{ session('success') }}</span>
                    <button type="button" aria-label="Dismiss message">Dismiss</button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    @stack('dialogs')
</body>
</html>
