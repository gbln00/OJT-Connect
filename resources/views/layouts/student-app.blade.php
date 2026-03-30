<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OJT Connect') — OJTConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

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
            --muted2:     rgba(171,171,171,0.55);
            --sidebar-w:  248px;

            --teal-color:   #2dd4bf;
            --teal-dim:     rgba(45,212,191,0.08);
            --teal-border:  rgba(45,212,191,0.2);
            --coral-color:  #f87171;
            --coral-dim:    rgba(248,113,113,0.08);
            --coral-border: rgba(248,113,113,0.2);
            --blue-color:   #60a5fa;
            --blue-dim:     rgba(96,165,250,0.08);
            --blue-border:  rgba(96,165,250,0.2);
            --gold-color:   #c9a84c;
            --gold-dim:     rgba(201,168,76,0.1);
            --gold-border:  rgba(201,168,76,0.25);
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
            --muted2:     #6b7280;

            --teal-color:   #0f9e8e;
            --teal-dim:     rgba(15,158,142,0.07);
            --teal-border:  rgba(15,158,142,0.2);
            --coral-color:  #e05252;
            --coral-dim:    rgba(224,82,82,0.07);
            --coral-border: rgba(224,82,82,0.2);
            --blue-color:   #1d4ed8;
            --blue-dim:     rgba(29,78,216,0.07);
            --blue-border:  rgba(29,78,216,0.2);
            --gold-color:   #9a6f00;
            --gold-dim:     rgba(154,111,0,0.08);
            --gold-border:  rgba(154,111,0,0.2);
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

        [data-theme="dark"] body::after {
            content: '';
            position: fixed; inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.045'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 999; opacity: 0.5;
        }

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
        .topbar-btn:hover { background: var(--surface2); color: var(--text); }
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

        /* ═══════════════════════════════════════════════
           PAGE CONTENT
        ═══════════════════════════════════════════════ */
        .main-content { padding: 28px 32px; flex: 1; max-width: 1400px; width: 100%; }

        /* ═══════════════════════════════════════════════
           CARDS
        ═══════════════════════════════════════════════ */
        .card {
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
        .card-action {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 12px; font-weight: 600;
            color: var(--crimson); text-decoration: none;
            letter-spacing: 0.08em; text-transform: uppercase;
            transition: opacity 0.2s;
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
        .stat-icon.crimson { border-color: rgba(140,14,3,0.35);    background: rgba(140,14,3,0.08);    color: var(--crimson); }
        .stat-icon.steel   { border-color: rgba(171,171,171,0.2);  background: rgba(171,171,171,0.06); color: var(--ash); }
        .stat-icon.night   { border-color: rgba(14,17,38,0.5);     background: rgba(14,17,38,0.2);     color: #60A5FA; }
        .stat-icon.gold    { border-color: var(--gold-border);     background: var(--gold-dim);         color: var(--gold-color); }
        .stat-icon.teal    { border-color: var(--teal-border);     background: var(--teal-dim);         color: var(--teal-color); }
        .stat-icon.blue    { border-color: var(--blue-border);     background: var(--blue-dim);         color: var(--blue-color); }
        .stat-icon.coral   { border-color: var(--coral-border);    background: var(--coral-dim);        color: var(--coral-color); }

        [data-theme="light"] .stat-icon.crimson { border-color: rgba(140,14,3,0.25); }
        [data-theme="light"] .stat-icon.steel   { color: #333740; }
        [data-theme="light"] .stat-icon.night   { color: var(--blue-color); border-color: var(--blue-border); background: var(--blue-dim); }

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
        thead th {
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
        tbody td { padding: 12px 16px; font-size: 13px; color: var(--text2); vertical-align: middle; }

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

        /* Status pill */
        .status-pill {
            display: inline-flex; align-items: center; padding: 3px 9px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.07em; text-transform: uppercase;
        }
        .status-pill.gold    { background: var(--gold-dim);   color: var(--gold-color); border:1px solid var(--gold-border); }
        .status-pill.green   { background: rgba(52,211,153,0.08); color: #34d399; border:1px solid rgba(52,211,153,0.2); }
        .status-pill.crimson { background: rgba(140,14,3,0.1);    color: #c0392b; border:1px solid rgba(140,14,3,0.2); }
        .status-pill.blue    { background: var(--blue-dim);   color: var(--blue-color); border:1px solid var(--blue-border); }
        .status-pill.steel   { background: rgba(171,171,171,0.07);color: var(--ash); border:1px solid var(--border2); }

        [data-theme="light"] .status-pill.green   { color: #0f9660; background: rgba(16,155,96,0.08); border-color: rgba(16,155,96,0.2); }
        [data-theme="light"] .status-pill.crimson { color: #8C0E03; }

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

        /* ═══════════════════════════════════════════════
           FORMS
        ═══════════════════════════════════════════════ */
        .form-section-divider {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 16px;
        }
        .form-section-divider::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
        .form-section-divider::after  { content: ''; flex: 1; height: 1px; background: var(--border); }
        .form-section-divider span {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.22em; text-transform: uppercase;
            color: var(--muted);
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            color: var(--text);
            font-size: 13px;
            font-family: 'Barlow', sans-serif;
            outline: none;
            transition: border-color 0.15s;
            box-sizing: border-box;
            border-radius: 0;
        }
        .form-input:focus { border-color: var(--crimson); }
        .form-input.is-invalid { border-color: var(--crimson); }
        .form-select {
            width: 100%; padding: 10px 14px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            color: var(--text); font-size: 13px;
            font-family: 'Barlow', sans-serif;
            outline: none; cursor: pointer;
            transition: border-color 0.15s;
            box-sizing: border-box; border-radius: 0;
        }
        .form-select:focus { border-color: var(--crimson); }
        .form-label {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.12em;
            text-transform: uppercase; color: var(--muted);
            margin-bottom: 6px;
        }
        .form-hint  { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 4px; letter-spacing: 0.04em; }
        .form-error { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--crimson); margin-top: 4px; letter-spacing: 0.04em; }

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
        .activity-dot {
            width: 7px; height: 7px; flex-shrink: 0;
            margin-top: 5px;
        }
        .activity-dot.crimson { background: var(--crimson); }
        .activity-dot.green   { background: #34d399; }
        .activity-dot.blue    { background: #60a5fa; }
        .activity-dot.steel   { background: var(--ash); }
        .activity-dot.gold    { background: var(--gold-color); }
        .activity-text { font-size: 13px; color: var(--text); line-height: 1.5; }
        .activity-text strong { color: var(--text); font-weight: 500; }
        .activity-time { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 3px; }

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
            text-decoration: none;
            transition: all 0.12s;
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
        .progress-fill.gold  { background: var(--gold-color); }
        .progress-fill.teal  { background: var(--teal-color); }

        /* ═══════════════════════════════════════════════
           QUICK ACTIONS
        ═══════════════════════════════════════════════ */
        .quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; padding: 14px; }
        .qa-btn {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 8px; padding: 16px 10px;
            background: var(--surface2); border: 1px solid var(--border);
            cursor: pointer; text-decoration: none; text-align: center;
            transition: all 0.2s;
        }
        .qa-btn:hover { border-color: rgba(140,14,3,0.35); background: rgba(140,14,3,0.06); }
        .qa-icon { width: 30px; height: 30px; border: 1px solid var(--border2); display: flex; align-items: center; justify-content: center; }
        .qa-btn:hover .qa-icon { border-color: rgba(140,14,3,0.35); color: var(--crimson); }
        .qa-label { font-family: 'Barlow Condensed', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text2); }
        .qa-btn:hover .qa-label { color: var(--crimson); }

        /* ═══════════════════════════════════════════════
           ALERT BANNERS
        ═══════════════════════════════════════════════ */
        .alert {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 16px; margin-bottom: 16px;
            border: 1px solid; font-size: 13px;
        }
        .alert.success { background: rgba(52,211,153,0.07); border-color: rgba(52,211,153,0.2); color: #34d399; }
        .alert.error   { background: rgba(140,14,3,0.07);   border-color: rgba(140,14,3,0.25);  color: var(--crimson); }
        .alert.warning { background: var(--gold-dim);        border-color: var(--gold-border);   color: var(--gold-color); }
        .alert.info    { background: var(--blue-dim);        border-color: var(--blue-border);   color: var(--blue-color); }

        [data-theme="light"] .alert.success { color: #0f9660; background: rgba(16,155,96,0.07); border-color: rgba(16,155,96,0.2); }
        [data-theme="light"] .alert.error   { color: #8C0E03; }

        /* ═══════════════════════════════════════════════
           MOBILE SIDEBAR OVERLAY
        ═══════════════════════════════════════════════ */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 190;
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
        [data-theme="light"] .topbar { box-shadow: 0 1px 0 var(--border); }
        [data-theme="light"] .card   { box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        [data-theme="light"] .status-dot.active { color: #0f9660; }
        [data-theme="light"] .activity-dot.green { background: #0f9660; }
        [data-theme="light"] .btn-approve { color: #0f9660; border-color: rgba(15,150,96,0.3); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); }
        ::-webkit-scrollbar-thumb:hover { background: var(--steel); }

        @stack('styles')
    </style>
    @stack('styles')
</head>
<body>

<div class="grid-overlay"></div>

<div class="app-shell">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('student.dashboard') }}" class="sidebar-brand">
            <div class="brand-icon"><span>O</span></div>
            <div class="brand-text">OJT<em>Connect</em></div>
        </a>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>

            <a href="{{ route('student.dashboard') }}"
               class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section-label">My OJT</div>

            <a href="{{ route('student.application.create') }}"
               class="nav-item {{ request()->routeIs('student.application.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                OJT Application
            </a>

            <a href="{{ route('student.hours.index') }}"
               class="nav-item {{ request()->routeIs('student.hours.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
                Hour Logs
            </a>

            <a href="{{ route('student.reports.index') }}"
               class="nav-item {{ request()->routeIs('student.reports.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="12" y1="17" x2="8" y2="17"/>
                </svg>
                Weekly Reports
            </a>

            <a href="{{ route('student.evaluation.show') }}"
               class="nav-item {{ request()->routeIs('student.evaluation.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                    <polyline points="22,4 12,14.01 9,11.01"/>
                </svg>
                My Evaluation
            </a>

            <div class="nav-section-label">System</div>

            <a href="{{ route('student.settings') }}"
               class="nav-item {{ request()->routeIs('student.settings*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                </svg>
                Settings
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Student' }}</div>
                    <div class="sidebar-user-role">Student Intern</div>
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

            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-actions">

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

                {{-- User dropdown --}}
                <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                    <div class="topbar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
                    </div>
                    <span class="topbar-user-name" id="topbar-name">
                        {{ explode(' ', auth()->user()->name ?? 'Student')[0] }}
                    </span>
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>

                    <div class="user-dropdown" id="user-dropdown">
                        <a href="{{ route('student.settings') }}" class="dropdown-item">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Profile & Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">@csrf</form>
                        @csrf
                        <a href="#" class="dropdown-item danger"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
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

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="alert success fade-up">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert error fade-up">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ session('error') }}
            </div>
            @endif

            @if(session('info'))
            <div class="alert info fade-up">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
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

    // ── Dropdown ──────────────────────────────────────────────────
    function toggleDropdown() {
        document.getElementById('user-dropdown').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const btn = document.getElementById('topbar-user-btn');
        const dd  = document.getElementById('user-dropdown');
        if (btn && !btn.contains(e.target)) dd.classList.remove('open');
    });

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