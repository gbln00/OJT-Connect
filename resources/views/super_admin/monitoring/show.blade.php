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
 
@section('content')
<div style="max-width:800px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">
 
    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;
              animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;
              text-transform:uppercase;color:var(--muted);">
            Super Admin / Monitoring / {{ $tenant->id }}
        </span>
    </div>
 
    {{-- ── Resource Metrics ── --}}
    <div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);">
        @foreach([
            [$metrics['db_size_mb'].' MB',  'Database Size',    'stat-icon crimson'],
            [$metrics['storage_mb'].' MB',  'File Storage',     'stat-icon steel'],
            [$metrics['requests_7d'],        'Requests (7 days)','stat-icon gold'],
        ] as [$val, $label, $iconClass])
        <div class="stat-card">
            <div class="stat-top">
                <div class="{{ $iconClass }}">
                    <svg width="15" height="15" fill="none" stroke="currentColor"
                         stroke-width="1.8" viewBox="0 0 24 24">
                        <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                    </svg>
                </div>
                <span class="stat-tag">{{ strtolower($label) }}</span>
            </div>
            <div class="stat-num">{{ $val }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
        @endforeach
    </div>
 
    {{-- ── Student Cap ── --}}
    @php $cap = $metrics['cap_usage']; @endphp
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div class="card-title">Student Cap Usage</div>
        </div>
        <div style="padding:20px;">
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span style="font-size:13px;color:var(--text2);">
                    {{ $cap['used'] }} students used
                </span>
                <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);">
                    {{ $cap['cap'] ? $cap['used'].'/'.$cap['cap'] : $cap['used'].' / Unlimited' }}
                </span>
            </div>
            @if($cap['percent'] !== null)
            <div class="progress-track">
                <div class="progress-fill {{ $cap['percent'] > 90 ? '' : ($cap['percent'] > 70 ? '' : 'green') }}"
                     style="width:{{ $cap['percent'] }}%;
                     background:{{ $cap['percent'] > 90 ? 'var(--crimson)' : ($cap['percent'] > 70 ? '#c9a84c' : '#34d399') }};">
                </div>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px;font-family:'DM Mono',monospace;">
                {{ $cap['percent'] }}% of quota used
            </div>
            @else
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:#34d399;margin-top:4px;">
                // Unlimited (Premium plan)
            </div>
            @endif
        </div>
    </div>
 
    {{-- ── Record Counts ── --}}
    <div class="card fade-up fade-up-2">
        <div class="card-header">
            <div class="card-title">Database Record Counts</div>
        </div>
        <div class="table-wrap">
            <table>
                <tbody>
                    @foreach([
                        ['Students',       $metrics['record_counts']['students']],
                        ['Applications',   $metrics['record_counts']['applications']],
                        ['Hour Logs',      $metrics['record_counts']['hour_logs']],
                        ['Weekly Reports', $metrics['record_counts']['weekly_reports']],
                        ['Evaluations',    $metrics['record_counts']['evaluations']],
                    ] as [$label, $count])
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;
                             color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;">
                            {{ $label }}
                        </td>
                        <td style="text-align:right;">
                            <span style="font-family:'Playfair Display',serif;font-size:16px;
                                 font-weight:700;color:var(--text);">
                                {{ number_format($count) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
 
    {{-- ── Activity ── --}}
    <div class="card fade-up fade-up-3">
        <div class="card-header">
            <div class="card-title">Request Activity</div>
        </div>
        <div style="padding:20px;display:flex;gap:32px;flex-wrap:wrap;">
            @foreach([
                [$metrics['requests_7d'],  '7 days'],
                [$metrics['requests_30d'], '30 days'],
            ] as [$count, $label])
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:28px;
                     font-weight:900;color:var(--text);">{{ number_format($count) }}</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;
                     color:var(--muted);letter-spacing:0.12em;text-transform:uppercase;">
                    requests / {{ $label }}
                </div>
            </div>
            @endforeach
            <div>
                <div style="font-size:13px;color:var(--text2);">
                    {{ $metrics['last_activity']
                        ? $metrics['last_activity']->logged_at->format('M d, Y g:i A')
                        : 'No activity recorded yet' }}
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;
                     color:var(--muted);text-transform:uppercase;margin-top:3px;">
                    Last activity
                </div>
            </div>
        </div>
    </div>
 
</div>
@endsection
