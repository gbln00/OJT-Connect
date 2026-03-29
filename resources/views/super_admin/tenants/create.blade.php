@extends('layouts.superadmin-app')
@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;
              background:transparent;border:1px solid rgba(171,171,171,0.15);
              color:rgba(171,171,171,0.55);font-family:'Barlow Condensed',sans-serif;
              font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;
              text-decoration:none;transition:all .15s;"
       onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
       onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.55)'">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap');

    :root {
        --crimson:  #8C0E03;
        --crimson2: #a81004;
        --void:     #0D0D0D;
        --night:    #0E1126;
        --steel:    #333740;
        --ash:      #ABABAB;
    }

    .create-tenant-wrap * { box-sizing: border-box; }

    .create-tenant-wrap {
        font-family: 'Barlow', sans-serif;
    }

    /* Section label */
    .ct-section-label {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 1.25rem;
    }
    .ct-section-label::before {
        content: '';
        width: 20px;
        height: 2px;
        background: var(--crimson);
        flex-shrink: 0;
    }
    .ct-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: rgba(171,171,171,0.07);
    }
    .ct-section-label span {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: rgba(171,171,171,0.28);
    }

    /* Inputs */
    .ct-input {
        width: 100%;
        height: 46px;
        padding: 0 1rem;
        border: 1px solid rgba(171,171,171,0.10);
        background: rgba(13,13,13,0.8);
        color: rgba(171,171,171,0.85);
        font-family: 'Barlow', sans-serif;
        font-size: 14px;
        font-weight: 300;
        outline: none;
        border-radius: 0;
        transition: border-color .2s, box-shadow .2s, background .2s;
        -webkit-appearance: none;
        appearance: none;
    }
    .ct-input::placeholder { color: rgba(171,171,171,0.18); }
    .ct-input:focus {
        border-color: rgba(140,14,3,0.7);
        background: rgba(14,17,38,0.85);
        box-shadow: 0 0 0 3px rgba(140,14,3,0.1);
    }
    .ct-input.has-icon { padding-left: 2.5rem; }
    .ct-input.error {
        border-color: rgba(140,14,3,0.5);
        background: rgba(140,14,3,0.04);
    }

    /* Input wrapper */
    .ct-input-wrap { position: relative; }
    .ct-input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 15px;
        height: 15px;
        stroke: rgba(171,171,171,0.2);
        fill: none;
        stroke-width: 1.5;
        pointer-events: none;
        transition: stroke .2s;
    }
    .ct-input-wrap:focus-within .ct-input-icon { stroke: rgba(140,14,3,0.6); }

    /* Password input */
    .ct-input[type="password"] { padding-left: 2.5rem; }

    /* Label */
    .ct-label {
        display: block;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgba(171,171,171,0.4);
        margin-bottom: 0.45rem;
    }
    .ct-label .req { color: var(--crimson); margin-left: .2rem; }

    /* Hint */
    .ct-hint {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: rgba(171,171,171,0.25);
        margin-top: 0.35rem;
        line-height: 1.6;
    }
    .ct-hint strong { color: rgba(171,171,171,0.55); font-weight: 500; }

    /* Field error */
    .ct-field-error {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--crimson);
        margin-top: 0.3rem;
    }

    /* Animations */
    @keyframes ctFadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes ctFlicker {
        0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:0.4} 94%{opacity:1} 96%{opacity:0.6} 97%{opacity:1}
    }

    .ct-card { animation: ctFadeUp .5s .1s ease both; }
    .ct-info  { animation: ctFadeUp .5s .25s ease both; }

    .ct-eyebrow-dot {
        width: 6px; height: 6px;
        background: var(--crimson);
        animation: ctFlicker 8s ease-in-out infinite;
    }

    /* Submit button */
    .ct-btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 0 1.75rem;
        height: 46px;
        background: var(--crimson);
        color: rgba(255,255,255,0.92);
        border: none;
        cursor: pointer;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        transition: background .2s, transform .15s, box-shadow .2s;
        white-space: nowrap;
    }
    .ct-btn-submit:hover {
        background: var(--crimson2);
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(140,14,3,0.35);
    }
    .ct-btn-submit:active { transform: scale(0.98); }
    .ct-btn-submit svg { transition: transform .2s; }
    .ct-btn-submit:hover svg { transform: translateX(3px); }

    /* Cancel button */
    .ct-btn-cancel {
        display: inline-flex;
        align-items: center;
        padding: 0 1.25rem;
        height: 46px;
        border: 1px solid rgba(171,171,171,0.12);
        background: transparent;
        color: rgba(171,171,171,0.45);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-decoration: none;
        transition: border-color .2s, color .2s, background .2s;
    }
    .ct-btn-cancel:hover {
        border-color: rgba(171,171,171,0.25);
        color: rgba(171,171,171,0.7);
        background: rgba(171,171,171,0.04);
    }

    /* Plan cards */
    .ct-plan-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1px;
        background: rgba(171,171,171,0.06);
    }
    @media (max-width: 580px) {
        .ct-plan-grid { grid-template-columns: 1fr; }
    }

    .ct-plan-label { position: relative; cursor: pointer; }
    .ct-plan-label input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
    .ct-plan-card {
        background: var(--void);
        padding: 1.1rem .9rem;
        text-align: center;
        border: 1.5px solid transparent;
        transition: border-color .2s, background .2s, box-shadow .2s;
        position: relative;
    }
    .ct-plan-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
        background: var(--crimson);
        transform: scaleX(0);
        transition: transform .25s;
    }
    .ct-plan-label:hover .ct-plan-card {
        background: rgba(14,17,38,0.6);
        border-color: rgba(171,171,171,0.1);
    }
    .ct-plan-label input:checked ~ .ct-plan-card {
        border-color: rgba(140,14,3,0.45);
        background: rgba(140,14,3,0.07);
        box-shadow: 0 0 0 3px rgba(140,14,3,0.08) inset;
    }
    .ct-plan-label input:checked ~ .ct-plan-card::before { transform: scaleX(1); }
    .ct-plan-name {
        display: block;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.15em; text-transform: uppercase;
        color: rgba(171,171,171,0.5);
        margin-bottom: .3rem;
        transition: color .2s;
    }
    .ct-plan-label input:checked ~ .ct-plan-card .ct-plan-name { color: var(--crimson); }
    .ct-plan-price {
        display: block;
        font-family: 'Playfair Display', serif;
        font-weight: 900; font-size: 1.4rem;
        color: #fff; line-height: 1;
        margin-bottom: .25rem;
    }
    .ct-plan-period {
        display: block;
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: .1em;
        color: rgba(171,171,171,0.2);
        text-transform: uppercase;
        margin-bottom: .5rem;
    }
    .ct-plan-desc {
        font-size: 11px; font-weight: 300;
        color: rgba(171,171,171,0.3); line-height: 1.5;
    }
</style>

<div class="create-tenant-wrap flex flex-col items-center justify-center w-full py-8 px-4">

    {{-- Outer container --}}
    <div class="w-full max-w-2xl flex flex-col gap-3">

        {{-- Page eyebrow --}}
        <div class="flex items-center gap-2.5 mb-1" style="animation: ctFadeUp .4s ease both;">
            <div class="ct-eyebrow-dot"></div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);">
                Super Admin / Tenants / New
            </span>
        </div>

        {{-- Main card --}}
        <div class="ct-card" style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;">

            {{-- Card header --}}
            <div style="padding:24px 28px 20px;border-bottom:1px solid rgba(171,171,171,0.06);">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:6px;">
                    <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                    <span style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#fff;letter-spacing:-0.01em;">
                        New Tenant
                    </span>
                    <div style="margin-left:auto;padding:2px 8px;border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.06);
                                font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(140,14,3,0.7);">
                        Provision
                    </div>
                </div>
                <p style="font-family:'DM Mono',monospace;font-size:12px;color:rgba(171,171,171,0.25);line-height:1.7;margin:0;">
                    // Isolated database · auto-migrations · domain routing · admin seeding
                </p>
            </div>

            <form method="POST" action="{{ route('super_admin.tenants.store') }}" style="padding:28px;">
                @csrf

                {{-- Global errors --}}
                @if ($errors->any())
                <div style="padding:14px 18px;border:1px solid rgba(140,14,3,0.3);border-left:3px solid #8C0E03;
                            background:rgba(140,14,3,0.06);margin-bottom:24px;">
                    <div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                                letter-spacing:0.15em;text-transform:uppercase;color:rgba(220,100,90,0.8);margin-bottom:8px;">
                        Please fix the following
                    </div>
                    <ul style="padding-left:1rem;font-family:'DM Mono',monospace;font-size:12px;color:rgba(171,171,171,0.5);line-height:1.8;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- ── SECTION: Tenant Identity ── --}}
                <div class="ct-section-label"><span>Tenant Identity</span></div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;margin-bottom:1.5rem;">

                    {{-- Tenant ID --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="tenant_id">Tenant ID <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                            </svg>
                            <input type="text" id="tenant_id" name="id"
                                   value="{{ old('id') }}"
                                   placeholder="e.g. bukidnon-state-u"
                                   class="ct-input has-icon {{ $errors->has('id') ? 'error' : '' }}">
                        </div>
                        <span class="ct-hint">// Lowercase, numbers, hyphens only.</span>
                        @error('id')<span class="ct-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Subdomain / Domain --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="domain">Domain <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
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
                                class="ct-input has-icon {{ $errors->has('subdomain') ? 'error' : '' }}"
                                autocomplete="off"
                                spellcheck="false"
                                oninput="updateSubdomainHint(this.value)"
                            />
                        </div>
                        <span class="ct-hint">
                            URL: <strong id="subdomain-preview">yourdomain</strong>.ojtconnect.edu.ph
                        </span>
                        @error('subdomain')<span class="ct-field-error">{{ $message }}</span>@enderror
                        {{-- Hidden domain field populated by JS --}}
                        <input type="hidden" name="domain" id="domain-hidden" value="{{ old('domain') }}">
                    </div>

                </div>

                {{-- ── SECTION: Plan ── --}}
                <div class="ct-section-label" style="margin-top:.5rem;"><span>Subscription Plan</span></div>

                <div style="margin-bottom:1.5rem;">
                    <label class="ct-label">Choose a Plan <span class="req">✦</span></label>
                    <div class="ct-plan-grid">
                        <label class="ct-plan-label">
                            <input type="radio" name="plan" value="basic"
                                {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }} />
                            <div class="ct-plan-card">
                                <span class="ct-plan-name">Basic</span>
                                <span class="ct-plan-price">₱10k</span>
                                <span class="ct-plan-period">per year</span>
                                <span class="ct-plan-desc">Up to 50 users.<br>Core OJT features.</span>
                            </div>
                        </label>
                        <label class="ct-plan-label">
                            <input type="radio" name="plan" value="standard"
                                {{ old('plan') === 'standard' ? 'checked' : '' }} />
                            <div class="ct-plan-card">
                                <span class="ct-plan-name">Standard</span>
                                <span class="ct-plan-price">₱20k</span>
                                <span class="ct-plan-period">per year</span>
                                <span class="ct-plan-desc">Up to 200 users.<br>Advanced tools.</span>
                            </div>
                        </label>
                        <label class="ct-plan-label">
                            <input type="radio" name="plan" value="premium"
                                {{ old('plan') === 'premium' ? 'checked' : '' }} />
                            <div class="ct-plan-card">
                                <span class="ct-plan-name">Premium</span>
                                <span class="ct-plan-price">₱30k</span>
                                <span class="ct-plan-period">per year</span>
                                <span class="ct-plan-desc">Unlimited users.<br>Full suite + SLA.</span>
                            </div>
                        </label>
                    </div>
                    @error('plan')<span class="ct-field-error" style="display:block;margin-top:.4rem;">{{ $message }}</span>@enderror
                </div>

                {{-- Divider --}}
                <div style="height:1px;background:rgba(171,171,171,0.06);margin:0.25rem 0 1.5rem;"></div>

                {{-- ── SECTION: Admin Account ── --}}
                <div class="ct-section-label"><span>Admin Account</span></div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem 1.25rem;margin-bottom:1.5rem;">

                    {{-- Admin Name --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="admin_name">Admin Name <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            <input type="text" id="admin_name" name="admin_name"
                                   value="{{ old('admin_name') }}"
                                   placeholder="Juan dela Cruz"
                                   class="ct-input has-icon {{ $errors->has('admin_name') ? 'error' : '' }}">
                        </div>
                        @error('admin_name')<span class="ct-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Admin Email --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="admin_email">Admin Email <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <input type="email" id="admin_email" name="admin_email"
                                   value="{{ old('admin_email') }}"
                                   placeholder="admin@institution.edu.ph"
                                   class="ct-input has-icon {{ $errors->has('admin_email') ? 'error' : '' }}">
                        </div>
                        @error('admin_email')<span class="ct-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Admin Password --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="admin_password">Password <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                            <input type="password" id="admin_password" name="admin_password"
                                   placeholder="Min. 8 characters"
                                   class="ct-input has-icon {{ $errors->has('admin_password') ? 'error' : '' }}">
                        </div>
                        @error('admin_password')<span class="ct-field-error">{{ $message }}</span>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div style="display:flex;flex-direction:column;gap:.4rem;">
                        <label class="ct-label" for="admin_password_confirmation">Confirm Password <span class="req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-input-icon" viewBox="0 0 24 24">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                            <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                                   placeholder="Re-enter password"
                                   class="ct-input has-icon">
                        </div>
                    </div>

                </div>

                {{-- Divider --}}
                <div style="height:1px;background:rgba(171,171,171,0.06);margin:0 0 1.5rem;"></div>

                {{-- Actions --}}
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                    <p style="font-family:'DM Mono',monospace;font-size:11px;color:rgba(171,171,171,0.25);line-height:1.7;">
                        Fields marked <span style="color:#8C0E03;">✦</span> are required.
                    </p>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <a href="{{ route('super_admin.tenants.index') }}" class="ct-btn-cancel">Cancel</a>
                        <button type="submit" class="ct-btn-submit">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <line x1="12" y1="4" x2="12" y2="20"/>
                                <line x1="4" y1="12" x2="20" y2="12"/>
                            </svg>
                            Create Tenant
                        </button>
                    </div>
                </div>

            </form>
        </div>

        {{-- Info note --}}
        <div class="ct-info" style="padding:14px 20px;border:1px solid rgba(140,14,3,0.15);background:rgba(140,14,3,0.04);">
            <div style="display:flex;align-items:flex-start;gap:10px;">
                <svg width="14" height="14" fill="none" stroke="rgba(140,14,3,0.6)" stroke-width="1.5" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:2px;">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <p style="font-family:'DM Mono',monospace;font-size:11px;color:rgba(171,171,171,0.35);line-height:1.8;margin:0;">
                    <span style="color:rgba(200,90,80,0.6);font-weight:700;">On creation:</span>
                    Isolated DB is provisioned → tenant migrations run → domain registered for routing → admin seeded into tenant DB.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
    function updateSubdomainHint(val) {
        const preview = document.getElementById('subdomain-preview');
        const hidden  = document.getElementById('domain-hidden');
        const slug    = val.trim() || 'yourdomain';
        if (preview) preview.textContent = slug;
        if (hidden)  hidden.value = slug !== 'yourdomain' ? slug + '.ojtconnect.edu.ph' : '';
    }

    // Pre-populate on load
    const existingSubdomain = document.getElementById('subdomain');
    if (existingSubdomain && existingSubdomain.value) {
        updateSubdomainHint(existingSubdomain.value);
    }
</script>
@endsection