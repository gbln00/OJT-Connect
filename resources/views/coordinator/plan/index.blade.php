@extends('layouts.coordinator-app')
@section('title', 'My Plan')
@section('page-title', 'Subscription Plan')

@section('content')

@php
use App\Models\Plan;

$planColors = [
    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)'],
    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
];
$pc = $planColors[$planName] ?? $planColors['basic'];

$featureLabels = [
    'hour_logs'       => 'Hour Log Tracking',
    'weekly_reports'  => 'Weekly Reports',
    'evaluations'     => 'Intern Evaluations',
    'pdf_export'      => 'PDF Export',
    'excel_export'    => 'Excel Export',
    'email_notifs'    => 'Email Notifications',
];
@endphp

{{-- Current plan hero --}}
<div class="card fade-up" style="margin-bottom:20px;border-top:2px solid {{ $pc['color'] }};">
    <div style="padding:24px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;
                        color:var(--muted);margin-bottom:6px;">Institution Plan</div>
            <div style="font-family:'Playfair Display',serif;font-size:26px;font-weight:900;
                        color:{{ $pc['color'] }};margin-bottom:4px;">
                {{ $plan?->label ?? ucfirst($planName) }}
            </div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                ₱{{ number_format($plan?->base_price ?? 0) }} / year
                &nbsp;·&nbsp;
                {{ $plan?->student_cap ? number_format($plan->student_cap).' student cap' : 'Unlimited students' }}
            </div>
        </div>
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                     border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                     font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;
                     letter-spacing:0.1em;text-transform:uppercase;color:{{ $pc['color'] }};">
            <span style="width:5px;height:5px;border-radius:50%;background:{{ $pc['color'] }};display:inline-block;"></span>
            {{ ucfirst($planName) }}
        </span>
    </div>

    @if($plan && is_array($plan->features))
    <div style="padding:0 28px 20px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">
            @foreach($featureLabels as $key => $label)
            @php $has = $plan->hasFeature($key); @endphp
            <div style="display:flex;align-items:center;gap:7px;padding:7px 10px;
                        border:1px solid {{ $has ? $pc['border'] : 'var(--border)' }};
                        background:{{ $has ? $pc['bg'] : 'var(--surface2)' }};">
                @if($has)
                    <svg width="11" height="11" fill="none" stroke="{{ $pc['color'] }}" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                @else
                    <svg width="11" height="11" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                @endif
                <span style="font-size:12px;color:{{ $has ? 'var(--text2)' : 'var(--muted)' }};">{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Active promos --}}
@if($promos->count() > 0)
<div class="card fade-up fade-up-1">
    <div class="card-header">
        <div class="card-title">Available Promotions</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Code</th><th>Description</th><th>Discount</th><th>Valid Until</th></tr>
            </thead>
            <tbody>
                @foreach($promos as $promo)
                <tr>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);
                                     background:{{ $pc['bg'] }};border:1px solid {{ $pc['border'] }};
                                     padding:2px 8px;letter-spacing:0.1em;">{{ $promo->code }}</span>
                    </td>
                    <td style="font-size:13px;color:var(--text2);">{{ $promo->label }}</td>
                    <td style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:{{ $pc['color'] }};">
                        {{ $promo->discount_label }}
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $promo->ends_at?->format('M d, Y') ?? 'No expiry' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection