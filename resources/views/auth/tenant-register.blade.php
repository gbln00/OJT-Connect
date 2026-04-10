<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Your Institution — OJTConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet" />
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
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ── Grain overlay ── */
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

        /* ── Grid background ── */
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
            background: radial-gradient(ellipse 80% 45% at 50% 0%, rgba(140,14,3,0.12) 0%, transparent 65%);
        }

        /* ── Orbs ── */
        .orb { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .orb-a { width: 700px; height: 700px; background: rgba(14,17,38,0.9); top: -300px; right: -200px; animation: drift1 20s ease-in-out infinite alternate; }
        .orb-b { width: 500px; height: 500px; background: rgba(13,13,13,0.95); bottom: -180px; left: -160px; animation: drift2 25s ease-in-out infinite alternate; }
        .orb-c { width: 400px; height: 400px; background: rgba(140,14,3,0.07); top: 40%; left: 35%; animation: drift1 30s ease-in-out infinite alternate; }

        /* ── Keyframes ── */
        @keyframes fadeUp   { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes drift1   { 0% { transform: translate(0,0); } 100% { transform: translate(-40px,55px); } }
        @keyframes drift2   { 0% { transform: translate(0,0); } 100% { transform: translate(50px,-35px); } }
        @keyframes flicker  { 0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:0.4} 94%{opacity:1} 96%{opacity:0.6} 97%{opacity:1} }
        @keyframes scandown { 0%{transform:translateY(-100%);opacity:0} 5%{opacity:1} 95%{opacity:1} 100%{transform:translateY(100vh);opacity:0} }
        @keyframes staggerUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }

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
            display: grid; place-items: center;
            position: relative;
        }
        .nav-logo span {
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
            letter-spacing: 0.12em; text-transform: uppercase;
            color: #fff;
        }
        .nav-title span { color: rgba(171,171,171,0.45); }

        .nav-badge {
            display: flex; align-items: center; gap: 6px;
            padding: 2px 8px;
            border: 1px solid rgba(171,171,171,0.1);
            background: rgba(171,171,171,0.04);
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(171,171,171,0.35);
        }
        .nav-badge-dot { width: 4px; height: 4px; background: var(--crimson); }

        .nav-actions { display: flex; align-items: center; gap: 10px; }

        .btn-ghost {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px;
            border: 1px solid rgba(171,171,171,0.15);
            color: rgba(171,171,171,0.6);
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 13px;
            letter-spacing: 0.1em; text-transform: uppercase;
            text-decoration: none; cursor: pointer;
            transition: border-color .2s, color .2s, background .2s, transform .15s;
            background: transparent;
        }
        .btn-ghost:hover {
            border-color: rgba(171,171,171,0.3);
            color: rgba(171,171,171,0.9);
            background: rgba(171,171,171,0.05);
            transform: translateY(-2px);
        }

        .btn-crimson {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 20px;
            background: var(--crimson);
            color: rgba(255,255,255,0.92);
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 13px;
            letter-spacing: 0.1em; text-transform: uppercase;
            text-decoration: none; cursor: pointer; border: none;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-crimson:hover {
            background: var(--crimson2);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(140,14,3,0.35);
        }
        .btn-crimson:active { transform: scale(0.98); }

        /* ── TWO-COLUMN LAYOUT ── */
        .layout {
            display: grid;
            grid-template-columns: 420px 1fr;
            min-height: 100vh;
            position: relative; z-index: 10;
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            position: sticky; top: 0;
            height: 100vh;
            padding: 7rem 3rem 3rem;
            display: flex; flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(171,171,171,0.06);
            background: rgba(14,17,38,0.4);
            overflow: hidden;
        }

        .panel-left::before {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 60% 40% at 10% 80%, rgba(140,14,3,0.10) 0%, transparent 60%),
                radial-gradient(ellipse 40% 50% at 90% 10%, rgba(140,14,3,0.06) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Giant decorative letter */
        .panel-ornament {
            position: absolute;
            bottom: -2rem; right: -1.5rem;
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 18rem;
            line-height: 1;
            color: rgba(171,171,171,0.025);
            user-select: none; pointer-events: none;
            letter-spacing: -0.05em;
        }

        .panel-top { position: relative; z-index: 1; }

        .panel-eyebrow {
            display: flex; align-items: center; gap: 10px;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(171,171,171,0.35);
            margin-bottom: 2rem;
        }
        .panel-eyebrow-dot {
            width: 6px; height: 6px;
            background: var(--crimson);
            animation: flicker 8s ease-in-out infinite;
        }

        .panel-heading {
            font-family: 'Playfair Display', serif;
            font-weight: 900;
            font-size: clamp(2.4rem, 3vw, 3rem);
            line-height: 1.05;
            color: #fff;
            margin-bottom: 1.25rem;
            letter-spacing: -0.02em;
        }
        .panel-heading em {
            font-style: italic;
            color: var(--crimson);
        }

        .panel-desc {
            font-size: 14px; font-weight: 300;
            line-height: 1.8;
            color: rgba(171,171,171,0.42);
            max-width: 28ch;
        }

        /* Steps */
        .steps { position: relative; z-index: 1; }

        .steps-label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.22em; text-transform: uppercase;
            color: rgba(171,171,171,0.25);
            margin-bottom: 1.25rem;
        }

        .step-list { display: flex; flex-direction: column; gap: 0; }

        .step-item {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: 1rem 0;
            border-top: 1px solid rgba(171,171,171,0.06);
        }
        .step-item:last-child { border-bottom: 1px solid rgba(171,171,171,0.06); }

        .step-num {
            width: 28px; height: 28px; flex-shrink: 0;
            border: 1px solid rgba(140,14,3,0.4);
            display: grid; place-items: center;
            font-family: 'Playfair Display', serif;
            font-weight: 700; font-size: 13px;
            color: rgba(140,14,3,0.7);
            margin-top: 1px;
        }

        .step-body strong {
            display: block;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 600; font-size: 13px;
            letter-spacing: 0.05em; text-transform: uppercase;
            color: rgba(171,171,171,0.75);
            margin-bottom: 2px;
        }
        .step-body span {
            font-size: 12px; font-weight: 300;
            color: rgba(171,171,171,0.3);
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            padding: 7rem 4rem 4rem;
            display: flex; flex-direction: column;
            justify-content: flex-start;
            max-width: 680px;
        }

        /* Form header */
        .form-header { margin-bottom: 2.5rem; animation: staggerUp 0.6s 0.1s ease both; }

        .form-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 4px 10px;
            border: 1px solid rgba(171,171,171,0.08);
            background: rgba(171,171,171,0.03);
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(140,14,3,0.8);
            margin-bottom: 1.25rem;
        }
        .form-eyebrow-dot { width: 4px; height: 4px; background: var(--crimson); }

        .form-heading {
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: clamp(1.8rem, 2.5vw, 2.4rem);
            line-height: 1.1; letter-spacing: -0.02em;
            color: #fff; margin-bottom: .75rem;
        }

        .form-subheading {
            font-size: 14px; font-weight: 300;
            color: rgba(171,171,171,0.42); line-height: 1.7;
        }
        .form-subheading span { color: rgba(140,14,3,0.8); }

        /* Alerts */
        .alert-success {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 1rem 1.25rem;
            border: 1px solid rgba(76,175,80,0.25);
            border-left: 3px solid #4caf50;
            background: rgba(76,175,80,0.06);
            color: rgba(171,171,171,0.8);
            font-size: 14px; font-weight: 300;
            margin-bottom: 2rem;
            animation: staggerUp 0.5s ease both;
        }
        .alert-success svg { color: #4caf50; flex-shrink: 0; margin-top: 2px; }

        .alert-error {
            padding: 1rem 1.25rem;
            border: 1px solid rgba(140,14,3,0.3);
            border-left: 3px solid var(--crimson);
            background: rgba(140,14,3,0.06);
            color: rgba(171,171,171,0.7);
            font-size: 13px; font-weight: 300;
            margin-bottom: 2rem;
            animation: staggerUp 0.5s ease both;
        }
        .alert-error strong { color: var(--crimson); display: block; margin-bottom: .4rem; font-weight: 600; }
        .alert-error ul { padding-left: 1.1rem; }
        .alert-error li { margin-bottom: .2rem; }

        /* Section divider */
        .form-section-label {
            display: flex; align-items: center; gap: 14px;
            margin-bottom: 1.25rem;
            grid-column: 1 / -1;
        }
        .form-section-label::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
        .form-section-label::after  { content: ''; flex: 1; height: 1px; background: rgba(171,171,171,0.07); }
        .form-section-label span {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.22em; text-transform: uppercase;
            color: rgba(171,171,171,0.28);
        }

        /* Grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem 1.5rem;
        }

        .field { display: flex; flex-direction: column; gap: .45rem; }
        .field.full { grid-column: 1 / -1; }

        .field-label {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 10px; font-weight: 600;
            letter-spacing: 0.2em; text-transform: uppercase;
            color: rgba(171,171,171,0.4);
        }
        .field-label .req { color: var(--crimson); margin-left: .2rem; }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%);
            width: 15px; height: 15px;
            stroke: rgba(171,171,171,0.2);
            fill: none; stroke-width: 1.5;
            pointer-events: none;
            transition: stroke .2s;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%; height: 46px;
            padding: 0 1rem 0 2.5rem;
            border: 1px solid rgba(171,171,171,0.1);
            background: rgba(14,17,38,0.6);
            border-radius: 0;
            font-family: 'Barlow', sans-serif;
            font-size: 14px; font-weight: 300;
            color: rgba(171,171,171,0.85);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            appearance: none; -webkit-appearance: none;
        }

        input::placeholder { color: rgba(171,171,171,0.18); }

        input:focus,
        select:focus {
            border-color: rgba(140,14,3,0.7);
            background: rgba(14,17,38,0.85);
            box-shadow: 0 0 0 3px rgba(140,14,3,0.1);
        }

        .input-wrap:focus-within .input-icon { stroke: rgba(140,14,3,0.6); }

        input.error, select.error {
            border-color: rgba(140,14,3,0.5);
            background: rgba(140,14,3,0.04);
        }

        .field-error {
            font-family: 'DM Mono', monospace;
            font-size: 11px; color: var(--crimson);
        }

        /* Select arrow */
        select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='rgba(171,171,171,0.3)' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }
        select option { background: var(--night); color: var(--ash); }

        /* Subdomain hint */
        .subdomain-hint {
            font-family: 'DM Mono', monospace;
            font-size: 11px;
            color: rgba(171,171,171,0.28);
        }
        .subdomain-hint strong {
            color: rgba(171,171,171,0.6);
            font-weight: 500;
        }

        /* Plan cards */
        .plan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1px;
            background: rgba(171,171,171,0.06);
            margin-top: .25rem;
        }

        .plan-label { position: relative; cursor: pointer; }

        .plan-label input[type="radio"] {
            position: absolute; opacity: 0; pointer-events: none;
        }

        .plan-card {
            background: var(--void);
            padding: 1.1rem .9rem;
            text-align: center;
            border: 1.5px solid transparent;
            transition: border-color .2s, background .2s, box-shadow .2s;
            position: relative;
        }

        .plan-card::before {
            content: ''; position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
            background: var(--crimson);
            transform: scaleX(0);
            transition: transform .25s;
        }

        .plan-label:hover .plan-card {
            background: rgba(14,17,38,0.6);
            border-color: rgba(171,171,171,0.1);
        }

        .plan-label input:checked ~ .plan-card {
            border-color: rgba(140,14,3,0.45);
            background: rgba(140,14,3,0.07);
            box-shadow: 0 0 0 3px rgba(140,14,3,0.08) inset;
        }
        .plan-label input:checked ~ .plan-card::before { transform: scaleX(1); }

        .plan-name {
            display: block;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 12px; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase;
            color: rgba(171,171,171,0.55);
            margin-bottom: .35rem;
            transition: color .2s;
        }

        .plan-label input:checked ~ .plan-card .plan-name { color: var(--crimson); }

        .plan-price {
            display: block;
            font-family: 'Playfair Display', serif;
            font-weight: 900; font-size: 1.5rem;
            color: #fff; line-height: 1;
            margin-bottom: .3rem;
        }

        .plan-period {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 9px; letter-spacing: .1em;
            color: rgba(171,171,171,0.22);
            text-transform: uppercase;
            margin-bottom: .6rem;
        }

        .plan-desc {
            font-size: 11px; font-weight: 300;
            color: rgba(171,171,171,0.35); line-height: 1.5;
        }

        /* Divider */
        .form-divider {
            grid-column: 1 / -1;
            height: 1px;
            background: rgba(171,171,171,0.06);
            margin: .5rem 0;
        }

        /* Submit row */
        .submit-row {
            grid-column: 1 / -1;
            display: flex; align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-top: .75rem;
        }

        .submit-footnote {
            font-size: 12px; font-weight: 300;
            color: rgba(171,171,171,0.3); line-height: 1.7;
        }

        .btn-submit {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 0 2rem; height: 48px;
            background: var(--crimson);
            color: rgba(255,255,255,0.92);
            border: none; cursor: pointer;
            font-family: 'Barlow Condensed', sans-serif;
            font-weight: 700; font-size: 14px;
            letter-spacing: 0.12em; text-transform: uppercase;
            white-space: nowrap; flex-shrink: 0;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-submit:hover {
            background: var(--crimson2);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(140,14,3,0.35);
        }
        .btn-submit:active { transform: scale(0.98); }
        .btn-submit svg { transition: transform .2s; }
        .btn-submit:hover svg { transform: translateX(3px); }

        .recaptcha-wrap {
            display: inline-flex;
            padding: .75rem 1rem;
            border: 1px solid rgba(171,171,171,0.1);
            background: rgba(14,17,38,0.6);
            transition: border-color .2s;
        }
        .recaptcha-wrap:focus-within {
            border-color: rgba(140,14,3,0.7);
            box-shadow: 0 0 0 3px rgba(140,14,3,0.1);
        }

        /* Login link */
        .login-link {
            grid-column: 1 / -1;
            text-align: center;
            font-size: 13px; font-weight: 300;
            color: rgba(171,171,171,0.3);
            padding-top: .5rem;
        }
        .login-link a {
            color: rgba(140,14,3,0.85);
            text-decoration: none; font-weight: 500;
            transition: color .2s;
        }
        .login-link a:hover { color: var(--crimson2); text-decoration: underline; }

        /* Stagger animations */
        .form-grid > *:nth-child(1)  { animation: staggerUp .5s .15s ease both; }
        .form-grid > *:nth-child(2)  { animation: staggerUp .5s .20s ease both; }
        .form-grid > *:nth-child(3)  { animation: staggerUp .5s .25s ease both; }
        .form-grid > *:nth-child(4)  { animation: staggerUp .5s .30s ease both; }
        .form-grid > *:nth-child(5)  { animation: staggerUp .5s .35s ease both; }
        .form-grid > *:nth-child(6)  { animation: staggerUp .5s .40s ease both; }
        .form-grid > *:nth-child(7)  { animation: staggerUp .5s .45s ease both; }
        .form-grid > *:nth-child(8)  { animation: staggerUp .5s .50s ease both; }
        .form-grid > *:nth-child(9)  { animation: staggerUp .5s .55s ease both; }
        .form-grid > *:nth-child(10) { animation: staggerUp .5s .60s ease both; }
        .form-grid > *:nth-child(11) { animation: staggerUp .5s .65s ease both; }

        /* ── RESPONSIVE ── */
        @media (max-width: 960px) {
            nav { padding: .875rem 1.5rem; }
            .layout { grid-template-columns: 1fr; }
            .panel-left {
                position: static; height: auto;
                padding: 6rem 2rem 2.5rem;
            }
            .panel-ornament { display: none; }
            .panel-desc { max-width: 100%; }
            .step-list { flex-direction: row; flex-wrap: wrap; gap: 0; }
            .step-item { flex: 1; min-width: 140px; border-bottom: 1px solid rgba(171,171,171,0.06); border-top: 1px solid rgba(171,171,171,0.06); border-right: 1px solid rgba(171,171,171,0.06); border-left: none; padding: 1rem; }
            .step-item:first-child { border-left: 1px solid rgba(171,171,171,0.06); }
            .panel-right { padding: 2.5rem 1.5rem 3rem; max-width: 100%; }
        }

        @media (max-width: 580px) {
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: 1; }
            .plan-grid { grid-template-columns: 1fr; gap: 1px; }
            .submit-row { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-submit { justify-content: center; }
            .nav-badge { display: none; }
        }
    </style>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
            <span>O</span>
        </div>
        <span class="nav-title">OJT<span>Connect</span></span>
        <div class="nav-badge" style="display:none;" id="nav-badge-buksu">
            <div class="nav-badge-dot"></div>
            BukSU
        </div>
    </a>
    <div class="nav-actions">
        <a href="{{ route('login') }}" class="btn-ghost">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
            Log in
        </a>
    </div>
</nav>

<div class="layout">

    {{-- ── LEFT PANEL ── --}}
    <aside class="panel-left">
        <div class="panel-top">
            <div class="panel-eyebrow">
                <div class="panel-eyebrow-dot"></div>
                New Institution Registration
            </div>
            <h1 class="panel-heading">
                Launch your<br /><em>institution</em><br />in 24 hours.
            </h1>
            <p class="panel-desc">
                Submit your details and our team will provision a dedicated OJT environment for your organisation.
            </p>
        </div>

        <div class="steps">
            <p class="steps-label">How it works</p>
            <div class="step-list">
                <div class="step-item">
                    <div class="step-num">1</div>
                    <div class="step-body">
                        <strong>Submit this form</strong>
                        <span>Takes under two minutes</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">2</div>
                    <div class="step-body">
                        <strong>Review &amp; approval</strong>
                        <span>Within 24 hours by email</span>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-num">3</div>
                    <div class="step-body">
                        <strong>Your subdomain goes live</strong>
                        <span>Fully isolated environment</span>
                    </div>
                </div>
            </div>
        </div>

        <span class="panel-ornament" aria-hidden="true">§</span>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="panel-right">

        <div class="form-header">
            <div class="form-eyebrow">
                <div class="form-eyebrow-dot"></div>
                New Institution
            </div>
            <h2 class="form-heading">Register your organisation</h2>
            <p class="form-subheading">
                Fields marked <span>✦</span> are required. We'll review your submission within 24 hours.
            </p>
        </div>

        {{-- Success --}}
        @if (session('success'))
        <div class="alert-success" role="alert">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        {{-- Errors --}}
        @if ($errors->any())
        <div class="alert-error" role="alert">
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tenant.register.submit') }}" method="POST" novalidate>
            @csrf

            <div class="form-grid">

                {{-- Section: Institution Info --}}
                <div class="form-section-label">
                    <span>Institution Info</span>
                </div>

                {{-- Company Name --}}
                <div class="field full">
                    <label class="field-label" for="company_name">Institution / Company Name <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name') }}"
                            placeholder="e.g. Bukidnon State University"
                            class="{{ $errors->has('company_name') ? 'error' : '' }}"
                            autocomplete="organization"
                        />
                    </div>
                    @error('company_name')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Subdomain --}}
                <div class="field full">
                    <label class="field-label" for="subdomain">Preferred Subdomain <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                        <input
                            type="text"
                            id="subdomain"
                            name="subdomain"
                            value="{{ old('subdomain') }}"
                            placeholder="yourdomain"
                            class="{{ $errors->has('subdomain') ? 'error' : '' }}"
                            autocomplete="off"
                            spellcheck="false"
                            oninput="updateSubdomainHint(this.value)"
                        />
                    </div>
                    <span class="subdomain-hint">
                        Your URL will be: <strong id="subdomain-preview">yourdomain</strong>.ojtconnect.edu.ph
                    </span>
                    @error('subdomain')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Section divider --}}
                <div class="form-divider"></div>

                {{-- Section: Contact --}}
                <div class="form-section-label">
                    <span>Point of Contact</span>
                </div>

                {{-- Contact Person --}}
                <div class="field">
                    <label class="field-label" for="contact_person">Contact Person <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <input
                            type="text"
                            id="contact_person"
                            name="contact_person"
                            value="{{ old('contact_person') }}"
                            placeholder="Full name"
                            class="{{ $errors->has('contact_person') ? 'error' : '' }}"
                            autocomplete="name"
                        />
                    </div>
                    @error('contact_person')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="field">
                    <label class="field-label" for="email">Email Address <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@institution.edu.ph"
                            class="{{ $errors->has('email') ? 'error' : '' }}"
                            autocomplete="email"
                        />
                    </div>
                    @error('email')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="field full">
                    <label class="field-label" for="phone">Phone Number</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.43 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 15.92z"/>
                        </svg>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+63 9XX XXX XXXX"
                            class="{{ $errors->has('phone') ? 'error' : '' }}"
                            autocomplete="tel"
                        />
                    </div>
                    @error('phone')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Section divider --}}
                <div class="form-divider"></div>

                {{-- Section: Subscription --}}
                <div class="form-section-label">
                    <span>Subscription Plan</span>
                </div>

                {{-- Plan --}}
                <div class="field full">
                    <label class="field-label">Choose a Plan <span class="req">✦</span></label>
                    <div class="plan-grid">

                        <label class="plan-label">
                            <input type="radio" name="plan" value="basic"
                                {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Basic</span>
                                <span class="plan-price">₱10k</span>
                                <span class="plan-period">per year</span>
                                <span class="plan-desc">Up to 50 users.<br />Core OJT features.</span>
                            </div>
                        </label>

                        <label class="plan-label">
                            <input type="radio" name="plan" value="standard"
                                {{ old('plan') === 'standard' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Standard</span>
                                <span class="plan-price">₱20k</span>
                                <span class="plan-period">per year</span>
                                <span class="plan-desc">Up to 200 users.<br />Advanced tools + reports.</span>
                            </div>
                        </label>

                        <label class="plan-label">
                            <input type="radio" name="plan" value="premium"
                                {{ old('plan') === 'premium' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Premium</span>
                                <span class="plan-price">₱30k</span>
                                <span class="plan-period">per year</span>
                                <span class="plan-desc">Unlimited users.<br />Full suite + SLA + PDF gen.</span>
                            </div>
                        </label>

                    </div>
                    @error('plan')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

               {{-- reCAPTCHA --}}
                <div class="field full" style="margin-top:.5rem;">
                    <label class="field-label">Security Check <span class="req">✦</span></label>
                    <div class="recaptcha-wrap">
                        <div class="g-recaptcha"
                            data-sitekey="{{ config('services.recaptcha.site_key') }}"
                            data-theme="dark">
                        </div>
                    </div>
                    @error('g-recaptcha-response')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="submit-row">
                    <p class="submit-footnote">
                        By submitting, you agree to our terms of service.<br />
                        We'll review your request within 24 hours.
                    </p>
                    <button type="submit" class="btn-submit">
                        Submit Request
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>

                <div class="login-link">
                    Already registered? <a href="{{ route('login') }}">Sign in here</a>
                </div>

            </div>{{-- /.form-grid --}}
        </form>
    </main>

</div>{{-- /.layout --}}

<script>
    // Live subdomain preview
    function updateSubdomainHint(val) {
        const preview = document.getElementById('subdomain-preview');
        preview.textContent = val.trim() || 'yourdomain';
    }

    // Pre-populate on load
    const existingSubdomain = document.getElementById('subdomain').value;
    if (existingSubdomain) updateSubdomainHint(existingSubdomain);

    // Nav badge reveal (desktop only)
    const badge = document.getElementById('nav-badge-buksu');
    if (badge && window.innerWidth >= 768) badge.style.display = 'flex';

    // Orb parallax
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.addEventListener('mousemove', e => {
            const x = (e.clientX / window.innerWidth  - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            const a = document.querySelector('.orb-a');
            const c = document.querySelector('.orb-c');
            if (a) a.style.transform = `translate(${x * 0.5}px, ${y * 0.5}px)`;
            if (c) c.style.transform = `translate(${-x * 0.25}px, ${-y * 0.25}px)`;
        });
    }
</script>
</body>
</html>