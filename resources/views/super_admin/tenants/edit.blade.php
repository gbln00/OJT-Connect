@extends('layouts.superadmin-app')
@section('title', 'Edit Tenant — ' . $tenant->id)
@section('page-title', 'Edit Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

@php
$currentDomain  = $tenant->domains->first()?->domain;
$currentStatus  = $tenant->status ?? 'active';
$currentPlan    = $tenant->plan ?? '';
$expiresAt      = $tenant->plan_expires_at;
$daysLeft       = $tenant->daysUntilExpiry ?? null;
$inGrace        = $tenant->inGracePeriod ?? false;
$isExpired      = $tenant->subscriptionExpired ?? false;

// Urgency color for expiry badge
if ($expiresAt) {
    $daysCalc = (int) now()->startOfDay()->diffInDays($expiresAt, false);
    $expiryUrgency = $isExpired ? 'expired' : ($daysCalc <= 7 ? 'critical' : ($daysCalc <= 30 ? 'warning' : 'ok'));
} else {
    $expiryUrgency = 'none';
}
$urgencyColors = [
    'expired'  => ['bg'=>'rgba(239,68,68,0.12)',  'border'=>'rgba(239,68,68,0.4)',  'color'=>'#ef4444',  'label'=>'EXPIRED'],
    'critical' => ['bg'=>'rgba(239,68,68,0.08)',  'border'=>'rgba(239,68,68,0.35)', 'color'=>'#f87171',  'label'=>'CRITICAL'],
    'warning'  => ['bg'=>'rgba(245,158,11,0.1)',  'border'=>'rgba(245,158,11,0.35)','color'=>'#f59e0b',  'label'=>'DUE SOON'],
    'ok'       => ['bg'=>'rgba(52,211,153,0.08)', 'border'=>'rgba(52,211,153,0.3)', 'color'=>'#34d399',  'label'=>'OK'],
    'none'     => ['bg'=>'rgba(150,150,150,0.06)','border'=>'rgba(150,150,150,0.2)','color'=>'var(--muted)','label'=>'NOT SET'],
];
$uc = $urgencyColors[$expiryUrgency];
@endphp

<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / {{ $tenant->id }} / Edit
        </span>
    </div>

    {{-- Grace period warning if applicable --}}
    @if($inGrace)
    <div style="border:1px solid rgba(245,158,11,0.35);background:rgba(245,158,11,0.07);padding:12px 18px;display:flex;align-items:center;gap:10px;" class="fade-up">
        <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:#f59e0b;margin-bottom:2px;letter-spacing:0.08em;">GRACE PERIOD ACTIVE</div>
            <div style="font-size:12px;color:var(--text2);">
                Subscription expired {{ $expiresAt?->format('M d, Y') }}. Grace period ends {{ $expiresAt?->copy()?->addDays(7)?->format('M d, Y') }}. Extend or renew below.
            </div>
        </div>
    </div>
    @endif

    @if($isExpired && !$inGrace)
    <div style="border:1px solid rgba(239,68,68,0.4);background:rgba(239,68,68,0.08);padding:12px 18px;display:flex;align-items:center;gap:10px;" class="fade-up">
        <svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:#ef4444;margin-bottom:2px;letter-spacing:0.08em;">SUBSCRIPTION EXPIRED — ACCESS BLOCKED</div>
            <div style="font-size:12px;color:var(--text2);">
                Grace period ended. Tenant access is blocked. Set a new expiry date below to restore access.
            </div>
        </div>
    </div>
    @endif

    {{-- Main edit card --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div>
                <div class="card-title-main">Edit Tenant</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Tenant ID is immutable. Domain, status, plan, and subscription expiry can be changed.
                </div>
            </div>
            <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;border:1px solid var(--border2);
                         font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">
                <span style="width:4px;height:4px;border-radius:50%;background:var(--crimson);"></span>
                {{ $tenant->id }}
            </span>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}" style="padding:24px;">
            @csrf @method('PUT')
            <input type="hidden" name="redirect_to" value="edit">

            {{-- Errors --}}
            @if($errors->any())
            <div class="flash flash-error" style="margin-bottom:18px;">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif

            {{-- Tenant ID (read-only) --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Tenant ID</label>
                <input type="text" value="{{ $tenant->id }}" disabled class="form-input" style="opacity:0.35;cursor:not-allowed;">
                <div class="form-hint">// Cannot be changed after creation.</div>
            </div>

            {{-- Domain --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Domain</label>
                @if($currentDomain)
                <div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;padding:3px 10px;
                            border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.06);">
                    <span style="width:5px;height:5px;background:var(--crimson);border-radius:50%;display:inline-block;"></span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">current: {{ $currentDomain }}</span>
                </div>
                @endif
                <input type="text" name="domain"
                       value="{{ old('domain', $currentDomain) }}"
                       placeholder="{{ $currentDomain ?? 'e.g. school.localhost' }}"
                       class="form-input {{ $errors->has('domain') ? 'is-invalid' : '' }}">
                <div class="form-hint">// Full subdomain this tenant will be served on.</div>
                @error('domain')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="height:1px;background:var(--border);margin-bottom:18px;"></div>

            {{-- Status & Plan --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" id="statusSelect" class="form-select">
                        @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $currentStatus) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="form-error">{{ $message }}</div>@enderror
                    <div id="status-badge" style="margin-top:6px;min-height:18px;font-family:'DM Mono',monospace;font-size:10px;"></div>
                </div>
                <div>
                    <label class="form-label">Subscription Plan</label>
                    <select name="plan" id="planSelect" class="form-select">
                        <option value="" {{ old('plan', $currentPlan) === '' ? 'selected' : '' }}>— None —</option>
                        @foreach(\App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get() as $p)
                            <option value="{{ $p->name }}" {{ old('plan', $currentPlan) === $p->name ? 'selected' : '' }}>
                                {{ $p->label }} (₱{{ number_format($p->base_price) }} / {{ $p->billing_cycle }})
                            </option>
                        @endforeach
                        {{-- Include current plan even if inactive --}}
                        @if($currentPlan && !\App\Models\Plan::where('name',$currentPlan)->where('is_active',true)->exists())
                            <option value="{{ $currentPlan }}" selected>{{ ucfirst($currentPlan) }} (inactive plan)</option>
                        @endif
                    </select>
                    @error('plan')<div class="form-error">{{ $message }}</div>@enderror
                    <div id="plan-badge" style="margin-top:6px;min-height:18px;font-family:'DM Mono',monospace;font-size:10px;"></div>
                </div>
            </div>

            {{-- Subscription Expiry ── NEW SECTION ── --}}
            <div style="border:1px solid var(--border2);padding:16px 20px;margin-bottom:18px;position:relative;overflow:hidden;">
                {{-- top accent line --}}
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $uc['border'] }};opacity:0.6;"></div>

                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:8px;">
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">
                            Subscription Expiry (per-tenant)
                        </div>
                        <div style="font-size:12px;color:var(--text2);">Controls when this tenant's access auto-expires. Leave blank for no expiry.</div>
                    </div>
                    {{-- Current expiry status badge --}}
                    <div style="padding:6px 12px;border:1px solid {{ $uc['border'] }};background:{{ $uc['bg'] }};
                                font-family:'DM Mono',monospace;font-size:10px;color:{{ $uc['color'] }};text-align:right;">
                        <div style="letter-spacing:0.1em;text-transform:uppercase;font-size:9px;margin-bottom:2px;">{{ $uc['label'] }}</div>
                        @if($expiresAt)
                        <div style="font-size:12px;font-weight:600;">{{ $expiresAt->format('M d, Y') }}</div>
                        @php $dc = (int) now()->startOfDay()->diffInDays($expiresAt, false); @endphp
                        <div style="font-size:10px;opacity:0.8;margin-top:1px;">
                            {{ $dc > 0 ? $dc.' days remaining' : ($dc === 0 ? 'expires today' : abs($dc).' days overdue') }}
                        </div>
                        @else
                        <div style="font-size:11px;opacity:0.6;">No expiry set</div>
                        @endif
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr auto;gap:12px;align-items:flex-end;">
                    <div>
                        <label class="form-label">New Expiry Date</label>
                        <input type="date" name="plan_expires_at"
                               value="{{ old('plan_expires_at', $expiresAt?->format('Y-m-d')) }}"
                               class="form-input {{ $errors->has('plan_expires_at') ? 'is-invalid' : '' }}"
                               id="expiryDateInput"
                               oninput="updateExpiryPreview(this.value)">
                        @error('plan_expires_at')<div class="form-error">{{ $message }}</div>@enderror
                        <div class="form-hint">// Setting a new plan above auto-calculates expiry from billing cycle. Override here if needed.</div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        <label class="form-label" style="white-space:nowrap;">Quick extend</label>
                        <div style="display:flex;gap:4px;">
                            <button type="button" onclick="extendExpiry(30)"  class="btn btn-ghost btn-sm" style="font-family:'DM Mono',monospace;font-size:10px;">+30d</button>
                            <button type="button" onclick="extendExpiry(90)"  class="btn btn-ghost btn-sm" style="font-family:'DM Mono',monospace;font-size:10px;">+90d</button>
                            <button type="button" onclick="extendExpiry(365)" class="btn btn-ghost btn-sm" style="font-family:'DM Mono',monospace;font-size:10px;">+1yr</button>
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                <div id="expiry-preview" style="margin-top:10px;font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);display:none;">
                    New expiry: <span id="expiry-preview-date" style="color:var(--text);"></span>
                    · <span id="expiry-preview-days"></span>
                </div>

                {{-- Grace controls --}}
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="plan_grace" value="1" {{ old('plan_grace', $tenant->plan_grace) ? 'checked' : '' }} style="accent-color:var(--crimson);">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">Manually set grace period active</span>
                    </label>
                    @if($tenant->grace_started_at)
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                        Grace started: {{ $tenant->grace_started_at->format('M d, Y') }}
                        · Ends: {{ $tenant->grace_started_at->copy()->addDays(7)->format('M d, Y') }}
                    </span>
                    @endif
                </div>
            </div>

            <div style="height:1px;background:var(--border);margin-bottom:18px;"></div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Info note --}}
    <div class="flash flash-info fade-up fade-up-2">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span>
            When you <strong>change the plan</strong> the expiry auto-calculates from the billing cycle (monthly = 30 days, yearly = 365 days) unless you override it. 
            Setting <strong>status inactive</strong> disables access immediately regardless of expiry.
            The <strong>grace period</strong> (7 days after expiry) lets tenants still log in but shows a warning banner everywhere.
        </span>
    </div>

</div>

@endsection

@push('styles')
<style>
.form-select {
    width: 100%; padding: 9px 12px;
    background: var(--surface2);
    border: 1px solid var(--border2);
    color: var(--text); font-size: 13px;
    font-family: 'Barlow', sans-serif;
    outline: none; cursor: pointer;
    appearance: none; -webkit-appearance: none;
    transition: border-color 0.15s;
    border-radius: 0;
}
.form-select:focus { border-color: var(--crimson); }
.form-input { border-radius: 0; }
.form-hint {
    font-family: 'DM Mono', monospace;
    font-size: 10px; color: var(--muted);
    margin-top: 5px;
}
.form-error {
    font-family: 'DM Mono', monospace;
    font-size: 10px; color: #ef4444;
    margin-top: 4px;
}
.form-label {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted); margin-bottom: 6px;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    // ── Status badge ──
    const statusSelect = document.getElementById('statusSelect');
    const statusBadge  = document.getElementById('status-badge');
    const statusCfg = {
        active:   { dot: '#22c55e', glow: 'rgba(34,197,94,0.5)',  text: 'Tenant will be active',   color: '#34d399' },
        inactive: { dot: '#ef4444', glow: 'none',                  text: 'Tenant will be inactive', color: '#f87171' },
    };
    function updateStatus(val) {
        const c = statusCfg[val];
        if (!c) { statusBadge.innerHTML = ''; return; }
        statusBadge.innerHTML = `<span style="display:inline-flex;align-items:center;gap:5px;color:${c.color};">
            <span style="width:6px;height:6px;border-radius:50%;background:${c.dot};box-shadow:0 0 5px ${c.glow};display:inline-block;flex-shrink:0;"></span>
            ${c.text}
        </span>`;
    }
    statusSelect.addEventListener('change', e => updateStatus(e.target.value));
    updateStatus(statusSelect.value);

    // ── Plan badge ──
    const planSelect = document.getElementById('planSelect');
    const planBadge  = document.getElementById('plan-badge');
    const planCfg = {
        '':        { text: '' },
        basic:     { text: 'Basic plan · billing cycle auto-calculates expiry', color: 'rgba(100,170,240,0.7)' },
        standard:  { text: 'Standard plan · billing cycle auto-calculates expiry', color: 'rgba(200,100,90,0.8)' },
        premium:   { text: 'Premium plan · billing cycle auto-calculates expiry', color: 'rgba(210,170,70,0.8)' },
    };
    function updatePlan(val) {
        const c = planCfg[val] || { text: val ? `// ${val} plan selected`, color: 'var(--muted)' };
        planBadge.innerHTML = c.text ? `<span style="color:${c.color};">${c.text}</span>` : '';
    }
    planSelect.addEventListener('change', e => updatePlan(e.target.value));
    updatePlan(planSelect.value);
})();

// ── Expiry date helpers ──
const currentExpiryStr = '{{ $expiresAt ? $expiresAt->format("Y-m-d") : "" }}';

function updateExpiryPreview(val) {
    const preview = document.getElementById('expiry-preview');
    const dateEl  = document.getElementById('expiry-preview-date');
    const daysEl  = document.getElementById('expiry-preview-days');
    if (!val) { preview.style.display = 'none'; return; }
    const d    = new Date(val + 'T00:00:00');
    const now  = new Date(); now.setHours(0,0,0,0);
    const diff = Math.round((d - now) / 86400000);
    const opts = { year:'numeric', month:'short', day:'numeric' };
    dateEl.textContent = d.toLocaleDateString('en-US', opts);
    daysEl.textContent = diff > 0 ? `${diff} days from today` : (diff === 0 ? 'today' : `${Math.abs(diff)} days overdue`);
    daysEl.style.color = diff < 0 ? '#ef4444' : (diff <= 7 ? '#f59e0b' : '#34d399');
    preview.style.display = 'block';
}

function extendExpiry(days) {
    const input = document.getElementById('expiryDateInput');
    // Start from current expiry or today, whichever is later
    const base = (currentExpiryStr && new Date(currentExpiryStr) > new Date())
        ? new Date(currentExpiryStr + 'T00:00:00')
        : new Date();
    base.setDate(base.getDate() + days);
    const y = base.getFullYear();
    const m = String(base.getMonth()+1).padStart(2,'0');
    const d = String(base.getDate()).padStart(2,'0');
    input.value = `${y}-${m}-${d}`;
    updateExpiryPreview(input.value);
}

// Run preview on load if there's an existing value
const initExpiry = document.getElementById('expiryDateInput')?.value;
if (initExpiry) updateExpiryPreview(initExpiry);
</script>
@endpush