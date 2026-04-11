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
$currentDomain = $tenant->domains->first()?->domain;
$currentStatus = $tenant->status ?? 'active';
$currentPlan   = $tenant->plan ?? '';
@endphp

{{-- Centered container --}}
<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / {{ $tenant->id }} / Edit
        </span>
    </div>


    {{-- Main edit card --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div>
                <div class="card-title-main">Edit Tenant</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Tenant ID is immutable. Domain, status, and plan can be changed.
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

            {{-- Tenant ID (read-only) --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Tenant ID</label>
                <input type="text" value="{{ $tenant->id }}" disabled
                       class="form-input" style="opacity:0.35;cursor:not-allowed;">
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
                        @foreach(['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $value => $label)
                            <option value="{{ $value }}" {{ old('plan', $currentPlan) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('plan')<div class="form-error">{{ $message }}</div>@enderror
                    <div id="plan-badge" style="margin-top:6px;min-height:18px;font-family:'DM Mono',monospace;font-size:10px;"></div>
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
        Setting status to <strong>active</strong> allows the tenant to access the system. Setting it to <strong>inactive</strong> disables access without deleting any data.
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
</style>
@endpush

@push('scripts')
<script>
(function() {
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

    const planSelect = document.getElementById('planSelect');
    const planBadge  = document.getElementById('plan-badge');
    const planCfg = {
        '':        { text: '' },
        basic:     { text: 'Basic plan selected',    color: 'rgba(100,170,240,0.7)' },
        standard:  { text: 'Standard plan selected', color: 'rgba(200,100,90,0.8)' },
        premium:   { text: 'Premium plan selected',  color: 'rgba(210,170,70,0.8)' },
    };
    function updatePlan(val) {
        const c = planCfg[val] || {};
        planBadge.innerHTML = c.text ? `<span style="color:${c.color};">// ${c.text}</span>` : '';
    }
    planSelect.addEventListener('change', e => updatePlan(e.target.value));
    updatePlan(planSelect.value);
})();
</script>
@endpush