@extends('layouts.superadmin-app')
@section('title', 'Plans & Promotions')
@section('page-title', 'Plans & Promotions')

@section('content')

@php
$planColors = [
    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)',  'dot'=>'#5b8fb9'],
    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)',    'dot'=>'#c0392b'],
    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)',    'dot'=>'#c9a84c'],
];
$featureKeys = [
    'hour_logs'      => 'Hour Log Tracking',
    'weekly_reports' => 'Weekly Reports',
    'evaluations'    => 'Evaluations',
    'pdf_export'     => 'PDF Export',
    'excel_export'   => 'Excel Export',
    'email_notifs'   => 'Email Notifications',
];
@endphp

@if(session('success'))
<div class="flash flash-success" style="margin-bottom:16px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="flash flash-error" style="margin-bottom:16px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ── Header ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        // {{ $plans->count() }} plan{{ $plans->count() !== 1 ? 's' : '' }} · {{ $plans->sum(fn($p) => $p->tenantCount()) }} active tenants
    </div>
    <button onclick="toggleSection('create-plan-form')" class="btn btn-primary btn-sm">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
        </svg>
        New Plan
    </button>
</div>

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat({{ min($plans->count(), 4) }},1fr);margin-bottom:20px;">
    @foreach($plans as $plan)
    @php
        $pc = $planColors[$plan->name] ?? ['border'=>'rgba(150,150,150,0.3)','bg'=>'rgba(150,150,150,0.08)','color'=>'rgba(180,180,180,0.85)','dot'=>'#888'];
        $tenantCount = $plan->tenantCount();
        $daysLeft    = $plan->daysUntilRenewal();
    @endphp
    <div class="stat-card" style="border-top:2px solid {{ $pc['dot'] }};">
        <div class="stat-top">
            <div class="stat-icon" style="border-color:{{ $pc['border'] }};background:{{ $pc['bg'] }};color:{{ $pc['color'] }};">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="stat-tag">{{ $plan->billing_cycle ?? 'yearly' }}</span>
        </div>
        <div class="stat-num" style="font-size:1.5rem;color:{{ $pc['color'] }};">₱{{ number_format($plan->base_price) }}</div>
        <div class="stat-label">{{ $plan->label }}</div>
        <div style="margin-top:8px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px;">
            <div style="display:flex;align-items:center;gap:5px;">
                <span style="width:5px;height:5px;border-radius:50%;background:{{ $plan->is_active ? '#34d399' : '#ef4444' }};display:inline-block;"></span>
                <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.06em;">
                    {{ $plan->is_active ? 'ACTIVE' : 'INACTIVE' }}
                </span>
            </div>
            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">
                {{ $tenantCount }} tenant{{ $tenantCount !== 1 ? 's' : '' }}
            </span>
        </div>
        {{-- Renewal warning on stat card --}}
        @if($plan->renewal_date && $daysLeft !== null && $daysLeft <= 30)
        <div style="margin-top:6px;font-family:'DM Mono',monospace;font-size:9px;
                    color:{{ $daysLeft <= 7 ? '#ef4444' : '#f59e0b' }};letter-spacing:0.06em;">
            ⚠ {{ $daysLeft > 0 ? $daysLeft.' days to renewal' : ($daysLeft === 0 ? 'due today' : abs($daysLeft).' days overdue') }}
        </div>
        @endif
    </div>
    @endforeach
</div>

{{-- ══ CREATE NEW PLAN FORM ══ --}}
<div id="create-plan-form" style="display:none;margin-bottom:24px;">
    <div class="card" style="border-top:2px solid var(--crimson);">
        <div class="card-header">
            <div class="card-title-main">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:6px;">
                    <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                </svg>
                Create New Plan
            </div>
            <button type="button" onclick="toggleSection('create-plan-form')" class="btn btn-ghost btn-sm">✕ Cancel</button>
        </div>
        <div style="padding:20px 24px;">
            <form method="POST" action="{{ route('super_admin.plans.store') }}">
                @csrf
                {{-- Row 1: name / label / billing / sort --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 80px;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Plan Key <span style="color:var(--crimson);">✦</span>
                            <span style="text-transform:none;font-weight:300;"> (lowercase, no spaces)</span>
                        </label>
                        <input type="text" name="name" class="form-input" required placeholder="e.g. gold"
                               pattern="[a-z0-9_]+" title="Lowercase letters, numbers, underscores only"
                               oninput="this.value=this.value.toLowerCase().replace(/[^a-z0-9_]/g,'')">
                    </div>
                    <div>
                        <label class="form-label">Display Label <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="label" class="form-input" required placeholder="e.g. Gold Plan">
                    </div>
                    <div>
                        <label class="form-label">Billing Cycle <span style="color:var(--crimson);">✦</span></label>
                        <select name="billing_cycle" class="form-select">
                            <option value="yearly">Yearly (365 days)</option>
                            <option value="monthly">Monthly (30 days)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Sort #</label>
                        <input type="number" name="sort_order" class="form-input" min="0" placeholder="0">
                    </div>
                </div>
                {{-- Row 2: price / cap / renewal --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Base Price (₱) <span style="color:var(--crimson);">✦</span></label>
                        <input type="number" name="base_price" class="form-input" required min="0" placeholder="e.g. 5000">
                    </div>
                    <div>
                        <label class="form-label">Student Cap <span style="font-weight:300;text-transform:none;">(blank = unlimited)</span></label>
                        <input type="number" name="student_cap" class="form-input" min="1" placeholder="Leave blank for unlimited">
                    </div>
                    <div>
                        <label class="form-label">
                            Plan-wide Renewal Date
                            <span style="font-weight:300;text-transform:none;">(optional — per-tenant is preferred)</span>
                        </label>
                        <input type="date" name="renewal_date" class="form-input">
                    </div>
                </div>
                {{-- Row 3: description --}}
                <div style="margin-bottom:14px;">
                    <label class="form-label">Description <span style="font-weight:300;text-transform:none;">(shown to tenants)</span></label>
                    <input type="text" name="description" class="form-input" placeholder="A short tagline for this plan..." maxlength="500">
                </div>
                {{-- Row 4: features --}}
                <div style="margin-bottom:16px;">
                    <label class="form-label" style="margin-bottom:10px;">Included Features</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @foreach($featureKeys as $key => $label)
                        <label class="feature-toggle">
                            <input type="checkbox" name="features[{{ $key }}]" value="1">
                            <span class="feature-toggle-inner">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                                {{ $label }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                {{-- Row 5: active + submit --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" checked style="accent-color:var(--crimson);">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">Plan is active (visible to tenants)</span>
                    </label>
                    <div style="display:flex;gap:8px;">
                        <button type="button" onclick="toggleSection('create-plan-form')" class="btn btn-ghost btn-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Create Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══ PLAN CARDS ══ --}}
@foreach($plans as $plan)
@php
    $pc = $planColors[$plan->name] ?? ['border'=>'rgba(150,150,150,0.3)','bg'=>'rgba(150,150,150,0.08)','color'=>'rgba(180,180,180,0.85)','dot'=>'#888'];
    $tenantCount = $plan->tenantCount();
    $daysLeft    = $plan->daysUntilRenewal();
    $promoCount  = $plan->promotions->count();
    $activePromos = $plan->promotions->filter(fn($p) => $p->isCurrentlyActive())->count();
@endphp

<div class="card fade-up" style="margin-bottom:20px;border-top:2px solid {{ $pc['dot'] }};">

    {{-- Plan header --}}
    <div class="card-header" style="padding:18px 24px;">
        <div style="display:flex;align-items:center;gap:14px;flex:1;">
            <div style="width:40px;height:40px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-family:'Playfair Display',serif;font-weight:900;font-size:15px;color:{{ $pc['color'] }};">
                    {{ strtoupper(substr($plan->name, 0, 1)) }}
                </span>
            </div>
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);">{{ $plan->label }}</span>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);background:var(--surface2);padding:2px 7px;border:1px solid var(--border);">{{ $plan->name }}</span>
                    @if(!$plan->is_active)
                    <span class="badge badge-muted">Inactive</span>
                    @else
                    <span class="badge badge-active">Active</span>
                    @endif
                    @if($activePromos > 0)
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:rgba(201,168,76,0.8);background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);padding:2px 7px;letter-spacing:0.06em;">
                        {{ $activePromos }} promo{{ $activePromos !== 1 ? 's' : '' }} live
                    </span>
                    @endif
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;">
                    <span>₱{{ number_format($plan->base_price) }} / {{ $plan->billing_cycle ?? 'year' }}</span>
                    <span>·</span>
                    <span>{{ $plan->student_cap ? number_format($plan->student_cap).' student cap' : '∞ unlimited' }}</span>
                    <span>·</span>
                    <span>{{ $tenantCount }} tenant{{ $tenantCount !== 1 ? 's' : '' }}</span>
                    @if($plan->renewal_date)
                    <span>·</span>
                    <span style="color:{{ $daysLeft !== null && $daysLeft <= 30 ? ($daysLeft <= 7 ? '#ef4444' : '#f59e0b') : 'var(--muted)' }};">
                        Plan-wide renewal: {{ $plan->renewal_date->format('M d, Y') }}
                        @if($daysLeft !== null)
                            ({{ $daysLeft > 0 ? $daysLeft.' days' : ($daysLeft === 0 ? 'today' : abs($daysLeft).' overdue') }})
                        @endif
                    </span>
                    @endif
                    @if($plan->description)
                    <span>·</span><span style="font-style:italic;">{{ $plan->description }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div style="display:flex;gap:6px;flex-shrink:0;">
            <form method="POST" action="{{ route('super_admin.plans.toggle', $plan) }}" style="margin:0;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-ghost btn-sm">{{ $plan->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
            <button onclick="toggleSection('plan-edit-{{ $plan->id }}')" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit
            </button>
            @if($tenantCount === 0)
            <form method="POST" action="{{ route('super_admin.plans.destroy', $plan) }}"
                  onsubmit="return confirm('Delete plan \'{{ $plan->label }}\'? This cannot be undone.')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                    </svg>
                    Delete
                </button>
            </form>
            @else
            <button class="btn btn-ghost btn-sm" disabled title="Cannot delete — {{ $tenantCount }} tenant(s) on this plan" style="opacity:0.4;cursor:not-allowed;">Delete</button>
            @endif
        </div>
    </div>

    {{-- ── Feature overview strip ── --}}
    <div style="padding:10px 24px;border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--surface2);display:flex;flex-wrap:wrap;gap:8px;align-items:center;">
        <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);margin-right:4px;">Features:</span>
        @foreach($featureKeys as $key => $label)
        @php $has = $plan->hasFeature($key); @endphp
        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;font-family:'DM Mono',monospace;font-size:10px;
                     border:1px solid {{ $has ? $pc['border'] : 'var(--border)' }};
                     background:{{ $has ? $pc['bg'] : 'transparent' }};
                     color:{{ $has ? $pc['color'] : 'var(--muted)' }};">
            @if($has)
            <svg width="8" height="8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            @else
            <svg width="8" height="8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            @endif
            {{ $label }}
        </span>
        @endforeach
    </div>

    {{-- ── Edit Plan Form ── --}}
    <div id="plan-edit-{{ $plan->id }}" style="display:none;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);background:var(--surface2);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;">// Editing: {{ $plan->label }}</div>
            <form method="POST" action="{{ route('super_admin.plans.update', $plan) }}">
                @csrf @method('PUT')
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 80px;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Display Label <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="label" value="{{ old('label', $plan->label) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Billing Cycle</label>
                        <select name="billing_cycle" class="form-select">
                            <option value="yearly" {{ ($plan->billing_cycle ?? 'yearly') === 'yearly' ? 'selected' : '' }}>Yearly (365 days)</option>
                            <option value="monthly" {{ ($plan->billing_cycle ?? '') === 'monthly' ? 'selected' : '' }}>Monthly (30 days)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Base Price (₱) <span style="color:var(--crimson);">✦</span></label>
                        <input type="number" name="base_price" value="{{ old('base_price', $plan->base_price) }}" class="form-input" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Sort #</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}" class="form-input" min="0">
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Student Cap <span style="font-weight:300;text-transform:none;">(blank = unlimited)</span></label>
                        <input type="number" name="student_cap" value="{{ old('student_cap', $plan->student_cap) }}" class="form-input" min="1" placeholder="Unlimited">
                    </div>
                    <div>
                        <label class="form-label">Plan-wide Renewal Date</label>
                        <input type="date" name="renewal_date" value="{{ old('renewal_date', $plan->renewal_date?->format('Y-m-d')) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Description</label>
                        <input type="text" name="description" value="{{ old('description', $plan->description) }}" class="form-input" placeholder="Short tagline..." maxlength="500">
                    </div>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label" style="margin-bottom:10px;">Included Features</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @foreach($featureKeys as $key => $label)
                        <label class="feature-toggle">
                            <input type="checkbox" name="features[{{ $key }}]" value="1" {{ ($plan->features[$key] ?? false) ? 'checked' : '' }}>
                            <span class="feature-toggle-inner">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                                {{ $label }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:12px;border-top:1px solid var(--border);">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }} style="accent-color:var(--crimson);">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">Plan is active</span>
                    </label>
                    <div style="display:flex;gap:8px;">
                        <button type="button" onclick="toggleSection('plan-edit-{{ $plan->id }}')" class="btn btn-ghost btn-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Promotions Section ── --}}
    <div style="padding:0 24px 4px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 10px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
                    Promotions ({{ $promoCount }})
                </span>
                @if($activePromos > 0)
                <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;
                             border:1px solid rgba(52,211,153,0.3);background:rgba(52,211,153,0.06);
                             font-family:'DM Mono',monospace;font-size:9px;color:#34d399;letter-spacing:0.06em;">
                    <span style="width:4px;height:4px;border-radius:50%;background:#34d399;display:inline-block;"></span>
                    {{ $activePromos }} active
                </span>
                @endif
            </div>
            <button onclick="toggleSection('promo-new-{{ $plan->id }}')" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                </svg>
                Add Promotion
            </button>
        </div>

        {{-- New promo form --}}
        <div id="promo-new-{{ $plan->id }}" style="display:none;margin-bottom:16px;">
            <form method="POST" action="{{ route('super_admin.plans.promotions.store', $plan) }}"
                  style="padding:16px;background:var(--surface2);border:1px solid var(--border2);">
                @csrf
                <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;">
                    // New promotion for {{ $plan->label }}
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label">Code <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="code" placeholder="BACK2SCHOOL" class="form-input" required oninput="this.value=this.value.toUpperCase()">
                    </div>
                    <div>
                        <label class="form-label">Label <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="label" placeholder="Back to school promo" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Discount Type <span style="color:var(--crimson);">✦</span></label>
                        <select name="discount_type" class="form-select" id="dtype-{{ $plan->id }}" onchange="updateDTypeHint({{ $plan->id }}, this.value)">
                            <option value="percent">Percent (%)</option>
                            <option value="fixed">Fixed (₱)</option>
                        </select>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label class="form-label" id="dval-label-{{ $plan->id }}">Discount (%) <span style="color:var(--crimson);">✦</span></label>
                        <input type="number" name="discount_value" min="1" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Starts At</label>
                        <input type="date" name="starts_at" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Ends At</label>
                        <input type="date" name="ends_at" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Max Uses <span style="color:var(--muted);">(blank=∞)</span></label>
                        <input type="number" name="max_uses" min="1" class="form-input" placeholder="Unlimited">
                    </div>
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" onclick="toggleSection('promo-new-{{ $plan->id }}')" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="submit" class="btn btn-approve btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/></svg>
                        Create Promotion
                    </button>
                </div>
            </form>
        </div>

        {{-- Promotions table --}}
        @if($plan->promotions->count() > 0)
        <div class="table-wrap" style="margin:0 -24px;padding:0 24px 16px;">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Label</th>
                        <th>Discount</th>
                        <th>Validity</th>
                        <th>Uses</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan->promotions as $promo)
                    @php
                        $isActive = $promo->isCurrentlyActive();
                        $usagePercent = $promo->max_uses ? min(100, round(($promo->used_count / $promo->max_uses) * 100)) : 0;
                    @endphp
                    <tr>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);
                                         background:var(--surface2);border:1px solid var(--border2);
                                         padding:2px 8px;letter-spacing:0.08em;">{{ $promo->code }}</span>
                        </td>
                        <td>
                            <div style="font-size:13px;color:var(--text2);">{{ $promo->label }}</div>
                            <button onclick="toggleSection('promo-edit-{{ $promo->id }}')"
                                    style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);background:none;border:none;cursor:pointer;padding:0;margin-top:2px;">
                                edit ↓
                            </button>
                        </td>
                        <td>
                            <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:{{ $pc['color'] }};">
                                {{ $promo->discount_label }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            @if($promo->starts_at || $promo->ends_at)
                                {{ $promo->starts_at?->format('M d, Y') ?? '—' }} → {{ $promo->ends_at?->format('M d, Y') ?? '∞' }}
                            @else
                                No expiry
                            @endif
                        </td>
                        <td>
                            <div style="font-family:'DM Mono',monospace;font-size:11px;margin-bottom:4px;">
                                {{ $promo->used_count }} / {{ $promo->max_uses ?? '∞' }}
                            </div>
                            @if($promo->max_uses)
                            <div style="width:80px;height:3px;background:var(--border2);">
                                <div style="width:{{ $usagePercent }}%;height:100%;background:{{ $usagePercent >= 90 ? '#ef4444' : ($usagePercent >= 70 ? '#f59e0b' : '#34d399') }};transition:width 0.3s;"></div>
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($isActive)
                                <span class="badge badge-active">
                                    <span style="width:5px;height:5px;border-radius:50%;background:#34d399;display:inline-block;margin-right:3px;"></span>
                                    Active
                                </span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('super_admin.plans.promotions.toggle', $promo) }}" style="margin:0;">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $promo->is_active ? 'btn-danger' : 'btn-approve' }}">
                                        {{ $promo->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super_admin.plans.promotions.destroy', $promo) }}"
                                      onsubmit="return confirm('Delete promo {{ $promo->code }}?')" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--muted);">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    {{-- Inline edit row --}}
                    <tr id="promo-edit-{{ $promo->id }}" style="display:none;background:var(--surface2);">
                        <td colspan="7" style="padding:14px 16px;">
                            <form method="POST" action="{{ route('super_admin.plans.promotions.update', $promo) }}">
                                @csrf @method('PUT')
                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr 1fr auto;gap:10px;align-items:flex-end;">
                                    <div>
                                        <label class="form-label">Code</label>
                                        <input type="text" name="code" value="{{ $promo->code }}" class="form-input" oninput="this.value=this.value.toUpperCase()" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Label</label>
                                        <input type="text" name="label" value="{{ $promo->label }}" class="form-input" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Type</label>
                                        <select name="discount_type" class="form-select">
                                            <option value="percent" {{ $promo->discount_type === 'percent' ? 'selected' : '' }}>%</option>
                                            <option value="fixed" {{ $promo->discount_type === 'fixed' ? 'selected' : '' }}>₱ Fixed</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Value</label>
                                        <input type="number" name="discount_value" value="{{ $promo->discount_value }}" class="form-input" min="1" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Starts</label>
                                        <input type="date" name="starts_at" value="{{ $promo->starts_at?->format('Y-m-d') }}" class="form-input">
                                    </div>
                                    <div>
                                        <label class="form-label">Ends</label>
                                        <input type="date" name="ends_at" value="{{ $promo->ends_at?->format('Y-m-d') }}" class="form-input">
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary btn-sm" style="white-space:nowrap;">Save</button>
                                    </div>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:16px 0;text-align:center;font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);border-top:1px solid var(--border);margin-bottom:16px;">
            // No promotions yet for this plan.
        </div>
        @endif
    </div>
</div>
@endforeach

@if($plans->isEmpty())
<div class="card" style="text-align:center;padding:48px 24px;">
    <div style="font-size:32px;margin-bottom:12px;">📋</div>
    <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:6px;">No plans yet</div>
    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-bottom:16px;">Create your first plan to get started.</div>
    <button onclick="toggleSection('create-plan-form');window.scrollTo({top:0,behavior:'smooth'})" class="btn btn-primary btn-sm">Create First Plan</button>
</div>
@endif

<div class="flash flash-info fade-up" style="margin-top:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Plans with active tenants cannot be deleted. The <strong>plan-wide renewal date</strong> is a display marker — per-tenant expiry is tracked on the tenant record via <code>plan_expires_at</code>. Promotions apply a one-time discount at plan assignment time.
</div>

@endsection

@push('styles')
<style>
.form-input, .form-select {
    width: 100%; padding: 9px 12px;
    background: var(--surface);
    border: 1px solid var(--border2);
    color: var(--text); font-size: 13px;
    font-family: 'Barlow', sans-serif;
    outline: none; border-radius: 0;
    transition: border-color 0.15s;
    appearance: none; -webkit-appearance: none;
}
.form-input:focus, .form-select:focus { border-color: var(--crimson); }
.form-label {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted); margin-bottom: 6px;
}
.feature-toggle { display: block; cursor: pointer; }
.feature-toggle input { display: none; }
.feature-toggle-inner {
    display: flex; align-items: center; gap: 8px;
    padding: 8px 12px;
    border: 1px solid var(--border);
    background: var(--surface);
    font-size: 12px; color: var(--muted);
    transition: all 0.15s;
}
.feature-toggle-inner svg { opacity: 0; transition: opacity 0.15s; }
.feature-toggle:hover .feature-toggle-inner { border-color: var(--border2); color: var(--text2); }
.feature-toggle input:checked + .feature-toggle-inner {
    border-color: rgba(192,38,38,0.5);
    background: rgba(192,38,38,0.06);
    color: var(--text);
}
.feature-toggle input:checked + .feature-toggle-inner svg { opacity: 1; color: var(--crimson); }
</style>
@endpush

@push('scripts')
<script>
function toggleSection(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.style.display === 'none' || el.style.display === '') {
        el.style.display = 'block';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';
        requestAnimationFrame(() => {
            el.style.transition = 'opacity .2s,transform .2s';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    } else {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';
        setTimeout(() => { el.style.display = 'none'; el.style.transform = ''; }, 200);
    }
}
function updateDTypeHint(planId, type) {
    const label = document.getElementById('dval-label-' + planId);
    if (label) {
        label.innerHTML = type === 'percent'
            ? 'Discount (%) <span style="color:var(--crimson);">✦</span>'
            : 'Discount (₱) <span style="color:var(--crimson);">✦</span>';
    }
}
</script>
@endpush