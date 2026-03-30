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

{{-- Centered container --}}
<div style="max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / New
        </span>
    </div>

    {{-- Main card --}}
    <div class="card fade-up fade-up-1">

        {{-- Card header --}}
        <div class="card-header">
            <div>
                <div class="card-title-main">New Tenant</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Isolated database · auto-migrations · domain routing · admin seeding
                </div>
            </div>
            <span style="display:inline-flex;align-items:center;padding:2px 8px;border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.06);
                         font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(140,14,3,0.8);">
                Provision
            </span>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.store') }}" style="padding:24px;">
            @csrf

            {{-- Global errors --}}
            @if ($errors->any())
            <div class="flash flash-error" style="margin-bottom:20px;">
                <div>
                    <strong style="display:block;margin-bottom:6px;">Please fix the following:</strong>
                    <ul style="padding-left:1rem;">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- ── Tenant Identity ── --}}
            <div class="form-section-divider">
                <span>Tenant Identity</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

                <div>
                    <label class="form-label">Tenant ID <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" name="id" id="tenant_id"
                           value="{{ old('id') }}"
                           placeholder="e.g. bukidnon-state-u"
                           class="form-input {{ $errors->has('id') ? 'is-invalid' : '' }}">
                    <div class="form-hint">// Lowercase, numbers, hyphens only.</div>
                    @error('id')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Subdomain <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" id="subdomain" name="subdomain"
                           value="{{ old('subdomain') }}"
                           placeholder="yourdomain"
                           class="form-input {{ $errors->has('subdomain') ? 'is-invalid' : '' }}"
                           autocomplete="off" spellcheck="false"
                           oninput="updateSubdomainHint(this.value)">
                    <div class="form-hint">URL: <strong id="subdomain-preview" style="color:var(--text2);">yourdomain</strong>.ojtconnect.com</div>
                    <input type="hidden" name="domain" id="domain-hidden" value="{{ old('domain') }}">
                    @error('subdomain')<div class="form-error">{{ $message }}</div>@enderror
                </div>

            </div>

            {{-- ── Plan ── --}}
            <div class="form-section-divider" style="margin-top:4px;">
                <span>Subscription Plan</span>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Choose a Plan <span style="color:var(--crimson);">✦</span></label>
                <div class="plan-grid">
                    @foreach([
                        ['basic',    'Basic',    '₱10k', 'Up to 50 users. Core OJT features.'],
                        ['standard', 'Standard', '₱20k', 'Up to 200 users. Advanced tools.'],
                        ['premium',  'Premium',  '₱30k', 'Unlimited users. Full suite + SLA.'],
                    ] as [$val, $name, $price, $desc])
                    <label class="plan-label">
                        <input type="radio" name="plan" value="{{ $val }}"
                               {{ old('plan', 'basic') === $val ? 'checked' : '' }}>
                        <div class="plan-card">
                            <span class="plan-name">{{ $name }}</span>
                            <span class="plan-price">{{ $price }}</span>
                            <span class="plan-period">per year</span>
                            <span class="plan-desc">{{ $desc }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('plan')<div class="form-error" style="margin-top:6px;">{{ $message }}</div>@enderror
            </div>

            <div class="form-divider"></div>

            {{-- ── Admin Account ── --}}
            <div class="form-section-divider">
                <span>Admin Account</span>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

                <div>
                    <label class="form-label">Admin Name <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                           placeholder="Juan dela Cruz"
                           class="form-input {{ $errors->has('admin_name') ? 'is-invalid' : '' }}">
                    @error('admin_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Admin Email <span style="color:var(--crimson);">✦</span></label>
                    <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                           placeholder="admin@institution.edu.ph"
                           class="form-input {{ $errors->has('admin_email') ? 'is-invalid' : '' }}">
                    @error('admin_email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Password <span style="color:var(--crimson);">✦</span></label>
                    <input type="password" name="admin_password"
                           placeholder="Min. 8 characters"
                           class="form-input {{ $errors->has('admin_password') ? 'is-invalid' : '' }}">
                    @error('admin_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="form-label">Confirm Password <span style="color:var(--crimson);">✦</span></label>
                    <input type="password" name="admin_password_confirmation"
                           placeholder="Re-enter password"
                           class="form-input">
                </div>

            </div>

            <div class="form-divider"></div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Create Tenant
                    </button>
                </div>
            </div>

        </form>
    </div>

    {{-- Info note --}}
    <div class="flash flash-info fade-up fade-up-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>On creation: Isolated DB is provisioned → tenant migrations run → domain registered for routing → admin seeded into tenant DB.</span>
    </div>

</div>

@endsection

@push('styles')
<style>
.form-section-divider {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
}
.form-section-divider::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
.form-section-divider::after  { content: ''; flex: 1; height: 1px; background: var(--border); }
.form-section-divider span {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 10px; font-weight: 600;
    letter-spacing: 0.22em; text-transform: uppercase;
    color: var(--muted);
}
.form-divider {
    height: 1px; background: var(--border); margin: 4px 0 20px;
}

/* Plan cards */
.plan-grid {
    display: grid; grid-template-columns: repeat(3,1fr);
    gap: 1px; background: var(--border);
}
@media (max-width: 580px) { .plan-grid { grid-template-columns: 1fr; } }

.plan-label { position: relative; cursor: pointer; }
.plan-label input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }
.plan-card {
    background: var(--surface2);
    padding: 14px 12px; text-align: center;
    border: 1.5px solid transparent;
    transition: border-color .2s, background .2s;
    position: relative;
}
.plan-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 2px;
    background: var(--crimson);
    transform: scaleX(0); transition: transform .25s;
}
.plan-label:hover .plan-card { background: var(--surface3); border-color: var(--border2); }
.plan-label input:checked ~ .plan-card {
    border-color: rgba(140,14,3,0.45);
    background: rgba(140,14,3,0.07);
}
.plan-label input:checked ~ .plan-card::before { transform: scaleX(1); }

.plan-name {
    display: block;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 11px; font-weight: 700;
    letter-spacing: 0.15em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 4px;
    transition: color .2s;
}
.plan-label input:checked ~ .plan-card .plan-name { color: var(--crimson); }
.plan-price {
    display: block;
    font-family: 'Playfair Display', serif;
    font-weight: 900; font-size: 1.3rem;
    color: var(--text); line-height: 1; margin-bottom: 2px;
}
.plan-period {
    display: block;
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: .1em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 6px;
}
.plan-desc { font-size: 11px; font-weight: 300; color: var(--muted); line-height: 1.5; }

.form-input { border-radius: 0; }
</style>
@endpush

@push('scripts')
<script>
function updateSubdomainHint(val) {
    const preview = document.getElementById('subdomain-preview');
    const hidden  = document.getElementById('domain-hidden');
    const slug    = val.trim() || 'yourdomain';
    if (preview) preview.textContent = slug;
    if (hidden)  hidden.value = slug !== 'yourdomain' ? slug + '.ojtconnect.edu.ph' : '';
}
const existingSubdomain = document.getElementById('subdomain');
if (existingSubdomain && existingSubdomain.value) updateSubdomainHint(existingSubdomain.value);
</script>
@endpush