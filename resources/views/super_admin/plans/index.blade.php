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
@endphp

{{-- ── Stat strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);">
    @foreach($plans as $plan)
    @php $pc = $planColors[$plan->name] ?? ['border'=>'var(--border2)','bg'=>'var(--surface2)','color'=>'var(--muted)','dot'=>'var(--muted)']; @endphp
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="border-color:{{ $pc['border'] }};background:{{ $pc['bg'] }};color:{{ $pc['color'] }};">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <span class="stat-tag">{{ $plan->billing_cycle ?? 'yearly' }}</span>
        </div>
        <div class="stat-num" style="font-size:1.6rem;">₱{{ number_format($plan->base_price) }}</div>
        <div class="stat-label">{{ $plan->label }}</div>
        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
            <span style="width:5px;height:5px;border-radius:50%;background:{{ $plan->is_active ? '#34d399' : '#ef4444' }};display:inline-block;"></span>
            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.08em;">
                {{ $plan->is_active ? 'ACTIVE' : 'INACTIVE' }}
                @if($plan->student_cap)
                    · {{ number_format($plan->student_cap) }} student cap
                @else
                    · Unlimited students
                @endif
            </span>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Plan cards ── --}}
@foreach($plans as $plan)
@php $pc = $planColors[$plan->name] ?? ['border'=>'var(--border2)','bg'=>'var(--surface2)','color'=>'var(--muted)','dot'=>'var(--muted)']; @endphp

<div class="card fade-up fade-up-1" style="margin-bottom:20px;border-top:2px solid {{ $pc['dot'] }};">

    {{-- Plan header --}}
    <div class="card-header" style="padding:20px 24px;">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:42px;height:42px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-family:'Playfair Display',serif;font-weight:900;font-size:16px;color:{{ $pc['color'] }};">
                    {{ strtoupper(substr($plan->name, 0, 1)) }}
                </span>
            </div>
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--text);">
                    {{ $plan->label }}
                    <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:400;color:var(--muted);margin-left:8px;">
                        ₱{{ number_format($plan->base_price) }} / year
                    </span>
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;letter-spacing:0.06em;">
                    Student cap: {{ $plan->student_cap ? number_format($plan->student_cap) : '∞ unlimited' }}
                    &nbsp;·&nbsp;
                    {{ $plan->promotions->count() }} promotion{{ $plan->promotions->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>
        <button onclick="toggleSection('plan-edit-{{ $plan->id }}')"
                class="btn btn-ghost btn-sm">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit Plan
        </button>
    </div>

    {{-- Edit plan form (collapsed by default) --}}
    <div id="plan-edit-{{ $plan->id }}" style="display:none;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);background:var(--surface2);">
            <form method="POST" action="{{ route('super_admin.plans.update', $plan) }}">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Label</label>
                        <input type="text" name="label" value="{{ old('label', $plan->label) }}"
                               class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Base Price (₱)</label>
                        <input type="number" name="base_price" value="{{ old('base_price', $plan->base_price) }}"
                               class="form-input" min="0" required>
                    </div>
                    <div>
                        <label class="form-label">Student Cap <span style="font-weight:300;text-transform:none;">(blank = unlimited)</span></label>
                        <input type="number" name="student_cap" value="{{ old('student_cap', $plan->student_cap) }}"
                               class="form-input" min="1" placeholder="Leave blank for unlimited">
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Features (one per key=value line)</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @php
                        $featureKeys = [
                            'weekly_reports'   => 'Weekly Reports',
                            'evaluations'      => 'Evaluations',
                            'pdf_export'       => 'PDF Export',
                            'excel_export'     => 'Excel Export',
                            'hour_logs'        => 'Hour Logs',
                            'email_notifs'     => 'Email Notifications',
                        ];
                        @endphp
                        @foreach($featureKeys as $key => $label)
                        <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border2);
                                      background:var(--surface);cursor:pointer;transition:border-color .15s;"
                               onmouseover="this.style.borderColor='var(--border2)'" onmouseout="this.style.borderColor='var(--border)'">
                            <input type="checkbox" name="features[{{ $key }}]" value="1"
                                   {{ ($plan->features[$key] ?? false) ? 'checked' : '' }}
                                   style="accent-color:var(--crimson);flex-shrink:0;">
                            <span style="font-size:12px;color:var(--text2);">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:12px;">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1"
                               {{ $plan->is_active ? 'checked' : '' }}
                               style="accent-color:var(--crimson);">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">Plan is active (visible to tenants)</span>
                    </label>
                    <div style="margin-left:auto;display:flex;gap:8px;">
                        <button type="button" onclick="toggleSection('plan-edit-{{ $plan->id }}')" class="btn btn-ghost btn-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Save Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Promotions list --}}
    <div style="padding:0 24px 4px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0 10px;">
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
                Promotions ({{ $plan->promotions->count() }})
            </span>
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
                        <input type="text" name="code" placeholder="e.g. BACK2SCHOOL" class="form-input" required
                               style="text-transform:uppercase;" oninput="this.value=this.value.toUpperCase()">
                    </div>
                    <div>
                        <label class="form-label">Label <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="label" placeholder="Back to school promo" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Discount Type <span style="color:var(--crimson);">✦</span></label>
                        <select name="discount_type" class="form-select" id="dtype-{{ $plan->id }}"
                                onchange="updateDTypeHint({{ $plan->id }}, this.value)">
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
                    <tr>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);
                                         background:var(--surface2);border:1px solid var(--border2);
                                         padding:2px 8px;letter-spacing:0.08em;">
                                {{ $promo->code }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--text2);">{{ $promo->label }}</td>
                        <td>
                            <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;
                                         color:{{ $pc['color'] }};">
                                {{ $promo->discount_label }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            @if($promo->starts_at || $promo->ends_at)
                                {{ $promo->starts_at?->format('M d, Y') ?? '—' }}
                                →
                                {{ $promo->ends_at?->format('M d, Y') ?? '∞' }}
                            @else
                                <span style="color:var(--muted);">No expiry</span>
                            @endif
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;">
                            {{ $promo->used_count }}
                            @if($promo->max_uses)
                                / {{ $promo->max_uses }}
                            @else
                                / ∞
                            @endif
                        </td>
                        <td>
                            @if($promo->isCurrentlyActive())
                                <span class="badge badge-active">
                                    <span style="width:5px;height:5px;border-radius:50%;background:#34d399;display:inline-block;margin-right:3px;"></span>
                                    Active
                                </span>
                            @else
                                <span class="badge badge-muted">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;">
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
                                            <polyline points="3,6 5,6 21,6"/>
                                            <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:20px 0;text-align:center;font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);border-top:1px solid var(--border);margin-bottom:16px;">
            // No promotions yet for this plan.
        </div>
        @endif
    </div>
</div>
@endforeach

{{-- Info note --}}
<div class="flash flash-info fade-up fade-up-2" style="margin-top:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Plan changes apply to newly assigned tenants. Existing tenants keep their current plan until manually changed via the Tenant edit page. Promotions apply a one-time discount at the time of plan assignment.
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
</style>
@endpush

@push('scripts')
<script>
function toggleSection(id) {
    const el = document.getElementById(id);
    if (!el) return;
    if (el.style.display === 'none') {
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
        setTimeout(() => el.style.display = 'none', 200);
    }
}

function updateDTypeHint(planId, type) {
    const label = document.getElementById('dval-label-' + planId);
    if (label) label.textContent = type === 'percent' ? 'Discount (%) ✦' : 'Discount (₱) ✦';
}
</script>
@endpush