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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ── CSS VARIABLES ─────────────────────────────────────────── */
        :root {
            --bg:          #0f1117;
            --surface:     #161b27;
            --surface2:    #1c2333;
            --border:      rgba(255,255,255,0.07);
            --border2:     rgba(255,255,255,0.11);

            --text:        #e8eaf0;
            --muted:       #6b7280;
            --muted2:      #9ca3af;

            --gold:        #f0b429;
            --gold-dim:    rgba(240,180,41,0.12);
            --gold-hover:  rgba(240,180,41,0.18);

            --teal:        #2dd4bf;
            --teal-dim:    rgba(45,212,191,0.12);

            --coral:       #f87171;
            --coral-dim:   rgba(248,113,113,0.12);

            --blue:        #60a5fa;
            --blue-dim:    rgba(96,165,250,0.12);

            --sidebar-w:   240px;
            --topbar-h:    58px;
            --radius:      10px;
        }

        /* Light mode */
        html[data-appearance="light"] {
            --bg:       #f4f5f7;
            --surface:  #ffffff;
            --surface2: #f9fafb;
            --border:   rgba(0,0,0,0.07);
            --border2:  rgba(0,0,0,0.11);
            --text:     #111827;
            --muted:    #9ca3af;
            --muted2:   #6b7280;
        }

        /* ── RESET ─────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }
        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
            height: 100%;
            display: flex;
            overflow: hidden;
        }
        a { color: inherit; text-decoration: none; }
        button { cursor: pointer; font-family: inherit; }

        /* ── SIDEBAR ───────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            left: 0; top: 0;
            z-index: 100;
            transition: transform 0.25s ease;
        }

        .sidebar-logo {
            height: var(--topbar-h);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 18px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sidebar-logo-icon {
            width: 30px; height: 30px;
            background: var(--gold-dim);
            border: 1px solid rgba(240,180,41,0.3);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: var(--gold);
            font-size: 14px;
            font-weight: 700;
        }
        .sidebar-logo-text {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: -0.3px;
            color: var(--text);
        }
        .sidebar-logo-text span { color: var(--gold); }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 10px;
            scrollbar-width: none;
        }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
            padding: 14px 8px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 450;
            color: var(--muted2);
            transition: background 0.15s, color 0.15s;
            margin-bottom: 1px;
        }
        .nav-item:hover {
            background: var(--surface2);
            color: var(--text);
        }
        .nav-item.active {
            background: var(--gold-dim);
            color: var(--gold);
            font-weight: 500;
        }
        .nav-item svg { flex-shrink: 0; opacity: 0.8; }
        .nav-item.active svg { opacity: 1; }

        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: 8px;
            transition: background 0.15s;
            cursor: pointer;
        }
        .sidebar-user:hover { background: var(--surface2); }
        .sidebar-avatar {
            width: 30px; height: 30px;
            border-radius: 50%;
            background: var(--gold-dim);
            border: 1px solid rgba(240,180,41,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600;
            color: var(--gold);
            flex-shrink: 0;
        }
        .sidebar-user-info { flex: 1; min-width: 0; }
        .sidebar-user-name {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 11px;
            color: var(--muted);
            margin-top: 1px;
        }

        /* ── TOPBAR ────────────────────────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            z-index: 90;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 600;
            letter-spacing: -0.3px;
            color: var(--text);
            flex: 1;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border2);
            background: var(--surface2);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted2);
            transition: background 0.15s, color 0.15s;
        }
        .topbar-btn:hover {
            background: var(--border);
            color: var(--text);
        }

        .topbar-divider {
            width: 1px;
            height: 20px;
            background: var(--border2);
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 5px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface2);
            cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }
        .topbar-user:hover { background: var(--border); }
        .topbar-user-avatar {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: var(--gold-dim);
            border: 1px solid rgba(240,180,41,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 10px; font-weight: 600;
            color: var(--gold);
        }
        .topbar-user-name {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text);
        }

        /* Dropdown */
        .user-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius);
            min-width: 180px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
            overflow: hidden;
            display: none;
            z-index: 200;
        }
        .user-dropdown.open { display: block; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--muted2);
            transition: background 0.12s, color 0.12s;
        }
        .dropdown-item:hover { background: var(--surface2); color: var(--text); }
        .dropdown-item.danger:hover { background: var(--coral-dim); color: var(--coral); }
        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* ── MAIN CONTENT ──────────────────────────────────────────── */
        .layout-body {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow: hidden;
        }

        .main-content {
            margin-top: var(--topbar-h);
            padding: 28px 28px;
            flex: 1;
            overflow-y: auto;
            height: calc(100vh - var(--topbar-h));
        }

        /* ── SHARED COMPONENTS (used across views) ─────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        .card-title {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
        }
        .card-action {
            font-size: 12px;
            color: var(--gold);
            transition: opacity 0.15s;
        }
        .card-action:hover { opacity: 0.75; }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 18px 20px;
        }
        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }
        .stat-icon {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-icon.gold   { background: var(--gold-dim);  color: var(--gold);  }
        .stat-icon.teal   { background: var(--teal-dim);  color: var(--teal);  }
        .stat-icon.coral  { background: var(--coral-dim); color: var(--coral); }
        .stat-icon.blue   { background: var(--blue-dim);  color: var(--blue);  }

        .stat-trend {
            font-size: 10.5px;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 20px;
        }
        .stat-trend.up      { background: var(--teal-dim);  color: var(--teal);  }
        .stat-trend.down    { background: var(--coral-dim); color: var(--coral); }
        .stat-trend.neutral { background: var(--surface2);  color: var(--muted); }

        .stat-num {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -1px;
            color: var(--text);
            line-height: 1;
            margin-bottom: 4px;
            font-family: 'DM Mono', monospace;
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
        }

        /* Role badges */
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
        }
        .role-badge.admin       { background: var(--gold-dim);  color: var(--gold);  }
        .role-badge.coordinator { background: var(--teal-dim);  color: var(--teal);  }
        .role-badge.supervisor  { background: var(--blue-dim);  color: var(--blue);  }
        .role-badge.student     { background: var(--coral-dim); color: var(--coral); }

        /* Status dots */
        .status-dot {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }
        .status-dot::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .status-dot.active::before   { background: var(--teal); }
        .status-dot.inactive::before { background: var(--coral); }
        .status-dot.active   { color: var(--teal);  }
        .status-dot.inactive { color: var(--coral); }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid var(--border); }
        th {
            padding: 10px 18px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            white-space: nowrap;
        }
        td {
            padding: 12px 18px;
            font-size: 13px;
            color: var(--muted2);
            border-bottom: 1px solid var(--border);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--surface2); }

        /* Quick actions */
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            padding: 14px;
        }
        .qa-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface2);
            transition: background 0.15s, border-color 0.15s;
        }
        .qa-btn:hover { background: var(--border); border-color: var(--border2); }
        .qa-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .qa-label {
            font-size: 12.5px;
            font-weight: 500;
            color: var(--text);
        }

        /* Activity feed */
        .activity-list { padding: 8px 0; }
        .activity-item {
            display: flex;
            gap: 12px;
            padding: 10px 18px;
        }
        .activity-dot-wrap {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 4px;
        }
        .activity-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .activity-dot.gold  { background: var(--gold);  }
        .activity-dot.teal  { background: var(--teal);  }
        .activity-dot.coral { background: var(--coral); }
        .activity-dot.blue  { background: var(--blue);  }
        .activity-body { flex: 1; min-width: 0; }
        .activity-text { font-size: 12.5px; color: var(--muted2); line-height: 1.5; }
        .activity-text strong { color: var(--text); font-weight: 500; }
        .activity-time { font-size: 11px; color: var(--muted); margin-top: 2px; }

        /* Bottom grid */
        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 16px;
        }

        /* Greeting */
        .greeting { margin-bottom: 24px; }
        .greeting-sub { font-size: 12px; color: var(--muted); margin-bottom: 4px; }
        .greeting-title {
            font-size: 22px;
            font-weight: 600;
            letter-spacing: -0.5px;
            color: var(--text);
        }
        .greeting-title span { color: var(--gold); }

        /* ── MOBILE OVERLAY ────────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99;
        }

        /* ── RESPONSIVE ────────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .bottom-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 900px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open { display: block; }
            .topbar { left: 0; }
            .layout-body { margin-left: 0; }
            .main-content { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
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