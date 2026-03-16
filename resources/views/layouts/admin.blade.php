<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard') — OJTConnect Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --bg:       #0F1117;
    --surface:  #16181F;
    --surface2: #1E2029;
    --border:   rgba(255,255,255,0.07);
    --border2:  rgba(255,255,255,0.12);
    --gold:     #F0B429;
    --gold-dim: rgba(240,180,41,0.12);
    --teal:     #2DD4BF;
    --teal-dim: rgba(45,212,191,0.1);
    --coral:    #FB7185;
    --coral-dim:rgba(251,113,133,0.1);
    --blue:     #60A5FA;
    --blue-dim: rgba(96,165,250,0.1);
    --text:     #E8EAF0;
    --muted:    #6B7280;
    --muted2:   #9CA3AF;
    --sidebar-w: 240px;
}

html, body { height: 100%; font-family: 'Outfit', sans-serif; background: var(--bg); color: var(--text); }

/* ── LAYOUT ── */
.app { display: flex; min-height: 100vh; }

/* ── SIDEBAR ── */
.sidebar {
    width: var(--sidebar-w);
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
    transition: transform 0.3s;
}

.sidebar-brand {
    padding: 24px 20px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand-icon {
    width: 34px; height: 34px;
    background: var(--gold);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 16px;
    color: #0F1117;
    flex-shrink: 0;
}

.brand-text { font-family: 'Syne', sans-serif; font-size: 17px; font-weight: 600; color: var(--text); }
.brand-text span { color: var(--gold); }

.sidebar-section { padding: 20px 12px 8px; }
.sidebar-label { font-size: 10px; font-weight: 500; letter-spacing: 1.2px; color: var(--muted); text-transform: uppercase; padding: 0 8px; margin-bottom: 6px; }

.nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px;
    border-radius: 8px;
    color: var(--muted2);
    text-decoration: none;
    font-size: 14px;
    font-weight: 400;
    transition: all 0.15s;
    margin-bottom: 2px;
    position: relative;
}

.nav-item:hover { background: var(--surface2); color: var(--text); }

.nav-item.active {
    background: var(--gold-dim);
    color: var(--gold);
    font-weight: 500;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    left: 0; top: 6px; bottom: 6px;
    width: 3px;
    background: var(--gold);
    border-radius: 0 3px 3px 0;
}

.nav-icon { width: 18px; height: 18px; flex-shrink: 0; opacity: 0.8; }
.nav-item.active .nav-icon { opacity: 1; }

.nav-badge {
    margin-left: auto;
    background: var(--coral);
    color: #fff;
    font-size: 10px;
    font-weight: 500;
    padding: 2px 7px;
    border-radius: 20px;
    line-height: 1.4;
}

.sidebar-footer {
    margin-top: auto;
    padding: 16px 12px;
    border-top: 1px solid var(--border);
}

.user-card {
    display: flex; align-items: center; gap: 10px;
    padding: 10px;
    border-radius: 10px;
    background: var(--surface2);
    cursor: pointer;
    transition: background 0.15s;
}
.user-card:hover { background: var(--border); }

.user-avatar {
    width: 34px; height: 34px;
    background: var(--gold-dim);
    border: 1.5px solid var(--gold);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif;
    font-size: 13px;
    font-weight: 600;
    color: var(--gold);
    flex-shrink: 0;
}

.user-info { flex: 1; min-width: 0; }
.user-name { font-size: 13px; font-weight: 500; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role { font-size: 11px; color: var(--muted); margin-top: 1px; }

/* ── MAIN ── */
.main {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* ── TOPBAR ── */
.topbar {
    height: 60px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    padding: 0 28px;
    gap: 16px;
    position: sticky;
    top: 0;
    background: var(--bg);
    z-index: 50;
}

.topbar-title { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 600; color: var(--text); flex: 1; }

.topbar-actions { display: flex; align-items: center; gap: 10px; }

.icon-btn {
    width: 36px; height: 36px;
    border-radius: 9px;
    border: 1px solid var(--border2);
    background: var(--surface);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    color: var(--muted2);
    transition: all 0.15s;
    position: relative;
}
.icon-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }

.notif-dot {
    position: absolute; top: 7px; right: 7px;
    width: 7px; height: 7px;
    background: var(--coral);
    border-radius: 50%;
    border: 1.5px solid var(--bg);
}

/* ── PAGE CONTENT ── */
.page-content { padding: 28px; flex: 1; }

/* ── GREETING ── */
.greeting { margin-bottom: 28px; animation: fadeUp 0.5s ease both; }
.greeting-sub { font-size: 13px; color: var(--muted); margin-bottom: 4px; }
.greeting-title { font-family: 'Syne', sans-serif; font-size: 24px; font-weight: 600; color: var(--text); }
.greeting-title span { color: var(--gold); }

/* ── STAT CARDS ── */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px; }

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    animation: fadeUp 0.5s ease both;
    transition: border-color 0.2s, transform 0.2s;
    cursor: default;
}
.stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.2s; }

.stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
.stat-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.stat-icon.gold  { background: var(--gold-dim); color: var(--gold); }
.stat-icon.teal  { background: var(--teal-dim); color: var(--teal); }
.stat-icon.coral { background: var(--coral-dim);color: var(--coral); }
.stat-icon.blue  { background: var(--blue-dim); color: var(--blue); }

.stat-trend { font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 20px; }
.stat-trend.up   { background: rgba(45,212,191,0.1); color: var(--teal); }
.stat-trend.down { background: var(--coral-dim); color: var(--coral); }
.stat-trend.neutral { background: var(--surface2); color: var(--muted2); }

.stat-num { font-family: 'Syne', sans-serif; font-size: 30px; font-weight: 700; color: var(--text); line-height: 1; margin-bottom: 4px; }
.stat-label { font-size: 13px; color: var(--muted); }

/* ── BOTTOM GRID ── */
.bottom-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }

/* ── CARD ── */
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    animation: fadeUp 0.5s 0.25s ease both;
}

.card-header {
    padding: 18px 20px 14px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-title { font-family: 'Syne', sans-serif; font-size: 14px; font-weight: 600; color: var(--text); }
.card-action { font-size: 12px; color: var(--gold); text-decoration: none; font-weight: 500; transition: opacity 0.2s; }
.card-action:hover { opacity: 0.7; }

/* ── TABLE ── */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead th { padding: 11px 16px; font-size: 11px; font-weight: 500; color: var(--muted); letter-spacing: 0.5px; text-transform: uppercase; text-align: left; border-bottom: 1px solid var(--border); }
tbody tr { border-bottom: 1px solid var(--border); transition: background 0.15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--surface2); }
tbody td { padding: 13px 16px; font-size: 13px; color: var(--muted2); vertical-align: middle; }
tbody td:first-child { color: var(--text); font-weight: 500; }

.role-badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
.role-badge.admin      { background: var(--gold-dim);  color: var(--gold); }
.role-badge.coordinator{ background: var(--teal-dim);  color: var(--teal); }
.role-badge.supervisor { background: var(--blue-dim);  color: var(--blue); }
.role-badge.student    { background: var(--coral-dim); color: var(--coral); }

.status-dot { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; }
.status-dot::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
.status-dot.active   { color: var(--teal); }
.status-dot.inactive { color: var(--muted); }

/* ── ACTIVITY FEED ── */
.activity-list { padding: 4px 0; }
.activity-item { display: flex; gap: 12px; padding: 14px 20px; border-bottom: 1px solid var(--border); transition: background 0.15s; }
.activity-item:last-child { border-bottom: none; }
.activity-item:hover { background: var(--surface2); }

.activity-dot-wrap { display: flex; flex-direction: column; align-items: center; padding-top: 3px; }
.activity-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.activity-dot.gold  { background: var(--gold); }
.activity-dot.teal  { background: var(--teal); }
.activity-dot.coral { background: var(--coral); }
.activity-dot.blue  { background: var(--blue); }

.activity-body { flex: 1; }
.activity-text { font-size: 13px; color: var(--text); line-height: 1.5; }
.activity-text strong { color: var(--text); font-weight: 500; }
.activity-time { font-size: 11px; color: var(--muted); margin-top: 3px; }

/* ── QUICK ACTIONS ── */
.quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 16px; }
.qa-btn {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 8px; padding: 16px 12px;
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 12px; cursor: pointer; text-decoration: none;
    transition: all 0.2s; text-align: center;
}
.qa-btn:hover { border-color: var(--gold); background: var(--gold-dim); }
.qa-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; }
.qa-label { font-size: 12px; font-weight: 500; color: var(--muted2); }
.qa-btn:hover .qa-label { color: var(--gold); }

@keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

/* ── MOBILE TOGGLE ── */
.menu-toggle { display: none; }

@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .bottom-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.open { transform: translateX(0); }
    .main { margin-left: 0; }
    .menu-toggle { display: flex; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .page-content { padding: 20px 16px; }
}
</style>
@stack('styles')
</head>
<body>
<div class="app">

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">O</div>
            <div class="brand-text">OJT<span>Connect</span></div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                User Management
                <span class="nav-badge">{{ $pendingCount ?? 0 }}</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">OJT Management</div>
            <a href="#" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Applications
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                Hours Monitoring
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Reports
            </a>
            <a href="#" class="nav-item">
                <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                Companies
            </a>
        </div>

       <a href="{{ route('admin.settings') }}"
        class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
            </svg>
            Settings
        </a>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="user-role">System Administrator</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin-left:auto;">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;" title="Logout">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main">
        <!-- TOPBAR -->
        <header class="topbar">
            <button class="icon-btn menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                <div class="icon-btn" title="Notifications">
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    <span class="notif-dot"></span>
                </div>
            </div>
        </header>

        <!-- PAGE -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>