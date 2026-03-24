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

        <a href="{{ route('supervisor.dashboard') }}"
           class="nav-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-section-label">Interns</div>

        <a href="{{ route('supervisor.interns.index') }}"
           class="nav-item {{ request()->routeIs('supervisor.interns.index') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                <path d="M16 3.13a4 4 0 010 7.75"/>
            </svg>
            My Interns
            @php
                $internCount = \App\Models\OjtApplication::where('company_id', auth()->user()->company_id ?? 0)
                    ->where('status', 'approved')->count();
            @endphp
            @if($internCount > 0)
                <span style="margin-left:auto;padding:1px 7px;background:var(--teal-dim);color:var(--teal);
                             border-radius:20px;font-size:10.5px;font-weight:700;">
                    {{ $internCount }}
                </span>
            @endif
        </a>

        <div class="nav-section-label">Evaluate</div>
         <a href="{{ route('supervisor.hours.index') }}"
           class="nav-item {{ request()->routeIs('supervisor.hours.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
            </svg>
            Hour Logs
            @php
                $pendingLogsCount = \App\Models\HourLog::where('status','pending')->count();
            @endphp
            @if($pendingLogsCount > 0)
                <span style="margin-left:auto;padding:1px 7px;background:var(--blue-dim);color:var(--blue);
                             border-radius:20px;font-size:10.5px;font-weight:700;">
                    {{ $pendingLogsCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('supervisor.evaluations.index') }}"
           class="nav-item {{ request()->routeIs('supervisor.evaluations.*') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                <polyline points="22,4 12,14.01 9,11.01"/>
            </svg>
            Evaluations
            @php
                $pendingEvals = \App\Models\OjtApplication::where('company_id', auth()->user()->company_id ?? 0)
                    ->where('status', 'approved')
                    ->whereDoesntHave('evaluation')
                    ->count();
            @endphp
            @if($pendingEvals > 0)
                <span style="margin-left:auto;padding:1px 7px;background:var(--gold-dim);color:var(--gold);
                             border-radius:20px;font-size:10.5px;font-weight:700;">
                    {{ $pendingEvals }}
                </span>
            @endif
        </a>

        <!-- <div class="nav-section-label">System</div>

        <a href="{{ route('supervisor.profile.settings') }}"
           class="nav-item {{ request()->routeIs('supervisor.profile.settings') ? 'active' : '' }}">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
            </svg>
            Settings
        </a> -->

    </nav>

    {{-- User footer --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Supervisor' }}</div>
                <div class="sidebar-user-role">Company Supervisor</div>
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
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
                </div>
                <span class="topbar-user-name">{{ explode(' ', auth()->user()->name ?? 'Supervisor')[0] }}</span>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);margin-left:2px;">
                    <polyline points="6,9 12,15 18,9"/>
                </svg>

                <div class="user-dropdown" id="user-dropdown">
                    <a href="{{ route('supervisor.profile.settings') }}" class="dropdown-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile & Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
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

        @if(session('info'))
        <div style="background:var(--surface2);border:1px solid var(--border2);border-radius:8px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--muted2);">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ session('info') }}
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

    const mq = window.matchMedia('(max-width: 768px)');
    function handleMq(e) {
        document.getElementById('menu-toggle').style.display = e.matches ? 'flex' : 'none';
    }
    mq.addEventListener('change', handleMq);
    handleMq(mq);
</script>

</body>
</html>