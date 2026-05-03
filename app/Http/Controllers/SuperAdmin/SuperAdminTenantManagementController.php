@extends('layouts.superadmin-app')
@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

@php $baseDomain = config('app.base_domain', 'localhost'); @endphp

<div style="max-width:780px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    {{-- Eyebrow --}}
    <div class="ct-fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:ct-flicker 8s ease-in-out infinite;flex-shrink:0;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / Provision New
        </span>
    </div>

    {{-- Page heading --}}
    <div class="ct-fade-up ct-fade-up-1" style="margin-bottom:4px;">
        <div style="font-family:'Playfair Display',serif;font-weight:900;font-size:clamp(1.6rem,2.5vw,2.2rem);line-height:1.1;letter-spacing:-0.02em;color:var(--text);">
            Provision Tenant
        </div>
        <div style="font-size:13px;font-weight:300;color:var(--muted);margin-top:6px;line-height:1.7;">
            Creates an isolated database, runs tenant migrations, registers
            <strong style="color:var(--text2);">*.{{ $baseDomain }}</strong>
            subdomain, and seeds the admin account automatically.
        </div>
    </div>

    {{-- Main card --}}
    <div class="card ct-fade-up ct-fade-up-2" style="overflow:hidden;">

        {{-- Card header --}}
        <div class="card-header" style="border-bottom:1px solid var(--border);padding:20px 28px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:3px;height:28px;background:var(--crimson);flex-shrink:0;"></div>
                <div>
                    <div class="card-title-main">New Tenant</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">
                        // Isolated DB · auto-migrations · domain routing · admin seeding
                    </div>
                </div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.06);font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(140,14,3,0.8);">
                <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:ct-flicker 4s ease-in-out infinite;"></span>
                Provision
            </span>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.store') }}" style="padding:28px;">
            @csrf

            {{-- Global errors --}}
            @if ($errors->any())
            <div style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;border:1px solid rgba(140,14,3,0.3);border-left:3px solid var(--crimson);background:rgba(140,14,3,0.06);margin-bottom:24px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--crimson);flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <strong style="display:block;margin-bottom:6px;font-size:12px;letter-spacing:0.05em;text-transform:uppercase;color:var(--crimson);">Please fix the following:</strong>
                    <ul style="padding-left:1rem;font-size:13px;font-weight:300;color:var(--muted);line-height:1.8;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- ── SECTION: Tenant Identity ── --}}
            <div class="ct-section-label"><span>Tenant Identity</span></div>

            <div class="ct-grid" style="margin-bottom:24px;">

                <div class="ct-field">
                    <label class="ct-label">Tenant ID <span class="ct-req">✦</span></label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                        <input type="text" name="id" id="tenant_id"
                               value="{{ old('id') }}"
                               placeholder="e.g. bukidnon-state-u"
                               class="ct-input {{ $errors->has('id') ? 'ct-input-error' : '' }}"
                               oninput="syncTenantId(this.value)">
                    </div>
                    <span class="ct-hint">// Lowercase letters, numbers, hyphens only.</span>
                    @error('id')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="ct-field">
                    <label class="ct-label">Subdomain <span class="ct-req">✦</span></label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        <input type="text" id="subdomain_input" name="subdomain"
                               value="{{ old('subdomain') }}"
                               placeholder="e.g. buksu"
                               class="ct-input {{ $errors->has('subdomain') ? 'ct-input-error' : '' }}"
                               autocomplete="off" spellcheck="false"
                               oninput="updateSubdomainHint(this.value)">
                    </div>

                    {{-- Live domain preview --}}
                    <div style="margin-top:6px;padding:8px 12px;background:rgba(45,212,191,0.05);border:1px solid rgba(45,212,191,0.15);display:flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:var(--teal-color);flex-shrink:0;animation:ct-pulse 2s ease-in-out infinite;"></span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            Tenant URL:
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;">
                            <strong id="subdomain-preview" style="color:var(--teal-color);">{{ old('subdomain', 'yoursubdomain') }}</strong><span style="color:var(--muted);">.{{ $baseDomain }}</span>
                        </span>
                    </div>

                    @error('subdomain')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

            </div>

            {{-- ── SECTION: Institution Info ── --}}
            <div class="ct-section-label"><span>Institution Info</span></div>

            <div class="ct-grid" style="margin-bottom:24px;">

                <div class="ct-field ct-full">
                    <label class="ct-label">Institution / Company Name</label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <input type="text" name="company_name"
                               value="{{ old('company_name') }}"
                               placeholder="e.g. Bukidnon State University"
                               class="ct-input {{ $errors->has('company_name') ? 'ct-input-error' : '' }}">
                    </div>
                    @error('company_name')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

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

            {{-- ── SECTION: Subscription Plan ── --}}
            <div class="ct-section-label"><span>Subscription Plan</span></div>

            <div style="margin-bottom:24px;">
                <label class="ct-label" style="display:block;margin-bottom:10px;">Choose a Plan <span class="ct-req">✦</span></label>
                <div class="ct-plan-grid">
                    @foreach([
                        ['basic',    'Basic',    '₱10k', 'Up to 50 users. Core OJT features.'],
                        ['standard', 'Standard', '₱20k', 'Up to 200 users. Advanced tools.'],
                        ['premium',  'Premium',  '₱30k', 'Unlimited users. Full suite + SLA.'],
                    ] as [$val, $name, $price, $desc])
                    <label class="ct-plan-label">
                        <input type="radio" name="plan" value="{{ $val }}"
                               {{ old('plan', 'basic') === $val ? 'checked' : '' }}>
                        <div class="ct-plan-card">
                            <span class="ct-plan-name">{{ $name }}</span>
                            <span class="ct-plan-price">{{ $price }}</span>
                            <span class="ct-plan-period">per year</span>
                            <span class="ct-plan-desc">{{ $desc }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('plan')<span class="ct-error-msg" style="margin-top:6px;display:block;">{{ $message }}</span>@enderror
            </div>

            {{-- ── SECTION: Admin Account ── --}}
            <div class="ct-section-label"><span>Admin Account</span></div>

            <div class="ct-grid" style="margin-bottom:24px;">

                <div class="ct-field">
                    <label class="ct-label">Admin Name <span class="ct-req">✦</span></label>
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
                    @error('admin_email')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="ct-field">
                    <label class="ct-label">Password <span class="ct-req">✦</span></label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" name="admin_password"
                               placeholder="Min. 8 characters"
                               class="ct-input {{ $errors->has('admin_password') ? 'ct-input-error' : '' }}">
                    </div>
                    @error('admin_password')<span class="ct-error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="ct-field">
                    <label class="ct-label">Confirm Password <span class="ct-req">✦</span></label>
                    <div class="ct-input-wrap">
                        <svg class="ct-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input type="password" name="admin_password_confirmation"
                               placeholder="Re-enter password"
                               class="ct-input">
                    </div>
                </div>

            </div>

            {{-- Divider --}}
            <div style="height:1px;background:var(--border);margin:4px 0 24px;"></div>

            {{-- Domain Summary Box --}}
            <div style="padding:14px 16px;background:rgba(45,212,191,0.04);border:1px solid rgba(45,212,191,0.15);margin-bottom:24px;">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                    Summary — What will be created:
                </div>
                <div style="display:flex;flex-direction:column;gap:5px;font-size:12px;color:var(--text2);">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span>Isolated tenant database</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span>Tenant migrations run automatically</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span>Domain registered:
                            <strong id="summary-domain" style="font-family:'DM Mono',monospace;color:var(--teal-color);">
                                {{ old('subdomain', 'yoursubdomain') }}.{{ $baseDomain }}
                            </strong>
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="color:var(--teal-color);">✓</span>
                        <span>Admin account seeded into tenant DB</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <p style="font-size:12px;font-weight:300;color:var(--muted);line-height:1.7;">
                    Fields marked <span style="color:var(--crimson);">✦</span> are required.<br>
                    Tenant DB will be provisioned immediately on submit.
                </p>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="ct-btn-submit">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Create Tenant
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Info note --}}
    <div class="flash flash-info ct-fade-up ct-fade-up-3">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>
            On creation: Isolated DB is provisioned → tenant migrations run →
            domain <strong>subdomain.{{ $baseDomain }}</strong> registered →
            admin seeded into tenant DB → tenant is immediately accessible.
        </span>
    </div>

</div>

@endsection

@push('styles')
<style>
@keyframes ct-flicker { 0%,100%{opacity:1} 92%{opacity:1} 93%{opacity:0.4} 94%{opacity:1} 96%{opacity:0.6} 97%{opacity:1} }
@keyframes ct-fadeUp { from{opacity:0;transform:translateY(18px)} to{opacity:1;transform:translateY(0)} }
@keyframes ct-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }

.ct-fade-up   { animation: ct-fadeUp .5s ease both; }
.ct-fade-up-1 { animation: ct-fadeUp .5s .08s ease both; }
.ct-fade-up-2 { animation: ct-fadeUp .5s .16s ease both; }
.ct-fade-up-3 { animation: ct-fadeUp .5s .24s ease both; }

.ct-section-label {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
}
.ct-section-label::before { content:''; width:20px; height:2px; background:var(--crimson); flex-shrink:0; }
.ct-section-label::after  { content:''; flex:1; height:1px; background:var(--border); }
.ct-section-label span {
    font-family:'Barlow Condensed',sans-serif;
    font-size:10px; font-weight:600;
    letter-spacing:0.22em; text-transform:uppercase;
    color:var(--muted);
}

.ct-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 20px;
}
@media (max-width:600px) { .ct-grid { grid-template-columns: 1fr; } }

.ct-field { display:flex; flex-direction:column; gap:5px; }
.ct-full  { grid-column: 1 / -1; }

.ct-label {
    font-family:'Barlow Condensed',sans-serif;
    font-size:10px; font-weight:600;
    letter-spacing:0.2em; text-transform:uppercase;
    color:var(--muted);
}
.ct-req { color:var(--crimson); margin-left:2px; }

.ct-input-wrap { position:relative; }
.ct-icon {
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:14px; height:14px;
    stroke:rgba(171,171,171,0.2); fill:none; stroke-width:1.5;
    pointer-events:none; transition:stroke .2s;
}
.ct-input-wrap:focus-within .ct-icon { stroke:rgba(140,14,3,0.6); }

.ct-input {
    width:100%; height:44px;
    padding:0 1rem 0 2.4rem;
    border:1px solid var(--border);
    background:var(--surface2);
    border-radius:0;
    font-family:'Barlow',sans-serif;
    font-size:14px; font-weight:300;
    color:var(--text);
    outline:none;
    transition:border-color .2s, box-shadow .2s, background .2s;
    appearance:none;
}
.ct-input::placeholder { color:rgba(171,171,171,0.18); }
.ct-input:focus {
    border-color:rgba(140,14,3,0.7);
    background:var(--surface3);
    box-shadow:0 0 0 3px rgba(140,14,3,0.1);
}
.ct-input-error {
    border-color:rgba(140,14,3,0.5) !important;
    background:rgba(140,14,3,0.04) !important;
}

.ct-hint {
    font-family:'DM Mono',monospace;
    font-size:11px;
    color:var(--muted);
    line-height:1.5;
}
.ct-error-msg {
    font-family:'DM Mono',monospace;
    font-size:11px;
    color:var(--crimson);
}

.ct-plan-grid {
    display:grid; grid-template-columns:repeat(3,1fr);
    gap:1px; background:var(--border);
}
@media (max-width:560px) { .ct-plan-grid { grid-template-columns:1fr; } }

.ct-plan-label { position:relative; cursor:pointer; }
.ct-plan-label input[type="radio"] { position:absolute; opacity:0; pointer-events:none; }

.ct-plan-card {
    background:var(--surface2);
    padding:16px 12px; text-align:center;
    border:1.5px solid transparent;
    transition:border-color .2s, background .2s, box-shadow .2s;
    position:relative;
}
.ct-plan-card::before {
    content:''; position:absolute;
    top:0; left:0; right:0; height:2px;
    background:var(--crimson);
    transform:scaleX(0); transition:transform .25s;
}
.ct-plan-label:hover .ct-plan-card {
    background:var(--surface3);
    border-color:var(--border2);
}
.ct-plan-label input:checked ~ .ct-plan-card {
    border-color:rgba(140,14,3,0.45);
    background:rgba(140,14,3,0.07);
    box-shadow:0 0 0 3px rgba(140,14,3,0.08) inset;
}
.ct-plan-label input:checked ~ .ct-plan-card::before { transform:scaleX(1); }

.ct-plan-name {
    display:block;
    font-family:'Barlow Condensed',sans-serif;
    font-size:11px; font-weight:700;
    letter-spacing:0.15em; text-transform:uppercase;
    color:var(--muted); margin-bottom:4px;
    transition:color .2s;
}
.ct-plan-label input:checked ~ .ct-plan-card .ct-plan-name { color:var(--crimson); }

.ct-plan-price {
    display:block;
    font-family:'Playfair Display',serif;
    font-weight:900; font-size:1.4rem;
    color:var(--text); line-height:1; margin-bottom:2px;
}
.ct-plan-period {
    display:block;
    font-family:'DM Mono',monospace;
    font-size:9px; letter-spacing:.1em; text-transform:uppercase;
    color:var(--muted); margin-bottom:6px;
}
.ct-plan-desc { font-size:11px; font-weight:300; color:var(--muted); line-height:1.5; }

.ct-btn-submit {
    display:inline-flex; align-items:center; gap:8px;
    padding:0 20px; height:40px;
    background:var(--crimson);
    color:rgba(255,255,255,0.92);
    border:none; cursor:pointer;
    font-family:'Barlow Condensed',sans-serif;
    font-weight:700; font-size:13px;
    letter-spacing:0.12em; text-transform:uppercase;
    white-space:nowrap;
    transition:background .2s, transform .15s, box-shadow .2s;
}
.ct-btn-submit:hover {
    background:#a81004;
    transform:translateY(-1px);
    box-shadow:0 6px 24px rgba(140,14,3,0.35);
}
.ct-btn-submit:active { transform:scale(0.98); }
</style>
@endpush

@push('scripts')
<script>
const BASE_DOMAIN = '{{ $baseDomain }}';

function updateSubdomainHint(val) {
    const slug    = (val || '').trim().toLowerCase().replace(/[^a-z0-9\-]/g, '') || 'yoursubdomain';
    const preview = document.getElementById('subdomain-preview');
    const summary = document.getElementById('summary-domain');
    if (preview) preview.textContent = slug;
    if (summary) summary.textContent = slug + '.' + BASE_DOMAIN;
}

function syncTenantId(val) {
    const subdomain = document.getElementById('subdomain_input');
    if (subdomain && !subdomain.dataset.manuallyEdited) {
        subdomain.value = val;
        updateSubdomainHint(val);
    }
}

document.getElementById('subdomain_input')?.addEventListener('input', function() {
    this.dataset.manuallyEdited = 'true';
    updateSubdomainHint(this.value);
});

// Init on page load
const existingSubdomain = document.getElementById('subdomain_input');
if (existingSubdomain?.value) updateSubdomainHint(existingSubdomain.value);
</script>
@endpush