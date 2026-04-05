@extends('layouts.superadmin-app')
@section('title', 'Tenant Monitoring')
@section('page-title', 'Tenant Monitoring')
 
@section('topbar-actions')
    <a href="{{ route('super_admin.monitoring.index') }}" class="btn btn-ghost btn-sm">
        Tenants
    </a>
@endsection
 
@section('content')
 
{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(4,1fr);">
    @php
        $totalTenants  = $tenants->count();
        $activeTenants = $tenants->where('status','active')->count();
        $totalDbMb     = round(array_sum(array_column($summaries,'db_size_mb')), 1);
        $totalStorageMb= round(array_sum(array_column($summaries,'storage_mb')), 1);
    @endphp
 
    @foreach([
        [$totalTenants,   'Total Tenants',  'stat-icon crimson', '...building SVG...'],
        [$activeTenants,  'Active',          'stat-icon green',   '...building SVG...'],
        [$totalDbMb.' MB','Total DB Size',   'stat-icon steel',   '...building SVG...'],
        [$totalStorageMb.' MB','File Storage','stat-icon gold',   '...building SVG...'],
    ] as [$val, $label, $iconClass, $svg])
    <div class="stat-card">
        <div class="stat-top">
            <div class="{{ $iconClass }}">...</div>
            <span class="stat-tag">{{ strtolower($label) }}</span>
        </div>
        <div class="stat-num">{{ $val }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>
    @endforeach
</div>
 
{{-- ── Monitoring Table ── --}}
<div class="card fade-up fade-up-1" style="margin-top:20px;">
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
                    <th>Requests (7d)</th>
                    <th>Last Activity</th>
                    <th>Actions</th>
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
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:28px;height:28px;border:1px solid rgba(140,14,3,0.3);
                                 background:rgba(140,14,3,0.07);display:flex;align-items:center;
                                 justify-content:center;font-family:'Playfair Display',serif;
                                 font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($tenant->id,0,2)) }}
                            </div>
                            <div>
                                <div style="font-family:'DM Mono',monospace;font-size:12px;
                                     color:var(--text);font-weight:500;">{{ $tenant->id }} -
                                     {{ $tenant->domains->first()?->domain ?? '—' }}</div>
                                <div style="font-size:10px;color:var(--muted);">
                                    
                                     @if($tenant->name)
                                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $tenant->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $planColors = [
                                'basic'   =>['border'=>'rgba(80,150,220,0.35)','bg'=>'rgba(80,150,220,0.08)','color'=>'rgba(100,170,240,0.8)'],
                                'standard'=>['border'=>'rgba(140,14,3,0.4)','bg'=>'rgba(140,14,3,0.1)','color'=>'rgba(200,100,90,0.9)'],
                                'premium' =>['border'=>'rgba(160,120,40,0.45)','bg'=>'rgba(160,120,40,0.1)','color'=>'rgba(210,170,70,0.9)'],
                            ];
                            $pc = $planColors[$tenant->plan ?? ''] ?? null;
                        @endphp
                        @if($pc)
                        <span style="display:inline-flex;padding:2px 8px;
                             border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                             font-family:'Barlow Condensed',sans-serif;font-size:11px;
                             font-weight:600;letter-spacing:0.08em;text-transform:uppercase;
                             color:{{ $pc['color'] }};">
                            {{ ucfirst($tenant->plan) }}
                        </span>
                        @else
                            <span style="font-family:'DM Mono',monospace;font-size:11px;
                                 color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $dbColor = $dbMb > 100 ? '#f87171' : ($dbMb > 50 ? '#c9a84c' : '#34d399');
                        @endphp
                        <span style="font-family:'DM Mono',monospace;font-size:12px;
                             color:{{ $dbColor }};">{{ $dbMb }} MB</span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;
                             color:var(--text2);">{{ $storageMb }} MB</span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;
                             color:var(--text2);">{{ number_format($req7d) }}</span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $lastAct ? $lastAct->diffForHumans() : 'Never' }}
                    </td>
                    <td>
                        <a href="{{ route('super_admin.monitoring.show', $tenant) }}"
                           class="btn btn-ghost btn-sm">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
 
@endsection
