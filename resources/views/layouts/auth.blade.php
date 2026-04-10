<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'OJTConnect')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --crimson:  #8C0E03;
            --crimson2: #a81004;
            --void:     #0D0D0D;
            --night:    #0E1126;
            --steel:    #333740;
            --ash:      #ABABAB;
        }

        html, body {
            font-family: 'Barlow', sans-serif;
            background: var(--void);
            color: var(--ash);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Grain ── */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.055'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 999; opacity: 0.5;
        }

        /* ── Scan line ── */
        .scanline {
            position: fixed; left: 0; right: 0; height: 200px;
            background: linear-gradient(to bottom, transparent, rgba(171,171,171,0.012), transparent);
            pointer-events: none; z-index: 1;
            animation: scandown 12s linear infinite;
        }

        /* ── Grid ── */
        .grid-bg {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(171,171,171,0.028) 1px, transparent 1px),
                linear-gradient(90deg, rgba(171,171,171,0.028) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .grid-bg::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 50% at 50% 0%, rgba(140,14,3,0.10) 0%, transparent 65%);
        }

        /* ── Orbs ── */
        .orb { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .orb-a { width: 650px; height: 650px; background: rgba(14,17,38,0.9); top: -260px; right: -180px; animation: drift1 20s ease-in-out infinite alternate; }
        .orb-b { width: 450px; height: 450px; background: rgba(13,13,13,0.95); bottom: -150px; left: -140px; animation: drift2 25s ease-in-out infinite alternate; }
        .orb-c { width: 350px; height: 350px; background: rgba(140,14,3,0.07); top: 50%; left: 50%; transform: translate(-50%,-50%); animation: drift1 30s ease-in-out infinite alternate; }

        /* ── Keyframes ── */
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeUp   { from { opacity: 0; transform: translateY(16px);  } to { opacity: 1; transform: translateY(0); } }
        @keyframes drift1   { 0% { transform: translate(0,0); } 100% { transform: translate(-40px,55px); } }
        @keyframes drift2   { 0% { transform: translate(0,0); } 100% { transform: translate(50px,-35px); } }
        @keyframes flicker  { 0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:0.4} 94%{opacity:1} 96%{opacity:0.6} 97%{opacity:1} }
        @keyframes scandown { 0%{transform:translateY(-100%);opacity:0} 5%{opacity:1} 95%{opacity:1} 100%{transform:translateY(100vh);opacity:0} }
        @keyframes cardIn   { from { opacity: 0; transform: translateY(24px) scale(0.985); } to { opacity: 1; transform: translateY(0) scale(1); } }

        /* ── NAV ── */
        nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            display: flex; align-items: center; justify-content: space-between;
            padding: .875rem 3.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            background: rgba(13,13,13,0.8);
            backdrop-filter: blur(20px);
            animation: fadeDown 0.45s ease both;
        }

        .nav-brand { display: flex; align-items: center; gap: .75rem; text-decoration: none; }
        .nav-logo {
            width: 32px; height: 32px;
            border: 1px solid rgba(140,14,3,0.6);
            display: grid; place-items: center; position: relative;
        }
        .nav-logo-letter {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 14px;
            color: var(--crimson); line-height: 1;
        }
        .nav-logo::after {
            content: ''; position: absolute;
            top: -1px; right: -1px;
            width: 6px; height: 6px;
            background: var(--crimson);
        }
        .nav-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 17px;
            letter-spacing: 0.12em; text-transform: uppercase; color: #fff;
        }
        .nav-title-muted { color: rgba(171,171,171,0.45); }

        .nav-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 2px 8px;
            border: 1px solid rgba(171,171,171,0.1);
            background: rgba(171,171,171,0.04);
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(171,171,171,0.35);
        }
        .nav-badge-dot { width: 4px; height: 4px; background: var(--crimson); }

        .btn-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px;
            border: 1px solid rgba(171,171,171,0.15);
            color: rgba(171,171,171,0.6);
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 13px;
            letter-spacing: 0.1em; text-transform: uppercase;
            text-decoration: none; background: transparent;
            transition: border-color .2s, color .2s, background .2s, transform .15s;
        }
        .btn-ghost:hover {
            border-color: rgba(171,171,171,0.3); color: rgba(171,171,171,0.9);
            background: rgba(171,171,171,0.05); transform: translateY(-2px);
        }
        .btn-ghost-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border: 1px solid rgba(171,171,171,0.15);
            color: rgba(171,171,171,0.6);
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            background: transparent;
            transition: border-color .2s, color .2s, background .2s, transform .15s;
        }

        .btn-ghost-nav:hover {
            border-color: rgba(171,171,171,0.3);
            color: rgba(171,171,171,0.9);
            background: rgba(171,171,171,0.05);
            transform: translateY(-2px);
        }

        /* ── PAGE SHELL ── */
        .page {
            position: relative; z-index: 10;
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 520px 1fr;
            grid-template-rows: 1fr;
            align-items: center;
            justify-items: center;
            padding: 5rem 1.5rem 3rem;
        }

        /* ── CARD ── */
        .auth-card {
            grid-column: 2;
            width: 100%;
            border: 1px solid rgba(171,171,171,0.08);
            background: rgba(14,17,38,0.55);
            backdrop-filter: blur(24px);
            padding: 3rem 3rem 2.5rem;
            position: relative;
            animation: cardIn 0.65s cubic-bezier(.22,.61,.36,1) 0.1s both;
        }

        /* Top crimson bar */
        .auth-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
            background: var(--crimson);
        }

        /* Corner accent */
        .auth-card::after {
            content: ''; position: absolute;
            bottom: 0; right: 0;
            width: 60px; height: 60px;
            border-bottom: 1px solid rgba(140,14,3,0.2);
            border-right:  1px solid rgba(140,14,3,0.2);
        }

        /* Card eyebrow */
        .card-eyebrow {
            display: flex; align-items: center; gap: 8px;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
            color: rgba(140,14,3,0.8);
            margin-bottom: 1.5rem;
        }
        .card-eyebrow-dot {
            width: 4px; height: 4px; background: var(--crimson);
            animation: flicker 8s ease-in-out infinite;
        }

        /* Decorative number */
        .card-deco {
            position: absolute; top: 1.5rem; right: 2rem;
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 5rem; line-height: 1;
            color: rgba(171,171,171,0.03);
            user-select: none; pointer-events: none;
        }

        .auth-card-title {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: clamp(1.7rem, 2.5vw, 2.1rem);
            line-height: 1.1; letter-spacing: -0.02em;
            color: #fff; margin-bottom: .6rem;
        }

        .auth-card-sub {
            font-size: 14px; font-weight: 300;
            color: rgba(171,171,171,0.4); line-height: 1.7;
            margin-bottom: 2rem;
        }

        /* ── Alerts ── */
        .alert {
            display: flex; align-items: flex-start; gap: 8px;
            padding: .85rem 1.1rem;
            font-size: 13px; font-weight: 300;
            margin-bottom: 1.5rem;
            border-left-width: 3px; border-left-style: solid;
        }
        .alert-success {
            border-color: #4caf50;
            background: rgba(76,175,80,0.06);
            color: rgba(171,171,171,0.75);
        }
        .alert-success svg { color: #4caf50; flex-shrink: 0; margin-top: 1px; }
        .alert-error {
            border-color: var(--crimson);
            background: rgba(140,14,3,0.07);
            color: rgba(171,171,171,0.7);
        }

        /* ── Form groups ── */
        .form-group {
            display: flex; flex-direction: column; gap: .45rem;
            margin-bottom: 1.25rem;
        }

        .form-group label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.2em; text-transform: uppercase;
            color: rgba(171,171,171,0.4);
        }

        /* Input wrapper */
        .input-wrap { position: relative; }

        .input-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            width: 15px; height: 15px;
            stroke: rgba(171,171,171,0.2);
            pointer-events: none;
            transition: stroke .2s;
        }

        .input-wrap:focus-within .input-icon { stroke: rgba(140,14,3,0.6); }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%; height: 46px;
            padding: 0 1rem 0 2.5rem;
            border: 1px solid rgba(171,171,171,0.1);
            background: rgba(13,13,13,0.6);
            border-radius: 0;
            font-family: 'Barlow', sans-serif;
            font-size: 14px; font-weight: 300;
            color: rgba(171,171,171,0.85);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            appearance: none;
        }

        input::placeholder { color: rgba(171,171,171,0.18); }

        input:focus {
            border-color: rgba(140,14,3,0.7);
            background: rgba(14,17,38,0.9);
            box-shadow: 0 0 0 3px rgba(140,14,3,0.1);
        }

        input.is-invalid {
            border-color: rgba(140,14,3,0.55);
            background: rgba(140,14,3,0.05);
        }

        .invalid-feedback {
            font-family: 'DM Mono', monospace;
            font-size: 11px; color: var(--crimson);
        }

        /* Password toggle */
        .password-wrap input[type="password"],
        .password-wrap input[type="text"] {
            padding-right: 3rem;
        }

        .toggle-pass {
            position: absolute; right: 0; top: 0; bottom: 0;
            width: 46px;
            display: grid; place-items: center;
            background: transparent; border: none; cursor: pointer;
            color: rgba(171,171,171,0.25);
            transition: color .2s;
        }
        .toggle-pass:hover { color: rgba(171,171,171,0.6); }

        /* ── Remember / forgot ── */
        .form-row {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem; gap: 1rem;
        }

        .remember-wrap {
            display: flex; align-items: center; gap: .6rem;
            font-size: 13px; font-weight: 300;
            color: rgba(171,171,171,0.45);
            cursor: pointer; user-select: none;
        }

        /* Hide native checkbox */
        .remember-wrap input[type="checkbox"] {
            position: absolute; opacity: 0; width: 0; height: 0;
        }

        /* Custom checkbox */
        .remember-box {
            width: 16px; height: 16px; flex-shrink: 0;
            border: 1px solid rgba(171,171,171,0.15);
            background: rgba(13,13,13,0.5);
            display: grid; place-items: center;
            transition: border-color .2s, background .2s;
        }
        .remember-wrap input:checked ~ .remember-box {
            border-color: rgba(140,14,3,0.6);
            background: rgba(140,14,3,0.15);
        }
        .remember-wrap input:checked ~ .remember-box::after {
            content: '';
            width: 8px; height: 8px;
            background: var(--crimson);
            display: block;
        }

        .link {
            font-size: 13px; font-weight: 400;
            color: rgba(140,14,3,0.8);
            text-decoration: none;
            transition: color .2s;
        }
        .link:hover { color: var(--crimson2); text-decoration: underline; }

        /* ── Submit ── */
        .btn-primary {
            width: 100%; height: 48px;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            background: var(--crimson);
            color: rgba(255,255,255,0.92);
            border: none; cursor: pointer;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 14px;
            letter-spacing: 0.12em; text-transform: uppercase;
            transition: background .2s, transform .15s, box-shadow .2s;
            margin-bottom: 1.5rem;
        }
        .btn-primary:hover {
            background: var(--crimson2);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(140,14,3,0.35);
        }
        .btn-primary:active { transform: scale(0.98); }
        .btn-primary svg { transition: transform .2s; }
        .btn-primary:hover svg { transform: translateX(3px); }

        /* Google button and divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 4px 0 16px;
            font-family: 'DM Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(171,171,171,0.2);
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(171,171,171,0.07);
        }

        .btn-google {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 48px;
            padding: 0 16px;
            border: 1px solid rgba(171,171,171,0.12);
            border-radius: 0;
            background: rgba(13,13,13,0.5);
            color: rgba(171,171,171,0.7);
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            text-decoration: none;
            transition: border-color .2s, background .2s, color .2s, transform .15s;
        }
        .btn-google:hover {
            border-color: rgba(171,171,171,0.25);
            background: rgba(14,17,38,0.7);
            color: rgba(171,171,171,0.95);
            transform: translateY(-2px);
        }

        /* reCAPTCHA — auth layout */
        .recaptcha-group { margin-top: 8px; }

        .recaptcha-label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: rgba(171,171,171,0.45);
            margin-bottom: 8px;
        }

        .recaptcha-frame {
            display: block;
            min-width: 304px;  
            min-height: 78px;  
            padding: 10px 12px;
            border: 1px solid rgba(171,171,171,0.12);
            background: rgba(14,17,38,0.55);
            transition: border-color .2s, box-shadow .2s;
            overflow: visible; 
        }
        .recaptcha-frame:focus-within {
            border-color: rgba(140,14,3,0.6);
            box-shadow: 0 0 0 3px rgba(140,14,3,0.09);
        }

        .recaptcha-error {
            margin-top: 6px;
            font-size: 12px;
            color: #8C0E03;
        }
        .g-recaptcha {
            transform-origin: left top;  
        }

        /* ── Footer link ── */
        .form-footer {
            text-align: center;
            font-size: 13px; font-weight: 300;
            color: rgba(171,171,171,0.3);
            padding-top: 1rem;
            border-top: 1px solid rgba(171,171,171,0.06);
        }
        .form-footer .link { margin-left: .3rem; }

        /* ── Side decorative text (desktop only) ── */
        .side-deco {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.25em;
            text-transform: uppercase;
            color: rgba(171,171,171,0.1);
            user-select: none;
            animation: fadeUp 1s 0.8s ease both;
        }
        .side-deco-left  { grid-column: 1; justify-self: end;   padding-right: 2rem; }
        .side-deco-right { grid-column: 3; justify-self: start; padding-left:  2rem; }

        /* ── Page footer ── */
        .page-footer {
            position: fixed; bottom: 0; left: 0; right: 0;
            z-index: 20;
            display: flex; align-items: center; justify-content: space-between;
            padding: .75rem 3.5rem;
            border-top: 1px solid rgba(171,171,171,0.05);
            background: rgba(13,13,13,0.7);
            backdrop-filter: blur(12px);
        }
        .footer-brand {
            display: flex; align-items: center; gap: .6rem;
        }
        .footer-logo {
            width: 22px; height: 22px;
            border: 1px solid rgba(140,14,3,0.5);
            display: grid; place-items: center;
        }
        .footer-logo span {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 11px; color: var(--crimson);
        }
        .footer-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 12px;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(171,171,171,0.35);
        }
        .footer-copy {
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.1em;
            color: rgba(171,171,171,0.15);
        }

        /* ── Responsive ── */
        @media (max-width: 720px) {
            nav { padding: .875rem 1.25rem; }
            .page { grid-template-columns: 1fr; padding: 5rem 1rem 5rem; }
            .auth-card { grid-column: 1; padding: 2rem 1.5rem; }
            .side-deco { display: none; }
            .page-footer { padding: .75rem 1.25rem; }
        }
        @media (max-width: 360px) {
            .g-recaptcha { transform: scale(0.87); transform-origin: left top; }
        }
        
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanline"></div>
<div class="orb orb-a"></div>
<div class="orb orb-b"></div>
<div class="orb orb-c"></div>

{{-- ── NAV ── --}}
<nav>
    <a href="/" class="nav-brand">
        <div class="nav-logo">
            <span class="nav-logo-letter">O</span>
        </div>
        <span class="nav-title">OJT<span class="nav-title-muted">Connect</span></span>
        <div class="nav-badge" style="display:none;" id="nav-badge">
            <div class="nav-badge-dot"></div>
             {{ tenant('name') }}
        </div>
        
    </a>
    
    <a href="{{ route('tenant.register') }}" class="btn-ghost">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M12 10v6M9 13h6"/>
        </svg>
        Register Institution
    </a>
</nav>

{{-- ── PAGE GRID ── --}}
<div class="page">

    {{-- Left decorative text --}}
    <div class="side-deco side-deco-left">OJT Management System · Bukidnon State University</div>

    {{-- ── CARD ── --}}
    <div class="auth-card">
        <span class="card-deco" aria-hidden="true">01</span>
        <div class="card-eyebrow">
            <div class="card-eyebrow-dot"></div>
            Secure Sign-in
        </div>
        @yield('content')
    </div>

    {{-- Right decorative text --}}
    <div class="side-deco side-deco-right">Internship · Records · Monitoring · Reports</div>

</div>

{{-- ── PAGE FOOTER ── --}}
<div class="page-footer">
    <div class="footer-brand">
        <div class="footer-logo"><span>O</span></div>
        <span class="footer-name">OJTConnect</span>
    </div>
    <span class="footer-copy">© {{ date('Y') }} Bukidnon State University</span>
</div>

<script>
    // Show nav badge on desktop
    const badge = document.getElementById('nav-badge');
    if (badge && window.innerWidth >= 768) badge.style.display = 'flex';

    // Password visibility toggle
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon  = document.getElementById('eye-icon');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
        } else {
            input.type = 'password';
            if (icon) icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        }
    }

    // Orb parallax
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.addEventListener('mousemove', e => {
            const x = (e.clientX / window.innerWidth  - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            const a = document.querySelector('.orb-a');
            const c = document.querySelector('.orb-c');
            if (a) a.style.transform = `translate(${x * 0.5}px, ${y * 0.5}px)`;
            if (c) c.style.transform = `translate(calc(-50% + ${-x * 0.25}px), calc(-50% + ${-y * 0.25}px))`;
        });
    }
</script>
@stack('scripts')
</body>
</html>