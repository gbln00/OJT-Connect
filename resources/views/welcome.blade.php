<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJTConnect — OJT Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #08090E;
            --surface: #0F1118;
            --surface2:#161B27;
            --border:  rgba(255,255,255,0.06);
            --border2: rgba(255,255,255,0.10);
            --gold:    #F0B429;
            --gold2:   #D99B1A;
            --teal:    #2DD4BF;
            --coral:   #F87171;
            --blue:    #60A5FA;
            --text:    #E8EAF0;
            --muted:   #6B7280;
            --muted2:  #9CA3AF;
        }

        html, body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }

        /* NOISE */
        body::after {
            content:''; position:fixed; inset:0;
            background:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events:none; z-index:999; opacity:0.35;
        }

        /* GRID */
        .grid-bg {
            position:fixed; inset:0;
            background-image: linear-gradient(rgba(240,180,41,0.025) 1px, transparent 1px), linear-gradient(90deg, rgba(240,180,41,0.025) 1px, transparent 1px);
            background-size:72px 72px; pointer-events:none; z-index:0;
        }
        .grid-bg::after {
            content:''; position:absolute; inset:0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(240,180,41,0.06) 0%, transparent 70%);
        }

        /* ORBS */
        .orb { position:fixed; border-radius:50%; filter:blur(120px); pointer-events:none; z-index:0; }
        .orb-1 { width:700px; height:700px; background:rgba(240,180,41,0.055); top:-250px; right:-200px; animation:d1 14s ease-in-out infinite alternate; }
        .orb-2 { width:600px; height:600px; background:rgba(45,212,191,0.04); bottom:-200px; left:-150px; animation:d2 18s ease-in-out infinite alternate; }
        .orb-3 { width:400px; height:400px; background:rgba(96,165,250,0.03); top:40%; left:40%; animation:d1 20s ease-in-out infinite alternate; }
        @keyframes d1 { to { transform:translate(-50px,60px); } }
        @keyframes d2 { to { transform:translate(60px,-40px); } }

        /* NAV */
        nav {
            position:fixed; top:0; left:0; right:0; z-index:100;
            display:flex; align-items:center; justify-content:space-between;
            padding:18px 52px;
            border-bottom:1px solid var(--border);
            background:rgba(8,9,14,0.75);
            backdrop-filter:blur(16px);
            animation:fadeDown 0.5s ease both;
        }
        .nav-brand { display:flex; align-items:center; gap:11px; text-decoration:none; }
        .nav-logo {
            width:34px; height:34px; background:var(--gold); border-radius:9px;
            display:flex; align-items:center; justify-content:center;
            font-family:'Syne',sans-serif; font-weight:800; font-size:15px; color:#08090E;
        }
        .nav-name { font-family:'Syne',sans-serif; font-size:17px; font-weight:700; color:var(--text); }
        .nav-name span { color:var(--gold); }
        .nav-tag {
            padding:3px 9px; border-radius:20px;
            border:1px solid rgba(240,180,41,0.2);
            background:rgba(240,180,41,0.07);
            font-size:10px; font-weight:500; color:var(--gold);
            font-family:'DM Mono',monospace; letter-spacing:0.05em;
        }
        .btn-login {
            display:inline-flex; align-items:center; gap:7px;
            padding:9px 22px; border-radius:8px;
            background:var(--gold); color:#08090E;
            font-size:13.5px; font-weight:600;
            text-decoration:none; font-family:'DM Sans',sans-serif;
            transition:all 0.2s;
        }
        .btn-login:hover { background:var(--gold2); transform:translateY(-1px); box-shadow:0 8px 24px rgba(240,180,41,0.22); }

        /* HERO */
        .hero {
            position:relative; z-index:1;
            min-height:100vh;
            display:flex; flex-direction:column;
            align-items:center; justify-content:center;
            text-align:center; padding:130px 24px 80px;
        }

        .hero-eyebrow {
            display:inline-flex; align-items:center; gap:8px;
            padding:5px 14px; border-radius:20px;
            border:1px solid rgba(240,180,41,0.2);
            background:rgba(240,180,41,0.07);
            font-size:11.5px; font-weight:500; color:var(--gold);
            font-family:'DM Mono',monospace; letter-spacing:0.05em;
            margin-bottom:30px;
            animation:fadeUp 0.5s 0.1s ease both;
        }
        .hero-eyebrow-dot {
            width:6px; height:6px; border-radius:50%; background:var(--gold);
            animation:pulse 2s ease-in-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(0.75)} }

        .hero-title {
            font-family:'Syne',sans-serif;
            font-size:clamp(44px,7.5vw,86px);
            font-weight:800; line-height:1.02;
            letter-spacing:-3px; color:var(--text);
            margin-bottom:20px; max-width:860px;
            animation:fadeUp 0.6s 0.15s ease both;
        }
        .hero-title .g { color:var(--gold); }
        .hero-title .t { color:var(--teal); }
        .hero-title .dim { color:rgba(232,234,240,0.35); }

        .hero-desc {
            font-size:17px; font-weight:300; font-style:italic;
            color:var(--muted2); max-width:500px;
            line-height:1.75; margin-bottom:44px;
            animation:fadeUp 0.6s 0.25s ease both;
        }

        .hero-cta {
            animation:fadeUp 0.6s 0.35s ease both;
        }
        .btn-hero {
            display:inline-flex; align-items:center; gap:9px;
            padding:15px 36px; border-radius:10px;
            background:var(--gold); color:#08090E;
            font-size:15px; font-weight:700;
            text-decoration:none; font-family:'DM Sans',sans-serif;
            transition:all 0.25s; letter-spacing:-0.2px;
        }
        .btn-hero:hover { background:var(--gold2); transform:translateY(-3px); box-shadow:0 16px 40px rgba(240,180,41,0.28); }
        .btn-hero svg { transition:transform 0.2s; }
        .btn-hero:hover svg { transform:translateX(4px); }

        .hero-note {
            margin-top:14px; font-size:12px; color:var(--muted);
            animation:fadeUp 0.6s 0.4s ease both;
        }

        /* UNI STRIP */
        .uni-strip {
            margin-top:52px;
            display:flex; align-items:center; gap:0;
            border:1px solid var(--border);
            border-radius:10px; overflow:hidden;
            animation:fadeUp 0.6s 0.45s ease both;
        }
        .uni-item {
            padding:10px 20px; font-size:11.5px;
            color:var(--muted2); font-weight:500;
            border-right:1px solid var(--border);
            display:flex; align-items:center; gap:6px;
            background:rgba(255,255,255,0.02);
        }
        .uni-item:last-child { border-right:none; }
        .uni-dot { width:5px; height:5px; border-radius:50%; background:var(--gold); flex-shrink:0; }

        /* STATS */
        .stats {
            position:relative; z-index:1;
            display:grid; grid-template-columns:repeat(4,1fr);
            border-top:1px solid var(--border);
            border-bottom:1px solid var(--border);
            background:rgba(255,255,255,0.012);
        }
        .stat {
            padding:32px 20px; text-align:center;
            border-right:1px solid var(--border);
        }
        .stat:last-child { border-right:none; }
        .stat-n {
            font-family:'Syne',sans-serif; font-size:38px;
            font-weight:800; color:var(--gold);
            letter-spacing:-2px; line-height:1; margin-bottom:5px;
        }
        .stat-l { font-size:12px; color:var(--muted); }

        /* DIVIDER */
        .section-divider {
            position:relative; z-index:1;
            display:flex; align-items:center; gap:16px;
            padding:0 52px; margin:72px 0 0;
        }
        .divider-line { flex:1; height:1px; background:var(--border); }
        .divider-label {
            font-family:'DM Mono',monospace; font-size:10px;
            letter-spacing:0.14em; color:var(--muted); text-transform:uppercase;
        }

        /* FEATURES */
        .features {
            position:relative; z-index:1;
            padding:40px 52px 80px;
            max-width:1160px; margin:0 auto;
        }
        .section-title {
            font-family:'Syne',sans-serif;
            font-size:clamp(26px,3.5vw,40px);
            font-weight:700; color:var(--text);
            text-align:center; margin-bottom:48px;
            letter-spacing:-1px;
        }
        .features-grid {
            display:grid; grid-template-columns:repeat(3,1fr); gap:14px;
        }
        .feat {
            padding:26px 24px; border-radius:14px;
            border:1px solid var(--border); background:var(--surface);
            transition:all 0.3s; position:relative; overflow:hidden;
        }
        .feat::after {
            content:''; position:absolute;
            top:0; left:0; right:0; height:1.5px;
            background:linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity:0; transition:opacity 0.3s;
        }
        .feat:hover { border-color:rgba(240,180,41,0.18); transform:translateY(-5px); }
        .feat:hover::after { opacity:1; }
        .feat-icon {
            width:42px; height:42px; border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            margin-bottom:16px;
        }
        .fi-g { background:rgba(240,180,41,0.11); color:var(--gold); }
        .fi-t { background:rgba(45,212,191,0.09); color:var(--teal); }
        .fi-b { background:rgba(96,165,250,0.09); color:var(--blue); }
        .fi-c { background:rgba(248,113,113,0.09); color:var(--coral); }
        .fi-p { background:rgba(167,139,250,0.09); color:#A78BFA; }
        .fi-gr{ background:rgba(74,222,128,0.09); color:#4ADE80; }
        .feat-title {
            font-family:'Syne',sans-serif; font-size:14.5px;
            font-weight:600; color:var(--text); margin-bottom:7px;
        }
        .feat-desc { font-size:12.5px; color:var(--muted); line-height:1.65; }

        /* ROLES */
        .roles {
            position:relative; z-index:1;
            padding:0 52px 80px;
            max-width:1160px; margin:0 auto;
        }
        .roles-grid {
            display:grid; grid-template-columns:repeat(4,1fr); gap:12px;
        }
        .role {
            padding:28px 20px; border-radius:13px;
            border:1px solid var(--border); background:var(--surface);
            text-align:center; transition:all 0.3s;
        }
        .role:hover { transform:translateY(-4px); border-color:var(--border2); }
        .role-av {
            width:54px; height:54px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 14px;
        }
        .role-n {
            font-family:'Syne',sans-serif; font-size:13px;
            font-weight:600; color:var(--text); margin-bottom:7px;
        }
        .role-d { font-size:11.5px; color:var(--muted); line-height:1.55; }

        /* FLOW */
        .flow {
            position:relative; z-index:1;
            padding:0 52px 80px;
            max-width:1160px; margin:0 auto;
        }
        .flow-steps {
            display:flex; align-items:flex-start;
            gap:0; overflow-x:auto; padding-bottom:8px;
        }
        .flow-step {
            flex:1; min-width:130px;
            display:flex; flex-direction:column; align-items:center;
            text-align:center; position:relative;
        }
        .flow-step:not(:last-child)::after {
            content:'→'; position:absolute;
            right:-14px; top:18px;
            color:var(--muted); font-size:16px;
        }
        .flow-circle {
            width:38px; height:38px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-family:'Syne',sans-serif; font-size:13px; font-weight:700;
            margin-bottom:10px; flex-shrink:0;
        }
        .flow-label { font-size:11.5px; color:var(--muted2); line-height:1.4; }
        .flow-sub { font-size:10.5px; color:var(--muted); margin-top:3px; }

        /* CTA */
        .cta-wrap {
            position:relative; z-index:1;
            padding:0 52px 80px;
            max-width:1160px; margin:0 auto;
        }
        .cta {
            padding:64px 52px; border-radius:20px;
            border:1px solid rgba(240,180,41,0.14);
            background:linear-gradient(135deg, rgba(240,180,41,0.055) 0%, rgba(45,212,191,0.035) 100%);
            text-align:center; position:relative; overflow:hidden;
        }
        .cta::before {
            content:''; position:absolute;
            top:-80px; left:50%; transform:translateX(-50%);
            width:360px; height:360px;
            background:rgba(240,180,41,0.07);
            border-radius:50%; filter:blur(70px); pointer-events:none;
        }
        .cta-title {
            font-family:'Syne',sans-serif;
            font-size:clamp(28px,4vw,48px);
            font-weight:800; color:var(--text);
            margin-bottom:12px; letter-spacing:-1.5px;
            position:relative;
        }
        .cta-sub {
            font-size:15px; color:var(--muted2);
            margin-bottom:36px; position:relative;
            font-weight:300;
        }
        .cta-btn {
            display:inline-flex; align-items:center; gap:9px;
            padding:14px 36px; border-radius:10px;
            background:var(--gold); color:#08090E;
            font-size:15px; font-weight:700;
            text-decoration:none; font-family:'DM Sans',sans-serif;
            transition:all 0.25s; position:relative;
        }
        .cta-btn:hover { background:var(--gold2); transform:translateY(-2px); box-shadow:0 14px 36px rgba(240,180,41,0.26); }

        /* FOOTER */
        footer {
            position:relative; z-index:1;
            border-top:1px solid var(--border);
            padding:26px 52px;
            display:flex; align-items:center;
            justify-content:space-between; flex-wrap:wrap; gap:12px;
        }
        .footer-l { display:flex; align-items:center; gap:10px; font-size:12.5px; color:var(--muted); }
        .footer-logo { width:22px; height:22px; background:var(--gold); border-radius:5px; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-weight:800; font-size:10px; color:#08090E; }
        .footer-r { display:flex; gap:24px; font-size:11.5px; color:var(--muted); }

        /* ANIMATIONS */
        @keyframes fadeUp   { from{opacity:0;transform:translateY(22px)} to{opacity:1;transform:translateY(0)} }
        @keyframes fadeDown { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }

        /* RESPONSIVE */
        @media(max-width:960px) {
            nav { padding:16px 24px; }
            .features, .roles, .flow, .cta-wrap { padding-left:24px; padding-right:24px; }
            .features-grid { grid-template-columns:1fr 1fr; }
            .roles-grid { grid-template-columns:1fr 1fr; gap:10px; }
            .stats { grid-template-columns:1fr 1fr; }
            .stat:nth-child(2) { border-right:none; }
            .stat:nth-child(3) { border-top:1px solid var(--border); }
            footer { padding:20px 24px; }
        }
        @media(max-width:580px) {
            .features-grid { grid-template-columns:1fr; }
            .flow-steps { gap:8px; }
            .flow-step:not(:last-child)::after { display:none; }
            .uni-strip { flex-direction:column; }
            .uni-item { border-right:none; border-bottom:1px solid var(--border); }
            .uni-item:last-child { border-bottom:none; }
        }
    </style>
</head>
<body>
<div class="grid-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- NAV -->
<nav>
    <div style="display:flex;align-items:center;gap:14px;">
        <a href="/" class="nav-brand">
            <div class="nav-logo">O</div>
            <div class="nav-name">OJT<span>Connect</span></div>
        </a>
        <span class="nav-tag">BukSU</span>
    </div>
    <a href="/login" class="btn-login">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
        Log in
    </a>
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero-eyebrow">
        <div class="hero-eyebrow-dot"></div>
        Bukidnon State University · OJT Management System
    </div>

    <h1 class="hero-title">
        <span class="dim">The smarter way</span><br>
        to manage <span class="g">OJT</span>
    </h1>

    <p class="hero-desc">
        From application to completion — OJTConnect handles everything digitally so you can focus on what matters.
    </p>

    <div class="hero-cta">
        <a href="/login" class="btn-hero">
            Access the system
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
        <div class="hero-note">Contact your coordinator if you don't have an account yet.</div>
    </div>

    <div class="uni-strip">
        <div class="uni-item"><div class="uni-dot"></div>College of Technology</div>
        <div class="uni-item"><div class="uni-dot"></div>College of Business</div>
        <div class="uni-item"><div class="uni-dot"></div>College of Education</div>
    </div>
</section>

<!-- STATS -->
<div class="stats">
    <div class="stat"><div class="stat-n">4</div><div class="stat-l">User roles</div></div>
    <div class="stat"><div class="stat-n">3</div><div class="stat-l">Colleges</div></div>
    <div class="stat"><div class="stat-n">100%</div><div class="stat-l">Paperless</div></div>
    <div class="stat"><div class="stat-n">∞</div><div class="stat-l">Students supported</div></div>
</div>

<!-- FEATURES -->
<div class="section-divider"><div class="divider-line"></div><div class="divider-label">What we offer</div><div class="divider-line"></div></div>
<section class="features">
    <div class="section-title">Everything OJT needs, in one place</div>
    <div class="features-grid">
        <div class="feat">
            <div class="feat-icon fi-g"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg></div>
            <div class="feat-title">OJT Application</div>
            <div class="feat-desc">Students apply to partner companies, upload documents, and track status in real time.</div>
        </div>
        <div class="feat">
            <div class="feat-icon fi-t"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div>
            <div class="feat-title">Hours Monitoring</div>
            <div class="feat-desc">Log daily time in/out, track approved hours, and visualize progress against required hours.</div>
        </div>
        <div class="feat">
            <div class="feat-icon fi-b"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
            <div class="feat-title">Weekly Reports</div>
            <div class="feat-desc">Submit weekly accomplishment reports with file attachments for coordinator review and feedback.</div>
        </div>
        <div class="feat">
            <div class="feat-icon fi-c"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg></div>
            <div class="feat-title">Student Evaluation</div>
            <div class="feat-desc">Supervisors submit attendance and performance ratings with an overall grade and recommendation.</div>
        </div>
        <div class="feat">
            <div class="feat-icon fi-p"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/></svg></div>
            <div class="feat-title">Export Reports</div>
            <div class="feat-desc">Generate PDF and Excel reports for student records, accreditation, and administrative use.</div>
        </div>
        <div class="feat">
            <div class="feat-icon fi-gr"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg></div>
            <div class="feat-title">Role-based Access</div>
            <div class="feat-desc">Dedicated dashboards for admins, coordinators, supervisors, and students with proper permissions.</div>
        </div>
    </div>
</section>

<!-- ROLES -->
<div class="section-divider"><div class="divider-line"></div><div class="divider-label">User roles</div><div class="divider-line"></div></div>
<section class="roles" style="padding-top:40px;">
    <div class="section-title">Built for every stakeholder</div>
    <div class="roles-grid">
        <div class="role">
            <div class="role-av" style="background:rgba(240,180,41,0.1);">
                <svg width="24" height="24" fill="none" stroke="#F0B429" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
            </div>
            <div class="role-n">Admin</div>
            <div class="role-d">Creates users and companies, monitors the full system, and exports reports</div>
        </div>
        <div class="role">
            <div class="role-av" style="background:rgba(45,212,191,0.09);">
                <svg width="24" height="24" fill="none" stroke="#2DD4BF" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/></svg>
            </div>
            <div class="role-n">OJT Coordinator</div>
            <div class="role-d">Approves applications, monitors hours and reports, and marks OJT completion</div>
        </div>
        <div class="role">
            <div class="role-av" style="background:rgba(96,165,250,0.09);">
                <svg width="24" height="24" fill="none" stroke="#60A5FA" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
            </div>
            <div class="role-n">Company Supervisor</div>
            <div class="role-d">Validates attendance, monitors intern performance, and submits evaluations</div>
        </div>
        <div class="role">
            <div class="role-av" style="background:rgba(248,113,113,0.09);">
                <svg width="24" height="24" fill="none" stroke="#F87171" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div class="role-n">Student Intern</div>
            <div class="role-d">Applies for OJT, logs daily hours, submits weekly reports, and views evaluation</div>
        </div>
    </div>
</section>

<!-- FLOW -->
<div class="section-divider"><div class="divider-line"></div><div class="divider-label">How it works</div><div class="divider-line"></div></div>
<section class="flow" style="padding-top:40px;padding-bottom:80px;">
    <div class="section-title">End-to-end OJT process</div>
    <div class="flow-steps">
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(240,180,41,0.12);color:var(--gold);">1</div>
            <div class="flow-label">Admin creates student account</div>
            <div class="flow-sub" style="color:var(--gold);">Admin</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(248,113,113,0.1);color:var(--coral);">2</div>
            <div class="flow-label">Student applies for OJT</div>
            <div class="flow-sub" style="color:var(--coral);">Student</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(45,212,191,0.1);color:var(--teal);">3</div>
            <div class="flow-label">Coordinator approves application</div>
            <div class="flow-sub" style="color:var(--teal);">Coordinator</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(248,113,113,0.1);color:var(--coral);">4</div>
            <div class="flow-label">Student logs hours + reports</div>
            <div class="flow-sub" style="color:var(--coral);">Student</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(45,212,191,0.1);color:var(--teal);">5</div>
            <div class="flow-label">Coordinator monitors progress</div>
            <div class="flow-sub" style="color:var(--teal);">Coordinator</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(96,165,250,0.1);color:var(--blue);">6</div>
            <div class="flow-label">Supervisor evaluates student</div>
            <div class="flow-sub" style="color:var(--blue);">Supervisor</div>
        </div>
        <div class="flow-step">
            <div class="flow-circle" style="background:rgba(45,212,191,0.1);color:var(--teal);">7</div>
            <div class="flow-label">Coordinator marks complete</div>
            <div class="flow-sub" style="color:var(--teal);">Coordinator</div>
        </div>
    </div>
</section>

<!-- CTA -->
<div class="cta-wrap">
    <div class="cta">
        <h2 class="cta-title">Ready to get started?</h2>
        <p class="cta-sub">Log in to access your dashboard and manage your OJT process.</p>
        <a href="{{ route('login') }}" class="cta-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            Log in to OJTConnect
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-l">
        <div class="footer-logo">O</div>
        <span><strong style="color:var(--gold);font-family:'Syne',sans-serif;">OJTConnect</strong> · OJT Management System · Bukidnon State University</span>
    </div>
    <div class="footer-r">
        <span>College of Technology</span>
        <span>·</span>
        <span>College of Business</span>
        <span>·</span>
        <span>College of Education</span>
    </div>
</footer>

</body>
</html>