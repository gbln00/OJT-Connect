@extends('layouts.superadmin-app')
@section('title', 'Monitoring — ' . $tenant->id)
@section('page-title', $tenant->id . ' — Metrics')

@section('topbar-actions')
    <a href="{{ route('super_admin.monitoring.index') }}" class="btn btn-ghost btn-sm">
        ← Monitoring
    </a>
    <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">
        Tenant Profile
    </a>
@endsection

@push('styles')
<style>
    .show-wrap {
        max-width: 860px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* ── Bandwidth bar chart ── */
    .bw-chart-wrap {
        padding: 20px 20px 14px;
    }
    .bw-chart-label {
        font-family: 'DM Mono', monospace;
        font-size: 9px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 10px;
    }
    .bw-bars {
        display: flex;
        align-items: flex-end;
        gap: 3px;
        height: 72px;
        padding-bottom: 4px;
    }
    .bw-bar {
        flex: 1;
        min-width: 3px;
        background: rgba(91,143,185,0.55);
        border-top: 1px solid rgba(91,143,185,0.8);
        transition: background 0.15s;
        position: relative;
        cursor: default;
    }
    .bw-bar:hover { background: rgba(91,143,185,0.85); }
    .bw-bar.zero  { background: var(--border); border-color: var(--border2); min-height: 2px; }
    .bw-axis {
        display: flex;
        justify-content: space-between;
        font-family: 'DM Mono', monospace;
        font-size: 9px;
        color: var(--muted);
        letter-spacing: 0.05em;
        margin-top: 6px;
        border-top: 1px solid var(--border);
        padding-top: 6px;
    }

    /* ── Bandwidth summary row ── */
    .bw-summary {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0;
        border-bottom: 1px solid var(--border);
    }
    .bw-stat {
        padding: 18px 20px;
        border-right: 1px solid var(--border);
    }
    .bw-stat:last-child { border-right: none; }
    .bw-stat-val {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 5px;
    }
    .bw-stat-label {
        font-family: 'DM Mono', monospace;
        font-size: 9px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .bw-stat-label .bw-arrow {
        display: inline-block;
        margin-right: 2px;
    }

    /* ── In/Out breakdown bar ── */
    .inout-wrap {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .inout-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .inout-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        width: 52px;
        flex-shrink: 0;
    }
    .inout-track {
        flex: 1;
        height: 5px;
        background: var(--border2);
        overflow: hidden;
    }
    .inout-fill { height: 100%; transition: width 0.4s; }
    .inout-val {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--text2);
        width: 64px;
        text-align: right;
        flex-shrink: 0;
    }

    /* ── Resource stat cards ── */
    .resource-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }

    /* ── Activity row ── */
    .activity-row {
        display: flex;
        gap: 0;
    }
    .activity-cell {
        flex: 1;
        padding: 20px 24px;
        border-right: 1px solid var(--border);
    }
    .activity-cell:last-child { border-right: none; }
    .activity-num {
        font-family: 'Playfair Display', serif;
        font-size: 28px;
        font-weight: 900;
        color: var(--text);
        line-height: 1;
        margin-bottom: 6px;
    }
    .activity-sublabel {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--muted);
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .activity-date {
        font-size: 13px;
        color: var(--text2);
        line-height: 1.4;
        margin-bottom: 4px;
    }

    /* ── Record count rows ── */
    .record-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 13px 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.12s;
    }
    .record-row:last-child { border-bottom: none; }
    .record-row:hover { background: var(--surface2); }
    .record-row-label {
        font-family: 'DM Mono', monospace;
        font-size: 11px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .record-dot {
        width: 5px;
        height: 5px;
        background: var(--border2);
        flex-shrink: 0;
    }
    .record-count {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--text);
    }

    @media (max-width: 640px) {
        .bw-summary { grid-template-columns: repeat(3, 1fr); }
        .resource-grid { grid-template-columns: 1fr 1fr; }
        .activity-row { flex-direction: column; }
        .activity-cell { border-right: none; border-bottom: 1px solid var(--border); }
    }
</style>
@endpush

@section('content')
@php
    $bw  = $metrics['bandwidth'];
    $cap = $metrics['cap_usage'];

    $daily   = $bw['daily'];
    $maxOut  = $daily->max('bytes_out') ?: 1;

    // fill missing dates so the chart always shows 30 slots
    $dateRange = collect();
    for ($i = 29; $i >= 0; $i--) {
        $dateRange->push(now()->subDays($i)->toDateString());
    }
    $dailyByDate = $daily->keyBy(fn($r) => \Carbon\Carbon::parse($r->date)->toDateString());
@endphp

<div class="show-wrap">

    {{-- ── Eyebrow ── --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;
              animation:flicker 8s ease-in-out infinite;flex-shrink:0;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;
              text-transform:uppercase;color:var(--muted);">
            Super Admin / Monitoring / {{ $tenant->id }}
        </span>
        @php $domain = $tenant->domains->first()?->domain; @endphp
        @if($domain)
        <span style="margin-left:auto;font-family:'DM Mono',monospace;font-size:10px;
              color:var(--muted);border:1px solid var(--border2);padding:2px 8px;">
            {{ $domain }}
        </span>
        @endif
    </div>

    {{-- ── Resource stat cards ── --}}
    <div class="resource-grid fade-up">

        {{-- DB Size --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon crimson">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M3 5v14a9 3 0 0018 0V5" stroke-linecap="round"/>
                        <path d="M3 12a9 3 0 0018 0" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="stat-tag">mysql</span>
            </div>
            <div class="stat-num">{{ $metrics['db_size_mb'] }} <span style="font-size:16px;font-weight:400;">MB</span></div>
            <div class="stat-label">Database Size</div>
        </div>

        {{-- Storage --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon steel">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                    </svg>
                </div>
                <span class="stat-tag">files</span>
            </div>
            <div class="stat-num">{{ $metrics['storage_mb'] }} <span style="font-size:16px;font-weight:400;">MB</span></div>
            <div class="stat-label">File Storage</div>
        </div>

        {{-- Requests 7d --}}
        <div class="stat-card">
            <div class="stat-top">
                <div class="stat-icon gold">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                    </svg>
                </div>
                <span class="stat-tag">7 days</span>
            </div>
            <div class="stat-num">{{ number_format($metrics['requests_7d']) }}</div>
            <div class="stat-label">Requests</div>
        </div>

    </div>

    {{-- ── Bandwidth card ── --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div class="card-title">Bandwidth Usage</div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// last 30 days</span>
        </div>

        {{-- Summary stats --}}
        <div class="bw-summary">
            <div class="bw-stat">
                <div class="bw-stat-val" style="color:#34d399;">{{ $bw['mb_in_7d'] }} MB</div>
                <div class="bw-stat-label"><span class="bw-arrow">↑</span> In (7d)</div>
            </div>
            <div class="bw-stat">
                <div class="bw-stat-val" style="color:#5b8fb9;">{{ $bw['mb_out_7d'] }} MB</div>
                <div class="bw-stat-label"><span class="bw-arrow">↓</span> Out (7d)</div>
            </div>
            <div class="bw-stat">
                <div class="bw-stat-val" style="color:#34d399;">{{ $bw['mb_in_30d'] }} MB</div>
                <div class="bw-stat-label"><span class="bw-arrow">↑</span> In (30d)</div>
            </div>
            <div class="bw-stat">
                <div class="bw-stat-val" style="color:#5b8fb9;">{{ $bw['mb_out_30d'] }} MB</div>
                <div class="bw-stat-label"><span class="bw-arrow">↓</span> Out (30d)</div>
            </div>
            <div class="bw-stat">
                <div class="bw-stat-val" style="color:var(--text);">{{ $bw['total_mb_30d'] }} MB</div>
                <div class="bw-stat-label">Total (30d)</div>
            </div>
        </div>

        {{-- In / Out proportion bars --}}
        @php
            $totalBytes = $bw['bytes_in_30d'] + $bw['bytes_out_30d'];
            $inPct  = $totalBytes > 0 ? round(($bw['bytes_in_30d']  / $totalBytes) * 100) : 0;
            $outPct = $totalBytes > 0 ? round(($bw['bytes_out_30d'] / $totalBytes) * 100) : 0;
        @endphp
        <div class="inout-wrap">
            <div class="inout-row">
                <div class="inout-label" style="color:#34d399;">↑ In</div>
                <div class="inout-track">
                    <div class="inout-fill" style="width:{{ $inPct }}%;background:#34d399;"></div>
                </div>
                <div class="inout-val">{{ $inPct }}%</div>
            </div>
            <div class="inout-row">
                <div class="inout-label" style="color:#5b8fb9;">↓ Out</div>
                <div class="inout-track">
                    <div class="inout-fill" style="width:{{ $outPct }}%;background:#5b8fb9;"></div>
                </div>
                <div class="inout-val">{{ $outPct }}%</div>
            </div>
        </div>

        {{-- Daily bar chart (30 slots, one per day) --}}
        <div class="bw-chart-wrap">
            <div class="bw-chart-label">Daily outbound — last 30 days</div>
            <div class="bw-bars">
                @foreach($dateRange as $date)
                @php
                    $row    = $dailyByDate->get($date);
                    $out    = $row ? $row->bytes_out : 0;
                    $height = $maxOut > 0 ? max(2, round(($out / $maxOut) * 100)) : 2;
                    $kb     = $out > 0 ? round($out / 1024, 1) . ' KB' : '0';
                    $label  = \Carbon\Carbon::parse($date)->format('M d');
                @endphp
                <div class="bw-bar {{ $out === 0 ? 'zero' : '' }}"
                     style="height:{{ $height }}%;"
                     title="{{ $label }}: {{ $kb }} out">
                </div>
                @endforeach
            </div>
            <div class="bw-axis">
                <span>{{ \Carbon\Carbon::parse($dateRange->first())->format('M d') }}</span>
                <span>{{ \Carbon\Carbon::parse($dateRange->last())->format('M d') }}</span>
            </div>
        </div>
    </div>

    {{-- ── Student Cap ── --}}
    <div class="card fade-up fade-up-2">
        <div class="card-header">
            <div class="card-title">Student Cap Usage</div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                {{ $cap['cap'] ? $cap['used'] . ' / ' . $cap['cap'] . ' slots' : 'Unlimited' }}
            </span>
        </div>
        <div style="padding:20px;">
            @if($cap['percent'] !== null)
            @php
                $capColor = $cap['percent'] > 90 ? 'var(--crimson)' : ($cap['percent'] > 70 ? '#c9a84c' : '#34d399');
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;">
                <span style="font-size:13px;color:var(--text2);">
                    <strong style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:{{ $capColor }};">
                        {{ $cap['used'] }}
                    </strong>
                    <span style="color:var(--muted);font-size:12px;"> students enrolled</span>
                </span>
                <span style="font-family:'DM Mono',monospace;font-size:11px;color:{{ $capColor }};">
                    {{ $cap['percent'] }}% of quota
                </span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:{{ $cap['percent'] }}%;background:{{ $capColor }};"></div>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:6px;font-family:'DM Mono',monospace;">
                @if($cap['percent'] > 90)
                    // Warning: approaching cap limit
                @elseif($cap['percent'] > 70)
                    // Moderate usage
                @else
                    // Within healthy range
                @endif
            </div>
            @else
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:6px;height:6px;background:#34d399;border-radius:50%;
                      box-shadow:0 0 6px rgba(52,211,153,0.5);flex-shrink:0;"></span>
                <span style="font-family:'DM Mono',monospace;font-size:11px;color:#34d399;">
                    Unlimited — Premium plan
                </span>
                <span style="margin-left:auto;font-family:'Playfair Display',serif;
                      font-size:22px;font-weight:900;color:var(--text);">
                    {{ $cap['used'] }}
                    <span style="font-size:12px;font-family:'Barlow',sans-serif;
                          color:var(--muted);font-weight:400;">students</span>
                </span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Record Counts ── --}}
    <div class="card fade-up fade-up-3">
        <div class="card-header">
            <div class="card-title">Database Record Counts</div>
        </div>
        @php
            $recordIcons = [
                'Students'       => ['crimson', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                'Applications'   => ['steel',   'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'Hour Logs'      => ['gold',    'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                'Weekly Reports' => ['steel',   'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                'Evaluations'    => ['gold',    'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
            ];
            $records = [
                'Students'       => $metrics['record_counts']['students'],
                'Applications'   => $metrics['record_counts']['applications'],
                'Hour Logs'      => $metrics['record_counts']['hour_logs'],
                'Weekly Reports' => $metrics['record_counts']['weekly_reports'],
                'Evaluations'    => $metrics['record_counts']['evaluations'],
            ];
        @endphp
        @foreach($records as $label => $count)
        @php [$iconColor, $iconPath] = $recordIcons[$label]; @endphp
        <div class="record-row">
            <div class="record-row-label">
                <div class="stat-icon {{ $iconColor }}" style="width:28px;height:28px;flex-shrink:0;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}"/>
                    </svg>
                </div>
                {{ $label }}
            </div>
            <div class="record-count">{{ number_format($count) }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Request Activity ── --}}
    <div class="card fade-up fade-up-4">
        <div class="card-header">
            <div class="card-title">Request Activity</div>
            @php
                $lastLog = $metrics['last_activity'];
            @endphp
            @if($lastLog)
            <span class="status-dot active" style="font-size:11px;">
                Active {{ $lastLog->logged_at->diffForHumans() }}
            </span>
            @else
            <span class="status-dot inactive" style="font-size:11px;">No activity</span>
            @endif
        </div>
        <div class="activity-row">
            <div class="activity-cell">
                <div class="activity-num">{{ number_format($metrics['requests_7d']) }}</div>
                <div class="activity-sublabel">Requests / 7 days</div>
            </div>
            <div class="activity-cell">
                <div class="activity-num">{{ number_format($metrics['requests_30d']) }}</div>
                <div class="activity-sublabel">Requests / 30 days</div>
            </div>
            <div class="activity-cell">
                @if($lastLog)
                <div class="activity-date">{{ $lastLog->logged_at->format('M d, Y') }}</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                    {{ $lastLog->logged_at->format('g:i A') }}
                </div>
                @else
                <div class="activity-date" style="color:var(--muted);">No activity recorded</div>
                @endif
                <div class="activity-sublabel" style="margin-top:4px;">Last request</div>
            </div>
        </div>
    </div>

    @php
        $status   = $tenant->status ?? 'active';
        $isActive = $status === 'active';
        $planColors = [
            'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)', 'dot'=>'#5b8fb9'],
            'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)',   'dot'=>'#c0392b'],
            'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)',   'dot'=>'#c9a84c'],
        ];
        $pc = $planColors[$tenant->plan ?? ''] ?? null;

    @endphp

    {{-- Subtle background texture --}}

    <div class="card fade-up" style="margin-bottom:16px;overflow:hidden;position:relative;">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%, rgba(140,14,3,0.04) 0%, transparent 65%);pointer-events:none;"></div>

        <div style="padding:28px 28px 24px;position:relative;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">

                {{-- Left: identity --}}
                <div style="display:flex;align-items:center;gap:18px;">
                    <div style="width:60px;height:60px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;">
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:24px;color:var(--crimson);">
                            {{ strtoupper(substr($tenant->id, 0, 1)) }}
                        </span>
                        {{-- Active indicator --}}
                        <span style="position:absolute;bottom:-3px;right:-3px;width:10px;height:10px;border-radius:50%;
                                    background:{{ $isActive ? '#22c55e' : '#ef4444' }};border:2px solid var(--surface);
                                    {{ $isActive ? 'box-shadow:0 0 6px rgba(34,197,94,0.5);' : '' }}"></span>
                    </div>
                    <div>
                        <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--text);line-height:1.1;margin-bottom:4px;">
                            {{ $tenant->id }}
                        </div>
                        @if($tenant->name)
                        <div style="font-size:13px;color:var(--muted);">{{ $tenant->name }}</div>
                        @endif
                        @if($tenant->email)
                        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:3px;opacity:0.7;">{{ $tenant->email }}</div>
                        @endif
                    </div>
                </div>

                {{-- Right: plan + actions --}}
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        {{-- Plan badge --}}
                        @if($pc)
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:{{ $pc['color'] }};">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $pc['dot'] }};display:inline-block;"></span>
                            {{ ucfirst($tenant->plan) }}
                        </span>
                        @endif
                        {{-- Status badge --}}
                        <span class="status-dot {{ $isActive ? 'active' : 'inactive' }}" style="font-size:12.5px;">
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

