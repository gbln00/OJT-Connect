<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — OJT Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #0a0a0f;
            --surface:   #111118;
            --border:    #1e1e2e;
            --accent:    #6c63ff;
            --accent-dim:#3d3880;
            --danger:    #ff4d6d;
            --success:   #2dd4a0;
            --warning:   #f59e0b;
            --text:      #e8e6f0;
            --muted:     #6b6880;
            --sidebar-w: 240px;
        }

        html, body { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand-badge {
            display: inline-block;
            background: var(--accent);
            color: #fff;
            font-family: 'Syne', sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 4px;
            margin-bottom: 6px;
        }

        .brand-name {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
        }

        .brand-name span { color: var(--accent); }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 8px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .15s;
        }

        .nav-link:hover { background: var(--border); color: var(--text); }

        .nav-link.active {
            background: var(--accent-dim);
            color: var(--text);
        }

        .nav-link svg { width: 16px; height: 16px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            background: var(--border);
        }

        .user-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info { flex: 1; min-width: 0; }
        .user-name { font-size: 13px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-role { font-size: 11px; color: var(--muted); }

        .logout-btn {
            background: none; border: none; cursor: pointer;
            color: var(--muted); padding: 4px;
            border-radius: 4px; transition: color .15s;
            display: flex; align-items: center;
        }
        .logout-btn:hover { color: var(--danger); }

        /* ── Main ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            padding: 16px 32px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
        }

        .content {
            padding: 32px;
            flex: 1;
        }

        /* ── Flash messages ── */
        .flash {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            border: 1px solid;
        }

        .flash-success { background: rgba(45,212,160,.08); border-color: rgba(45,212,160,.25); color: var(--success); }
        .flash-error   { background: rgba(255,77,109,.08); border-color: rgba(255,77,109,.25); color: var(--danger); }

        /* ── Cards ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-primary   { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #7c74ff; }
        .btn-ghost     { background: transparent; color: var(--muted); border-color: var(--border); }
        .btn-ghost:hover { color: var(--text); border-color: var(--muted); }
        .btn-danger    { background: transparent; color: var(--danger); border-color: rgba(255,77,109,.3); }
        .btn-danger:hover { background: rgba(255,77,109,.1); }
        .btn-sm        { padding: 6px 12px; font-size: 13px; }

        /* ── Table ── */
        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        th {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 10px 16px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 16px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* ── Badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-green  { background: rgba(45,212,160,.12); color: var(--success); }
        .badge-purple { background: rgba(108,99,255,.15); color: #a09aff; }
        .badge-muted  { background: var(--border); color: var(--muted); }

        /* ── Form ── */
        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select, textarea {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 10px 14px;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            transition: border-color .15s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
        }

        .input-hint { font-size: 12px; color: var(--muted); margin-top: 5px; }

        .field-error { font-size: 12px; color: var(--danger); margin-top: 5px; }

        /* ── Grid ── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }

        /* ── Stat card ── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px 24px;
        }

        .stat-label { font-size: 12px; color: var(--muted); font-weight: 500; letter-spacing: .05em; text-transform: uppercase; margin-bottom: 8px; }
        .stat-value { font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 800; color: var(--text); }
        .stat-sub   { font-size: 12px; color: var(--muted); margin-top: 4px; }

        /* ── Empty state ── */
        .empty {
            text-align: center;
            padding: 60px 24px;
            color: var(--muted);
        }

        .empty-icon { font-size: 40px; margin-bottom: 12px; opacity: .4; }
        .empty-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .empty-text  { font-size: 14px; }

        /* ── Modal ── */
        .modal-backdrop {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.7);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }

        .modal-backdrop.open { display: flex; }

        .modal {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px;
            width: 100%;
            max-width: 420px;
        }

        .modal-title { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 8px; }
        .modal-text  { font-size: 14px; color: var(--muted); margin-bottom: 24px; line-height: 1.6; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; }

        /* ── Pagination ── */
        .pagination { display: flex; align-items: center; gap: 6px; margin-top: 20px; justify-content: flex-end; }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 7px;
            font-size: 13px; font-weight: 500; text-decoration: none;
            border: 1px solid var(--border); color: var(--muted);
            transition: all .15s;
        }
        .pagination a:hover { border-color: var(--accent); color: var(--text); }
        .pagination .active span { background: var(--accent); border-color: var(--accent); color: #fff; }

        /* ── Section header ── */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
        .section-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; }

        /* ── Divider ── */
        .divider { border: none; border-top: 1px solid var(--border); margin: 24px 0; }

        /* ── Detail row ── */
        .detail-row { display: flex; gap: 8px; padding: 12px 0; border-bottom: 1px solid var(--border); font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-key { width: 160px; color: var(--muted); flex-shrink: 0; }
        .detail-val { color: var(--text); font-weight: 500; word-break: break-all; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-badge">Super Admin</div>
        <div class="brand-name">OJT<span>Hub</span></div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Overview</div>
        <a href="{{ route('super_admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <div class="nav-label">Management</div>
        <a href="{{ route('super_admin.tenants.index') }}"
           class="nav-link {{ request()->routeIs('super_admin.tenants.*') ? 'active' : '' }}">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Tenants
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Super Admin</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" title="Logout" type="submit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- Main --}}
<div class="main">
    <header class="topbar">
        <div class="page-title">@yield('page-title', 'Dashboard')</div>
        <div>@yield('topbar-actions')</div>
    </header>

    <main class="content">
        @if(session('success'))
            <div class="flash flash-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash flash-error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>