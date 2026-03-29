@extends('layouts.superadmin-app')
@section('title', 'Edit Tenant — ' . $tenant->id)
@section('page-title', 'Edit Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.show', $tenant) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid rgba(171,171,171,0.15);
              background:transparent;color:rgba(171,171,171,0.6);font-size:12px;font-weight:700;
              letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

@php
$inputStyle  = "width:100%;padding:10px 14px;background:#0D0D0D;border:1px solid rgba(171,171,171,0.12);
                color:#fff;font-size:14px;font-family:'Barlow',sans-serif;outline:none;
                transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box;";
$selectStyle = "width:100%;padding:10px 14px;background:#0D0D0D;border:1px solid rgba(171,171,171,0.12);
                color:#fff;font-size:14px;font-family:'Barlow',sans-serif;outline:none;
                appearance:none;-webkit-appearance:none;cursor:pointer;
                transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box;";
$labelStyle  = "display:block;font-size:10px;color:rgba(171,171,171,0.4);letter-spacing:0.18em;
                text-transform:uppercase;font-family:monospace;margin-bottom:8px;";
$hintStyle   = "font-size:11px;color:rgba(171,171,171,0.2);margin-top:6px;font-family:monospace;";
$errorStyle  = "font-size:12px;color:rgba(220,100,90,0.8);margin-top:6px;font-family:monospace;";
$focusOn     = "this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'";
$focusOff    = "this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'";

$currentDomain = $tenant->domains->first()?->domain;
$currentStatus = $tenant->status ?? 'active';
$currentPlan   = $tenant->plan ?? '';

$planColors = [
    'basic'   => 'rgba(80,150,220,0.8)',
    'standard'     => 'rgba(200,100,90,0.9)',
    'premium' => 'rgba(210,170,70,0.9)',
];
@endphp

<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- ── Main Edit Card ── --}}
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:28px;">

        {{-- Card header --}}
        <div style="margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Edit Tenant</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;">
                // Tenant ID is immutable. Domain, status, and plan can be changed.
            </div>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}">
            @csrf @method('PUT')

            {{-- Tenant ID (read-only) --}}
            <div style="margin-bottom:20px;">
                <label style="{{ $labelStyle }}">Tenant ID</label>
                <input type="text" value="{{ $tenant->id }}" disabled
                       style="{{ $inputStyle }} opacity:0.35;cursor:not-allowed;">
                <div style="{{ $hintStyle }}">// Cannot be changed after creation.</div>
            </div>

            {{-- Domain --}}
            <div style="margin-bottom:20px;">
                <label style="{{ $labelStyle }}">Domain</label>

                {{-- Current domain pill --}}
                @if($currentDomain)
                <div style="display:inline-flex;align-items:center;gap:6px;margin-bottom:10px;padding:4px 10px;
                            border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.06);">
                    <span style="width:5px;height:5px;background:#8C0E03;border-radius:50%;flex-shrink:0;display:inline-block;
                                 box-shadow:0 0 5px rgba(140,14,3,0.6);"></span>
                    <span style="font-size:11px;color:rgba(200,100,90,0.7);font-family:monospace;letter-spacing:0.04em;">
                        current: {{ $currentDomain }}
                    </span>
                </div>
                @endif

                <input type="text" name="domain"
                       value="{{ old('domain', $currentDomain) }}"
                       placeholder="{{ $currentDomain ?? 'e.g. school.ojtconnect.com' }}"
                       style="{{ $inputStyle }}"
                       onfocus="{{ $focusOn }}" onblur="{{ $focusOff }}"
                       autofocus>
                <div style="{{ $hintStyle }}">// Full subdomain this tenant will be served on.</div>
                @error('domain')
                    <div style="{{ $errorStyle }}">{{ $message }}</div>
                @enderror
            </div>

            <div style="height:1px;background:rgba(171,171,171,0.06);margin-bottom:20px;"></div>

            {{-- Status & Plan side by side --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">

                {{-- Status --}}
                <div>
                    <label style="{{ $labelStyle }}">Status</label>
                    <div style="position:relative;">
                        <select name="status" id="statusSelect"
                                style="{{ $selectStyle }}"
                                onfocus="{{ $focusOn }}" onblur="{{ $focusOff }}">
                            @foreach(['active' => 'Active', 'inactive' => 'Inactive'] as $value => $label)
                                <option value="{{ $value }}"
                                        style="background:#0D0D0D;"
                                        {{ old('status', $currentStatus) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;opacity:0.4;"
                             width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    @error('status')
                        <div style="{{ $errorStyle }}">{{ $message }}</div>
                    @enderror
                    {{-- Live badge --}}
                    <div id="status-badge"
                         style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;
                                font-size:10px;font-family:monospace;letter-spacing:0.1em;min-height:18px;">
                    </div>
                </div>

                {{-- Plan --}}
                <div>
                    <label style="{{ $labelStyle }}">Subscription Plan</label>
                    <div style="position:relative;">
                        <select name="plan" id="planSelect"
                                style="{{ $selectStyle }}"
                                onfocus="{{ $focusOn }}" onblur="{{ $focusOff }}">
                            <option value="" style="background:#0D0D0D;"
                                    {{ old('plan', $currentPlan) === '' ? 'selected' : '' }}>
                                — None —
                            </option>
                            @foreach(['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $value => $label)
                                <option value="{{ $value }}"
                                        style="background:#0D0D0D;"
                                        {{ old('plan', $currentPlan) === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;opacity:0.4;"
                             width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                    @error('plan')
                        <div style="{{ $errorStyle }}">{{ $message }}</div>
                    @enderror
                    {{-- Live plan badge --}}
                    <div id="plan-badge"
                         style="margin-top:8px;display:inline-flex;align-items:center;gap:6px;
                                font-size:10px;font-family:monospace;letter-spacing:0.1em;min-height:18px;">
                    </div>
                </div>

            </div>

            <div style="height:1px;background:rgba(171,171,171,0.06);margin-bottom:20px;"></div>

            {{-- Actions --}}
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.show', $tenant) }}"
                   style="display:inline-flex;align-items:center;padding:9px 20px;border:1px solid rgba(171,171,171,0.15);
                          background:transparent;color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;
                          letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;
                          transition:border-color 0.2s,color 0.2s;"
                   onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                   onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                    Cancel
                </a>
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;
                               background:#8C0E03;color:rgba(255,255,255,0.92);border:none;cursor:pointer;
                               font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;
                               font-family:'Barlow Condensed',sans-serif;transition:background 0.2s,box-shadow 0.2s;"
                        onmouseover="this.style.background='#a81004';this.style.boxShadow='0 6px 24px rgba(140,14,3,0.35)'"
                        onmouseout="this.style.background='#8C0E03';this.style.boxShadow='none'">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
                        <polyline points="17,21 17,13 7,13 7,21"/>
                        <polyline points="7,3 7,8 15,8"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- ── Info strip ── --}}
    <div style="padding:14px 20px;border:1px solid rgba(140,14,3,0.15);background:rgba(140,14,3,0.03);
                font-size:12px;color:rgba(171,171,171,0.35);line-height:1.8;font-family:monospace;">
        <span style="color:rgba(200,90,80,0.6);font-weight:700;">// note:</span>
        Setting status to <span style="color:rgba(74,222,128,0.6);">active</span> allows the tenant to access the system.
        Setting it to <span style="color:rgba(252,165,165,0.6);">inactive</span> disables their access without deleting any data.
    </div>

</div>

<script>
(function () {
    // ── Status badge ──
    const statusSelect = document.getElementById('statusSelect');
    const statusBadge  = document.getElementById('status-badge');

    const statusConfigs = {
        active:   { dot: '#22c55e', glow: 'rgba(34,197,94,0.5)',  text: 'Tenant will be active',   color: 'rgba(74,222,128,0.65)'  },
        inactive: { dot: '#ef4444', glow: 'rgba(239,68,68,0)',     text: 'Tenant will be inactive', color: 'rgba(252,165,165,0.6)'  },
    };

    function updateStatus(val) {
        const c = statusConfigs[val] || {};
        statusBadge.innerHTML = c.dot
            ? `<span style="width:6px;height:6px;border-radius:50%;background:${c.dot};
                            box-shadow:0 0 6px ${c.glow};flex-shrink:0;display:inline-block;"></span>
               <span style="color:${c.color};">${c.text}</span>`
            : '';
    }

    statusSelect.addEventListener('change', e => updateStatus(e.target.value));
    updateStatus(statusSelect.value);

    // ── Plan badge ──
    const planSelect = document.getElementById('planSelect');
    const planBadge  = document.getElementById('plan-badge');

    const planConfigs = {
        '':        { text: '',          color: '' },
        basic:     { text: 'Basic plan selected',   color: 'rgba(100,170,240,0.7)' },
        standard:  { text: 'Standard plan selected', color: 'rgba(200,100,90,0.8)'  },
        premium:   { text: 'Premium plan selected', color: 'rgba(210,170,70,0.8)'  },
    };

    function updatePlan(val) {
        const c = planConfigs[val] || {};
        planBadge.innerHTML = c.text
            ? `<span style="color:${c.color};">// ${c.text}</span>`
            : '';
    }

    planSelect.addEventListener('change', e => updatePlan(e.target.value));
    updatePlan(planSelect.value);
})();
</script>

@endsection