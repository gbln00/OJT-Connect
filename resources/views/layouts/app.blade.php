<!DOCTYPE html>
<html lang="en" data-appearance="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OJT Connect') — OJT Connect</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/layout/app.layout.css', 'resources/js/app.js'])

</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">O</div>
        <div class="sidebar-logo-text">OJT<span>Connect</span></div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        <a href="{{ route('admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-section-label">Management</div>

        <a href="{{ route('admin.users.index') }}"
           class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Users
        </a>

        <a href="{{ route('admin.companies.index') }}"
           class="nav-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <polyline points="9,22 9,12 15,12 15,22"/>
            </svg>
            Companies
        </a>

        <a href="{{ route('admin.applications.index') }}"
            class="nav-item {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
            </svg>
            Applications
        </a>

        <a href="{{ route('admin.hours.index') }}"
            class="nav-item {{ request()->routeIs('admin.hours.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
            </svg>
            Hour Logs
        </a>

        <a href="{{ route('admin.reports.index') }}"
            class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
            </svg>
            Weekly Reports
        </a>

        <a href="{{ route('admin.evaluations.index') }}"
            class="nav-item {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10,9 9,9 8,9"/>
            </svg>
           Evaluations
        </a>

        <a href="{{ route('admin.export.index') }}"
            class="nav-item {{ request()->routeIs('admin.export.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7,10 12,15 17,10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Reports
        </a>

        <div class="nav-section-label">System</div>

        <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
            </svg>
            Settings
        </a>

    </nav>

    {{-- User footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="sidebar-user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}</div>
            </div>
        </div>
    </div>

</aside>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ── MAIN LAYOUT BODY ── --}}
<div class="layout-body">

    {{-- Topbar --}}
    <header class="topbar">

        {{-- Mobile menu toggle --}}
        <button class="topbar-btn" id="menu-toggle" onclick="toggleSidebar()" style="display:none;" aria-label="Menu">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

        <div class="topbar-actions">

            {{-- Appearance toggle --}}
            <button class="topbar-btn" onclick="toggleAppearance()" title="Toggle theme" aria-label="Toggle theme">
                <svg id="theme-icon-dark" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                <svg id="theme-icon-light" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>

            <div class="topbar-divider"></div>

            {{-- User dropdown --}}
            <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                <div class="topbar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                </div>
                <span class="topbar-user-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);margin-left:2px;">
                    <polyline points="6,9 12,15 18,9"/>
                </svg>

                <div class="user-dropdown" id="user-dropdown">
                    <a href="{{ route('admin.settings') }}" class="dropdown-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile & Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="/logout" id="logout-form">
                        @csrf
                    </form>
                    <a href="#" class="dropdown-item danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Log out
                    </a>
                </div>
            </div>

        </div>
    </header>

    {{-- Page content --}}
    <main class="main-content">

        {{-- Flash messages --}}
        @if(session('success'))
        <div style="background:var(--teal-dim);border:1px solid rgba(45,212,191,0.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--teal);">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="20,6 9,17 4,12"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div style="background:var(--coral-dim);border:1px solid rgba(248,113,113,0.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--coral);">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')

    </main>

</div>{{-- end layout-body --}}

<script>
    // ── Dropdown ──────────────────────────────────────────────────
    function toggleDropdown() {
        document.getElementById('user-dropdown').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('topbar-user-btn');
        const dd  = document.getElementById('user-dropdown');
        if (btn && !btn.contains(e.target)) dd.classList.remove('open');
    });

    // ── Theme ─────────────────────────────────────────────────────
    (function() {
        const saved = localStorage.getItem('appearance') || 'light';
        applyAppearance(saved);
    })();

    function applyAppearance(mode) {
        document.documentElement.setAttribute('data-appearance', mode);
        document.getElementById('theme-icon-dark').style.display  = mode === 'light' ? 'block' : 'none';
        document.getElementById('theme-icon-light').style.display = mode === 'dark'  ? 'block' : 'none';
        localStorage.setItem('appearance', mode);
    }

    function toggleAppearance() {
        const current = document.documentElement.getAttribute('data-appearance') || 'light';
        applyAppearance(current === 'light' ? 'dark' : 'light');
    }

    // ── Mobile sidebar ────────────────────────────────────────────
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebar-overlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar-overlay').classList.remove('open');
    }

    // Show hamburger on small screens
    const mq = window.matchMedia('(max-width: 768px)');
    function handleMq(e) {
        document.getElementById('menu-toggle').style.display = e.matches ? 'flex' : 'none';
    }
    mq.addEventListener('change', handleMq);
    handleMq(mq);
</script>

</body>
</html>