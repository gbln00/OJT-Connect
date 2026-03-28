<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — OJTConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }

        :root {
            --crimson:     #8C0E03;
            --crimson2:    #a81004;
            --void:        #0D0D0D;
            --night:       #0E1126;
            --night2:      #111830;
            --steel:       #333740;
            --steel2:      #2a2e3a;
            --ash:         #ABABAB;
            --ash-dim:     rgba(171,171,171,0.5);
            --ash-faint:   rgba(171,171,171,0.12);
            --ash-ghost:   rgba(171,171,171,0.06);
            --border:      rgba(171,171,171,0.08);
            --border2:     rgba(171,171,171,0.14);
            --crimson-dim: rgba(140,14,3,0.12);
            --crimson-glow:rgba(140,14,3,0.08);
            --sb-w:        228px;
            --topbar-h:    63px;
        }

        body {
            font-family: 'Barlow', sans-serif;
            background: var(--void);
            color: var(--ash);
            display: flex;
            min-height: 100vh;
        }

        /* ═══ GRAIN OVERLAY ═══ */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 9999; opacity: 0.4;
        }

        /* ═══ SCROLLBAR ═══ */
        ::-webkit-scrollbar { width: 3px; height: 3px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 3px; }

        /* ═══════════════════════════
           SIDEBAR
        ═══════════════════════════ */
        .sidebar {
            width: var(--sb-w);
            background: var(--night);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 200;
            transition: transform .22s ease;
        }

        /* Grid texture on sidebar */
        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(171,171,171,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(171,171,171,0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        /* ── Logo ── */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 17px 16px 15px;
            border-bottom: 1px solid var(--border);
            position: relative;
            z-index: 1;
        }

        .sidebar-logo::after {
            content: '';
            position: absolute;
            bottom: 0; left: 16px; right: 16px;
            height: 1px;
            background: linear-gradient(90deg, var(--crimson), transparent);
            opacity: 0.4;
        }

        .logo-mark {
            width: 30px; height: 30px;
            border: 1px solid rgba(140,14,3,0.6);
            display: grid; place-items: center;
            position: relative; flex-shrink: 0;
        }
        .logo-mark::after {
            content: '';
            position: absolute; top: -1px; right: -1px;
            width: 5px; height: 5px;
            background: var(--crimson);
        }
        .logo-mark span {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 13px;
            color: var(--crimson); line-height: 1;
        }

        .logo-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 15px;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: #fff; line-height: 1;
        }
        .logo-text-muted { color: rgba(171,171,171,0.35); }

        .sidebar-sa-tag {
            margin-left: auto;
            display: inline-flex; align-items: center; gap: 4px;
            padding: 2px 6px;
            border: 1px solid rgba(140,14,3,0.3);
            background: rgba(140,14,3,0.08);
            font-family: 'DM Mono', monospace;
            font-size: 8px; letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(140,14,3,0.8);
        }

        /* ── Nav ── */
        .sidebar-nav {
            flex: 1;
            padding: 6px 0;
            overflow-y: auto;
            position: relative; z-index: 1;
        }

        .nav-section-label {
            padding: 14px 16px 5px;
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase;
            color: rgba(171,171,171,0.2);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 14px;
            margin: 1px 8px;
            border-left: 2px solid transparent;
            color: rgba(171,171,171,0.45);
            text-decoration: none;
            font-size: 13px; font-weight: 400;
            transition: all .13s;
            position: relative;
        }
        .nav-item:hover {
            color: rgba(171,171,171,0.85);
            background: var(--ash-ghost);
            border-left-color: var(--border2);
        }
        .nav-item.active {
            color: #fff;
            background: rgba(140,14,3,0.1);
            border-left-color: var(--crimson);
        }
        .nav-item.active .nav-item-icon { color: var(--crimson); }

        .nav-item-icon { width: 15px; height: 15px; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; padding: 0 5px;
            background: rgba(140,14,3,0.2);
            border: 1px solid rgba(140,14,3,0.35);
            color: rgba(140,14,3,0.9);
            font-family: 'DM Mono', monospace;
            font-size: 10px; font-weight: 500;
        }

        /* ── Footer user ── */
        .sidebar-footer {
            padding: 10px;
            border-top: 1px solid var(--border);
            position: relative; z-index: 1;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            background: var(--ash-ghost);
            border: 1px solid var(--border);
        }

        .sidebar-avatar {
            width: 28px; height: 28px;
            border: 1px solid rgba(140,14,3,0.5);
            background: rgba(140,14,3,0.15);
            color: var(--crimson);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 11px; font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-user-name { font-size: 12px; font-weight: 500; color: var(--ash); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role {
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(171,171,171,0.28);
        }

        /* Mobile overlay */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.65); z-index: 190; }
        .sidebar-overlay.open { display: block; }

        /* ═══════════════════════════
           LAYOUT BODY
        ═══════════════════════════ */
        .layout-body {
            margin-left: var(--sb-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ═══════════════════════════
           TOPBAR
        ═══════════════════════════ */
        .topbar {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 28px;
            height: var(--topbar-h);
            min-height: var(--topbar-h);
            background: rgba(14,17,38,0.8);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
            backdrop-filter: blur(12px);
        }

        .topbar-title {
            flex: 1;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 13px; font-weight: 600;
            letter-spacing: 0.18em; text-transform: uppercase;
            color: rgba(171,171,171,0.4);
        }
        .topbar-title strong {
            font-family: 'Playfair Display', serif;
            font-size: 17px; font-weight: 700;
            letter-spacing: -0.01em; text-transform: none;
            color: #fff; margin-left: .4rem;
        }

        .topbar-actions { display: flex; align-items: center; gap: 6px; }

        .topbar-btn {
            display: flex; align-items: center; justify-content: center;
            width: 30px; height: 30px;
            border: 1px solid var(--border);
            background: transparent;
            color: rgba(171,171,171,0.4);
            cursor: pointer;
            transition: all .13s;
        }
        .topbar-btn:hover { background: var(--ash-ghost); color: var(--ash); border-color: var(--border2); }

        .topbar-divider { width: 1px; height: 18px; background: var(--border); margin: 0 2px; }

        /* User dropdown */
        .topbar-user {
            display: flex; align-items: center; gap: 7px;
            padding: 4px 10px 4px 5px;
            border: 1px solid var(--border);
            cursor: pointer; position: relative;
            transition: all .13s; user-select: none;
        }
        .topbar-user:hover { background: var(--ash-ghost); border-color: var(--border2); }

        .topbar-avatar {
            width: 24px; height: 24px;
            border: 1px solid rgba(140,14,3,0.5);
            background: rgba(140,14,3,0.15);
            color: var(--crimson);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 10px; font-weight: 700;
        }
        .topbar-user-name { font-size: 12.5px; font-weight: 500; color: var(--ash); }

        .user-dropdown {
            display: none;
            position: absolute; top: calc(100% + 6px); right: 0;
            width: 196px;
            background: var(--night2);
            border: 1px solid var(--border2);
            padding: 5px;
            box-shadow: 0 12px 32px rgba(0,0,0,.4);
            z-index: 300;
        }
        .user-dropdown.open { display: block; }

        .dropdown-header {
            padding: 8px 10px 7px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }
        .dropdown-header-name { font-size: 12px; font-weight: 500; color: var(--ash); }
        .dropdown-header-role {
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(171,171,171,0.28); margin-top: 1px;
        }

        .dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px;
            color: rgba(171,171,171,0.5);
            text-decoration: none;
            font-size: 12.5px;
            transition: all .12s;
        }
        .dropdown-item:hover { background: var(--ash-ghost); color: var(--ash); }
        .dropdown-item.danger:hover { color: var(--crimson); }
        .dropdown-divider { height: 1px; background: var(--border); margin: 3px 0; }

        /* ═══════════════════════════
           MAIN CONTENT
        ═══════════════════════════ */
        .main-content {
            flex: 1;
            padding: 36px 26px;
            animation: fadeUp .3s ease both;
        }

        /* ═══ SHARED COMPONENTS ═══ */

        /* Card */
        .sa-card {
            background: var(--night);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
        }
        .sa-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, var(--crimson), transparent 60%);
            opacity: 0;
            transition: opacity .2s;
        }
        .sa-card:hover::before { opacity: 0.5; }

        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.18em; text-transform: uppercase;
            color: rgba(171,171,171,0.35);
        }
        .card-title-main {
            font-family: 'Playfair Display', serif;
            font-size: 14px; font-weight: 700;
            color: rgba(171,171,171,0.8);
        }

        .card-action {
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(140,14,3,0.7);
            text-decoration: none;
            display: flex; align-items: center; gap: 4px;
            transition: color .15s;
        }
        .card-action:hover { color: var(--crimson); }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 9px 16px;
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.18em; text-transform: uppercase;
            color: rgba(171,171,171,0.22); font-weight: 400;
            border-bottom: 1px solid var(--border);
            background: rgba(171,171,171,0.02);
        }
        td {
            padding: 11px 16px; font-size: 13px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle; color: rgba(171,171,171,0.55);
        }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .1s; }
        tbody tr:hover td { background: rgba(171,171,171,0.025); }

        /* Status badges */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 2px 8px;
            font-family: 'DM Mono', monospace;
            font-size: 10px; font-weight: 500;
        }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .badge-active   { background: rgba(74,222,128,0.08); color: #4ade80; border: 1px solid rgba(74,222,128,0.2); }
        .badge-pending  { background: rgba(251,191,36,0.08); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .badge-approved { background: rgba(74,222,128,0.08); color: #4ade80; border: 1px solid rgba(74,222,128,0.2); }
        .badge-rejected { background: var(--crimson-dim); color: rgba(140,14,3,0.9); border: 1px solid rgba(140,14,3,0.25); }
        .badge-basic    { background: rgba(171,171,171,0.07); color: rgba(171,171,171,0.6); border: 1px solid var(--border2); }
        .badge-standard { background: rgba(91,143,185,0.08); color: #5b8fb9; border: 1px solid rgba(91,143,185,0.2); }
        .badge-premium  { background: rgba(201,168,76,0.08); color: #c9a84c; border: 1px solid rgba(201,168,76,0.2); }

        /* Flash */
        .flash {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 15px;
            font-size: 13px; font-weight: 400;
            border: 1px solid;
            margin-bottom: 20px;
        }
        .flash-success { background: rgba(74,222,128,0.05); border-color: rgba(74,222,128,0.2); color: #4ade80; }
        .flash-error   { background: var(--crimson-dim); border-color: rgba(140,14,3,0.25); color: rgba(140,14,3,0.9); }
        .flash-info    { background: rgba(91,143,185,0.07); border-color: rgba(91,143,185,0.2); color: #5b8fb9; }

        /* ═══ KEYFRAMES ═══ */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes flicker { 0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:0.3} 94%{opacity:1} 97%{opacity:0.7} }

        /* ═══ MOBILE ═══ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .layout-body { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <div class="logo-mark"><span>O</span></div>
        <span class="logo-text">OJT<span class="logo-text-muted">Connect</span></span>
        <span class="sidebar-sa-tag">SA</span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-label">Overview</div>

        <a href="{{ route('super_admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
            <svg class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-section-label">Management</div>

        <a href="{{ route('super_admin.tenants.index') }}"
           class="nav-item {{ request()->routeIs('super_admin.tenants.*') ? 'active' : '' }}">
            <svg class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Tenants
        </a>

        <a href="{{ route('super_admin.approvals.pending') }}"
           class="nav-item {{ request()->routeIs('super_admin.approvals.*') ? 'active' : '' }}">
            <svg class="nav-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span style="flex:1;">Approvals</span>
            @php $pendingCount = \App\Models\TenantRegistration::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="nav-badge">{{ $pendingCount }}</span>
            @endif
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}</div>
            <div style="flex:1; min-width:0;">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                <div class="sidebar-user-role">Super Admin</div>
            </div>
        </div>
    </div>

</aside>

<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ── MAIN ── --}}
<div class="layout-body">

    <header class="topbar">

        <button class="topbar-btn" id="menu-toggle" onclick="toggleSidebar()" style="display:none;" aria-label="Menu">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <div class="topbar-title">
            Super Admin <strong>@yield('page-title', 'Dashboard')</strong>
        </div>

        <div class="topbar-actions">

            @yield('topbar-actions')

            <div class="topbar-divider"></div>

            {{-- User dropdown --}}
            <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'SA', 0, 2)) }}</div>
                <span class="topbar-user-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:rgba(171,171,171,0.3); margin-left:2px;">
                    <polyline points="6,9 12,15 18,9"/>
                </svg>

                <div class="user-dropdown" id="user-dropdown">
                    <div class="dropdown-header">
                        <div class="dropdown-header-name">{{ auth()->user()->name }}</div>
                        <div class="dropdown-header-role">Super Admin</div>
                    </div>
                    
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" id="sa-logout-form">@csrf</form>
                    <a href="#" class="dropdown-item danger"
                       onclick="event.preventDefault(); document.getElementById('sa-logout-form').submit();">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                        Log out
                    </a>
                </div>
            </div>

        </div>
    </header>

    <main class="main-content">

        @if(session('success'))
            <div class="flash flash-success">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash flash-error">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="flash flash-info">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('info') }}
            </div>
        @endif

        @yield('content')

    </main>
</div>

<script>
    /* ── Dropdown ── */
    function toggleDropdown() { document.getElementById('user-dropdown').classList.toggle('open'); }
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('topbar-user-btn');
        const dd  = document.getElementById('user-dropdown');
        if (btn && dd && !btn.contains(e.target)) dd.classList.remove('open');
    });

    /* ── Mobile sidebar ── */
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
        const btn = document.getElementById('menu-toggle');
        if (btn) btn.style.display = e.matches ? 'flex' : 'none';
    }
    mq.addEventListener('change', handleMq); handleMq(mq);
</script>

@stack('scripts')
</body>
</html>