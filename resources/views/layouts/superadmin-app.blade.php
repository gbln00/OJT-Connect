<!DOCTYPE html>
<html lang="en" data-appearance="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — OJTConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=DM+Mono:wght@400;500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fraunces','serif'],
                        mono:    ['DM Mono','monospace'],
                        sans:    ['DM Sans','sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body { font-family: 'DM Sans', sans-serif; }

        /* ═══════════════════════════════════════
           TOKEN SYSTEM  –  dark (default)
        ═══════════════════════════════════════ */
        :root,
        [data-appearance="dark"] {
            --pg:          #112031;
            --surface:     #152D35;
            --surface2:    #1a3540;
            --border:      rgba(212,236,221,0.07);
            --border2:     rgba(212,236,221,0.13);
            --text:        #D4ECDD;
            --text2:       rgba(212,236,221,0.50);
            --text3:       rgba(212,236,221,0.24);
            --accent:      #345B63;
            --accent-h:    #2e505a;
            --teal:        #4a9e8e;
            --teal-dim:    rgba(74,158,142,0.12);
            --gold:        #c9a84c;
            --gold-dim:    rgba(201,168,76,0.12);
            --coral:       #d97068;
            --coral-dim:   rgba(217,112,104,0.12);
            --blue:        #5b8fb9;
            --blue-dim:    rgba(91,143,185,0.12);
            --muted:       rgba(212,236,221,0.38);
            --muted2:      rgba(212,236,221,0.55);
            --nav-active:  rgba(212,236,221,0.08);
            --nav-act-bd:  #D4ECDD;
            --sb-w:        230px;
        }

        [data-appearance="light"] {
            --pg:          #eef3f0;
            --surface:     #ffffff;
            --surface2:    #f5f9f6;
            --border:      rgba(52,91,99,0.10);
            --border2:     rgba(52,91,99,0.20);
            --text:        #152D35;
            --text2:       rgba(21,45,53,0.52);
            --text3:       rgba(21,45,53,0.30);
            --accent:      #345B63;
            --accent-h:    #2b4d54;
            --teal:        #258f7d;
            --teal-dim:    rgba(37,143,125,0.10);
            --gold:        #a8791e;
            --gold-dim:    rgba(168,121,30,0.10);
            --coral:       #c0514a;
            --coral-dim:   rgba(192,81,74,0.10);
            --blue:        #3a6d99;
            --blue-dim:    rgba(58,109,153,0.10);
            --muted:       rgba(21,45,53,0.40);
            --muted2:      rgba(21,45,53,0.60);
            --nav-active:  rgba(52,91,99,0.08);
            --nav-act-bd:  #345B63;
        }

        /* ─── Layout shell ─── */
        body {
            background: var(--pg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            transition: background .25s, color .25s;
        }

        /* ─── Sidebar ─── */
        .sidebar {
            width: var(--sb-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 200;
            transition: background .25s, border-color .25s, transform .22s ease;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px 17px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-logo-icon {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif;
            font-size: 15px; font-weight: 600;
            color: #D4ECDD;
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            font-family: 'Fraunces', serif;
            font-size: 16px; font-weight: 600;
            color: var(--text);
            line-height: 1;
        }
        .sidebar-logo-text span { opacity: .35; font-weight: 300; font-style: italic; }

        .sidebar-super-tag {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 3px;
            background: rgba(212,236,221,0.08);
            border: 1px solid var(--border2);
            font-family: 'DM Mono', monospace;
            font-size: 8.5px;
            letter-spacing: .13em;
            text-transform: uppercase;
            color: var(--text3);
            margin-left: 2px;
            vertical-align: middle;
        }
        [data-appearance="light"] .sidebar-super-tag {
            background: rgba(52,91,99,0.07);
        }

        .sidebar-nav {
            flex: 1;
            padding: 8px 0;
            overflow-y: auto;
        }

        .nav-section-label {
            padding: 14px 16px 4px;
            font-family: 'DM Mono', monospace;
            font-size: 9px;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--text3);
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8.5px 16px;
            margin: 1px 8px;
            border-radius: 0 7px 7px 0;
            border-left: 2px solid transparent;
            color: var(--muted);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .13s;
        }
        .nav-item:hover { background: var(--nav-active); color: var(--text); border-left-color: var(--border2); }
        .nav-item.active { background: var(--nav-active); color: var(--text); border-left-color: var(--nav-act-bd); }
        .nav-item svg { width: 15px; height: 15px; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 18px; height: 18px; padding: 0 5px;
            border-radius: 20px;
            background: rgba(212,236,221,0.08);
            border: 1px solid var(--border2);
            color: var(--text2);
            font-family: 'DM Mono', monospace;
            font-size: 10px;
        }
        [data-appearance="light"] .nav-badge {
            background: rgba(52,91,99,0.08);
        }

        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 9px;
            background: var(--surface2);
            border: 1px solid var(--border);
        }

        .sidebar-avatar {
            width: 30px; height: 30px;
            border-radius: 7px;
            background: var(--accent);
            color: #D4ECDD;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif;
            font-size: 12px; font-weight: 600;
            flex-shrink: 0;
        }

        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name { font-size: 12.5px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-family: 'DM Mono', monospace; font-size: 9px; letter-spacing: .10em; text-transform: uppercase; color: var(--text3); }

        /* Mobile overlay */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 190;
        }
        .sidebar-overlay.open { display: block; }

        /* ─── Layout body ─── */
        .layout-body {
            margin-left: var(--sb-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ─── Topbar ─── */
        .topbar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 24px;
            height: 54px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 100;
            transition: background .25s, border-color .25s;
        }

        .topbar-title {
            flex: 1;
            font-family: 'Fraunces', serif;
            font-size: 17px; font-weight: 600;
            color: var(--text);
            transition: color .25s;
        }

        .topbar-actions { display: flex; align-items: center; gap: 6px; }

        .topbar-btn {
            display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px;
            border-radius: 7px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text2);
            cursor: pointer;
            transition: all .13s;
        }
        .topbar-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }

        .topbar-divider { width: 1px; height: 20px; background: var(--border); margin: 0 4px; }

        /* User dropdown */
        .topbar-user {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 10px 5px 6px;
            border-radius: 8px;
            border: 1px solid var(--border);
            cursor: pointer;
            position: relative;
            transition: all .13s;
            user-select: none;
        }
        .topbar-user:hover { background: var(--surface2); border-color: var(--border2); }

        .topbar-user-avatar {
            width: 26px; height: 26px;
            border-radius: 6px;
            background: var(--accent);
            color: #D4ECDD;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif;
            font-size: 11px; font-weight: 600;
            flex-shrink: 0;
        }

        .topbar-user-name { font-size: 13px; font-weight: 500; color: var(--text); }

        .user-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 200px;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 10px;
            padding: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,.25);
            z-index: 300;
        }
        .user-dropdown.open { display: block; }

        .dropdown-item {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px;
            border-radius: 6px;
            color: var(--text2);
            text-decoration: none;
            font-size: 13px;
            transition: all .12s;
        }
        .dropdown-item:hover { background: var(--surface2); color: var(--text); }
        .dropdown-item.danger:hover { color: var(--coral); }
        .dropdown-item svg { flex-shrink: 0; }

        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ─── Main content ─── */
        .main-content { flex: 1; padding: 24px 26px; }

        /* ─── Shared components ─── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            transition: background .25s, border-color .25s;
        }

        .card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }

        .card-title { font-family: 'Fraunces', serif; font-size: 14.5px; font-weight: 600; color: var(--text); }
        .card-action { font-size: 12.5px; color: var(--teal); text-decoration: none; transition: opacity .13s; }
        .card-action:hover { opacity: .75; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 16px; font-family: 'DM Mono', monospace; font-size: 9.5px; letter-spacing: .12em; text-transform: uppercase; color: var(--text3); font-weight: 500; border-bottom: 1px solid var(--border); }
        td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid var(--border); vertical-align: middle; color: var(--text2); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(212,236,221,0.02); }
        [data-appearance="light"] tr:hover td { background: rgba(52,91,99,0.03); }

        /* ─── Role badges ─── */
        .role-badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px; border-radius: 20px;
            font-family: 'DM Mono', monospace;
            font-size: 10.5px; font-weight: 500;
        }
        .role-badge.admin       { background: var(--gold-dim);  color: var(--gold);  border: 1px solid rgba(201,168,76,.2); }
        .role-badge.coordinator { background: var(--teal-dim);  color: var(--teal);  border: 1px solid rgba(74,158,142,.2); }
        .role-badge.supervisor  { background: var(--blue-dim);  color: var(--blue);  border: 1px solid rgba(91,143,185,.2); }
        .role-badge.student     { background: var(--coral-dim); color: var(--coral); border: 1px solid rgba(217,112,104,.2); }

        /* ─── Status dot ─── */
        .status-dot {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 500;
        }
        .status-dot::before { content: ''; width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        .status-dot.active::before   { background: var(--teal); }
        .status-dot.active   { color: var(--teal); }
        .status-dot.inactive::before { background: var(--text3); }
        .status-dot.inactive { color: var(--text3); }

        /* ─── Quick actions ─── */
        .quick-actions { display: grid; grid-template-columns: repeat(2,1fr); gap: 8px; padding: 14px; }
        .qa-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 12px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--surface2);
            text-decoration: none;
            transition: all .13s;
        }
        .qa-btn:hover { border-color: var(--border2); background: var(--surface); transform: translateY(-1px); }
        .qa-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .qa-label { font-size: 12.5px; font-weight: 500; color: var(--text2); }
        .qa-btn:hover .qa-label { color: var(--text); }

        /* ─── Activity feed ─── */
        .activity-list { padding: 8px 0; }
        .activity-item { display: flex; gap: 12px; padding: 10px 18px; }
        .activity-dot-wrap { padding-top: 4px; }
        .activity-dot { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
        .activity-dot.gold   { background: var(--gold); }
        .activity-dot.teal   { background: var(--teal); }
        .activity-dot.coral  { background: var(--coral); }
        .activity-dot.blue   { background: var(--blue); }
        .activity-body { flex: 1; }
        .activity-text { font-size: 12.5px; color: var(--text2); line-height: 1.5; }
        .activity-text strong { color: var(--text); font-weight: 600; }
        .activity-time { font-family: 'DM Mono', monospace; font-size: 10.5px; color: var(--text3); margin-top: 2px; }

        /* ─── Greeting ─── */
        .greeting { margin-bottom: 22px; }
        .greeting-sub { font-family: 'DM Mono', monospace; font-size: 10px; letter-spacing: .15em; text-transform: uppercase; color: var(--text3); margin-bottom: 4px; }
        .greeting-title { font-family: 'Fraunces', serif; font-size: 26px; font-weight: 600; color: var(--text); line-height: 1.2; }
        .greeting-title span { opacity: .35; font-weight: 300; font-style: italic; }

        /* ─── Stats grid ─── */
        .stats-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; margin-bottom: 14px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: 11px; padding: 18px 20px; transition: background .25s, border-color .25s; }
        .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .stat-icon { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; }
        .stat-icon.gold  { background: var(--gold-dim);  color: var(--gold);  border: 1px solid rgba(201,168,76,.18); }
        .stat-icon.teal  { background: var(--teal-dim);  color: var(--teal);  border: 1px solid rgba(74,158,142,.18); }
        .stat-icon.coral { background: var(--coral-dim); color: var(--coral); border: 1px solid rgba(217,112,104,.18); }
        .stat-icon.blue  { background: var(--blue-dim);  color: var(--blue);  border: 1px solid rgba(91,143,185,.18); }
        .stat-trend { font-family: 'DM Mono', monospace; font-size: 9.5px; letter-spacing: .10em; text-transform: uppercase; color: var(--text3); }
        .stat-num { font-family: 'Fraunces', serif; font-size: 34px; font-weight: 600; color: var(--text); line-height: 1; margin-bottom: 4px; }
        .stat-label { font-size: 12px; color: var(--text3); }

        /* ─── Bottom grid ─── */
        .bottom-grid { display: grid; grid-template-columns: 1fr 340px; gap: 14px; }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 4px; }

        /* ─── Flash ─── */
        .flash { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 9px; margin-bottom: 18px; font-size: 13px; font-weight: 500; border: 1px solid; }
        .flash-success { background: var(--teal-dim);  border-color: rgba(74,158,142,.25); color: var(--teal);  }
        .flash-error   { background: var(--coral-dim); border-color: rgba(217,112,104,.25); color: var(--coral); }

        /* ─── Mobile ─── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .layout-body { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .bottom-grid { grid-template-columns: 1fr; }
        }

        /* ─── Animations ─── */
        @keyframes fade-up { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        .main-content { animation: fade-up .28s ease both; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">O</div>
        <div class="sidebar-logo-text">
            OJT<span>Connect</span>
        </div>
        <span class="sidebar-super-tag">SA</span>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section-label">Overview</div>

        <a href="{{ route('super_admin.dashboard') }}"
           class="nav-item {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Tenants
        </a>

        <a href="{{ route('super_admin.approvals.pending') }}"
           class="nav-item {{ request()->routeIs('super_admin.approvals.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                <div class="sidebar-user-role">Super Admin</div>
            </div>
        </div>
    </div>

</aside>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

{{-- ── MAIN ── --}}
<div class="layout-body">

    <header class="topbar">

        {{-- Mobile menu --}}
        <button class="topbar-btn" id="menu-toggle" onclick="toggleSidebar()" style="display:none;" aria-label="Menu">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>

        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

        <div class="topbar-actions">

            {{-- Theme toggle --}}
            <button class="topbar-btn" onclick="toggleAppearance()" title="Toggle theme" aria-label="Toggle theme">
                {{-- Moon = shown in light mode --}}
                <svg id="theme-icon-moon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
                {{-- Sun = shown in dark mode --}}
                <svg id="theme-icon-sun" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>

            @yield('topbar-actions')

            <div class="topbar-divider"></div>

            {{-- User dropdown --}}
            <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                <div class="topbar-user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'SA', 0, 2)) }}</div>
                <span class="topbar-user-name">{{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}</span>
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--text3); margin-left:2px;">
                    <polyline points="6,9 12,15 18,9"/>
                </svg>

                <div class="user-dropdown" id="user-dropdown">
                    <div style="padding:8px 10px 6px; border-bottom:1px solid var(--border); margin-bottom:4px;">
                        <div style="font-size:12.5px; font-weight:500; color:var(--text);">{{ auth()->user()->name }}</div>
                        <div style="font-family:'DM Mono',monospace; font-size:9.5px; letter-spacing:.10em; text-transform:uppercase; color:var(--text3);">Super Admin</div>
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
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash flash-error">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="flash" style="background:var(--blue-dim);border-color:rgba(91,143,185,.25);color:var(--blue);">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('info') }}
            </div>
        @endif

        @yield('content')

    </main>
</div>

<script>
    /* ── Theme ── */
    (function () {
        const saved = localStorage.getItem('sa-appearance') || 'dark';
        applyAppearance(saved);
    })();

    function applyAppearance(mode) {
        document.documentElement.setAttribute('data-appearance', mode);
        const moon = document.getElementById('theme-icon-moon');
        const sun  = document.getElementById('theme-icon-sun');
        if (moon && sun) {
            moon.style.display = mode === 'light' ? 'block' : 'none';
            sun.style.display  = mode === 'dark'  ? 'block' : 'none';
        }
        localStorage.setItem('sa-appearance', mode);
    }

    function toggleAppearance() {
        const current = document.documentElement.getAttribute('data-appearance') || 'dark';
        applyAppearance(current === 'dark' ? 'light' : 'dark');
    }

    /* ── Dropdown ── */
    function toggleDropdown() {
        document.getElementById('user-dropdown').classList.toggle('open');
    }
    document.addEventListener('click', function (e) {
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
    mq.addEventListener('change', handleMq);
    handleMq(mq);
</script>

@stack('scripts')
</body>
</html>