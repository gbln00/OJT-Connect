@extends('layouts.app')
@section('title', 'My Plan')
@section('page-title', 'Subscription Plan')

@section('content')

@php
use App\Models\Plan;
use App\Models\Tenant;

$tenant     = tenancy()->tenant;
$planName   = $tenant?->plan ?? 'basic';
$plan       = Plan::where('name', $planName)->first();
$promos     = $plan ? $plan->activePromotions()->get() : collect();

$planColors = [
    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)'],
    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
];
$planLevels = ['basic'=>1,'standard'=>2,'premium'=>3];

$pc          = $planColors[$planName] ?? $planColors['basic'];
$allPlans    = Plan::where('is_active', true)->orderBy('sort_order')->get();

$featureLabels = [
    'hour_logs'       => 'Hour Log Tracking',
    'weekly_reports'  => 'Weekly Reports',
    'evaluations'     => 'Intern Evaluations',
    'pdf_export'      => 'PDF Export',
    'excel_export'    => 'Excel Export',
    'email_notifs'    => 'Email Notifications',
];
@endphp

{{-- ── Current plan hero ── --}}
<div class="card fade-up" style="margin-bottom:20px;position:relative;overflow:hidden;border-top:2px solid {{ $pc['color'] }};">
    <div style="position:absolute;top:0;right:0;width:200px;height:200px;border-radius:50%;
                background:radial-gradient(circle,{{ $pc['bg'] }} 0%,transparent 70%);
                transform:translate(40%,-40%);pointer-events:none;"></div>

    <div style="padding:28px 32px;display:flex;align-items:flex-start;gap:24px;flex-wrap:wrap;">
        <div style="flex:1;min-width:220px;">
            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;
                        color:var(--muted);margin-bottom:8px;">Current Plan</div>
            <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;
                        color:{{ $pc['color'] }};line-height:1;margin-bottom:6px;">
                {{ $plan?->label ?? ucfirst($planName) }}
            </div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                ₱{{ number_format($plan?->base_price ?? 0) }} / year
                &nbsp;·&nbsp;
                @if($plan?->student_cap)
                    Up to {{ number_format($plan->student_cap) }} students
                @else
                    Unlimited students
                @endif
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                         border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                         font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;
                         letter-spacing:0.1em;text-transform:uppercase;color:{{ $pc['color'] }};">
                <span style="width:5px;height:5px;border-radius:50%;background:{{ $pc['color'] }};display:inline-block;"></span>
                {{ ucfirst($planName) }} Plan
            </span>
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
                    color:var(--muted);margin-bottom:12px;">Included features</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            @foreach($featureLabels as $key => $label)
            @php $has = $plan->hasFeature($key); @endphp
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;
                        border:1px solid {{ $has ? $pc['border'] : 'var(--border)' }};
                        background:{{ $has ? $pc['bg'] : 'var(--surface2)' }};">
                @if($has)
                    <svg width="12" height="12" fill="none" stroke="{{ $pc['color'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                @else
                    <svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                @endif
                <span style="font-size:12px;color:{{ $has ? 'var(--text2)' : 'var(--muted)' }};">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- ── Plan comparison ── --}}
<div class="card fade-up fade-up-1" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title-main">Plan Comparison</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// Contact support to upgrade</span>
    </div>

    <div style="display:grid;grid-template-columns:repeat({{ $allPlans->count() }},1fr);gap:1px;background:var(--border);">
        @foreach($allPlans as $p)
        @php
            $ppc     = $planColors[$p->name] ?? $planColors['basic'];
            $isCurrent = $p->name === $planName;
            $isHigher  = ($planLevels[$p->name] ?? 0) > ($planLevels[$planName] ?? 0);
        @endphp
        <div style="background:var(--surface{{ $isCurrent ? '' : '2' }});padding:20px;position:relative;
                    {{ $isCurrent ? 'border:1.5px solid '.$ppc['border'].';margin:-1px;' : '' }}">
            @if($isCurrent)
            <div style="position:absolute;top:0;left:0;right:0;height:2px;background:{{ $ppc['color'] }};"></div>
            <div style="position:absolute;top:8px;right:10px;font-family:'DM Mono',monospace;font-size:9px;
                        letter-spacing:0.12em;text-transform:uppercase;color:{{ $ppc['color'] }};opacity:0.7;">current</div>
            @endif

            <div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                        letter-spacing:0.18em;text-transform:uppercase;color:{{ $ppc['color'] }};margin-bottom:4px;">
                {{ $p->label }}
            </div>
            <div style="font-family:'Playfair Display',serif;font-weight:900;font-size:20px;
                        color:var(--text);margin-bottom:2px;">₱{{ number_format($p->base_price) }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:14px;">per year</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:12px;">
                {{ $p->student_cap ? number_format($p->student_cap).' students' : '∞ unlimited' }}
            </div>

            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($featureLabels as $key => $label)
                @php $has = $p->hasFeature($key); @endphp
                <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:{{ $has ? 'var(--text2)' : 'var(--muted)' }};">
                    @if($has)
                        <svg width="10" height="10" fill="none" stroke="{{ $ppc['color'] }}" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    @else
                        <svg width="10" height="10" fill="none" stroke="var(--border2)" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    @endif
                    {{ $label }}
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── Active promotions ── --}}
@if($promos->count() > 0)
<div class="card fade-up fade-up-2" style="margin-bottom:20px;">
    <div class="card-header">
        <div class="card-title-main">Active Promotions</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
            // Apply these codes when contacted by your system admin for plan upgrade
        </span>
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
                                     padding:3px 10px;letter-spacing:0.1em;">
                            {{ $promo->code }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:var(--text2);">{{ $promo->label }}</td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:{{ $pc['color'] }};">
                            {{ $promo->discount_label }}
                        </span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $promo->ends_at?->format('M d, Y') ?? 'No expiry' }}
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        @if($promo->max_uses)
                            {{ $promo->max_uses - $promo->used_count }} of {{ $promo->max_uses }}
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

{{-- ── Upgrade CTA ── --}}
@php $isNotPremium = ($planLevels[$planName] ?? 0) < 3; @endphp
@if($isNotPremium)
<div style="padding:20px 24px;border:1px solid var(--border2);background:var(--surface);display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;" class="fade-up fade-up-3">
    <div>
        <div style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">
            Need more features?
        </div>
        <div style="font-size:13px;color:var(--muted);">
            Contact your system administrator at
            <a href="mailto:support@ojtconnect.com" style="color:var(--crimson);text-decoration:none;">support@ojtconnect.com</a>
            to upgrade your plan.
        </div>
    </div>
    <a href="mailto:support@ojtconnect.com?subject=Plan upgrade request for {{ tenant('id') }}"
       class="btn btn-primary btn-sm">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,12 2,6"/>
        </svg>
        Request Upgrade
    </a>
</div>
@endif

@endsection