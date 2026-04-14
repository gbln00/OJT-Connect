@extends('layouts.app')
@section('title', 'My Plan')
@section('page-title', 'Subscription Plan')

@section('content')

@php
use App\Models\Plan;

$tenant     = tenancy()->tenant;
$planName   = $tenant?->plan ?? 'basic';
$plan       = Plan::where('name', $planName)->with('promotions')->first();
$allPlans   = Plan::where('is_active', true)->orderBy('sort_order')->get();
$promos     = $plan ? $plan->activePromotions()->get() : collect();

// Per-tenant subscription state
$expiresAt    = $tenant?->plan_expires_at;
$inGrace      = method_exists($tenant, 'inGracePeriod') ? $tenant->inGracePeriod() : false;
$isExpired    = method_exists($tenant, 'subscriptionExpired') ? $tenant->subscriptionExpired() : false;
$daysUntil    = method_exists($tenant, 'daysUntilExpiry') ? $tenant->daysUntilExpiry() : null;
$graceEndAt   = $expiresAt ? $expiresAt->copy()->addDays(7) : null;
$graceDaysLeft= $graceEndAt ? (int) now()->diffInDays($graceEndAt, false) : null;

$planColors = [
    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)',  'dot'=>'#5b8fb9'],
    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)',    'dot'=>'#c0392b'],
    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)',    'dot'=>'#c9a84c'],
];
$planLevels = [];
$i = 0;
foreach ($allPlans as $p) { $planLevels[$p->name] = $i++; }
if (!isset($planLevels[$planName])) $planLevels[$planName] = -1;
$currentLevel = $planLevels[$planName] ?? 0;
$pc = $planColors[$planName] ?? ['border'=>'rgba(150,150,150,0.3)','bg'=>'rgba(150,150,150,0.08)','color'=>'rgba(180,180,180,0.85)','dot'=>'#888'];

// Subscription urgency
if ($isExpired && !$inGrace) {
    $subUrgency = 'blocked';
} elseif ($inGrace) {
    $subUrgency = 'grace';
} elseif ($daysUntil !== null && $daysUntil <= 7) {
    $subUrgency = 'critical';
} elseif ($daysUntil !== null && $daysUntil <= 30) {
    $subUrgency = 'warning';
} else {
    $subUrgency = 'ok';
}

$featureLabels = [
    'hour_logs'      => ['label' => 'Hour Log Tracking',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    'weekly_reports' => ['label' => 'Weekly Reports',      'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    'evaluations'    => ['label' => 'Intern Evaluations',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
    'pdf_export'     => ['label' => 'PDF Export',          'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
    'excel_export'   => ['label' => 'Excel Export',        'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
    'email_notifs'   => ['label' => 'Email Notifications', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
];
@endphp

{{-- ══ GRACE PERIOD BANNER ══ --}}
@if($inGrace)
<div style="border:1px solid rgba(245,158,11,0.4);background:rgba(245,158,11,0.08);padding:14px 20px;
            margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;"
     class="fade-up">
    <div style="display:flex;align-items:center;gap:10px;">
        <svg width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <div style="font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#f59e0b;margin-bottom:2px;">
                Grace Period — {{ $graceDaysLeft }} day{{ $graceDaysLeft !== 1 ? 's' : '' }} remaining
            </div>
            <div style="font-size:12px;color:var(--text2);">
                Your subscription expired on {{ $expiresAt?->format('M d, Y') }}.
                Access will be blocked after {{ $graceEndAt?->format('M d, Y') }}.
                Request a renewal below.
            </div>
        </div>
    </div>
    <button onclick="openPlanRequest('{{ $planName }}', '{{ $plan?->label ?? ucfirst($planName) }}', 'upgrade')"
            class="btn btn-sm" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);color:#f59e0b;white-space:nowrap;flex-shrink:0;">
        Renew Now
    </button>
</div>
@endif

{{-- ══ FLASH ══ --}}
@if(session('success'))
<div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
    ✓ {{ session('success') }}
</div>
@endif

{{-- ══ CURRENT PLAN HERO ══ --}}
<div class="card fade-up" style="margin-bottom:20px;position:relative;overflow:hidden;border-top:2px solid {{ $pc['dot'] }};">
    <div style="position:absolute;top:0;right:0;width:220px;height:220px;border-radius:50%;
                background:radial-gradient(circle,{{ $pc['bg'] }} 0%,transparent 70%);
                transform:translate(40%,-40%);pointer-events:none;"></div>

    <div style="padding:28px 32px;display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;position:relative;">
        <div style="flex:1;min-width:220px;">
            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">
                Current Plan
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:{{ $pc['color'] }};line-height:1;margin-bottom:6px;">
                {{ $plan?->label ?? ucfirst($planName) }}
            </div>
            @if($plan?->description)
            <div style="font-size:13px;color:var(--text2);margin-bottom:8px;font-style:italic;">{{ $plan->description }}</div>
            @endif
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);display:flex;flex-wrap:wrap;gap:10px;">
                <span>₱{{ number_format($plan?->base_price ?? 0) }} / {{ $plan?->billing_cycle ?? 'year' }}</span>
                <span>·</span>
                @if($plan?->student_cap)
                    <span>Up to {{ number_format($plan->student_cap) }} students</span>
                @else
                    <span>Unlimited students</span>
                @endif
            </div>
        </div>

        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0;">
            <span style="display:inline-flex;align-items:center;gap:5px;padding:5px 14px;
                         border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                         font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;
                         letter-spacing:0.12em;text-transform:uppercase;color:{{ $pc['color'] }};">
                <span style="width:6px;height:6px;border-radius:50%;background:{{ $pc['dot'] }};display:inline-block;"></span>
                {{ ucfirst($planName) }} Plan
            </span>

            {{-- ── Subscription expiry widget ── --}}
            @if($expiresAt)
            @php
                $urgencyMap = [
                    'blocked'  => ['bg'=>'rgba(239,68,68,0.12)',  'border'=>'rgba(239,68,68,0.5)',  'color'=>'#ef4444',  'label'=>'ACCESS BLOCKED'],
                    'grace'    => ['bg'=>'rgba(245,158,11,0.12)', 'border'=>'rgba(245,158,11,0.5)', 'color'=>'#f59e0b',  'label'=>'GRACE PERIOD'],
                    'critical' => ['bg'=>'rgba(239,68,68,0.08)',  'border'=>'rgba(239,68,68,0.4)',  'color'=>'#f87171',  'label'=>'EXPIRING SOON'],
                    'warning'  => ['bg'=>'rgba(245,158,11,0.08)', 'border'=>'rgba(245,158,11,0.3)', 'color'=>'#fbbf24',  'label'=>'Renewal Due'],
                    'ok'       => ['bg'=>'rgba(52,211,153,0.06)', 'border'=>'rgba(52,211,153,0.25)','color'=>'#34d399',  'label'=>'Active'],
                ];
                $um = $urgencyMap[$subUrgency];
            @endphp
            <div style="padding:8px 14px;border:1px solid {{ $um['border'] }};background:{{ $um['bg'] }};
                        font-family:'DM Mono',monospace;font-size:10px;color:{{ $um['color'] }};text-align:right;min-width:160px;">
                <div style="letter-spacing:0.1em;text-transform:uppercase;margin-bottom:3px;font-size:9px;">
                    {{ $um['label'] }}
                </div>
                <div style="font-size:13px;font-weight:600;">{{ $expiresAt->format('M d, Y') }}</div>
                @if($inGrace)
                <div style="margin-top:2px;font-size:10px;opacity:0.8;">
                    Grace ends {{ $graceEndAt?->format('M d') }}
                    @if($graceDaysLeft !== null)
                        ({{ $graceDaysLeft }} day{{ $graceDaysLeft !== 1 ? 's' : '' }} left)
                    @endif
                </div>
                @elseif($daysUntil !== null)
                <div style="margin-top:2px;font-size:10px;opacity:0.8;">
                    {{ $daysUntil > 0 ? $daysUntil.' days remaining' : ($daysUntil === 0 ? 'Expires today' : abs($daysUntil).' days overdue') }}
                </div>
                @endif
            </div>

            {{-- Countdown bar (visual only) --}}
            @if(!$isExpired && $daysUntil !== null)
            @php
                $totalDays = $plan?->billing_cycle === 'monthly' ? 30 : 365;
                $pct       = max(0, min(100, round(($daysUntil / $totalDays) * 100)));
                $barColor  = $pct > 50 ? '#34d399' : ($pct > 20 ? '#f59e0b' : '#ef4444');
            @endphp
            <div style="width:160px;">
                <div style="display:flex;justify-content:space-between;font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-bottom:4px;">
                    <span>Subscription</span><span>{{ $pct }}% remaining</span>
                </div>
                <div style="height:3px;background:var(--border2);width:100%;">
                    <div style="width:{{ $pct }}%;height:100%;background:{{ $barColor }};transition:width 1s ease;"></div>
                </div>
            </div>
            @endif
            @else
            <div style="padding:6px 14px;border:1px solid var(--border);background:var(--surface2);
                        font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-align:center;">
                <div style="margin-bottom:2px;letter-spacing:0.06em;font-size:9px;text-transform:uppercase;">No Expiry Set</div>
                <div style="font-size:11px;">Contact administrator</div>
            </div>
            @endif

            @if($promos->count() > 0)
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                {{ $promos->count() }} active promo{{ $promos->count() !== 1 ? 's' : '' }} available
            </span>
            @endif
        </div>
    </div>

    {{-- Feature grid --}}
    @if($plan && is_array($plan->features))
    <div style="padding:0 32px 24px;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;
                    color:var(--muted);margin-bottom:12px;">Included Features</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            @foreach($featureLabels as $key => $info)
            @php $has = $plan->hasFeature($key); @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                        border:1px solid {{ $has ? $pc['border'] : 'var(--border)' }};
                        background:{{ $has ? $pc['bg'] : 'var(--surface2)' }};transition:all 0.15s;">
                <div style="width:28px;height:28px;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                            border:1px solid {{ $has ? $pc['border'] : 'var(--border)' }};background:{{ $has ? 'rgba(255,255,255,0.03)' : 'transparent' }};">
                    <svg width="13" height="13" fill="none" stroke="{{ $has ? $pc['color'] : 'var(--muted)' }}" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $info['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:12px;color:{{ $has ? 'var(--text)' : 'var(--muted)' }};font-weight:500;">{{ $info['label'] }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:9px;color:{{ $has ? $pc['color'] : 'var(--muted)' }};letter-spacing:0.06em;margin-top:1px;">
                        {{ $has ? '✓ INCLUDED' : '✗ NOT INCLUDED' }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ══ SUBSCRIPTION TIMELINE ══ --}}
@if($expiresAt)
<div class="card fade-up fade-up-1" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title-main">Subscription Timeline</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// Current billing period</span>
    </div>
    <div style="padding:20px 28px;">
        @php
            // Compute start from history or approximate
            $historyStart = \App\Models\TenantPlanHistory::where('tenant_id', $tenant?->id)
                ->orderBy('starts_at', 'desc')->first();
            $startDate = $historyStart?->starts_at ?? $expiresAt->copy()->subDays(
                $plan?->billing_cycle === 'monthly' ? 30 : 365
            );
            $totalRange = $startDate->diffInDays($expiresAt);
            $elapsed    = $startDate->diffInDays(now()->min($expiresAt));
            $elapsedPct = $totalRange > 0 ? min(100, round(($elapsed / $totalRange) * 100)) : 100;
        @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:8px;">
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                Started: <span style="color:var(--text2);">{{ $startDate->format('M d, Y') }}</span>
            </div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                Expires: <span style="color:{{ $subUrgency === 'ok' ? 'var(--text2)' : ($subUrgency === 'warning' ? '#f59e0b' : '#ef4444') }};">{{ $expiresAt->format('M d, Y') }}</span>
            </div>
        </div>
        {{-- Progress bar --}}
        <div style="height:8px;background:var(--border2);position:relative;overflow:hidden;">
            {{-- Elapsed --}}
            <div style="position:absolute;left:0;top:0;height:100%;width:{{ $elapsedPct }}%;
                        background:{{ $subUrgency === 'ok' ? $pc['dot'] : ($subUrgency === 'warning' ? '#f59e0b' : '#ef4444') }};
                        transition:width 1s ease;opacity:0.7;"></div>
            {{-- Grace zone indicator --}}
            @if(!$isExpired || $inGrace)
            <div style="position:absolute;right:0;top:0;height:100%;width:{{ max(1, round(7 / max($totalRange,1) * 100)) }}%;
                        background:rgba(245,158,11,0.3);border-left:1px dashed rgba(245,158,11,0.4);"
                 title="7-day grace period"></div>
            @endif
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:4px;">
            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">{{ $elapsedPct }}% elapsed</span>
            <span style="font-family:'DM Mono',monospace;font-size:9px;color:rgba(245,158,11,0.6);">▌ grace period</span>
        </div>

        {{-- Key dates --}}
        <div style="margin-top:16px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            <div style="padding:10px 14px;border:1px solid var(--border);background:var(--surface2);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Start Date</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $startDate->format('M d, Y') }}</div>
            </div>
            <div style="padding:10px 14px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;text-transform:uppercase;color:{{ $pc['color'] }};margin-bottom:4px;opacity:0.7;">Renewal Date</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:{{ $pc['color'] }};">{{ $expiresAt->format('M d, Y') }}</div>
                @if($daysUntil !== null && !$isExpired)
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">{{ $daysUntil }} day{{ $daysUntil !== 1 ? 's' : '' }} from now</div>
                @endif
            </div>
            <div style="padding:10px 14px;border:1px solid rgba(245,158,11,0.2);background:rgba(245,158,11,0.05);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;text-transform:uppercase;color:rgba(245,158,11,0.6);margin-bottom:4px;">Grace Ends</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:rgba(245,158,11,0.8);">{{ $graceEndAt?->format('M d, Y') ?? '—' }}</div>
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">7 days after renewal</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ══ PLAN COMPARISON ══ --}}
<div class="card fade-up fade-up-2" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title-main">Compare Plans</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// Submit a request to upgrade or downgrade</span>
    </div>
    <div style="display:grid;grid-template-columns:repeat({{ $allPlans->count() }},1fr);gap:1px;background:var(--border);">
        @foreach($allPlans as $p)
        @php
            $ppc      = $planColors[$p->name] ?? ['border'=>'rgba(150,150,150,0.3)','bg'=>'rgba(150,150,150,0.08)','color'=>'rgba(180,180,180,0.85)','dot'=>'#888'];
            $isCurrent = $p->name === $planName;
            $pLevel   = $planLevels[$p->name] ?? 0;
            $isHigher = $pLevel > $currentLevel;
            $isLower  = $pLevel < $currentLevel;
        @endphp
        <div style="background:var(--surface{{ $isCurrent ? '' : '2' }});padding:20px;position:relative;
                    {{ $isCurrent ? 'border:1.5px solid '.$ppc['border'].';margin:-1px;z-index:1;' : '' }}">
            @if($isCurrent)
            <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $ppc['dot'] }};"></div>
            <div style="position:absolute;top:10px;right:10px;font-family:'DM Mono',monospace;font-size:9px;
                        letter-spacing:0.12em;text-transform:uppercase;color:{{ $ppc['color'] }};opacity:0.7;">● CURRENT</div>
            @endif
            <div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                        letter-spacing:0.18em;text-transform:uppercase;color:{{ $ppc['color'] }};margin-bottom:4px;">{{ $p->label }}</div>
            <div style="font-family:'Playfair Display',serif;font-weight:900;font-size:22px;color:var(--text);margin-bottom:2px;">₱{{ number_format($p->base_price) }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:4px;">per {{ $p->billing_cycle ?? 'year' }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:14px;">
                {{ $p->student_cap ? number_format($p->student_cap).' students' : '∞ unlimited' }}
            </div>
            @if($p->description)
            <div style="font-size:11px;color:var(--text2);margin-bottom:12px;font-style:italic;border-left:2px solid {{ $ppc['border'] }};padding-left:8px;">{{ $p->description }}</div>
            @endif
            <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
                @foreach($featureLabels as $key => $info)
                @php $has = $p->hasFeature($key); @endphp
                <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:{{ $has ? 'var(--text2)' : 'var(--muted)' }};">
                    @if($has)
                        <svg width="10" height="10" fill="none" stroke="{{ $ppc['color'] }}" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    @else
                        <svg width="10" height="10" fill="none" stroke="var(--border2)" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @endif
                    {{ $info['label'] }}
                </div>
                @endforeach
            </div>
            @if($isCurrent)
            {{-- Renew current plan button --}}
            @if($expiresAt && ($daysUntil === null || $daysUntil <= 30))
            <button onclick="openPlanRequest('{{ $p->name }}', '{{ $p->label }}', 'upgrade')"
                    class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                ↻ Request Renewal
            </button>
            @else
            <div style="text-align:center;font-family:'DM Mono',monospace;font-size:10px;color:{{ $ppc['color'] }};padding:6px 0;opacity:0.7;">✓ Active</div>
            @endif
            @else
            <div>
                @if($isHigher)
                <button onclick="openPlanRequest('{{ $p->name }}', '{{ $p->label }}', 'upgrade')"
                        class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                    ↑ Request Upgrade
                </button>
                @elseif($isLower)
                <button onclick="openPlanRequest('{{ $p->name }}', '{{ $p->label }}', 'downgrade')"
                        class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;color:var(--muted);">
                    ↓ Request Downgrade
                </button>
                @endif
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ══ ACTIVE PROMOTIONS ══ --}}
@if($promos->count() > 0)
<div class="card fade-up fade-up-3" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title-main">Active Promotions</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// Mention these codes in your request message</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Discount</th>
                    <th>Valid Until</th>
                    <th>Uses Remaining</th>
                </tr>
            </thead>
            <tbody>
                @foreach($promos as $promo)
                <tr>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);
                                     background:{{ $pc['bg'] }};border:1px solid {{ $pc['border'] }};
                                     padding:3px 10px;letter-spacing:0.1em;">{{ $promo->code }}</span>
                    </td>
                    <td style="font-size:13px;color:var(--text2);">{{ $promo->label }}</td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:{{ $pc['color'] }};">
                            {{ $promo->discount_label }}
                        </span>
                        @if($plan)
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">
                            → ₱{{ number_format($plan->finalPrice($promo)) }}
                        </div>
                        @endif
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $promo->ends_at?->format('M d, Y') ?? 'No expiry' }}
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        @if($promo->max_uses)
                            {{ number_format($promo->max_uses - $promo->used_count) }} of {{ number_format($promo->max_uses) }}
                        @else
                            Unlimited
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ══ PLAN CHANGE REQUEST MODAL ══ --}}
<div id="plan-request-modal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);">
    <div style="background:var(--surface);border:1px solid var(--border2);max-width:480px;width:90%;padding:32px;position:relative;border-top:2px solid var(--crimson);">
        <button onclick="closePlanRequest()" style="position:absolute;top:12px;right:16px;background:none;border:none;color:var(--muted);cursor:pointer;font-size:18px;line-height:1;">✕</button>

        <div id="modal-badge" style="display:inline-block;font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:700;
             letter-spacing:0.16em;text-transform:uppercase;padding:3px 10px;border:1px solid var(--border2);color:var(--muted);margin-bottom:14px;">
            REQUEST
        </div>

        <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:var(--text);margin-bottom:6px;">
            <span id="modal-title">Plan Change Request</span>
        </div>
        <div id="modal-subtitle" style="font-size:13px;color:var(--muted);margin-bottom:20px;">
            Submits a request to your system administrator.
        </div>

        {{-- Promo code hint if available --}}
        @if($promos->count() > 0)
        <div style="padding:10px 14px;border:1px solid rgba(201,168,76,0.25);background:rgba(201,168,76,0.05);margin-bottom:16px;font-size:12px;color:rgba(201,168,76,0.8);">
            <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.1em;text-transform:uppercase;display:block;margin-bottom:4px;">Promo codes available</span>
            @foreach($promos as $p)
            <span style="font-family:'DM Mono',monospace;font-size:11px;background:rgba(201,168,76,0.1);border:1px solid rgba(201,168,76,0.2);padding:2px 6px;margin-right:4px;">{{ $p->code }}</span>
            @endforeach
            <span style="display:block;margin-top:4px;font-size:11px;opacity:0.7;">Include a promo code in your message to request a discount.</span>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.plan.request') }}">
            @csrf
            <input type="hidden" name="requested_plan" id="modal-plan-input">
            <input type="hidden" name="request_type" id="modal-type-input">
            <input type="hidden" name="tenant_id" value="{{ $tenant?->id }}">

            <div style="margin-bottom:14px;">
                <label class="form-label">Message to Administrator <span style="color:var(--crimson);">✦</span></label>
                <textarea name="message" class="form-input" rows="4" required
                          style="resize:vertical;font-family:'Barlow',sans-serif;"
                          placeholder="Describe your request, include any promo codes, billing questions, etc..."></textarea>
            </div>

            <div style="margin-bottom:20px;">
                <label class="form-label">Your Contact Email <span style="color:var(--crimson);">✦</span></label>
                <input type="email" name="contact_email" value="{{ auth()->user()->email }}" class="form-input" required>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closePlanRequest()" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,12 2,6"/>
                    </svg>
                    Send Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ QUICK CONTACT ══ --}}
@php $currentLvl = $planLevels[$planName] ?? 0; $maxLvl = count($planLevels) - 1; @endphp
@if($currentLvl < $maxLvl || $inGrace || ($daysUntil !== null && $daysUntil <= 30))
<div style="padding:20px 24px;border:1px solid var(--border2);background:var(--surface);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;" class="fade-up fade-up-4">
    <div>
        <div style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">
            @if($inGrace) Your grace period is ending — renew now
            @elseif($daysUntil !== null && $daysUntil <= 7) Subscription expiring in {{ $daysUntil }} day{{ $daysUntil !== 1 ? 's' : '' }}
            @elseif($daysUntil !== null && $daysUntil <= 30) Time to renew your subscription
            @else Need more features or capacity?
            @endif
        </div>
        <div style="font-size:13px;color:var(--muted);">
            Contact your system administrator or use the comparison above to request a change.
        </div>
    </div>
    <a href="mailto:support@ojtconnect.com?subject=Plan request — {{ tenant('id') }}&body=Institution: {{ tenant('id') }}%0ACurrent Plan: {{ $planName }}%0AExpiry: {{ $expiresAt?->format('Y-m-d') ?? 'not set' }}%0A%0ARequest: "
       class="btn btn-primary btn-sm">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,12 2,6"/>
        </svg>
        Email Support
    </a>
</div>
@endif

@endsection

@push('styles')
<style>
.form-input {
    width: 100%; padding: 9px 12px;
    background: var(--surface);
    border: 1px solid var(--border2);
    color: var(--text); font-size: 13px;
    font-family: 'Barlow', sans-serif;
    outline: none; border-radius: 0;
    transition: border-color 0.15s;
}
.form-input:focus { border-color: var(--crimson); }
.form-label {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted); margin-bottom: 6px;
}
#plan-request-modal.open { display: flex !important; }
</style>
@endpush

@push('scripts')
<script>
function openPlanRequest(planKey, planLabel, type) {
    document.getElementById('modal-plan-input').value = planKey;
    document.getElementById('modal-type-input').value  = type;
    const isUpgrade = type === 'upgrade';
    const isRenewal = planKey === '{{ $planName }}';
    let title, subtitle;
    if (isRenewal) {
        title    = '↻ Renew ' + planLabel;
        subtitle = 'Request a renewal of your current subscription.';
    } else if (isUpgrade) {
        title    = '↑ Upgrade to ' + planLabel;
        subtitle = 'Submitting an upgrade request to your system administrator.';
    } else {
        title    = '↓ Downgrade to ' + planLabel;
        subtitle = 'Some features may be restricted after downgrade.';
    }
    document.getElementById('modal-title').textContent    = title;
    document.getElementById('modal-subtitle').textContent = subtitle;
    const badge = document.getElementById('modal-badge');
    badge.textContent  = isRenewal ? '↻ RENEWAL REQUEST' : (isUpgrade ? '↑ UPGRADE REQUEST' : '↓ DOWNGRADE REQUEST');
    badge.style.color  = isUpgrade || isRenewal ? 'var(--crimson)' : 'var(--muted)';
    badge.style.borderColor = isUpgrade || isRenewal ? 'rgba(192,38,38,0.4)' : 'var(--border2)';
    const modal = document.getElementById('plan-request-modal');
    modal.classList.add('open');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePlanRequest() {
    const modal = document.getElementById('plan-request-modal');
    modal.classList.remove('open');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
document.getElementById('plan-request-modal').addEventListener('click', function(e) {
    if (e.target === this) closePlanRequest();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePlanRequest();
});
</script>
@endpush