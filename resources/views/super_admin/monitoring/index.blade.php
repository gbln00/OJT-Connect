@extends('layouts.superadmin-app')
@section('title', 'Tenant Monitoring')
@section('page-title', 'Tenant Monitoring')

@section('topbar-actions')
    <a href="{{ route('super_admin.monitoring.index') }}" class="btn btn-ghost btn-sm">
        Refresh
    </a>
@endsection

@section('content')

@php
    $totalTenants   = $tenants->count();
    $activeTenants  = $tenants->where('status', 'active')->count();
    $totalDbMb      = round(array_sum(array_column($summaries, 'db_size_mb')), 1);
    $totalStorageMb = round(array_sum(array_column($summaries, 'storage_mb')), 1);
    $totalBwMb      = round(array_sum(array_column($summaries, 'bandwidth_mb_30d')), 2);
@endphp

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                </svg>
            </div>
            <span class="stat-tag">total</span>
        </div>
        <div class="stat-num">{{ $totalTenants }}</div>
        <div class="stat-label">Total Tenants</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon green">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag">live</span>
        </div>
        <div class="stat-num green-num">{{ $activeTenants }}</div>
        <div class="stat-label">Active</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14a9 3 0 0018 0V5"/>
                </svg>
            </div>
            <span class="stat-tag">db</span>
        </div>
        <div class="stat-num">{{ $totalDbMb }}</div>
        <div class="stat-label">Total DB (MB)</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                </svg>
            </div>
            <span class="stat-tag">files</span>
        </div>
        <div class="stat-num">{{ $totalStorageMb }}</div>
        <div class="stat-label">File Storage (MB)</div>
    </div>

    {{-- NEW --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="border-color:rgba(91,143,185,0.35);background:rgba(91,143,185,0.08);color:#5b8fb9;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                </svg>
            </div>
            <span class="stat-tag">30d</span>
        </div>
        <div class="stat-num" style="color:#5b8fb9;">{{ $totalBwMb }}</div>
        <div class="stat-label">Bandwidth (MB)</div>
    </div>

</div>

{{-- ── Monitoring Table ── --}}
<div class="card fade-up fade-up-1">
    <div class="card-header">
        <div class="card-title-main">Tenant Resource Overview</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
            // refreshed on page load
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Plan</th>
                    <th>DB Size</th>
                    <th>Storage</th>
                    <th>Bandwidth (30d)</th>
                    <th>In / Out</th>
                    <th>Requests (7d)</th>
                    <th>Last Activity</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tenants as $tenant)
                @php
                    $s        = $summaries[$tenant->id] ?? [];
                    $dbMb     = $s['db_size_mb'] ?? 0;
                    $storageMb= $s['storage_mb'] ?? 0;
                    $req7d    = $s['requests_7d'] ?? 0;
                    $lastAct  = $s['last_activity'] ?? null;
                    $bwMb     = $s['bandwidth_mb_30d'] ?? 0;
                    $bytesIn  = $s['bytes_in_30d'] ?? 0;
                    $bytesOut = $s['bytes_out_30d'] ?? 0;
                    $mbIn     = round($bytesIn / 1048576, 2);
                    $mbOut    = round($bytesOut / 1048576, 2);
                    $bwColor  = $bwMb > 500 ? '#f87171' : ($bwMb > 100 ? '#c9a84c' : '#34d399');
                    $dbColor  = $dbMb > 100 ? '#f87171' : ($dbMb > 50 ? '#c9a84c' : '#34d399');
                    $planColors = [
                        'basic'   => ['border'=>'rgba(80,150,220,0.35)','bg'=>'rgba(80,150,220,0.08)','color'=>'rgba(100,170,240,0.8)'],
                        'standard'=> ['border'=>'rgba(140,14,3,0.4)','bg'=>'rgba(140,14,3,0.1)','color'=>'rgba(200,100,90,0.9)'],
                        'premium' => ['border'=>'rgba(160,120,40,0.45)','bg'=>'rgba(160,120,40,0.1)','color'=>'rgba(210,170,70,0.9)'],
                    ];
                    $pc = $planColors[$tenant->plan ?? ''] ?? null;
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($tenant->id, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);font-weight:500;">
                                    {{ $tenant->id }}
                                </div>
                                <div style="font-size:11px;color:var(--muted);">
                                    {{ $tenant->domains->first()?->domain ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($pc)
                        <span style="display:inline-flex;padding:2px 8px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:{{ $pc['color'] }};">
                            {{ ucfirst($tenant->plan) }}
                        </span>
                        @else
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:{{ $dbColor }};">{{ $dbMb }} MB</span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $storageMb }} MB</span>
                    </td>
                    {{-- NEW: Bandwidth total --}}
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;font-weight:600;color:{{ $bwColor }};">
                            {{ $bwMb }} MB
                        </span>
                        {{-- Mini bar --}}
                        @php $total = $bytesIn + $bytesOut ?: 1; @endphp
                        <div style="height:3px;background:var(--border2);margin-top:4px;width:80px;overflow:hidden;">
                            <div style="height:100%;width:{{ round(($bytesOut/$total)*100) }}%;background:#5b8fb9;"></div>
                        </div>
                    </td>
                    {{-- NEW: In / Out breakdown --}}
                    <td>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                            <span style="color:#34d399;">↑ {{ $mbIn }} MB</span>
                        </div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                            <span style="color:#5b8fb9;">↓ {{ $mbOut }} MB</span>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ number_format($req7d) }}</span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $lastAct ? $lastAct->logged_at->diffForHumans() : 'Never' }}
                    </td>
                    <td>
                        <a href="{{ route('super_admin.monitoring.show', $tenant) }}" class="btn btn-ghost btn-sm">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection