<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register Your Institution</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #0f0e0c;
            --paper:     #faf8f4;
            --cream:     #f0ece3;
            --gold:      #b8893a;
            --gold-lt:   #d4aa65;
            --rust:      #c0392b;
            --muted:     #7a746a;
            --border:    #ddd8ce;
            --shadow:    0 2px 24px rgba(15,14,12,.07);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── LEFT PANEL ── */
        .panel-left {
            position: sticky;
            top: 0;
            height: 100vh;
            background: var(--ink);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3.5rem;
            overflow: hidden;
        }

        .panel-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 60% 50% at 20% 80%, rgba(184,137,58,.18) 0%, transparent 60%),
                radial-gradient(ellipse 40% 60% at 80% 10%, rgba(184,137,58,.10) 0%, transparent 50%);
            pointer-events: none;
        }

        /* decorative serif ornament */
        .ornament {
            font-family: 'Cormorant Garamond', serif;
            font-size: 11rem;
            line-height: 1;
            color: rgba(255,255,255,.04);
            position: absolute;
            bottom: -1.5rem;
            right: -1rem;
            user-select: none;
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            position: relative;
            z-index: 1;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            border: 1.5px solid var(--gold);
            display: grid;
            place-items: center;
        }

        .brand-mark svg { width: 18px; height: 18px; fill: var(--gold); }

        .brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.15rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--paper);
        }

        .panel-copy { position: relative; z-index: 1; }

        .panel-copy h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.6rem, 3.5vw, 3.4rem);
            font-weight: 400;
            line-height: 1.12;
            color: var(--paper);
            margin-bottom: 1.5rem;
        }

        .panel-copy h1 em {
            font-style: italic;
            color: var(--gold-lt);
        }

        .panel-copy p {
            font-size: .9rem;
            font-weight: 300;
            line-height: 1.8;
            color: rgba(250,248,244,.55);
            max-width: 30ch;
        }

        .steps {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .step {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .step-num {
            width: 26px;
            height: 26px;
            border: 1px solid var(--gold);
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: .65rem;
            font-weight: 500;
            letter-spacing: .06em;
            color: var(--gold);
            flex-shrink: 0;
            margin-top: .1rem;
        }

        .step-text strong {
            display: block;
            font-size: .8rem;
            font-weight: 500;
            color: rgba(250,248,244,.85);
            margin-bottom: .15rem;
        }

        .step-text span {
            font-size: .75rem;
            font-weight: 300;
            color: rgba(250,248,244,.4);
        }

        /* ── RIGHT PANEL ── */
        .panel-right {
            padding: 4rem 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 640px;
        }

        .form-header { margin-bottom: 2.5rem; }

        .form-header .eyebrow {
            font-size: .7rem;
            font-weight: 500;
            letter-spacing: .2em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: .75rem;
            display: block;
        }

        .form-header h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2rem;
            font-weight: 600;
            line-height: 1.2;
            color: var(--ink);
        }

        .form-header p {
            margin-top: .6rem;
            font-size: .85rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.7;
        }

        /* alerts */
        .alert-success {
            background: #edf7ed;
            border-left: 3px solid #4caf50;
            padding: .9rem 1.1rem;
            border-radius: 2px;
            font-size: .85rem;
            color: #2e7d32;
            margin-bottom: 1.8rem;
            display: flex;
            align-items: flex-start;
            gap: .6rem;
        }

        .alert-success svg { flex-shrink: 0; margin-top: .05rem; }

        .alert-error {
            background: #fdf2f2;
            border-left: 3px solid var(--rust);
            padding: .9rem 1.1rem;
            border-radius: 2px;
            font-size: .83rem;
            color: var(--rust);
            margin-bottom: 1.8rem;
        }

        .alert-error ul { padding-left: 1.1rem; margin-top: .35rem; }
        .alert-error li { margin-bottom: .2rem; }

        /* form grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem 1.5rem;
        }

        .field { display: flex; flex-direction: column; gap: .4rem; }
        .field.full { grid-column: 1 / -1; }

        label {
            font-size: .72rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        label .req { color: var(--gold); margin-left: .2rem; }

        .input-wrap { position: relative; }

        .input-wrap svg {
            position: absolute;
            left: .9rem;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            stroke: var(--border);
            fill: none;
            stroke-width: 1.5;
            pointer-events: none;
            transition: stroke .2s;
        }

        input, select {
            width: 100%;
            height: 44px;
            padding: 0 1rem 0 2.5rem;
            border: 1px solid var(--border);
            background: #fff;
            border-radius: 2px;
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem;
            font-weight: 300;
            color: var(--ink);
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            appearance: none;
            -webkit-appearance: none;
        }

        select { cursor: pointer; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a746a' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; }

        input:focus, select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(184,137,58,.1);
        }

        input:focus + svg,
        .input-wrap:focus-within svg { stroke: var(--gold-lt); }

        input.error, select.error { border-color: var(--rust); }

        .field-error {
            font-size: .73rem;
            color: var(--rust);
            margin-top: -.1rem;
        }

        /* subdomain hint */
        .subdomain-hint {
            font-size: .73rem;
            color: var(--muted);
            margin-top: -.1rem;
        }

        .subdomain-hint span {
            font-weight: 500;
            color: var(--ink);
        }

        /* plan cards */
        .plan-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: .75rem;
            margin-top: .1rem;
        }

        .plan-label {
            position: relative;
            cursor: pointer;
        }

        .plan-label input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .plan-card {
            border: 1.5px solid var(--border);
            padding: .9rem .75rem;
            border-radius: 2px;
            transition: border-color .2s, background .2s, box-shadow .2s;
            text-align: center;
        }

        .plan-card .plan-name {
            display: block;
            font-size: .75rem;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--ink);
            margin-bottom: .3rem;
        }

        .plan-card .plan-desc {
            font-size: .72rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.5;
        }

        .plan-label input:checked ~ .plan-card {
            border-color: var(--gold);
            background: rgba(184,137,58,.05);
            box-shadow: 0 0 0 3px rgba(184,137,58,.1);
        }

        .plan-label input:checked ~ .plan-card .plan-name { color: var(--gold); }

        .plan-label:hover .plan-card { border-color: var(--gold-lt); }

        /* divider */
        .divider {
            grid-column: 1 / -1;
            height: 1px;
            background: var(--border);
            margin: .25rem 0;
        }

        /* submit */
        .submit-row {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: .5rem;
        }

        .submit-row .footnote {
            font-size: .75rem;
            font-weight: 300;
            color: var(--muted);
            line-height: 1.6;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            background: var(--ink);
            color: var(--paper);
            border: none;
            padding: 0 1.8rem;
            height: 46px;
            font-family: 'DM Sans', sans-serif;
            font-size: .82rem;
            font-weight: 500;
            letter-spacing: .08em;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 2px;
            transition: background .2s, transform .15s;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-submit:hover { background: #222; }
        .btn-submit:active { transform: scale(.98); }

        .btn-submit svg {
            width: 14px; height: 14px;
            fill: none; stroke: currentColor; stroke-width: 2;
            transition: transform .2s;
        }

        .btn-submit:hover svg { transform: translateX(3px); }

        /* login link */
        .login-link {
            grid-column: 1 / -1;
            text-align: center;
            font-size: .8rem;
            font-weight: 300;
            color: var(--muted);
        }

        .login-link a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover { text-decoration: underline; }

        /* staggered fade-in */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-header, .alert-success, .alert-error,
        .form-grid > * {
            animation: fadeUp .45s ease both;
        }

        .form-grid > *:nth-child(1)  { animation-delay: .05s; }
        .form-grid > *:nth-child(2)  { animation-delay: .10s; }
        .form-grid > *:nth-child(3)  { animation-delay: .15s; }
        .form-grid > *:nth-child(4)  { animation-delay: .20s; }
        .form-grid > *:nth-child(5)  { animation-delay: .25s; }
        .form-grid > *:nth-child(6)  { animation-delay: .30s; }
        .form-grid > *:nth-child(7)  { animation-delay: .35s; }
        .form-grid > *:nth-child(8)  { animation-delay: .40s; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            body { grid-template-columns: 1fr; }

            .panel-left {
                position: static;
                height: auto;
                padding: 2.5rem 2rem;
                min-height: 0;
            }

            .ornament { display: none; }

            .panel-copy h1 { font-size: 2rem; }

            .steps { flex-direction: row; flex-wrap: wrap; gap: .9rem; }

            .panel-right {
                padding: 2.5rem 2rem 3rem;
                max-width: 100%;
            }
        }

        @media (max-width: 520px) {
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: 1; }
            .plan-grid { grid-template-columns: 1fr; }
            .submit-row { flex-direction: column; align-items: stretch; text-align: center; }
            .btn-submit { justify-content: center; }
        }
    </style>
</head>
<body>

    {{-- ── LEFT PANEL ── --}}
    <aside class="panel-left">
        <div class="brand">
            <div class="brand-mark">
                <svg viewBox="0 0 18 18"><path d="M9 1L1 5v8l8 4 8-4V5L9 1z"/></svg>
            </div>
            <span class="brand-name">YourPlatform</span>
        </div>

        <div class="panel-copy">
            <h1>Launch your<br /><em>institution</em><br />in 24 hours.</h1>
            <p>Submit your details below and our team will provision a dedicated environment for your organisation.</p>
        </div>

        <div class="steps">
            <div class="step">
                <div class="step-num">01</div>
                <div class="step-text">
                    <strong>Submit this form</strong>
                    <span>Takes under two minutes</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <div class="step-text">
                    <strong>Review & approval</strong>
                    <span>Within 24 hours by email</span>
                </div>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <div class="step-text">
                    <strong>Your subdomain goes live</strong>
                    <span>Fully isolated environment</span>
                </div>
            </div>
        </div>

        <span class="ornament" aria-hidden="true">§</span>
    </aside>

    {{-- ── RIGHT PANEL ── --}}
    <main class="panel-right">

        <div class="form-header">
            <span class="eyebrow">New Institution</span>
            <h2>Register your organisation</h2>
            <p>All fields marked <span style="color:var(--gold)">✦</span> are required.</p>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div class="alert-success" role="alert">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
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

                {{-- Company Name --}}
                <div class="field full">
                    <label for="company_name">Institution / Company Name <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            value="{{ old('company_name') }}"
                            placeholder="e.g. Westbrook Academy"
                            class="{{ $errors->has('company_name') ? 'error' : '' }}"
                            autocomplete="organization"
                        />
                        <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    @error('company_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Contact Person --}}
                <div class="field">
                    <label for="contact_person">Contact Person <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            id="contact_person"
                            name="contact_person"
                            value="{{ old('contact_person') }}"
                            placeholder="Full name"
                            class="{{ $errors->has('contact_person') ? 'error' : '' }}"
                            autocomplete="name"
                        />
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    @error('contact_person')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="field">
                    <label for="email">Email Address <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="you@institution.edu"
                            class="{{ $errors->has('email') ? 'error' : '' }}"
                            autocomplete="email"
                        />
                        <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Subdomain --}}
                <div class="field">
                    <label for="subdomain">Subdomain <span class="req">✦</span></label>
                    <div class="input-wrap">
                        <input
                            type="text"
                            id="subdomain"
                            name="subdomain"
                            value="{{ old('subdomain') }}"
                            placeholder="westbrook"
                            class="{{ $errors->has('subdomain') ? 'error' : '' }}"
                            autocomplete="off"
                            spellcheck="false"
                            oninput="updateSubdomainHint(this.value)"
                        />
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <span class="subdomain-hint" id="subdomain-hint">
                        Your URL: <span id="subdomain-preview">yourname</span>.yourdomain.com
                    </span>
                    @error('subdomain')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="field">
                    <label for="phone">Phone Number</label>
                    <div class="input-wrap">
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="+1 555 000 0000"
                            class="{{ $errors->has('phone') ? 'error' : '' }}"
                            autocomplete="tel"
                        />
                        <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.43 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 15.92z"/></svg>
                    </div>
                    @error('phone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="divider"></div>

                {{-- Plan --}}
                <div class="field full">
                    <label>Subscription Plan <span class="req">✦</span></label>
                    <div class="plan-grid">

                        <label class="plan-label">
                            <input type="radio" name="plan" value="basic"
                                {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Basic</span>
                                <span class="plan-desc">Up to 50 users.<br />Core features.</span>
                            </div>
                        </label>

                        <label class="plan-label">
                            <input type="radio" name="plan" value="standard"
                                {{ old('plan') === 'standard' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Standard</span>
                                <span class="plan-desc">Up to 200 users.<br />Advanced tools.</span>
                            </div>
                        </label>

                        <label class="plan-label">
                            <input type="radio" name="plan" value="premium"
                                {{ old('plan') === 'premium' ? 'checked' : '' }} />
                            <div class="plan-card">
                                <span class="plan-name">Premium</span>
                                <span class="plan-desc">Unlimited users.<br />Full suite + SLA.</span>
                            </div>
                        </label>

                    </div>
                    @error('plan')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="submit-row">
                    <p class="footnote">
                        By submitting, you agree to our terms of service.<br />
                        We'll review your request within 24 hours.
                    </p>
                    <button type="submit" class="btn-submit">
                        Submit Request
                        <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>

                <div class="login-link">
                    Already registered? <a href="{{ route('login') }}">Sign in here</a>
                </div>

            </div>{{-- /.form-grid --}}
        </form>
    </main>

    <script>
        // Live subdomain preview
        function updateSubdomainHint(val) {
            const preview = document.getElementById('subdomain-preview');
            preview.textContent = val.trim() || 'yourname';
        }

        // Pre-populate from old input if subdomain exists
        const existingSubdomain = document.getElementById('subdomain').value;
        if (existingSubdomain) updateSubdomainHint(existingSubdomain);
    </script>
</body>
</html>