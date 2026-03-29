<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — OJTConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ═══════════════════════════════════════════════
           CSS CUSTOM PROPERTIES — THEME TOKENS
        ═══════════════════════════════════════════════ */
        :root {
            --crimson:    #8C0E03;
            --crimson-lo: rgba(140,14,3,0.08);
            --crimson-md: rgba(140,14,3,0.18);
            --night:      #0E1126;
            --steel:      #333740;
            --ash:        #ABABAB;
            --void:       #0D0D0D;

            /* DARK theme defaults */
            --bg:         #0D0D0D;
            --surface:    #0E1126;
            --surface2:   #131929;
            --surface3:   #1a2235;
            --border:     rgba(171,171,171,0.07);
            --border2:    rgba(171,171,171,0.13);
            --text:       rgba(255,255,255,0.88);
            --text2:      rgba(171,171,171,0.65);
            --muted:      rgba(171,171,171,0.35);
            --sidebar-w:  248px;
        }

        [data-theme="light"] {
            --bg:         #F5F4F0;
            --surface:    #FFFFFF;
            --surface2:   #F0EEE9;
            --surface3:   #E8E5DE;
            --border:     rgba(51,55,64,0.1);
            --border2:    rgba(51,55,64,0.18);
            --text:       #0D0D0D;
            --text2:      #333740;
            --muted:      #8a8e99;
        }

        /* ═══════════════════════════════════════════════
           RESET + BASE
        ═══════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body {
            height: 100%;
            font-family: 'Barlow', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* Grain overlay (dark only) */
        [data-theme="dark"] body::after {
            content: '';
            position: fixed; inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.045'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 999; opacity: 0.5;
        }

        /* Subtle grid */
        [data-theme="dark"] .grid-overlay {
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(171,171,171,0.022) 1px, transparent 1px),
                linear-gradient(90deg, rgba(171,171,171,0.022) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        [data-theme="light"] .grid-overlay { display: none; }

        /* ═══════════════════════════════════════════════
           LAYOUT SHELL
        ═══════════════════════════════════════════════ */
        .app-shell { display: flex; min-height: 100vh; position: relative; }

        /* ═══════════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 200;
            transition: transform 0.3s cubic-bezier(.22,.61,.36,1);
        }

        /* Brand */
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }
        .brand-icon {
            width: 32px; height: 32px;
            border: 1px solid rgba(140,14,3,0.55);
            background: rgba(140,14,3,0.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; position: relative;
        }
        .brand-icon::after {
            content: '';
            position: absolute; top: -3px; right: -3px;
            width: 6px; height: 6px; background: var(--crimson);
        }
        .brand-icon span {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 14px; color: var(--crimson);
        }
        .brand-text {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 16px; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--text);
        }
        .brand-text em { color: var(--muted); font-style: normal; }

        /* Super Admin tag in brand */
        .brand-sa-tag {
            margin-left: auto;
            display: inline-flex; align-items: center;
            padding: 2px 6px;
            border: 1px solid rgba(140,14,3,0.3);
            background: rgba(140,14,3,0.08);
            font-family: 'DM Mono', monospace;
            font-size: 8px; letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(140,14,3,0.8);
            flex-shrink: 0;
        }

        /* Nav sections */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border2); border-radius: 2px; }

        .nav-section-label {
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--muted); padding: 14px 10px 6px;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px;
            border-radius: 0;
            color: var(--text2);
            text-decoration: none;
            font-family: 'Barlow', sans-serif;
            font-size: 13.5px; font-weight: 400;
            transition: all 0.15s;
            margin-bottom: 1px;
            position: relative;
            border: 1px solid transparent;
        }
        .nav-item:hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--border);
        }
        .nav-item.active {
            background: rgba(140,14,3,0.08);
            color: var(--crimson);
            font-weight: 500;
            border-color: rgba(140,14,3,0.2);
        }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 4px; bottom: 4px;
            width: 2px; background: var(--crimson);
        }
        .nav-badge {
            margin-left: auto;
            background: var(--crimson); color: #fff;
            font-family: 'DM Mono', monospace;
            font-size: 10px; padding: 1px 6px;
            border-radius: 0;
            line-height: 1.5;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
        }
        .sidebar-avatar {
            width: 32px; height: 32px; flex-shrink: 0;
            border: 1px solid rgba(140,14,3,0.4);
            background: rgba(140,14,3,0.08);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 12px; font-weight: 700; color: var(--crimson);
        }
        .sidebar-user-name {
            font-size: 13px; font-weight: 500; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-family: 'DM Mono', monospace;
            font-size: 10px; color: var(--muted);
            letter-spacing: 0.05em; text-transform: uppercase; margin-top: 1px;
        }

        /* ═══════════════════════════════════════════════
           MAIN AREA
        ═══════════════════════════════════════════════ */
        .layout-body {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex; flex-direction: column;
            min-height: 100vh;
            position: relative; z-index: 1;
        }

        /* ═══════════════════════════════════════════════
           TOPBAR
        ═══════════════════════════════════════════════ */
        .topbar {
            height: 73px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 28px; gap: 14px;
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 15px; font-weight: 700;
            color: var(--text); flex: 1;
            letter-spacing: -0.01em;
        }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .topbar-btn {
            width: 34px; height: 34px;
            border: 1px solid var(--border2);
            background: var(--surface);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text2);
            transition: all 0.15s;
        }
        .topbar-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }
        .topbar-divider { width: 1px; height: 20px; background: var(--border2); }

        .topbar-user {
            display: flex; align-items: center; gap: 7px;
            padding: 5px 10px; cursor: pointer;
            border: 1px solid transparent;
            position: relative;
            transition: all 0.15s;
        }
        .topbar-user:hover { border-color: var(--border2); background: var(--surface); }
        .topbar-user-avatar {
            width: 28px; height: 28px;
            border: 1px solid rgba(140,14,3,0.45);
            background: rgba(140,14,3,0.08);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 11px; font-weight: 700; color: var(--crimson);
        }
        .topbar-user-name { font-size: 13px; font-weight: 500; color: var(--text); }

        /* Dropdown */
        .user-dropdown {
            display: none; position: absolute; top: calc(100% + 8px); right: 0;
            background: var(--surface);
            border: 1px solid var(--border2);
            min-width: 200px; z-index: 500;
            padding: 6px 0;
            box-shadow: 0 16px 48px rgba(0,0,0,0.35);
        }
        .user-dropdown.open { display: block; animation: ddFade 0.15s ease; }
        @keyframes ddFade { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }
        .dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 14px;
            font-size: 13px; color: var(--text2);
            text-decoration: none; cursor: pointer;
            transition: all 0.12s;
        }
        .dropdown-item:hover { background: var(--surface2); color: var(--text); }
        .dropdown-item.danger { color: var(--crimson); }
        .dropdown-item.danger:hover { background: rgba(140,14,3,0.08); }
        .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

        /* Dropdown header */
        .dropdown-header {
            padding: 9px 14px 8px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }
        .dropdown-header-name { font-size: 13px; font-weight: 500; color: var(--text); }
        .dropdown-header-role {
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase;
            color: var(--muted); margin-top: 2px;
        }

        /* ═══════════════════════════════════════════════
           PAGE CONTENT
        ═══════════════════════════════════════════════ */
        .main-content { padding: 28px 32px; flex: 1; max-width: 1400px; width: 100%; }

        /* ═══════════════════════════════════════════════
           CARDS
        ═══════════════════════════════════════════════ */
        .card, .sa-card {
            background: var(--surface);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .card-header {
            padding: 16px 20px 14px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 13.5px; font-weight: 700; color: var(--text);
        }
        .card-title-main {
            font-family: 'Playfair Display', serif;
            font-size: 14px; font-weight: 700; color: var(--text);
        }
        .card-action {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 12px; font-weight: 600;
            color: var(--crimson); text-decoration: none;
            letter-spacing: 0.08em; text-transform: uppercase;
            transition: opacity 0.2s; display: flex; align-items: center; gap: 4px;
        }
        .card-action:hover { opacity: 0.7; }

        /* ═══════════════════════════════════════════════
           STAT CARDS
        ═══════════════════════════════════════════════ */
        .stats-grid { display: grid; gap: 12px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 18px 20px;
            transition: border-color 0.2s, transform 0.2s;
            cursor: default; position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: var(--crimson); transform: scaleX(0); transform-origin: left;
            transition: transform 0.3s cubic-bezier(.22,.61,.36,1);
        }
        .stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
        .stat-card:hover::before { transform: scaleX(1); }

        .stat-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .stat-icon {
            width: 36px; height: 36px;
            border: 1px solid;
            display: flex; align-items: center; justify-content: center;
        }
        .stat-icon.crimson { border-color: rgba(140,14,3,0.35); background: rgba(140,14,3,0.08); color: var(--crimson); }
        .stat-icon.steel   { border-color: rgba(171,171,171,0.2); background: rgba(171,171,171,0.06); color: var(--ash); }
        .stat-icon.night   { border-color: rgba(14,17,38,0.5); background: rgba(14,17,38,0.2); color: #60A5FA; }
        .stat-icon.gold    { border-color: rgba(171,171,171,0.2); background: rgba(171,171,171,0.06); color: #c9a84c; }

        .stat-tag {
            font-family: 'DM Mono', monospace;
            font-size: 9px; color: var(--muted);
            letter-spacing: 0.15em; text-transform: uppercase;
            border: 1px solid var(--border2); padding: 2px 7px;
        }
        .stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 28px; font-weight: 900; color: var(--text);
            line-height: 1; margin-bottom: 4px;
        }
        .stat-label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--muted);
        }

        /* ═══════════════════════════════════════════════
           TABLE
        ═══════════════════════════════════════════════ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th, th {
            padding: 10px 16px;
            font-family: 'DM Mono', monospace;
            font-size: 10px; font-weight: 500;
            color: var(--muted); letter-spacing: 0.12em; text-transform: uppercase;
            text-align: left; border-bottom: 1px solid var(--border);
            background: var(--surface2);
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        tbody td, td { padding: 12px 16px; font-size: 13px; color: var(--text2); vertical-align: middle; border-bottom: 1px solid var(--border); }
        tr:last-child td { border-bottom: none; }

        /* ═══════════════════════════════════════════════
           BADGES
        ═══════════════════════════════════════════════ */
        .role-badge {
            display: inline-flex; align-items: center;
            padding: 2px 8px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
        }
        .role-badge.admin       { background: rgba(140,14,3,0.1);   color: #c0392b; border: 1px solid rgba(140,14,3,0.25); }
        .role-badge.coordinator { background: rgba(52,211,153,0.08); color: #34d399; border: 1px solid rgba(52,211,153,0.2); }
        .role-badge.supervisor  { background: rgba(96,165,250,0.08); color: #60a5fa; border: 1px solid rgba(96,165,250,0.2); }
        .role-badge.student     { background: rgba(171,171,171,0.06);color: var(--ash); border: 1px solid var(--border2); }

        [data-theme="light"] .role-badge.admin       { background: rgba(140,14,3,0.08); color: #8C0E03; }
        [data-theme="light"] .role-badge.coordinator { background: rgba(16,155,96,0.08); color: #0f9660; }
        [data-theme="light"] .role-badge.supervisor  { background: rgba(29,78,216,0.08); color: #1d4ed8; }
        [data-theme="light"] .role-badge.student     { background: rgba(51,55,64,0.07); color: #333740; }

        .status-dot { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; }
        .status-dot::before { content: ''; width: 6px; height: 6px; background: currentColor; }
        .status-dot.active   { color: #34d399; }
        .status-dot.inactive { color: var(--muted); }

        [data-theme="light"] .status-dot.active { color: #0f9660; }

        /* Status pill */
        .status-pill {
            display: inline-flex; align-items: center; padding: 3px 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
        }
        .status-pill.gold    { background: rgba(201,168,76,0.1);  color: #c9a84c; border:1px solid rgba(201,168,76,0.25); }
        .status-pill.green   { background: rgba(52,211,153,0.08); color: #34d399; border:1px solid rgba(52,211,153,0.2); }
        .status-pill.crimson { background: rgba(140,14,3,0.1);    color: #c0392b; border:1px solid rgba(140,14,3,0.2); }
        .status-pill.blue    { background: rgba(96,165,250,0.08); color: #60a5fa; border:1px solid rgba(96,165,250,0.2); }
        .status-pill.steel   { background: rgba(171,171,171,0.07);color: var(--ash); border:1px solid var(--border2); }

        [data-theme="light"] .status-pill.green   { color: #0f9660; background: rgba(16,155,96,0.08); border-color: rgba(16,155,96,0.2); }
        [data-theme="light"] .status-pill.crimson { color: #8C0E03; }
        [data-theme="light"] .status-pill.gold    { color: #9a6f00; background: rgba(154,111,0,0.1); border-color: rgba(154,111,0,0.2); }

        /* Generic badge (from superadmin original) */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 2px 8px;
            font-family: 'DM Mono', monospace;
            font-size: 10px; font-weight: 500;
        }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .badge-active   { background: rgba(52,211,153,0.08); color: #34d399; border: 1px solid rgba(52,211,153,0.2); }
        .badge-pending  { background: rgba(251,191,36,0.08); color: #fbbf24; border: 1px solid rgba(251,191,36,0.2); }
        .badge-approved { background: rgba(52,211,153,0.08); color: #34d399; border: 1px solid rgba(52,211,153,0.2); }
        .badge-rejected { background: rgba(140,14,3,0.1);    color: #c0392b; border: 1px solid rgba(140,14,3,0.25); }
        .badge-basic    { background: rgba(171,171,171,0.07); color: var(--ash); border: 1px solid var(--border2); }
        .badge-standard { background: rgba(91,143,185,0.08); color: #5b8fb9; border: 1px solid rgba(91,143,185,0.2); }
        .badge-premium  { background: rgba(201,168,76,0.08); color: #c9a84c; border: 1px solid rgba(201,168,76,0.2); }

        [data-theme="light"] .badge-active   { color: #0f9660; background: rgba(16,155,96,0.08); border-color: rgba(16,155,96,0.2); }
        [data-theme="light"] .badge-rejected { color: #8C0E03; }

        /* ═══════════════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; cursor: pointer;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 12px; font-weight: 600;
            letter-spacing: 0.1em; text-transform: uppercase;
            border: 1px solid; text-decoration: none;
            transition: all 0.15s;
        }
        .btn-primary { background: var(--crimson); color: rgba(255,255,255,0.92); border-color: var(--crimson); }
        .btn-primary:hover { background: #a81004; border-color: #a81004; transform: translateY(-1px); }
        .btn-ghost  { background: transparent; color: var(--text2); border-color: var(--border2); }
        .btn-ghost:hover { border-color: var(--border2); background: var(--surface2); color: var(--text); }
        .btn-sm { padding: 5px 10px; font-size: 11px; }
        .btn-danger { background: transparent; color: #c0392b; border-color: rgba(140,14,3,0.3); }
        .btn-danger:hover { background: rgba(140,14,3,0.08); }
        .btn-approve { background: transparent; color: #34d399; border-color: rgba(52,211,153,0.25); }
        .btn-approve:hover { background: rgba(52,211,153,0.06); }

        [data-theme="light"] .btn-approve { color: #0f9660; border-color: rgba(15,150,96,0.3); }

        /* ═══════════════════════════════════════════════
           FORMS
        ═══════════════════════════════════════════════ */
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 9px 12px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            color: var(--text); font-size: 13px;
            font-family: 'Barlow', sans-serif;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: var(--crimson);
        }
        .form-label {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--muted);
            margin-bottom: 6px;
        }
        .form-hint { font-size: 11px; color: var(--muted); margin-top: 4px; }
        .form-error { font-size: 11px; color: var(--crimson); margin-top: 4px; }

        /* ═══════════════════════════════════════════════
           FLASH / ALERTS
        ═══════════════════════════════════════════════ */
        .flash {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 15px;
            font-size: 13px; font-weight: 400;
            border: 1px solid;
            margin-bottom: 20px;
        }
        .flash-success { background: rgba(52,211,153,0.05); border-color: rgba(52,211,153,0.2); color: #34d399; }
        .flash-error   { background: rgba(140,14,3,0.08); border-color: rgba(140,14,3,0.25); color: #c0392b; }
        .flash-info    { background: rgba(91,143,185,0.07); border-color: rgba(91,143,185,0.2); color: #5b8fb9; }

        [data-theme="light"] .flash-success { color: #0f9660; background: rgba(16,155,96,0.06); border-color: rgba(16,155,96,0.2); }
        [data-theme="light"] .flash-error   { color: #8C0E03; }

        /* ═══════════════════════════════════════════════
           NOTIFICATION BELL
        ═══════════════════════════════════════════════ */
        .notif-bell {
            position: relative;
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border: 1px solid var(--border2);
            background: var(--surface);
            color: var(--text2);
            cursor: pointer;
            transition: all 0.15s;
        }
        .notif-bell:hover { background: var(--surface2); color: var(--text); border-color: var(--border2); }

        .notif-count {
            position: absolute; top: -5px; right: -5px;
            min-width: 16px; height: 16px; padding: 0 4px;
            background: var(--crimson); color: #fff;
            font-family: 'DM Mono', monospace;
            font-size: 9px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            line-height: 1;
        }

        .notif-dropdown {
            display: none;
            position: absolute; top: calc(100% + 8px); right: 0;
            width: 320px;
            background: var(--surface);
            border: 1px solid var(--border2);
            box-shadow: 0 16px 48px rgba(0,0,0,0.35);
            z-index: 400;
        }
        .notif-dropdown.open { display: block; animation: ddFade 0.15s ease; }

        .notif-dd-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }
        .notif-dd-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.16em; text-transform: uppercase;
            color: var(--muted);
        }
        .notif-dd-mark-all {
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--crimson);
            background: none; border: none; cursor: pointer;
            transition: opacity 0.15s; padding: 0; opacity: 0.8;
        }
        .notif-dd-mark-all:hover { opacity: 1; }

        .notif-list { max-height: 340px; overflow-y: auto; }

        .notif-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 11px 16px;
            border-bottom: 1px solid var(--border);
            cursor: pointer; transition: background 0.12s;
            text-decoration: none;
        }
        .notif-item:last-child { border-bottom: none; }
        .notif-item:hover { background: var(--surface2); }
        .notif-item.unread {
            background: rgba(140,14,3,0.04);
            border-left: 2px solid rgba(140,14,3,0.4);
            padding-left: 14px;
        }
        .notif-item.unread:hover { background: rgba(140,14,3,0.07); }

        .notif-icon-wrap {
            width: 28px; height: 28px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid; margin-top: 1px;
        }
        .notif-title { font-size: 12px; font-weight: 600; color: var(--text); line-height: 1.3; }
        .notif-msg   { font-size: 11px; color: var(--text2); font-family: 'DM Mono', monospace; margin-top: 3px; line-height: 1.4; }
        .notif-time  { font-size: 10px; color: var(--muted); font-family: 'DM Mono', monospace; margin-top: 4px; }

        .notif-dd-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--border);
            text-align: center;
        }
        .notif-dd-footer a {
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--muted); text-decoration: none; transition: color 0.15s;
        }
        .notif-dd-footer a:hover { color: var(--text); }

        .notif-empty {
            padding: 32px 16px; text-align: center;
            font-family: 'DM Mono', monospace;
            font-size: 11px; color: var(--muted);
        }

        /* ═══════════════════════════════════════════════
           ACTIVITY / FEED
        ═══════════════════════════════════════════════ */
        .activity-list { padding: 4px 0; }
        .activity-item {
            display: flex; gap: 12px; padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            transition: background 0.12s;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-item:hover { background: var(--surface2); }
        .activity-dot { width: 7px; height: 7px; flex-shrink: 0; margin-top: 5px; }
        .activity-dot.crimson { background: var(--crimson); }
        .activity-dot.green   { background: #34d399; }
        .activity-dot.blue    { background: #60a5fa; }
        .activity-dot.steel   { background: var(--ash); }
        .activity-text { font-size: 13px; color: var(--text); line-height: 1.5; }
        .activity-text strong { color: var(--text); font-weight: 500; }
        .activity-time { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 3px; }

        /* ═══════════════════════════════════════════════
           PAGINATION
        ═══════════════════════════════════════════════ */
        .pagination {
            padding: 12px 18px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .pagination-info { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.05em; }
        .page-link {
            display: inline-flex; align-items: center;
            padding: 5px 10px;
            border: 1px solid var(--border2);
            font-family: 'DM Mono', monospace;
            font-size: 11px; color: var(--text2);
            text-decoration: none; transition: all 0.12s;
        }
        .page-link:hover { background: var(--surface2); color: var(--text); }
        .page-link.disabled { color: var(--muted); pointer-events: none; border-color: var(--border); }

        /* ═══════════════════════════════════════════════
           PROGRESS BAR
        ═══════════════════════════════════════════════ */
        .progress-track { height: 6px; background: var(--border2); overflow: hidden; }
        .progress-fill { height: 100%; transition: width 0.4s; background: var(--crimson); }
        .progress-fill.green { background: #34d399; }
        .progress-fill.blue  { background: #60a5fa; }

        /* ═══════════════════════════════════════════════
           GREETING
        ═══════════════════════════════════════════════ */
        .greeting { margin-bottom: 28px; }
        .greeting-sub {
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 6px;
        }
        .greeting-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 3vw, 30px); font-weight: 900;
            color: var(--text); line-height: 1.1;
        }
        .greeting-title span { color: var(--crimson); font-style: italic; }

        /* ═══════════════════════════════════════════════
           DANGER ZONE
        ═══════════════════════════════════════════════ */
        .danger-zone { border-color: rgba(140,14,3,0.2) !important; }
        .danger-zone .card-header { border-bottom-color: rgba(140,14,3,0.12); }

        /* ═══════════════════════════════════════════════
           SIDEBAR OVERLAY (mobile)
        ═══════════════════════════════════════════════ */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55); z-index: 190;
        }
        .sidebar-overlay.open { display: block; }

        /* ═══════════════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════════════ */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up { animation: fadeUp 0.5s cubic-bezier(.22,.61,.36,1) both; }
        .fade-up-1 { animation-delay: 0.05s; }
        .fade-up-2 { animation-delay: 0.10s; }
        .fade-up-3 { animation-delay: 0.15s; }
        .fade-up-4 { animation-delay: 0.20s; }
        .fade-up-5 { animation-delay: 0.25s; }

        @keyframes flicker { 0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:.4} 94%{opacity:1} 96%{opacity:.6} 97%{opacity:1} }
        .flicker { animation: flicker 8s ease-in-out infinite; }

        /* ═══════════════════════════════════════════════
           RESPONSIVE
        ═══════════════════════════════════════════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .layout-body { margin-left: 0; }
            .main-content { padding: 20px 16px; }
        }
        @media (max-width: 1024px) {
            .main-content { padding: 22px 20px; }
        }

        /* ═══════════════════════════════════════════════
           LIGHT MODE OVERRIDES
        ═══════════════════════════════════════════════ */
        [data-theme="light"] .sidebar { box-shadow: 2px 0 20px rgba(0,0,0,0.06); }
        [data-theme="light"] .stat-icon.crimson { border-color: rgba(140,14,3,0.25); }
        [data-theme="light"] .stat-icon.steel   { color: #333740; }
        [data-theme="light"] .topbar { box-shadow: 0 1px 0 var(--border); }
        [data-theme="light"] .card, [data-theme="light"] .sa-card { box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        [data-theme="light"] .notif-dropdown { box-shadow: 0 16px 48px rgba(0,0,0,0.12); }
        [data-theme="light"] .status-dot.active { color: #0f9660; }
        [data-theme="light"] .activity-dot.green { background: #0f9660; }

        /* ─── Scrollbar theming ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); }
        ::-webkit-scrollbar-thumb:hover { background: var(--steel); }
    </style>

    @stack('styles')
</head>
<body>

<div class="grid-overlay"></div>

<div class="app-shell">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('super_admin.dashboard') }}" class="sidebar-brand">
            <div class="brand-icon"><span>O</span></div>
            <div class="brand-text">OJT<em>Connect</em></div>
            <span class="brand-sa-tag">SA</span>
        </a>

        <nav class="sidebar-nav">

            <div class="nav-section-label">Overview</div>

            <a href="{{ route('super_admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section-label">Management</div>

            <a href="{{ route('super_admin.tenants.index') }}"
               class="nav-item {{ request()->routeIs('super_admin.tenants.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Tenants
            </a>

            <a href="{{ route('super_admin.approvals.pending') }}"
               class="nav-item {{ request()->routeIs('super_admin.approvals.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="flex:1;">Approvals</span>
                @php $pendingCount = \App\Models\TenantRegistration::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="nav-badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('super_admin.notifications.index') }}"
               class="nav-item {{ request()->routeIs('super_admin.notifications.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span style="flex:1;">Notifications</span>
                @if(($notifUnread ?? 0) > 0)
                    <span class="nav-badge">{{ $notifUnread }}</span>
                @endif
            </a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Super Admin' }}</div>
                    <div class="sidebar-user-role">Super Admin</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="flex-shrink:0;">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;display:flex;align-items:center;" title="Log out">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                            <polyline points="16,17 21,12 16,7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

    {{-- ═══ MAIN BODY ═══ --}}
    <div class="layout-body">

        {{-- TOPBAR --}}
        <header class="topbar">
            <button class="topbar-btn" id="menu-toggle" onclick="toggleSidebar()"
                    style="display:none;" aria-label="Menu">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            {{-- Page title --}}
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-actions">

                @yield('topbar-actions')

                {{-- Theme toggle --}}
                <button class="topbar-btn" onclick="toggleTheme()" title="Toggle theme" aria-label="Toggle theme">
                    <svg id="icon-moon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                    <svg id="icon-sun" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none;">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                </button>

                <div class="topbar-divider"></div>

                {{-- 🔔 Notification Bell --}}
                @php
                    $notifUnread  = \App\Models\SuperAdminNotification::unread()->count();
                    $notifPreview = \App\Models\SuperAdminNotification::latest()->take(6)->get();

                    $notifIcons = [
                        'bell'   => ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>', 'color' => 'var(--text2)', 'border' => 'var(--border2)', 'bg' => 'transparent'],
                        'check'  => ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',                                   'color' => '#34d399', 'border' => 'rgba(52,211,153,0.2)',  'bg' => 'rgba(52,211,153,0.06)'],
                        'x'      => ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',                            'color' => '#f87171', 'border' => 'rgba(239,68,68,0.2)',   'bg' => 'rgba(239,68,68,0.06)'],
                        'plus'   => ['svg' => '<line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>',                              'color' => '#60a5fa', 'border' => 'rgba(96,165,250,0.2)',  'bg' => 'rgba(96,165,250,0.06)'],
                        'toggle' => ['svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>', 'color' => '#c9a84c', 'border' => 'rgba(201,168,76,0.25)', 'bg' => 'rgba(201,168,76,0.06)'],
                    ];
                @endphp

                <div style="position:relative;" id="notif-wrapper">
                    <button class="notif-bell" id="notif-bell-btn" onclick="toggleNotifDropdown()" aria-label="Notifications">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($notifUnread > 0)
                            <span class="notif-count">{{ $notifUnread > 99 ? '99+' : $notifUnread }}</span>
                        @endif
                    </button>

                    <div class="notif-dropdown" id="notif-dropdown">
                        <div class="notif-dd-header">
                            <span class="notif-dd-title">
                                Notifications
                                @if($notifUnread > 0)
                                    <span style="color:var(--crimson);margin-left:4px;">({{ $notifUnread }})</span>
                                @endif
                            </span>
                            @if($notifUnread > 0)
                                <form method="POST" action="{{ route('super_admin.notifications.markAllRead') }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="notif-dd-mark-all">Mark all read</button>
                                </form>
                            @endif
                        </div>

                        <div class="notif-list">
                            @forelse($notifPreview as $notif)
                                @php $ic = $notifIcons[$notif->icon] ?? $notifIcons['bell']; @endphp
                                <a href="{{ $notif->link ?? route('super_admin.notifications.index') }}"
                                   class="notif-item {{ !$notif->is_read ? 'unread' : '' }}"
                                   onclick="markRead(event, {{ $notif->id }}, '{{ $notif->link ?? route('super_admin.notifications.index') }}')">
                                    <div class="notif-icon-wrap"
                                         style="color:{{ $ic['color'] }};border-color:{{ $ic['border'] }};background:{{ $ic['bg'] }};">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            {!! $ic['svg'] !!}
                                        </svg>
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <div class="notif-title">{{ $notif->title }}</div>
                                        <div class="notif-msg">{{ Str::limit($notif->message, 60) }}</div>
                                        <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                                    </div>
                                    @if(!$notif->is_read)
                                        <span style="width:6px;height:6px;border-radius:50%;background:var(--crimson);flex-shrink:0;margin-top:4px;"></span>
                                    @endif
                                </a>
                            @empty
                                <div class="notif-empty">// No notifications yet</div>
                            @endforelse
                        </div>

                        @if($notifPreview->count() > 0)
                            <div class="notif-dd-footer">
                                <a href="{{ route('super_admin.notifications.index') }}">View all notifications</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="topbar-divider"></div>

                {{-- User dropdown --}}
                <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                    <div class="topbar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'SA', 0, 2)) }}
                    </div>
                    <span class="topbar-user-name" style="display:none;" id="topbar-name">
                        {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}
                    </span>
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);">
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
                           onclick="event.preventDefault();document.getElementById('sa-logout-form').submit();">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                            Log out
                        </a>
                    </div>
                </div>

            </div>
        </header>

        {{-- PAGE CONTENT --}}
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
</div>

<script>
    // ── Theme ─────────────────────────────────────────────────────
    (function() {
        const saved = localStorage.getItem('ojt-theme') || 'dark';
        applyTheme(saved);
    })();

    function applyTheme(mode) {
        document.documentElement.setAttribute('data-theme', mode);
        document.getElementById('icon-moon').style.display = mode === 'dark'  ? 'block' : 'none';
        document.getElementById('icon-sun').style.display  = mode === 'light' ? 'block' : 'none';
        localStorage.setItem('ojt-theme', mode);
    }
    function toggleTheme() {
        const cur = document.documentElement.getAttribute('data-theme') || 'dark';
        applyTheme(cur === 'dark' ? 'light' : 'dark');
    }

    // ── User Dropdown ─────────────────────────────────────────────
    function toggleDropdown() {
        document.getElementById('user-dropdown').classList.toggle('open');
        document.getElementById('notif-dropdown').classList.remove('open');
    }
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('topbar-user-btn');
        const dd  = document.getElementById('user-dropdown');
        if (btn && !btn.contains(e.target)) dd.classList.remove('open');
    });

    // ── Notification Bell ─────────────────────────────────────────
    function toggleNotifDropdown() {
        document.getElementById('notif-dropdown').classList.toggle('open');
        document.getElementById('user-dropdown').classList.remove('open');
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notif-wrapper');
        const dd      = document.getElementById('notif-dropdown');
        if (wrapper && dd && !wrapper.contains(e.target)) dd.classList.remove('open');
    });

    function markRead(e, id, link) {
        e.preventDefault();
        fetch('{{ url('super-admin/notifications') }}/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
        }).then(() => { window.location.href = link; });
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
        const btn  = document.getElementById('menu-toggle');
        const name = document.getElementById('topbar-name');
        if (btn)  btn.style.display  = e.matches ? 'flex' : 'none';
        if (name) name.style.display = e.matches ? 'none' : 'inline';
    }
    mq.addEventListener('change', handleMq);
    handleMq(mq);
</script>

@stack('scripts')
</body>
</html>