@extends('layouts.superadmin-app')
@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Tenants
    </a>
@endsection

@section('content')

@php $baseDomain = config('app.base_domain', 'ojt-connect.xyz'); @endphp

<div style="max-width:820px;margin:0 auto;display:flex;flex-direction:column;gap:20px;">

    {{-- Eyebrow --}}
    <div class="ct-fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;flex-shrink:0;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / New
        </span>
    </div>

    {{-- Page heading --}}
    <div class="ct-fade-up ct-fade-up-1">
        <div style="font-family:'Playfair Display',serif;font-weight:900;font-size:clamp(1.6rem,2.5vw,2.1rem);line-height:1.1;letter-spacing:-0.02em;color:var(--text);">
            Provision New Tenant
        </div>
        <div style="font-size:13px;font-weight:300;color:var(--muted);margin-top:6px;line-height:1.7;">
            Fill in all required fields below. The system will automatically create an isolated database,
            run migrations, register the subdomain, and seed the admin account.
        </div>
    </div>

    {{-- Global errors --}}
    @if ($errors->any())
    <div class="ct-fade-up" style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;border:1px solid rgba(140,14,3,0.3);border-left:3px solid var(--crimson);background:rgba(140,14,3,0.06);">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--crimson);flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <strong style="display:block;margin-bottom:6px;font-size:12px;letter-spacing:0.05em;text-transform:uppercase;color:var(--crimson);">Please fix the following:</strong>
            <ul style="padding-left:1rem;font-size:13px;font-weight:300;color:var(--muted);line-height:1.8;margin:0;">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('super_admin.tenants.store') }}" id="tenant-form">
        @csrf

        {{-- ══ STEP 1: Institution ══ --}}
        <div class="ct-card ct-fade-up ct-fade-up-2">
            <div class="ct-card-header">
                <div class="ct-step-badge">1</div>
                <div>
                    <div class="ct-card-title">Institution Details</div>
                    <div class="ct-card-sub">Basic info about the school or organization.</div>
                </div>
            </div>
            <div class="ct-card-body">

                {{-- Institution name (full width, generates slugs) --}}
                <div class="ct-field ct-full" style="margin-bottom:20px;">
                    <label class="ct-label">
                        Institution / School Name <span class="ct-req">✦</span>
                        <span class="ct-label-hint">— used to auto-generate the subdomain and ID below</span>
                    </label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <input type="text" name="company_name" id="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="e.g. Bukidnon State University"
                               class="ct-input {{ $errors->has('company_name') ? 'ct-input-error' : '' }}"
                               oninput="autoGenerateSlugs(this.value)">
                    </div>
                    @error('company_name')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="ct-grid-3" style="margin-bottom:0;">
                    <div class="ct-field">
                        <label class="ct-label">Contact Person</label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" name="contact_person"
                                   value="{{ old('contact_person') }}"
                                   placeholder="Full name"
                                   class="ct-input {{ $errors->has('contact_person') ? 'ct-input-error' : '' }}">
                        </div>
                        @error('contact_person')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="ct-field">
                        <label class="ct-label">Contact Email</label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" name="contact_email"
                                   value="{{ old('contact_email') }}"
                                   placeholder="contact@institution.edu.ph"
                                   class="ct-input {{ $errors->has('contact_email') ? 'ct-input-error' : '' }}">
                        </div>
                        @error('contact_email')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="ct-field">
                        <label class="ct-label">Phone Number</label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.43 2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 15.92z"/></svg>
                            <input type="tel" name="phone"
                                   value="{{ old('phone') }}"
                                   placeholder="+63 9XX XXX XXXX"
                                   class="ct-input {{ $errors->has('phone') ? 'ct-input-error' : '' }}">
                        </div>
                        @error('phone')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ STEP 2: Domain & Access ══ --}}
        <div class="ct-card ct-fade-up ct-fade-up-2" style="margin-top:16px;">
            <div class="ct-card-header">
                <div class="ct-step-badge">2</div>
                <div>
                    <div class="ct-card-title">Domain & System Access</div>
                    <div class="ct-card-sub">How this tenant is identified in the system and on the web.</div>
                </div>
            </div>
            <div class="ct-card-body">

                <div class="ct-grid" style="margin-bottom:16px;">

                    {{-- Subdomain --}}
                    <div class="ct-field">
                        <label class="ct-label">
                            Subdomain <span class="ct-req">✦</span>
                        </label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <input type="text" id="subdomain_input" name="subdomain"
                                   value="{{ old('subdomain') }}"
                                   placeholder="e.g. buksu"
                                   class="ct-input {{ $errors->has('subdomain') ? 'ct-input-error' : '' }}"
                                   autocomplete="off" spellcheck="false"
                                   oninput="this.dataset.manuallyEdited='true';updatePreview()">
                        </div>
                        <div class="ct-hint">Lowercase letters, numbers, hyphens only. Keep it short.</div>
                        @error('subdomain')<span class="ct-error-msg">{{ $message }}</span>@enderror

                        {{-- Live preview --}}
                        <div class="ct-domain-preview">
                            <span style="width:6px;height:6px;border-radius:50%;background:var(--teal-color);flex-shrink:0;animation:ct-pulse 2s ease-in-out infinite;"></span>
                            <span style="color:var(--muted);font-size:11px;">Tenant URL →</span>
                            <span style="font-size:11px;">
                                <strong id="subdomain-preview" style="color:var(--teal-color);">{{ old('subdomain', 'yoursubdomain') }}</strong><span style="color:var(--muted);">.{{ $baseDomain }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Tenant ID --}}
                    <div class="ct-field">
                        <label class="ct-label">
                            Tenant ID <span class="ct-req">✦</span>
                        </label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                            <input type="text" name="id" id="tenant_id"
                                   value="{{ old('id') }}"
                                   placeholder="e.g. bukidnon-state-u"
                                   class="ct-input {{ $errors->has('id') ? 'ct-input-error' : '' }}"
                                   oninput="this.dataset.manuallyEdited='true'">
                        </div>
                        <div class="ct-hint">Internal unique key used by the system. Usually matches the subdomain.</div>
                        @error('id')<span class="ct-error-msg">{{ $message }}</span>@enderror

                        {{-- Clarification box --}}
                        <div style="margin-top:8px;padding:10px 12px;background:rgba(96,165,250,0.05);border:1px solid rgba(96,165,250,0.15);border-left:2px solid rgba(96,165,250,0.4);">
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(147,197,253,0.8);line-height:1.6;">
                                <strong style="display:block;margin-bottom:2px;letter-spacing:0.08em;">SUBDOMAIN vs TENANT ID</strong>
                                <span style="opacity:0.8;">
                                    <strong>Subdomain</strong> = the URL prefix (what users type in the browser).<br>
                                    <strong>Tenant ID</strong> = the internal database key (used behind the scenes).<br>
                                    They can be different but keeping them the same avoids confusion.
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Summary strip --}}
                <div id="domain-summary" style="padding:12px 16px;background:rgba(45,212,191,0.04);border:1px solid rgba(45,212,191,0.15);display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span style="font-size:12px;color:var(--muted);">URL:</span>
                        <strong id="summary-url" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--teal-color);">yoursubdomain.{{ $baseDomain }}</strong>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span style="font-size:12px;color:var(--muted);">Isolated DB created automatically</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span style="font-size:12px;color:var(--muted);">Migrations run automatically</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ STEP 3: Subscription Plan ══ --}}
        <div class="ct-card ct-fade-up ct-fade-up-2" style="margin-top:16px;">
            <div class="ct-card-header">
                <div class="ct-step-badge">3</div>
                <div>
                    <div class="ct-card-title">Subscription Plan</div>
                    <div class="ct-card-sub">Choose the plan and set an optional expiry date.</div>
                </div>
            </div>
            <div class="ct-card-body">

                {{-- Plan cards --}}
                <div class="ct-plan-grid" style="margin-bottom:20px;">

                    {{-- BASIC --}}
                    <label class="ct-plan-label">
                        <input type="radio" name="plan" value="basic" {{ old('plan', 'basic') === 'basic' ? 'checked' : '' }}>
                        <div class="ct-plan-card">
                            <div class="ct-plan-top">
                                <span class="ct-plan-name">Basic</span>
                                <span class="ct-plan-badge" style="border-color:rgba(96,165,250,0.3);color:rgba(147,197,253,0.8);background:rgba(96,165,250,0.06);">50 students</span>
                            </div>
                            <div class="ct-plan-price">₱10,000</div>
                            <div class="ct-plan-period">per year</div>
                            <ul class="ct-feature-list">
                                <li class="on">Hour log monitoring</li>
                                <li class="on">QR clock-in</li>
                                <li class="on">Email notifications</li>
                                <li class="on">Support tickets</li>
                                <li class="off">Weekly reports</li>
                                <li class="off">Student evaluations</li>
                                <li class="off">PDF / Excel exports</li>
                            </ul>
                        </div>
                    </label>

                    {{-- STANDARD --}}
                    <label class="ct-plan-label">
                        <input type="radio" name="plan" value="standard" {{ old('plan') === 'standard' ? 'checked' : '' }}>
                        <div class="ct-plan-card">
                            <div class="ct-plan-top">
                                <span class="ct-plan-name">Standard</span>
                                <span class="ct-plan-badge" style="border-color:rgba(140,14,3,0.3);color:rgba(200,100,90,0.9);background:rgba(140,14,3,0.06);">150 students</span>
                            </div>
                            <div class="ct-plan-price">₱20,000</div>
                            <div class="ct-plan-period">per year</div>
                            <ul class="ct-feature-list">
                                <li class="on">Everything in Basic</li>
                                <li class="on">Weekly reports</li>
                                <li class="on">Student evaluations</li>
                                <li class="on">CSV bulk import</li>
                                <li class="on">2FA authentication</li>
                                <li class="off">PDF / Excel exports</li>
                                <li class="off">Analytics dashboard</li>
                            </ul>
                        </div>
                    </label>

                    {{-- PREMIUM --}}
                    <label class="ct-plan-label">
                        <input type="radio" name="plan" value="premium" {{ old('plan') === 'premium' ? 'checked' : '' }}>
                        <div class="ct-plan-card">
                            <div class="ct-plan-top">
                                <span class="ct-plan-name">Premium</span>
                                <span class="ct-plan-badge" style="border-color:rgba(201,168,76,0.35);color:rgba(210,170,70,0.9);background:rgba(201,168,76,0.07);">Unlimited</span>
                            </div>
                            <div class="ct-plan-price">₱30,000</div>
                            <div class="ct-plan-period">per year</div>
                            <ul class="ct-feature-list">
                                <li class="on">Everything in Standard</li>
                                <li class="on">PDF & Excel exports</li>
                                <li class="on">Analytics dashboard</li>
                                <li class="on">Tenant branding</li>
                                <li class="on">OJT certificates</li>
                                <li class="on">Unlimited students</li>
                                <li class="on">Priority support</li>
                            </ul>
                        </div>
                    </label>

                </div>

                {{-- Plan expiry --}}
                <div class="ct-field" style="max-width:280px;">
                    <label class="ct-label">
                        Plan Expiry Date
                        <span class="ct-label-hint">— optional, leave blank for no expiry</span>
                    </label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="plan_expires_at"
                               value="{{ old('plan_expires_at') }}"
                               class="ct-input"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    <div class="ct-hint">If set, the tenant subscription will expire on this date.</div>
                </div>

                @error('plan')<span class="ct-error-msg" style="display:block;margin-top:6px;">{{ $message }}</span>@enderror

            </div>
        </div>

        {{-- ══ STEP 4: Admin Account ══ --}}
        <div class="ct-card ct-fade-up ct-fade-up-3" style="margin-top:16px;">
            <div class="ct-card-header">
                <div class="ct-step-badge">4</div>
                <div>
                    <div class="ct-card-title">Admin Account</div>
                    <div class="ct-card-sub">This person will have full access to manage the tenant's OJT system.</div>
                </div>
            </div>
            <div class="ct-card-body">

                <div class="ct-grid" style="margin-bottom:16px;">
                    <div class="ct-field">
                        <label class="ct-label">Admin Full Name <span class="ct-req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input type="text" name="admin_name"
                                   value="{{ old('admin_name') }}"
                                   placeholder="Juan dela Cruz"
                                   class="ct-input {{ $errors->has('admin_name') ? 'ct-input-error' : '' }}">
                        </div>
                        @error('admin_name')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="ct-field">
                        <label class="ct-label">Admin Email <span class="ct-req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <input type="email" name="admin_email"
                                   value="{{ old('admin_email') }}"
                                   placeholder="admin@institution.edu.ph"
                                   class="ct-input {{ $errors->has('admin_email') ? 'ct-input-error' : '' }}">
                        </div>
                        <div class="ct-hint">This will be the login email for the tenant admin.</div>
                        @error('admin_email')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="ct-grid">
                    <div class="ct-field">
                        <label class="ct-label">Password <span class="ct-req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" name="admin_password" id="admin_password"
                                   placeholder="Min. 8 characters"
                                   class="ct-input {{ $errors->has('admin_password') ? 'ct-input-error' : '' }}"
                                   oninput="checkPasswordStrength(this.value)">
                        </div>
                        {{-- Password strength bar --}}
                        <div style="margin-top:6px;">
                            <div style="height:3px;background:var(--border);overflow:hidden;">
                                <div id="pw-strength-bar" style="height:100%;width:0%;transition:width .3s,background .3s;"></div>
                            </div>
                            <div id="pw-strength-label" style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:4px;"></div>
                        </div>
                        @error('admin_password')<span class="ct-error-msg">{{ $message }}</span>@enderror
                    </div>
                    <div class="ct-field">
                        <label class="ct-label">Confirm Password <span class="ct-req">✦</span></label>
                        <div class="ct-input-wrap">
                            <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <input type="password" name="admin_password_confirmation" id="admin_password_confirmation"
                                   placeholder="Re-enter password"
                                   class="ct-input"
                                   oninput="checkPasswordMatch()">
                        </div>
                        <div id="pw-match-label" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:4px;"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ══ Submit Bar ══ --}}
        <div class="ct-fade-up ct-fade-up-3" style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:20px 24px;background:var(--surface2);border:1px solid var(--border);margin-top:4px;">
            <div style="font-size:12px;font-weight:300;color:var(--muted);line-height:1.7;">
                Fields marked <span style="color:var(--crimson);">✦</span> are required.
                Tenant database is provisioned immediately on submit — this may take a few seconds.
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="ct-btn-submit" id="submit-btn">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                    </svg>
                    <span id="submit-label">Create Tenant</span>
                </button>
            </div>
        </div>

    </form>

</div>

@endsection

@push('styles')
<style>
@keyframes ct-fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
@keyframes ct-pulse  { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }

.ct-fade-up   { animation: ct-fadeUp .45s ease both; }
.ct-fade-up-1 { animation: ct-fadeUp .45s .06s ease both; }
.ct-fade-up-2 { animation: ct-fadeUp .45s .12s ease both; }
.ct-fade-up-3 { animation: ct-fadeUp .45s .18s ease both; }

/* ── Card ── */
.ct-card {
    background: var(--surface);
    border: 1px solid var(--border);
    overflow: hidden;
}
.ct-card-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    background: var(--surface2);
}
.ct-card-body { padding: 24px; }
.ct-card-title {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 15px; font-weight: 700;
    letter-spacing: 0.06em; text-transform: uppercase;
    color: var(--text);
}
.ct-card-sub {
    font-size: 12px; font-weight: 300;
    color: var(--muted); margin-top: 2px;
}
.ct-step-badge {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--crimson);
    color: rgba(255,255,255,0.9);
    font-family: 'DM Mono', monospace;
    font-size: 12px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}

/* ── Grid ── */
.ct-grid   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 20px; }
.ct-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px 20px; }
.ct-full   { grid-column: 1 / -1; }
@media (max-width: 640px) {
    .ct-grid, .ct-grid-3 { grid-template-columns: 1fr; }
}

/* ── Fields ── */
.ct-field { display: flex; flex-direction: column; gap: 5px; }
.ct-label {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.18em; text-transform: uppercase;
    color: var(--muted);
}
.ct-label-hint {
    font-weight: 300; font-size: 10px;
    text-transform: none; letter-spacing: 0;
    color: var(--muted); opacity: 0.7;
}
.ct-req { color: var(--crimson); margin-left: 2px; }
.ct-hint { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); line-height: 1.5; }
.ct-error-msg { font-family: 'DM Mono', monospace; font-size: 11px; color: var(--crimson); }

.ct-input-wrap { position: relative; }
.ct-icon {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    width: 13px; height: 13px;
    stroke: rgba(171,171,171,0.25); fill: none; stroke-width: 1.5;
    pointer-events: none; transition: stroke .2s;
}
.ct-input-wrap:focus-within .ct-icon { stroke: rgba(140,14,3,0.55); }

.ct-input {
    width: 100%; height: 44px;
    padding: 0 1rem 0 2.4rem;
    border: 1px solid var(--border);
    background: var(--surface2);
    border-radius: 0;
    font-family: 'Barlow', sans-serif;
    font-size: 14px; font-weight: 300;
    color: var(--text);
    outline: none;
    transition: border-color .2s, box-shadow .2s, background .2s;
    appearance: none;
}
.ct-input::placeholder { color: rgba(171,171,171,0.2); }
.ct-input:focus {
    border-color: rgba(140,14,3,0.6);
    background: var(--surface3, var(--surface));
    box-shadow: 0 0 0 3px rgba(140,14,3,0.08);
}
.ct-input-error {
    border-color: rgba(140,14,3,0.5) !important;
    background: rgba(140,14,3,0.04) !important;
}
input[type="date"].ct-input { padding-left: 2.4rem; }

.ct-domain-preview {
    margin-top: 8px;
    padding: 8px 12px;
    background: rgba(45,212,191,0.04);
    border: 1px solid rgba(45,212,191,0.15);
    display: flex; align-items: center; gap: 8px;
    font-family: 'DM Mono', monospace;
}

/* ── Plan Cards ── */
.ct-plan-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--border);
}
@media (max-width: 580px) { .ct-plan-grid { grid-template-columns: 1fr; } }

.ct-plan-label { position: relative; cursor: pointer; display: block; }
.ct-plan-label input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }

.ct-plan-card {
    background: var(--surface2);
    padding: 18px 16px;
    border: 1.5px solid transparent;
    transition: border-color .2s, background .2s, box-shadow .2s;
    position: relative;
    height: 100%;
    box-sizing: border-box;
}
.ct-plan-card::after {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: var(--crimson);
    transform: scaleX(0); transition: transform .25s;
}
.ct-plan-label:hover .ct-plan-card { background: var(--surface3, var(--surface)); border-color: var(--border2); }
.ct-plan-label input:checked ~ .ct-plan-card {
    border-color: rgba(140,14,3,0.4);
    background: rgba(140,14,3,0.06);
    box-shadow: 0 0 0 3px rgba(140,14,3,0.06) inset;
}
.ct-plan-label input:checked ~ .ct-plan-card::after { transform: scaleX(1); }

.ct-plan-top {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.ct-plan-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; text-transform: uppercase;
    color: var(--muted); transition: color .2s;
}
.ct-plan-label input:checked ~ .ct-plan-card .ct-plan-name { color: var(--crimson); }
.ct-plan-badge {
    font-family: 'DM Mono', monospace;
    font-size: 9px; padding: 2px 7px;
    border: 1px solid; border-radius: 0;
    letter-spacing: 0.05em;
}
.ct-plan-price {
    font-family: 'Playfair Display', serif;
    font-weight: 900; font-size: 1.5rem;
    color: var(--text); line-height: 1; margin-bottom: 2px;
}
.ct-plan-period {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 14px;
    display: block;
}
.ct-feature-list {
    list-style: none; margin: 0; padding: 0;
    display: flex; flex-direction: column; gap: 5px;
}
.ct-feature-list li {
    font-size: 12px; font-weight: 300;
    color: var(--muted);
    display: flex; align-items: center; gap: 7px;
    padding: 3px 0;
    border-bottom: 1px solid var(--border);
}
.ct-feature-list li:last-child { border-bottom: none; }
.ct-feature-list li::before {
    content: ''; display: inline-block;
    width: 14px; height: 14px;
    flex-shrink: 0; border-radius: 50%;
    background: rgba(52,211,153,0.12);
    border: 1px solid rgba(52,211,153,0.3);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%2334d399' stroke-width='3'%3E%3Cpolyline points='20,6 9,17 4,12'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    background-size: 8px;
}
.ct-feature-list li.off { opacity: 0.4; }
.ct-feature-list li.off::before {
    background: transparent;
    border-color: rgba(171,171,171,0.2);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23888' stroke-width='2.5'%3E%3Cline x1='18' y1='6' x2='6' y2='18'/%3E%3Cline x1='6' y1='6' x2='18' y2='18'/%3E%3C/svg%3E");
}

/* ── Submit ── */
.ct-btn-submit {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 0 24px; height: 42px;
    background: var(--crimson);
    color: rgba(255,255,255,0.92);
    border: none; cursor: pointer;
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 700; font-size: 13px;
    letter-spacing: 0.12em; text-transform: uppercase;
    white-space: nowrap;
    transition: background .2s, transform .15s, box-shadow .2s;
}
.ct-btn-submit:hover { background: #a81004; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(140,14,3,0.3); }
.ct-btn-submit:active { transform: scale(0.98); }
.ct-btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }
</style>
@endpush

@push('scripts')
<script>
const BASE_DOMAIN = '{{ $baseDomain }}';

function slugify(val) {
    return (val || '')
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s\-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .substring(0, 30);
}

function updatePreview() {
    const subdomain = document.getElementById('subdomain_input');
    const preview   = document.getElementById('subdomain-preview');
    const summaryUrl = document.getElementById('summary-url');
    const slug = slugify(subdomain?.value) || 'yoursubdomain';
    if (preview)   preview.textContent   = slug;
    if (summaryUrl) summaryUrl.textContent = slug + '.' + BASE_DOMAIN;
}

function autoGenerateSlugs(val) {
    const slug = slugify(val);

    const subdomainInput = document.getElementById('subdomain_input');
    const tenantIdInput  = document.getElementById('tenant_id');

    // Only auto-fill if user hasn't manually edited them
    if (subdomainInput && !subdomainInput.dataset.manuallyEdited) {
        subdomainInput.value = slug;
        updatePreview();
    }
    if (tenantIdInput && !tenantIdInput.dataset.manuallyEdited) {
        tenantIdInput.value = slug;
    }
}

// Password strength
function checkPasswordStrength(val) {
    const bar   = document.getElementById('pw-strength-bar');
    const label = document.getElementById('pw-strength-label');
    if (!bar || !label) return;

    let score = 0;
    if (val.length >= 8)  score++;
    if (val.length >= 12) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%',   color: 'transparent', text: '' },
        { pct: '25%',  color: '#ef4444',      text: 'Weak' },
        { pct: '50%',  color: '#f59e0b',      text: 'Fair' },
        { pct: '75%',  color: '#60a5fa',      text: 'Good' },
        { pct: '100%', color: '#34d399',      text: 'Strong' },
    ];

    const lvl = levels[Math.min(score, 4)];
    bar.style.width     = lvl.pct;
    bar.style.background = lvl.color;
    label.textContent   = lvl.text;
    label.style.color   = lvl.color;

    checkPasswordMatch();
}

// Password match
function checkPasswordMatch() {
    const pw   = document.getElementById('admin_password')?.value;
    const conf = document.getElementById('admin_password_confirmation')?.value;
    const lbl  = document.getElementById('pw-match-label');
    if (!lbl || !conf) return;

    if (!conf) { lbl.textContent = ''; return; }
    if (pw === conf) {
        lbl.textContent = '✓ Passwords match';
        lbl.style.color = '#34d399';
    } else {
        lbl.textContent = '✗ Passwords do not match';
        lbl.style.color = '#ef4444';
    }
}

// Prevent double-submit
document.getElementById('tenant-form')?.addEventListener('submit', function(e) {
    const btn   = document.getElementById('submit-btn');
    const label = document.getElementById('submit-label');
    if (btn && label) {
        btn.disabled  = true;
        label.textContent = 'Creating...';
    }
});

// Init preview on load (in case of old() values)
updatePreview();
</script>
@endpush