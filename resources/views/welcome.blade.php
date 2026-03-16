<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OJTConnect — OJT Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,400;0,9..144,600;1,9..144,300;1,9..144,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #0B1623;
            --navy2:  #111E2E;
            --navy3:  #192840;
            --gold:   #E9A83A;
            --gold2:  #F2BF5E;
            --cream:  #F7F3EB;
            --cream2: #EDE8DE;
            --muted:  #7A8FA6;
            --text:   #C8D4E0;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ── GRID BACKGROUND ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 48px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            background: rgba(11,22,35,0.85);
            backdrop-filter: blur(12px);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-mark {
            width: 36px; height: 36px;
            background: var(--gold);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Fraunces', serif;
            font-size: 18px;
            color: var(--navy);
            font-weight: 600;
        }

        .nav-name {
            font-family: 'Fraunces', serif;
            font-size: 20px;
            font-weight: 400;
            color: #fff;
        }

        .nav-name span { color: var(--gold); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-link {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 400;
            text-decoration: none;
            transition: all 0.2s;
            color: var(--text);
            border: 1px solid transparent;
        }

        .nav-link:hover {
            border-color: rgba(255,255,255,0.12);
            color: #fff;
        }

        .nav-link.primary {
            background: var(--gold);
            color: var(--navy);
            font-weight: 500;
            border-color: var(--gold);
        }

        .nav-link.primary:hover {
            background: var(--gold2);
            border-color: var(--gold2);
            color: var(--navy);
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
            padding: 140px 24px 80px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid rgba(233,168,58,0.3);
            background: rgba(233,168,58,0.08);
            font-size: 12px;
            font-weight: 500;
            color: var(--gold);
            letter-spacing: 0.5px;
            margin-bottom: 32px;
            animation: fadeUp 0.6s ease both;
        }

        .hero-badge::before {
            content: '';
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .hero-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(42px, 6vw, 84px);
            font-weight: 300;
            line-height: 1.08;
            color: #fff;
            margin-bottom: 12px;
            animation: fadeUp 0.6s 0.1s ease both;
            max-width: 900px;
            letter-spacing: -1px;
        }

        .hero-title em {
            font-style: italic;
            color: var(--gold);
        }

        .hero-title .line2 {
            display: block;
            color: rgba(255,255,255,0.4);
            font-weight: 300;
        }

        .hero-sub {
            font-size: 17px;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.7;
            max-width: 520px;
            margin: 24px auto 48px;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-gold {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 32px;
            background: var(--gold);
            color: var(--navy);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-gold:hover {
            background: var(--gold2);
            transform: translateY(-1px);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            color: var(--text);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 400;
            text-decoration: none;
            transition: all 0.2s;
            border: 1px solid rgba(255,255,255,0.12);
        }

        .btn-ghost:hover {
            border-color: rgba(255,255,255,0.25);
            color: #fff;
        }

        /* ── FLOATING CARDS ── */
        .hero-cards {
            display: flex;
            gap: 16px;
            margin-top: 72px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 0.6s 0.4s ease both;
        }

        .float-card {
            background: var(--navy2);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 180px;
            transition: border-color 0.2s, transform 0.2s;
        }

        .float-card:hover {
            border-color: rgba(233,168,58,0.25);
            transform: translateY(-3px);
        }

        .float-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .float-icon.gold  { background: rgba(233,168,58,0.15); color: var(--gold); }
        .float-icon.teal  { background: rgba(45,212,191,0.12); color: #2DD4BF; }
        .float-icon.coral { background: rgba(251,113,133,0.12); color: #FB7185; }
        .float-icon.blue  { background: rgba(96,165,250,0.12); color: #60A5FA; }

        .float-num {
            font-family: 'Fraunces', serif;
            font-size: 24px;
            font-weight: 300;
            color: #fff;
            line-height: 1;
        }

        .float-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── SECTION ── */
        section {
            position: relative;
            z-index: 1;
            padding: 100px 48px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-tag {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(32px, 4vw, 52px);
            font-weight: 300;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -0.5px;
        }

        .section-title em { font-style: italic; color: var(--gold); }

        .section-sub {
            font-size: 16px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 480px;
        }

        /* ── HOW IT WORKS ── */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 56px;
        }

        .step-card {
            background: var(--navy2);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 28px;
            transition: border-color 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }

        .step-card::before {
            content: attr(data-step);
            position: absolute;
            top: -10px; right: 20px;
            font-family: 'Fraunces', serif;
            font-size: 80px;
            font-weight: 300;
            color: rgba(255,255,255,0.03);
            line-height: 1;
            pointer-events: none;
        }

        .step-card:hover {
            border-color: rgba(233,168,58,0.2);
            transform: translateY(-4px);
        }

        .step-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 18px;
        }

        .step-title {
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            margin-bottom: 8px;
        }

        .step-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── USER ROLES ── */
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 56px;
        }

        .role-card {
            background: var(--navy2);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: border-color 0.2s, transform 0.2s;
        }

        .role-card:hover {
            border-color: rgba(255,255,255,0.14);
            transform: translateY(-3px);
        }

        .role-avatar {
            width: 48px; height: 48px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-family: 'Fraunces', serif;
            font-size: 20px;
        }

        .role-body { flex: 1; }
        .role-name { font-size: 16px; font-weight: 500; color: #fff; margin-bottom: 4px; }
        .role-desc { font-size: 13px; color: var(--muted); line-height: 1.6; margin-bottom: 14px; }

        .role-perms {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .perm-tag {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 400;
            border: 1px solid;
        }

        /* ── PLANS ── */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 56px;
            align-items: start;
        }

        .plan-card {
            background: var(--navy2);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 18px;
            padding: 32px;
            transition: border-color 0.2s, transform 0.2s;
            position: relative;
        }

        .plan-card.featured {
            border-color: rgba(233,168,58,0.4);
            background: rgba(233,168,58,0.05);
        }

        .plan-card:not(.featured):hover {
            border-color: rgba(255,255,255,0.14);
            transform: translateY(-4px);
        }

        .plan-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 500;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            background: var(--gold);
            color: var(--navy);
            margin-bottom: 20px;
        }

        .plan-name {
            font-family: 'Fraunces', serif;
            font-size: 22px;
            font-weight: 300;
            color: #fff;
            margin-bottom: 8px;
        }

        .plan-price {
            font-family: 'Fraunces', serif;
            font-size: 40px;
            font-weight: 300;
            color: #fff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .plan-price span {
            font-size: 16px;
            font-weight: 400;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
        }

        .plan-period { font-size: 13px; color: var(--muted); margin-bottom: 28px; }

        .plan-divider {
            height: 1px;
            background: rgba(255,255,255,0.07);
            margin-bottom: 24px;
        }

        .plan-feature {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--text);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .plan-feature::before {
            content: '';
            width: 16px; height: 16px;
            border-radius: 50%;
            background: rgba(233,168,58,0.2);
            border: 1px solid rgba(233,168,58,0.5);
            flex-shrink: 0;
            margin-top: 1px;
            position: relative;
        }

        .plan-feature::before {
            background-image: url("data:image/svg+xml,%3Csvg width='10' height='8' viewBox='0 0 10 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 4L3.5 6.5L9 1' stroke='%23E9A83A' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
        }

        /* ── FOOTER ── */
        footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 40px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 100%;
        }

        .footer-brand {
            font-family: 'Fraunces', serif;
            font-size: 18px;
            color: rgba(255,255,255,0.4);
        }

        .footer-brand span { color: var(--gold); }

        .footer-note {
            font-size: 13px;
            color: var(--muted);
        }

        /* ── DIVIDER LINE ── */
        .section-divider {
            position: relative;
            z-index: 1;
            height: 1px;
            background: rgba(255,255,255,0.06);
            max-width: 1100px;
            margin: 0 auto;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            nav { padding: 16px 20px; }
            section { padding: 60px 20px; }
            .steps-grid { grid-template-columns: 1fr; }
            .roles-grid  { grid-template-columns: 1fr; }
            .plans-grid  { grid-template-columns: 1fr; }
            .hero-cards  { flex-direction: column; align-items: center; }
            footer { flex-direction: column; gap: 12px; text-align: center; }
        }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a href="#" class="nav-brand">
        <div class="nav-mark">O</div>
        <div class="nav-name">OJT<span>Connect</span></div>
    </a>
    <div class="nav-links">
        @auth
            <a href="{{ url('/dashboard') }}" class="nav-link">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="nav-link">Sign in</a>
            <a href="#plans" class="nav-link primary">Get started</a>
        @endauth
    </div>
</nav>

<!-- HERO -->
<div class="hero">
    <div class="hero-badge">Now serving Bukidnon State University</div>

    <h1 class="hero-title">
        Internship management,
        <em>finally</em> paperless.
        <span class="line2">Built for BukSU colleges.</span>
    </h1>

    <p class="hero-sub">
        OJTConnect streamlines the entire On-the-Job Training process — from student applications to supervisor evaluations — in one unified platform.
    </p>

    <div class="hero-actions">
   @auth
        @php
            $dashboardRoute = match(auth()->user()->role) {
                'admin'              => route('admin.dashboard'),
                'ojt_coordinator'    => route('coordinator.dashboard'),
                'company_supervisor' => route('supervisor.dashboard'),
                'student_intern'     => route('student.dashboard'),
                default              => url('/'),
            };
        @endphp
        <a href="{{ $dashboardRoute }}" class="nav-link">Dashboard</a>
    @else
        <a href="{{ route('login') }}" class="nav-link">Sign in</a>
        <a href="#plans" class="nav-link primary">Get started</a>
    @endauth
    </div>

    <div class="hero-cards">
        <div class="float-card">
            <div class="float-icon gold">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div>
                <div class="float-num">4</div>
                <div class="float-label">User roles</div>
            </div>
        </div>
        <div class="float-card">
            <div class="float-icon teal">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
            </div>
            <div>
                <div class="float-num">3</div>
                <div class="float-label">Partner colleges</div>
            </div>
        </div>
        <div class="float-card">
            <div class="float-icon coral">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
            <div>
                <div class="float-num">100%</div>
                <div class="float-label">Paperless</div>
            </div>
        </div>
        <div class="float-card">
            <div class="float-icon blue">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <div>
                <div class="float-num">Real-time</div>
                <div class="float-label">Hours tracking</div>
            </div>
        </div>
    </div>
</div>

<div class="section-divider"></div>

<!-- HOW IT WORKS -->
<section id="how-it-works">
    <div class="section-tag">How it works</div>
    <h2 class="section-title">From application to <em>completion</em></h2>
    <p class="section-sub">A streamlined process that connects students, coordinators, and supervisors in one place.</p>

    <div class="steps-grid">
        <div class="step-card" data-step="01">
            <div class="step-icon" style="background:rgba(233,168,58,0.12);color:var(--gold);">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <div class="step-title">Student registers & applies</div>
            <div class="step-desc">Students create an account, fill out their OJT application form, and choose a partner company to intern at.</div>
        </div>
        <div class="step-card" data-step="02">
            <div class="step-icon" style="background:rgba(45,212,191,0.1);color:#2DD4BF;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <div class="step-title">Coordinator approves</div>
            <div class="step-desc">OJT coordinators review applications, approve placements, and monitor student progress and logged hours.</div>
        </div>
        <div class="step-card" data-step="03">
            <div class="step-icon" style="background:rgba(96,165,250,0.1);color:#60A5FA;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <div class="step-title">Student logs hours daily</div>
            <div class="step-desc">Students record their daily time in and out, submit weekly reports, and track their total OJT hours in real time.</div>
        </div>
        <div class="step-card" data-step="04">
            <div class="step-icon" style="background:rgba(251,113,133,0.1);color:#FB7185;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div class="step-title">Reports submitted online</div>
            <div class="step-desc">Weekly and final OJT reports are submitted digitally. Coordinators can review and approve with a single click.</div>
        </div>
        <div class="step-card" data-step="05">
            <div class="step-icon" style="background:rgba(167,139,250,0.1);color:#A78BFA;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            </div>
            <div class="step-title">Supervisor evaluates</div>
            <div class="step-desc">Company supervisors rate student performance using a structured evaluation form covering skills, attitude, and output.</div>
        </div>
        <div class="step-card" data-step="06">
            <div class="step-icon" style="background:rgba(52,211,153,0.1);color:#34D399;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="step-title">OJT completed</div>
            <div class="step-desc">Once all hours are logged, reports submitted, and evaluations done, the OJT is marked complete and records are archived.</div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- ROLES -->
<section id="roles">
    <div class="section-tag">User types</div>
    <h2 class="section-title">Built for <em>everyone</em> in the process</h2>
    <p class="section-sub">Four distinct roles, each with a tailored dashboard and a focused set of tools.</p>

    <div class="roles-grid">
        <div class="role-card">
            <div class="role-avatar" style="background:rgba(233,168,58,0.12);color:var(--gold);">A</div>
            <div class="role-body">
                <div class="role-name">Admin</div>
                <div class="role-desc">Manages the entire system — user accounts, company partners, and system-wide settings for the college.</div>
                <div class="role-perms">
                    <span class="perm-tag" style="border-color:rgba(233,168,58,0.3);color:var(--gold);background:rgba(233,168,58,0.08);">Manage users</span>
                    <span class="perm-tag" style="border-color:rgba(233,168,58,0.3);color:var(--gold);background:rgba(233,168,58,0.08);">System settings</span>
                    <span class="perm-tag" style="border-color:rgba(233,168,58,0.3);color:var(--gold);background:rgba(233,168,58,0.08);">Companies</span>
                </div>
            </div>
        </div>

        <div class="role-card">
            <div class="role-avatar" style="background:rgba(45,212,191,0.1);color:#2DD4BF;">C</div>
            <div class="role-body">
                <div class="role-name">OJT Coordinator</div>
                <div class="role-desc">Monitors students, approves applications, reviews submitted reports, and tracks internship progress.</div>
                <div class="role-perms">
                    <span class="perm-tag" style="border-color:rgba(45,212,191,0.3);color:#2DD4BF;background:rgba(45,212,191,0.08);">Approve OJT</span>
                    <span class="perm-tag" style="border-color:rgba(45,212,191,0.3);color:#2DD4BF;background:rgba(45,212,191,0.08);">Monitor hours</span>
                    <span class="perm-tag" style="border-color:rgba(45,212,191,0.3);color:#2DD4BF;background:rgba(45,212,191,0.08);">Review reports</span>
                </div>
            </div>
        </div>

        <div class="role-card">
            <div class="role-avatar" style="background:rgba(96,165,250,0.1);color:#60A5FA;">S</div>
            <div class="role-body">
                <div class="role-name">Company Supervisor</div>
                <div class="role-desc">Monitors student interns on-site, validates attendance, and submits performance evaluations at completion.</div>
                <div class="role-perms">
                    <span class="perm-tag" style="border-color:rgba(96,165,250,0.3);color:#60A5FA;background:rgba(96,165,250,0.08);">Monitor attendance</span>
                    <span class="perm-tag" style="border-color:rgba(96,165,250,0.3);color:#60A5FA;background:rgba(96,165,250,0.08);">Evaluate interns</span>
                </div>
            </div>
        </div>

        <div class="role-card">
            <div class="role-avatar" style="background:rgba(251,113,133,0.1);color:#FB7185;">I</div>
            <div class="role-body">
                <div class="role-name">Student Intern</div>
                <div class="role-desc">Applies for OJT, logs daily hours, submits weekly and final reports, and views their own evaluation results.</div>
                <div class="role-perms">
                    <span class="perm-tag" style="border-color:rgba(251,113,133,0.3);color:#FB7185;background:rgba(251,113,133,0.08);">Apply for OJT</span>
                    <span class="perm-tag" style="border-color:rgba(251,113,133,0.3);color:#FB7185;background:rgba(251,113,133,0.08);">Log hours</span>
                    <span class="perm-tag" style="border-color:rgba(251,113,133,0.3);color:#FB7185;background:rgba(251,113,133,0.08);">Submit reports</span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="section-divider"></div>

<!-- PLANS -->
<section id="plans">
    <div class="section-tag">Pricing</div>
    <h2 class="section-title">One plan for every <em>college</em></h2>
    <p class="section-sub">Transparent annual pricing per college. Upgrade anytime as your needs grow.</p>

    <div class="plans-grid">
        <!-- Basic -->
        <div class="plan-card">
            <div class="plan-name">Basic</div>
            <div class="plan-price">₱10,000 <span>/ year</span></div>
            <div class="plan-period">Per college · billed annually</div>
            <div class="plan-divider"></div>
            <div class="plan-feature">Student registration & accounts</div>
            <div class="plan-feature">OJT application submission</div>
            <div class="plan-feature">Hours monitoring dashboard</div>
            <div class="plan-feature">Basic admin reports</div>
        </div>

        <!-- Standard -->
        <div class="plan-card featured">
            <div class="plan-badge">Most popular</div>
            <div class="plan-name">Standard</div>
            <div class="plan-price">₱20,000 <span>/ year</span></div>
            <div class="plan-period">Per college · billed annually</div>
            <div class="plan-divider"></div>
            <div class="plan-feature">Everything in Basic</div>
            <div class="plan-feature">Online report submission</div>
            <div class="plan-feature">Student evaluation system</div>
            <div class="plan-feature">Internship progress monitoring</div>
            <div class="plan-feature">Email notifications</div>
        </div>

        <!-- Premium -->
        <div class="plan-card">
            <div class="plan-name">Premium</div>
            <div class="plan-price">₱30,000 <span>/ year</span></div>
            <div class="plan-period">Per college · billed annually</div>
            <div class="plan-divider"></div>
            <div class="plan-feature">Everything in Standard</div>
            <div class="plan-feature">Unlimited student records</div>
            <div class="plan-feature">Advanced analytics & charts</div>
            <div class="plan-feature">Automated PDF report generation</div>
            <div class="plan-feature">Priority support</div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-brand">OJT<span>Connect</span></div>
    <div class="footer-note">Bukidnon State University · OJT Management System</div>
</footer>

</body>
</html>