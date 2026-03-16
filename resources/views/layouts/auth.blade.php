<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OJTConnect') — OJTConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --navy:   #0D1B2A;
            --navy2:  #162236;
            --navy3:  #1E3050;
            --gold:   #E8A838;
            --gold2:  #F0C060;
            --cream:  #FAF6EE;
            --muted:  #8A9BB0;
            --border: rgba(255,255,255,0.08);
            --red:    #E05A5A;
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: var(--navy);
            color: #fff;
        }

        .auth-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* LEFT PANEL */
        .auth-left {
            background: var(--navy);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 56px;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            content: '';
            position: absolute;
            top: -120px; left: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(232,168,56,0.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .auth-left::after {
            content: '';
            position: absolute;
            bottom: -80px; right: -80px;
            width: 360px; height: 360px;
            background: radial-gradient(circle, rgba(30,48,80,0.8) 0%, transparent 70%);
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeUp 0.6s ease both;
        }

        .brand-mark {
            width: 40px; height: 40px;
            background: var(--gold);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Serif Display', serif;
            font-size: 20px;
            color: var(--navy);
            font-weight: 400;
        }

        .brand-name {
            font-family: 'DM Serif Display', serif;
            font-size: 22px;
            letter-spacing: -0.3px;
            color: #fff;
        }

        .brand-name span { color: var(--gold); }

        .left-hero {
            position: relative;
            z-index: 1;
            animation: fadeUp 0.7s 0.1s ease both;
        }

        .left-hero h1 {
            font-family: 'DM Serif Display', serif;
            font-size: clamp(32px, 3.5vw, 48px);
            line-height: 1.15;
            font-weight: 400;
            color: #fff;
            margin-bottom: 20px;
        }

        .left-hero h1 em {
            font-style: italic;
            color: var(--gold);
        }

        .left-hero p {
            font-size: 15px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 340px;
        }

        .left-stats {
            display: flex;
            gap: 32px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.7s 0.2s ease both;
        }

        .stat-item { }
        .stat-num {
            font-family: 'DM Serif Display', serif;
            font-size: 28px;
            color: var(--gold);
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
            letter-spacing: 0.3px;
        }

        /* RIGHT PANEL */
        .auth-right {
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            animation: fadeUp 0.6s 0.15s ease both;
        }

        .auth-card-title {
            font-family: 'DM Serif Display', serif;
            font-size: 30px;
            color: var(--navy);
            font-weight: 400;
            margin-bottom: 6px;
        }

        .auth-card-sub {
            font-size: 14px;
            color: #6B7A8D;
            margin-bottom: 36px;
            line-height: 1.5;
        }

        /* FORM ELEMENTS */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #3D4F62;
            margin-bottom: 7px;
            letter-spacing: 0.2px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #DDE3EC;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--navy);
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        input:focus {
            border-color: var(--navy3);
            box-shadow: 0 0 0 3px rgba(30,48,80,0.08);
        }

        input.is-invalid {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(224,90,90,0.08);
        }

        .invalid-feedback {
            font-size: 12px;
            color: var(--red);
            margin-top: 5px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input { padding-right: 44px; }

        .toggle-pass {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; padding: 2px;
            color: var(--muted);
            display: flex; align-items: center;
        }

        .toggle-pass:hover { color: var(--navy); }

        .form-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #6B7A8D;
            cursor: pointer;
        }

        .remember-wrap input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--navy);
            cursor: pointer;
        }

        .link {
            font-size: 13px;
            color: var(--navy3);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .link:hover { color: var(--gold); }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.2px;
        }

        .btn-primary:hover { background: var(--navy3); }
        .btn-primary:active { transform: scale(0.99); }

        .btn-outline {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--navy);
            border: 1.5px solid #DDE3EC;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            display: block;
            text-decoration: none;
            margin-top: 12px;
        }

        .btn-outline:hover {
            border-color: var(--navy);
            background: rgba(13,27,42,0.04);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0;
            color: #B0BBC8;
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .alert-success {
            background: #EDF7F0;
            color: #2D6A4F;
            border: 1px solid #B7DFC6;
        }

        .alert-error {
            background: #FEF0F0;
            color: #9B2C2C;
            border: 1px solid #FBCECE;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .auth-wrap { grid-template-columns: 1fr; }
            .auth-left  { display: none; }
            .auth-right { background: var(--navy); }
            .auth-card-title { color: #fff; }
            .auth-card-sub { color: var(--muted); }
            label { color: var(--muted); }
            input[type="email"],
            input[type="password"],
            input[type="text"] {
                background: var(--navy2);
                border-color: var(--border);
                color: #fff;
            }
            input:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(232,168,56,0.12); }
            .btn-primary { background: var(--gold); color: var(--navy); }
            .btn-primary:hover { background: var(--gold2); }
            .btn-outline { color: #fff; border-color: var(--border); }
            .form-row a { color: var(--gold); }
            .remember-wrap { color: var(--muted); }
            .divider { color: rgba(255,255,255,0.2); }
            .divider::before, .divider::after { background: rgba(255,255,255,0.1); }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="auth-wrap">
    <!-- Left branding panel -->
    <div class="auth-left">
        <div class="brand">
            <div class="brand-mark">O</div>
            <div class="brand-name">OJT<span>Connect</span></div>
        </div>

        <div class="left-hero">
            <h1>Manage your <em>internship</em> journey with ease.</h1>
            <p>A unified platform for students, coordinators, and company supervisors to streamline the entire OJT process.</p>
        </div>

        <div class="left-stats">
            <div class="stat-item">
                <div class="stat-num">3</div>
                <div class="stat-label">Partner colleges</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">4</div>
                <div class="stat-label">User roles</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">100%</div>
                <div class="stat-label">Paperless</div>
            </div>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="auth-right">
        <div class="auth-card">
            @yield('content')
        </div>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    const btn   = input.nextElementSibling;
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.innerHTML = isText ? eyeIcon() : eyeOffIcon();
}

function eyeIcon() {
    return `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}

function eyeOffIcon() {
    return `<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`;
}
</script>
@stack('scripts')
</body>
</html>