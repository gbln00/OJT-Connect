<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJTConnect — OJT Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #0A0C12;
            --surface: #111318;
            --border:  rgba(255,255,255,0.06);
            --gold:    #F0B429;
            --gold2:   #E8A020;
            --teal:    #2DD4BF;
            --text:    #E8EAF0;
            --muted:   #6B7280;
            --muted2:  #9CA3AF;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── NOISE TEXTURE OVERLAY ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* ── GRID BACKGROUND ── */
        .grid-bg {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(240,180,41,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(240,180,41,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── GLOW ORBS ── */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .orb-1 {
            width: 600px; height: 600px;
            background: rgba(240,180,41,0.07);
            top: -200px; right: -100px;
            animation: drift1 12s ease-in-out infinite alternate;
        }
        .orb-2 {
            width: 500px; height: 500px;
            background: rgba(45,212,191,0.05);
            bottom: -150px; left: -100px;
            animation: drift2 15s ease-in-out infinite alternate;
        }
        @keyframes drift1 { from { transform: translate(0,0); } to { transform: translate(-40px,40px); } }
        @keyframes drift2 { from { transform: translate(0,0); } to { transform: translate(40px,-30px); } }

        /* ── NAVBAR ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 48px;
            border-bottom: 1px solid var(--border);
            background: rgba(10,12,18,0.8);
            backdrop-filter: blur(12px);
            animation: fadeDown 0.6s ease both;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-logo {
            width: 36px; height: 36px;
            background: var(--gold);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: #0A0C12;
        }
        .nav-name {
            font-family: 'Syne', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }
        .nav-name span { color: var(--gold); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-ghost {
            padding: 9px 22px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.04);
            color: var(--muted2);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .btn-ghost:hover {
            border-color: rgba(255,255,255,0.15);
            color: var(--text);
            background: rgba(255,255,255,0.07);
        }

        .btn-gold {
            padding: 9px 22px;
            border-radius: 8px;
            border: none;
            background: var(--gold);
            color: #0A0C12;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .btn-gold:hover {
            background: var(--gold2);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(240,180,41,0.25);
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 24px 80px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid rgba(240,180,41,0.25);
            background: rgba(240,180,41,0.08);
            font-size: 12px;
            font-weight: 500;
            color: var(--gold);
            margin-bottom: 28px;
            font-family: 'DM Mono', monospace;
            letter-spacing: 0.04em;
            animation: fadeUp 0.6s 0.1s ease both;
        }
        .hero-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(0.8)} }

        .hero-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(42px, 7vw, 80px);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -2px;
            color: var(--text);
            margin-bottom: 24px;
            max-width: 900px;
            animation: fadeUp 0.7s 0.2s ease both;
        }
        .hero-title .accent {
            color: var(--gold);
            position: relative;
            display: inline-block;
        }
        .hero-title .accent::after {
            content: '';
            position: absolute;
            bottom: -4px; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), transparent);
            border-radius: 2px;
        }
        .hero-title .accent2 { color: var(--teal); }

        .hero-sub {
            font-size: 17px;
            font-weight: 300;
            color: var(--muted2);
            max-width: 540px;
            line-height: 1.7;
            margin-bottom: 40px;
            animation: fadeUp 0.7s 0.3s ease both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 0.7s 0.4s ease both;
        }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 10px;
            background: var(--gold);
            color: #0A0C12;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .btn-hero-primary:hover {
            background: var(--gold2);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(240,180,41,0.3);
        }
        .btn-hero-primary svg { transition: transform 0.2s; }
        .btn-hero-primary:hover svg { transform: translateX(3px); }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.04);
            color: var(--muted2);
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.2s;
        }
        .btn-hero-secondary:hover {
            border-color: rgba(255,255,255,0.2);
            color: var(--text);
            background: rgba(255,255,255,0.08);
        }

        /* ── UNIVERSITY BADGE ── */
        .uni-badge {
            margin-top: 48px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
            font-size: 12px;
            color: var(--muted);
            animation: fadeUp 0.7s 0.5s ease both;
        }
        .uni-badge strong { color: var(--muted2); font-weight: 500; }

        /* ── STATS STRIP ── */
        .stats-strip {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.015);
            padding: 0;
            overflow: hidden;
        }

        .stat-item {
            flex: 1;
            max-width: 220px;
            padding: 28px 32px;
            text-align: center;
            border-right: 1px solid var(--border);
            animation: fadeUp 0.6s ease both;
        }
        .stat-item:last-child { border-right: none; }
        .stat-num {
            font-family: 'Syne', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: var(--gold);
            letter-spacing: -1px;
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 400;
        }

        /* ── FEATURES ── */
        .features {
            position: relative;
            z-index: 1;
            padding: 100px 48px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .features-label {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            letter-spacing: 0.12em;
            color: var(--gold);
            text-transform: uppercase;
            margin-bottom: 12px;
            text-align: center;
        }
        .features-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 700;
            color: var(--text);
            text-align: center;
            margin-bottom: 56px;
            letter-spacing: -1px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .feature-card {
            padding: 28px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--surface);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .feature-card:hover {
            border-color: rgba(240,180,41,0.2);
            transform: translateY(-4px);
            background: rgba(240,180,41,0.03);
        }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 44px; height: 44px;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
        }
        .f-gold  { background: rgba(240,180,41,0.12); color: var(--gold); }
        .f-teal  { background: rgba(45,212,191,0.10); color: var(--teal); }
        .f-blue  { background: rgba(96,165,250,0.10); color: #60A5FA; }
        .f-coral { background: rgba(248,113,113,0.10); color: #F87171; }
        .f-purple{ background: rgba(167,139,250,0.10); color: #A78BFA; }
        .f-green { background: rgba(74,222,128,0.10); color: #4ADE80; }

        .feature-title {
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }
        .feature-desc {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── ROLES SECTION ── */
        .roles {
            position: relative;
            z-index: 1;
            padding: 0 48px 100px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .roles-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(24px, 3vw, 36px);
            font-weight: 700;
            color: var(--text);
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .role-card {
            padding: 24px 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface);
            text-align: center;
            transition: all 0.3s;
        }
        .role-card:hover { transform: translateY(-3px); border-color: rgba(255,255,255,0.12); }

        .role-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
            font-size: 20px;
        }
        .role-name {
            font-family: 'Syne', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }
        .role-desc {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.5;
        }

        /* ── CTA ── */
        .cta {
            position: relative;
            z-index: 1;
            margin: 0 48px 80px;
            padding: 60px 48px;
            border-radius: 20px;
            border: 1px solid rgba(240,180,41,0.15);
            background: linear-gradient(135deg, rgba(240,180,41,0.06) 0%, rgba(45,212,191,0.04) 100%);
            text-align: center;
            overflow: hidden;
        }
        .cta::before {
            content: '';
            position: absolute;
            top: -60px; left: 50%;
            transform: translateX(-50%);
            width: 300px; height: 300px;
            background: rgba(240,180,41,0.08);
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }

        .cta-title {
            font-family: 'Syne', sans-serif;
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: var(--text);
            margin-bottom: 14px;
            letter-spacing: -1px;
            position: relative;
        }
        .cta-sub {
            font-size: 15px;
            color: var(--muted2);
            margin-bottom: 32px;
            position: relative;
        }
        .cta-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            position: relative;
        }

        /* ── FOOTER ── */
        footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid var(--border);
            padding: 28px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .footer-brand {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--muted);
        }
        .footer-brand strong { color: var(--gold); font-family: 'Syne', sans-serif; }
        .footer-links {
            display: flex; gap: 20px;
            font-size: 12px; color: var(--muted);
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            nav { padding: 16px 24px; }
            .features, .roles { padding: 60px 24px; }
            .features-grid { grid-template-columns: 1fr 1fr; }
            .roles-grid { grid-template-columns: 1fr 1fr; }
            .cta { margin: 0 24px 60px; padding: 40px 28px; }
            footer { padding: 24px; flex-direction: column; text-align: center; }
            .stats-strip { flex-wrap: wrap; }
            .stat-item { border-right: none; border-bottom: 1px solid var(--border); }
            .stat-item:last-child { border-bottom: none; }
        }
        @media (max-width: 600px) {
            .features-grid { grid-template-columns: 1fr; }
            .roles-grid { grid-template-columns: 1fr 1fr; }
            .nav-links .btn-ghost { display: none; }
        }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>

<!-- NAVBAR -->
<nav>
    <a href="/" class="nav-brand">
        <div class="nav-logo">O</div>
        <div class="nav-name">OJT<span>Connect</span></div>
    </a>
    <div class="nav-links">
        <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
        <a href="{{ route('register') }}" class="btn-gold">Register as student</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-badge">Bukidnon State University · OJT Management System</div>

    <h1 class="hero-title">
        Manage your OJT<br>
        <span class="accent">smarter</span> and
        <span class="accent2">faster</span>
    </h1>

    <p class="hero-sub">
        OJTConnect streamlines the entire On-the-Job Training process — from application to completion — for students, coordinators, and supervisors.
    </p>

    <div class="hero-actions">
        <a href="{{ route('register') }}" class="btn-hero-primary">
            Get started
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
        <a href="{{ route('login') }}" class="btn-hero-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            Log in
        </a>
    </div>

    <div class="uni-badge">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        <span>Available for <strong>College of Technology</strong> · <strong>College of Business</strong> · <strong>College of Education</strong></span>
    </div>
</section>

<!-- STATS STRIP -->
<div class="stats-strip">
    <div class="stat-item" style="animation-delay:0.1s;">
        <div class="stat-num">4</div>
        <div class="stat-label">User roles</div>
    </div>
    <div class="stat-item" style="animation-delay:0.2s;">
        <div class="stat-num">3</div>
        <div class="stat-label">Colleges supported</div>
    </div>
    <div class="stat-item" style="animation-delay:0.3s;">
        <div class="stat-num">100%</div>
        <div class="stat-label">Paperless process</div>
    </div>
    <div class="stat-item" style="animation-delay:0.4s;">
        <div class="stat-num">OJT</div>
        <div class="stat-label">End-to-end management</div>
    </div>
</div>

<!-- FEATURES -->
<section class="features">
    <div class="features-label">What we offer</div>
    <div class="features-title">Everything you need for OJT</div>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon f-gold">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
            <div class="feature-title">Online OJT Application</div>
            <div class="feature-desc">Students apply online, upload requirements, and track application status in real time.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon f-teal">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <div class="feature-title">Hours Monitoring</div>
            <div class="feature-desc">Log daily time in/out, track total approved hours, and monitor progress against required hours.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon f-blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div class="feature-title">Weekly Reports</div>
            <div class="feature-desc">Submit weekly accomplishment reports with file attachments for coordinator review.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon f-coral">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div class="feature-title">Student Evaluation</div>
            <div class="feature-desc">Company supervisors rate attendance, performance, and submit overall evaluation grades.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon f-purple">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
            </div>
            <div class="feature-title">Export Reports</div>
            <div class="feature-desc">Generate PDF summaries and Excel workbooks for records, analytics, and accreditation.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon f-green">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div class="feature-title">Multi-role Access</div>
            <div class="feature-desc">Separate dashboards for admins, coordinators, supervisors, and students — each with the right permissions.</div>
        </div>
    </div>
</section>

<!-- ROLES -->
<section class="roles">
    <div class="roles-title">Built for everyone involved in OJT</div>
    <div class="roles-grid">
        <div class="role-card">
            <div class="role-avatar" style="background:rgba(240,180,41,0.12);">
                <svg width="24" height="24" fill="none" stroke="#F0B429" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
            </div>
            <div class="role-name">Admin</div>
            <div class="role-desc">Manages users, companies, verifications, and system settings</div>
        </div>
        <div class="role-card">
            <div class="role-avatar" style="background:rgba(45,212,191,0.1);">
                <svg width="24" height="24" fill="none" stroke="#2DD4BF" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
            </div>
            <div class="role-name">OJT Coordinator</div>
            <div class="role-desc">Reviews applications, monitors hours, and marks OJT completion</div>
        </div>
        <div class="role-card">
            <div class="role-avatar" style="background:rgba(96,165,250,0.1);">
                <svg width="24" height="24" fill="none" stroke="#60A5FA" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            </div>
            <div class="role-name">Company Supervisor</div>
            <div class="role-desc">Validates attendance, monitors interns, and submits evaluations</div>
        </div>
        <div class="role-card">
            <div class="role-avatar" style="background:rgba(248,113,113,0.1);">
                <svg width="24" height="24" fill="none" stroke="#F87171" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div class="role-name">Student Intern</div>
            <div class="role-desc">Applies for OJT, logs hours, submits reports, and views evaluation</div>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="cta" style="max-width:1100px;margin:0 auto 80px;">
    <h2 class="cta-title">Ready to start your OJT journey?</h2>
    <p class="cta-sub">Register your student account and get verified by your coordinator to begin.</p>
    <div class="cta-actions">
        <a href="{{ route('register') }}" class="btn-hero-primary">
            Register now
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
        <a href="{{ route('login') }}" class="btn-hero-secondary">
            Already have an account? Log in
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-brand">
        <div style="width:24px;height:24px;background:var(--gold);border-radius:6px;display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:800;font-size:11px;color:#0A0C12;">O</div>
        <span><strong>OJTConnect</strong> · OJT Management System · Bukidnon State University</span>
    </div>
    <div class="footer-links">
        <span>College of Technology</span>
        <span>College of Business</span>
        <span>College of Education</span>
    </div>
</footer>

</body>
</html>