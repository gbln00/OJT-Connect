<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OJT Connect') — OJTConnect</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        $activeFont  = $tenantBrandFont ?? 'barlow';
        $cssFontName = match($activeFont) {
            'inter'   => 'Inter',
            'poppins' => 'Poppins',
            'roboto'  => 'Roboto',
            default   => '{{ $cssFontName }}',
        };
        $fontQuery = match($activeFont) {
            'inter'   => 'Inter:wght@300;400;500;600;700',
            'poppins' => 'Poppins:wght@300;400;500;600;700',
            'roboto'  => 'Roboto:wght@300;400;500;700',
            default   => '{{ $cssFontName }}:ital,wght@0,300;0,400;0,500;0,600;1,300',
        };
    @endphp
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family={{ $cssFontName }}+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&family={{ $fontQuery }}&display=swap" rel="stylesheet">
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
            --muted2:     rgba(171,171,171,0.55);
            --sidebar-w:  248px;

            /* Extended palette — used across pages */
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

            /* Extended palette — light overrides */
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
            font-family: '{{ $cssFontName }}', sans-serif;
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
            font-size: 16px; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: var(--text);
        }
        .brand-text em { color: var(--muted); font-style: normal; }

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
            font-family: '{{ $cssFontName }}', sans-serif;
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

        /* Locked nav item style — for gated features on lower plans */
        .nav-item-locked {
            display: flex; align-items: center; gap: 10px;
            padding: 8px 10px;
            color: var(--muted);
            font-family: '{{ $cssFontName }}', sans-serif;
            font-size: 13.5px; font-weight: 400;
            margin-bottom: 1px;
            position: relative;
            border: 1px solid transparent;
            opacity: 0.5;
            cursor: not-allowed;
            user-select: none;
        }
        .nav-lock-icon {
            margin-left: auto;
            color: var(--muted);
            opacity: 0.6;
            flex-shrink: 0;
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
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
        /* Stat icon color variants */
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
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
            font-family: '{{ $cssFontName }} Condensed', sans-serif;
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
        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 9px 12px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            color: var(--text); font-size: 13px;
            font-family: '{{ $cssFontName }}', sans-serif;
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
        .activity-text { font-size: 13px; color: var(--text); line-height: 1.5; }
        .activity-text strong { color: var(--text); font-weight: 500; }
        .activity-time { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 3px; }

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
        .qa-label { font-family: '{{ $cssFontName }} Condensed', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text2); }
        .qa-btn:hover .qa-label { color: var(--crimson); }

        /* ═══════════════════════════════════════════════
           MODALS
        ═══════════════════════════════════════════════ */
        .modal-backdrop {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            z-index: 400; align-items: center; justify-content: center;
        }
        .modal-backdrop.open { display: flex; }
        .modal {
            background: var(--surface);
            border: 1px solid var(--border2);
            padding: 28px; width: 100%; max-width: 440px;
            margin: 16px; position: relative;
            animation: modalIn 0.2s cubic-bezier(.22,.61,.36,1);
        }
        @keyframes modalIn { from { opacity:0; transform:translateY(-12px) scale(0.97); } to { opacity:1; transform:none; } }
        .modal::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: var(--crimson);
        }
        .modal-title { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .modal-sub   { font-size: 13px; color: var(--muted); margin-bottom: 18px; }

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
        [data-theme="light"] .card   { box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
        [data-theme="light"] .modal  { box-shadow: 0 20px 60px rgba(0,0,0,0.18); }
        [data-theme="light"] .status-dot.active { color: #0f9660; }
        [data-theme="light"] .activity-dot.green { background: #0f9660; }
        [data-theme="light"] .btn-approve { color: #0f9660; border-color: rgba(15,150,96,0.3); }

        /* ─── Scrollbar theming ─── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--border2); }
        ::-webkit-scrollbar-thumb:hover { background: var(--steel); }
    </style>
    

    @stack('styles')

    @include('layouts.partials.tenant_inject')
 
</head>
<body>

<div class="grid-overlay"></div>

<div class="app-shell">

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar" id="sidebar">

        @include('layouts.partials.tenant_brand', ['dashboardRoute' => 'admin.dashboard'])

        <nav class="sidebar-nav">
            {{-- Tenant name pill --}}
            @if(tenant('name'))
            <span class="hidden md:flex items-center gap-1.5 px-2 py-0.5 border border-[#8C0E03]/20 bg-[#8C0E03]/[0.06]
                        font-['DM_Mono'] text-[10px] tracking-[0.12em] text-[#8C0E03]/60 uppercase">
                <span class="w-1 h-1 bg-[#8C0E03] inline-block animate-flicker flex-shrink-0"></span>
                {{ tenant('name') }}
            </span>
            @endif

            <div class="nav-section-label">Main</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                    <rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section-label">Management</div>

            {{-- ── Basic+ (always visible) ── --}}
            <a href="{{ route('admin.users.index') }}"
               class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.companies.index') }}"
               class="nav-item {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                    <polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
                Companies
            </a>

            <a href="{{ route('admin.applications.index') }}"
               class="nav-item {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Applications
            </a>

            <a href="{{ route('admin.hours.index') }}"
               class="nav-item {{ request()->routeIs('admin.hours.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
                Hour Logs
            </a>

            {{-- ── Standard+ only ── --}}
            @php $tenantPlan = tenancy()->tenant?->plan ?? 'basic'; @endphp

            @if(in_array($tenantPlan, ['standard', 'premium']))
                <a href="{{ route('admin.reports.index') }}"
                   class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Weekly Reports
                </a>

                <a href="{{ route('admin.evaluations.index') }}"
                   class="nav-item {{ request()->routeIs('admin.evaluations.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    Evaluations
                </a>
            @else
                {{-- Locked: Weekly Reports --}}
                <span class="nav-item-locked" title="Upgrade to Standard or above to access Weekly Reports">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    Weekly Reports
                    <span class="nav-lock-icon">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                    <span class="nav-badge" style="background:var(--gold-color);font-size:8px;">STD</span>
                </span>

                {{-- Locked: Evaluations --}}
                <span class="nav-item-locked" title="Upgrade to Standard or above to access Evaluations">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    Evaluations
                    <span class="nav-lock-icon">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                    <span class="nav-badge" style="background:var(--gold-color);font-size:8px;">STD</span>
                </span>
            @endif

            {{-- ── Premium only ── --}}
            @if($tenantPlan === 'premium')
                <a href="{{ route('admin.export.index') }}"
                   class="nav-item {{ request()->routeIs('admin.export.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export Reports
                    <span class="nav-badge" style="background:var(--teal-color);font-size:8px;">PREMIUM</span>
                </a>

                <a href="{{ route('admin.analytics.index') }}"
                   class="nav-item {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6"  y1="20" x2="6"  y2="14"/>
                    </svg>
                    Analytics
                    <span class="nav-badge" style="background:var(--teal-color);font-size:8px;">PREMIUM</span>
                </a>
            @else
                {{-- Locked: Export Reports --}}
                <span class="nav-item-locked" title="Upgrade to Premium to access Export Reports">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export Reports
                    <span class="nav-lock-icon">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                    <span class="nav-badge" style="background:var(--gold-color);font-size:8px;">PREMIUM</span>
                </span>

                {{-- Locked: Analytics --}}
                <span class="nav-item-locked" title="Upgrade to Premium to access Analytics">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6"  y1="20" x2="6"  y2="14"/>
                    </svg>
                    Analytics
                    <span class="nav-lock-icon">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </span>
                </span>
            @endif

            <div class="nav-section-label">Plan & Promotions</div>

            <a href="{{ route('admin.plan.index') }}"
               class="nav-item {{ request()->routeIs('admin.plan.index') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Plan
                {{-- Show current plan badge --}}
                @if($tenantPlan === 'premium')
                    <span class="nav-badge" style="background:var(--teal-color);font-size:8px;margin-left:auto;">PREMIUM</span>
                @elseif($tenantPlan === 'standard')
                    <span class="nav-badge" style="background:var(--blue-color);font-size:8px;margin-left:auto;">STD</span>
                @else
                    <span class="nav-badge" style="background:var(--muted);font-size:8px;margin-left:auto;">BASIC</span>
                @endif
            </a>
            {{-- ── <a href="{{ route('admin.2fa.setup') }}" class="nav-item {{ request()->routeIs('admin.2fa.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                {{ auth()->user()->two_factor_enabled ? 'Manage 2FA' : 'Enable 2FA' }}
            </a> ── --}}
            
            <div class="nav-section-label">Alerts</div>
            {{-- ── Always visible ── --}}
            <a href="{{ route('admin.notifications.index') }}"
                class="nav-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                Notifications
                @php
                    $notifCount = \App\Models\TenantNotification::forRole('admin')->forUser(auth()->id())->unread()->count();
                @endphp
                
                @if($notifCount > 0)
                    <span class="nav-badge">{{ $notifCount }}</span>
                @endif
            </a>

            <div class="nav-section-label">System</div>
             @if($tenantPlan === 'premium')
                 {{-- ── Customization (Premium only) ── --}}
                <a href="{{ route('admin.customization.index') }}"
                    class="nav-item {{ request()->routeIs('admin.customization.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor"
                        stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83
                                2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0
                                00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0
                                00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0
                                004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0
                                004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06
                                A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09
                                a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0
                                012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21
                                a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Customization
                    <span class="nav-badge" style="background:var(--teal-color);font-size:8px;">PREMIUM</span>
                </a>
            @else
                {{-- ── Customization (Premium only) ── --}}
                <a href="{{ route('admin.customization.index') }}"
                    class="nav-item {{ request()->routeIs('admin.customization.*') ? 'active' : '' }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor"
                        stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83
                                2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0
                                00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0
                                00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0
                                004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0
                                004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06
                                A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09
                                a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0
                                012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21
                                a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                    Customization
                    <span class="nav-badge" style="background:var(--gold-color);font-size:8px;">PREMIUM</span>
                </a>
            @endif

            <a href="{{ route('admin.settings') }}"
               class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                </svg>
                Settings
            </a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="sidebar-user-role">{{ str_replace('_', ' ', auth()->user()->role ?? 'admin') }}</div>
                </div>
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

            {{-- Breadcrumb / page title --}}
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

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

            <div class="topbar-actions">
                @php
                    $tenantUnread = \App\Models\TenantNotification::forRole('admin')->forUser(auth()->id())->unread()->count();
                    $tenantNotifs = \App\Models\TenantNotification::forRole('admin')->forUser(auth()->id())->latest()->take(5)->get();
                @endphp
                <div style="position:relative;" id="tenant-notif-wrapper">
                    <button class="topbar-btn" id="tenant-notif-btn"
                            onclick="toggleTenantNotif()" title="Notifications"
                            style="position:relative;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @if($tenantUnread > 0)
                        <span style="position:absolute;top:-5px;right:-5px;min-width:16px;height:16px;padding:0 3px;
                                    background:var(--crimson);color:#fff;font-family:'DM Mono',monospace;
                                    font-size:9px;display:flex;align-items:center;justify-content:center;line-height:1;">
                            {{ $tenantUnread > 9 ? '9+' : $tenantUnread }}
                        </span>
                        @endif
                    </button>
                
                    <div id="tenant-notif-dropdown" style="display:none;position:absolute;top:calc(100% + 8px);right:0;
                        width:300px;background:var(--surface);border:1px solid var(--border2);
                        box-shadow:0 16px 48px rgba(0,0,0,0.35);z-index:400;">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid var(--border);">
                            <span style="font-family:'{{ $cssFontName }} Condensed',sans-serif;font-size:11px;font-weight:700;
                                        letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);">
                                Notifications
                                @if($tenantUnread > 0)
                                    <span style="color:var(--crimson);">({{ $tenantUnread }})</span>
                                @endif
                            </span>
                            <a href="{{ route('admin.notifications.index') }}"
                            style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.1em;
                                    text-transform:uppercase;color:var(--muted);text-decoration:none;transition:color 0.15s;"
                            onmouseover="this.style.color='var(--text)'"
                            onmouseout="this.style.color='var(--muted)'">
                            View all
                            </a>
                        </div>
                        <div style="max-height:320px;overflow-y:auto;">
                            @forelse($tenantNotifs as $tn)
                            @php
                                $tnc = match($tn->type) {
                                    'success' => ['color'=>'#34d399','border'=>'rgba(52,211,153,0.2)','bg'=>'rgba(52,211,153,0.06)'],
                                    'warning' => ['color'=>'#c9a84c','border'=>'rgba(201,168,76,0.25)','bg'=>'rgba(201,168,76,0.06)'],
                                    default   => ['color'=>'#60a5fa','border'=>'rgba(96,165,250,0.2)','bg'=>'rgba(96,165,250,0.06)'],
                                };
                            @endphp
                            <div style="display:flex;align-items:flex-start;gap:10px;padding:11px 16px;
                                        border-bottom:1px solid var(--border);
                                        background:{{ !$tn->is_read ? 'rgba(140,14,3,0.03)' : 'transparent' }};
                                        {{ !$tn->is_read ? 'border-left:2px solid rgba(140,14,3,0.4);padding-left:14px;' : '' }}
                                        cursor:default;">
                                <div style="width:24px;height:24px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                                            border:1px solid {{ $tnc['border'] }};background:{{ $tnc['bg'] }};margin-top:2px;">
                                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $tnc['color'] }};display:inline-block;"></span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:12px;font-weight:600;color:{{ $tn->is_read ? 'var(--text2)' : 'var(--text)' }};line-height:1.3;">
                                        {{ $tn->title }}
                                    </div>
                                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.4;
                                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $tn->message }}
                                    </div>
                                    <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:3px;opacity:0.6;">
                                        {{ $tn->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                @if(!$tn->is_read)
                                <span style="width:5px;height:5px;border-radius:50%;background:var(--crimson);flex-shrink:0;margin-top:5px;"></span>
                                @endif
                            </div>
                            @empty
                            <div style="padding:28px 16px;text-align:center;font-family:'DM Mono',monospace;
                                        font-size:11px;color:var(--muted);">
                                // No notifications
                            </div>
                            @endforelse
                        </div>
                        <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
                            <a href="{{ route('admin.notifications.index') }}"
                            style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;
                                    text-transform:uppercase;color:var(--muted);text-decoration:none;">
                                View all notifications →
                            </a>
                        </div>
                    </div>
                </div>

                <div class="topbar-divider"></div>

                {{-- User dropdown --}}
                <div class="topbar-user" id="topbar-user-btn" onclick="toggleDropdown()">
                    <div class="topbar-user-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                    </div>
                    <span class="topbar-user-name" style="display:none;" id="topbar-name">
                        {{ explode(' ', auth()->user()->name ?? 'Admin')[0] }}
                    </span>
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--muted);">
                        <polyline points="6,9 12,15 18,9"/>
                    </svg>

                    <div class="user-dropdown" id="user-dropdown">
                        <a href="{{ route('admin.settings') }}" class="dropdown-item">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Profile & Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="/logout" id="logout-form">@csrf</form>
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

            {{-- ── Tenant Announcement Banner ───────────────────────────── --}}
            @if(!empty($tenantAnnouncementActive) && !empty($tenantAnnouncement))
            <div id="ojt-announcement"
                style="background:rgba({{ implode(',', sscanf($tenantBrandColor ?? '8C0E03', '%02x%02x%02x')) }},0.08);
                        border:1px solid rgba({{ implode(',', sscanf($tenantBrandColor ?? '8C0E03', '%02x%02x%02x')) }},0.25);
                        padding:10px 16px;margin-bottom:20px;
                        display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <span style="font-size:13px;color:var(--text);">
                    📢 {{ $tenantAnnouncement }}
                </span>
                <button onclick="document.getElementById('ojt-announcement').style.display='none'"
                        style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:16px;
                            line-height:1;flex-shrink:0;" aria-label="Dismiss">✕</button>
            </div>
            @elseif (session('subscription_warning'))
                @include('layouts.partials.subscription-warning', [
                    'warning' => session('subscription_warning')
                ])
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
        const btn = document.getElementById('menu-toggle');
        const name = document.getElementById('topbar-name');
        if (btn) btn.style.display = e.matches ? 'flex' : 'none';
        if (name) name.style.display = e.matches ? 'none' : 'inline';
    }
    mq.addEventListener('change', handleMq);
    handleMq(mq);

    // ── Tenant notifications dropdown ─────────────────────────────
    function toggleTenantNotif() {
        const dd = document.getElementById('tenant-notif-dropdown');
        dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
        // Close user dropdown if open
        document.getElementById('user-dropdown')?.classList.remove('open');
    }
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('tenant-notif-wrapper');
        const dd = document.getElementById('tenant-notif-dropdown');
        if (wrapper && dd && !wrapper.contains(e.target)) dd.style.display = 'none';
    });
</script>

 @stack('scripts')

</body>
</html>