@extends('layouts.superadmin-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── Greeting ── */
    .greeting { margin-bottom: 24px; }
    .greeting-eyebrow {
        display: flex; align-items: center; gap: 8px;
        font-family: 'DM Mono', monospace;
        font-size: 10px; letter-spacing: 0.18em; text-transform: uppercase;
        color: rgba(171,171,171,0.25);
        margin-bottom: 6px;
    }
    .greeting-dot {
        width: 5px; height: 5px; background: var(--crimson);
        animation: flicker 8s ease-in-out infinite;
    }
    .greeting-title {
        font-family: 'Playfair Display', serif;
        font-weight: 900; font-size: clamp(1.5rem, 2vw, 1.9rem);
        color: #fff; line-height: 1.1; letter-spacing: -0.02em;
    }
    .greeting-title em { font-style: italic; color: rgba(171,171,171,0.4); font-weight: 400; }

    /* ── Stat cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 10px;
    }

    .stat-card {
        background: var(--night);
        border: 1px solid var(--border);
        padding: 18px 20px;
        position: relative; overflow: hidden;
        transition: border-color .2s;
    }
    .stat-card:hover { border-color: var(--border2); }

    .stat-card::after {
        content: ''; position: absolute;
        bottom: 0; right: 0;
        width: 40px; height: 40px;
        border-bottom: 1px solid rgba(140,14,3,0.15);
        border-right:  1px solid rgba(140,14,3,0.15);
    }

    .stat-top {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-icon {
        width: 32px; height: 32px;
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        color: rgba(171,171,171,0.35);
        flex-shrink: 0;
    }
    .stat-icon.crimson { border-color: rgba(140,14,3,0.3); background: rgba(140,14,3,0.08); color: rgba(140,14,3,0.8); }
    .stat-icon.gold    { border-color: rgba(201,168,76,0.25); background: rgba(201,168,76,0.06); color: #c9a84c; }
    .stat-icon.blue    { border-color: rgba(91,143,185,0.25); background: rgba(91,143,185,0.06); color: #5b8fb9; }
    .stat-icon.green   { border-color: rgba(74,222,128,0.2); background: rgba(74,222,128,0.05); color: #4ade80; }
    .stat-icon.red     { border-color: rgba(239,68,68,0.25); background: rgba(239,68,68,0.06); color: rgba(252,165,165,0.8); }

    .stat-tag {
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
        color: rgba(171,171,171,0.2); padding-top: 2px;
    }
    .stat-tag.pulse { color: #fbbf24; animation: flicker 3s ease-in-out infinite; }

    .stat-num {
        font-family: 'Playfair Display', serif;
        font-weight: 900; font-size: 2.4rem;
        color: #fff; line-height: 1;
        margin-bottom: 5px;
        letter-spacing: -0.02em;
    }
    .stat-num.crimson-num { color: var(--crimson); }
    .stat-num.gold-num    { color: #c9a84c; }
    .stat-num.green-num   { color: #4ade80; }
    .stat-num.red-num     { color: rgba(252,165,165,0.85); }

    .stat-label {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 10px; font-weight: 600;
        letter-spacing: 0.18em; text-transform: uppercase;
        color: rgba(171,171,171,0.25);
    }

    /* ── Active/Inactive mini bar inside stat card ── */
    .stat-mini-bar {
        display: flex;
        height: 2px;
        margin-top: 10px;
        gap: 2px;
        overflow: hidden;
    }

    /* ── Plan strip ── */
    .plan-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .plan-tile {
        background: var(--night);
        border: 1px solid var(--border);
        padding: 12px 14px;
        display: flex; align-items: center;
        justify-content: space-between;
        transition: border-color .2s;
    }
    .plan-tile:hover { border-color: var(--border2); }

    .plan-tile-label {
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
        color: rgba(171,171,171,0.28); margin-bottom: 4px;
    }
    .plan-tile-val {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem; font-weight: 700;
        color: rgba(171,171,171,0.75); line-height: 1;
    }
    .plan-tile-tag {
        width: 26px; height: 26px;
        border: 1px solid var(--border2);
        display: grid; place-items: center;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.05em;
        color: rgba(171,171,171,0.35);
        flex-shrink: 0;
    }
    .plan-tile-tag.crimson-tag { border-color: rgba(140,14,3,0.3); color: rgba(140,14,3,0.6); background: rgba(140,14,3,0.05); }

    /* ── Bottom grid ── */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 10px;
    }

    .right-col { display: flex; flex-direction: column; gap: 10px; }

    /* ── Quick actions ── */
    .qa-grid {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 1px;
        background: var(--border);
    }

    .qa-btn {
        display: flex; flex-direction: column; align-items: center;
        gap: 8px; padding: 14px 10px;
        background: var(--night);
        text-decoration: none;
        transition: background .15s;
        text-align: center; position: relative;
    }
    .qa-btn:hover { background: rgba(171,171,171,0.03); }
    .qa-btn:hover .qa-icon { border-color: rgba(140,14,3,0.4); color: var(--crimson); }

    .qa-icon {
        width: 30px; height: 30px;
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        color: rgba(171,171,171,0.35);
        transition: border-color .15s, color .15s;
    }

    .qa-label {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 10px; font-weight: 600;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: rgba(171,171,171,0.35);
    }

    .qa-badge {
        position: absolute; top: 8px; right: 8px;
        background: var(--crimson);
        color: #fff;
        font-family: 'DM Mono', monospace;
        font-size: 9px; font-weight: 500;
        padding: 1px 5px;
        line-height: 14px;
    }

    /* ── Reg status ── */
    .reg-bar-wrap { padding: 14px 16px 10px; }
    .reg-bar {
        display: flex; height: 3px;
        background: var(--border);
        overflow: hidden;
        margin-bottom: 10px;
    }
    .reg-bar-seg { transition: width .5s; }

    .reg-legend {
        display: flex; gap: 12px;
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
        color: rgba(171,171,171,0.28);
    }
    .reg-legend-item { display: flex; align-items: center; gap: 4px; }
    .reg-legend-dot { width: 5px; height: 5px; border-radius: 50%; }

    .reg-rows { padding: 0; }
    .reg-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 9px 16px;
        border-top: 1px solid var(--border);
        font-size: 12px;
    }
    .reg-row-label { color: rgba(171,171,171,0.35); font-weight: 300; }
    .reg-row-val { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 14px; color: rgba(171,171,171,0.7); }
    .reg-row-val.pending-val  { color: #fbbf24; }
    .reg-row-val.approved-val { color: #4ade80; }
    .reg-row-val.rejected-val { color: rgba(140,14,3,0.8); }

    /* ── Feed ── */
    .feed-item {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 10px 16px;
        border-top: 1px solid var(--border);
        transition: background .12s;
    }
    .feed-item:hover { background: rgba(171,171,171,0.02); }
    .feed-dot { width: 6px; height: 6px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
    .feed-body { flex: 1; min-width: 0; }
    .feed-text { font-size: 12px; color: rgba(171,171,171,0.5); line-height: 1.5; }
    .feed-text strong { color: rgba(171,171,171,0.8); font-weight: 500; }
    .feed-time {
        font-family: 'DM Mono', monospace;
        font-size: 10px; color: rgba(171,171,171,0.22); margin-top: 2px;
    }

    /* ── Recent tenants table ── */
    .tenant-avatar {
        width: 28px; height: 28px; flex-shrink: 0;
        border: 1px solid var(--border2);
        background: rgba(171,171,171,0.04);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Playfair Display', serif;
        font-size: 11px; font-weight: 700;
        color: rgba(171,171,171,0.4);
    }

    .domain-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 2px 8px;
        border: 1px solid var(--border2);
        background: rgba(171,171,171,0.03);
        font-family: 'DM Mono', monospace;
        font-size: 10.5px;
        color: rgba(171,171,171,0.5);
    }

    @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .plan-strip { grid-template-columns: repeat(2, 1fr); }
        .bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ── GREETING ── --}}
<div class="greeting">
    <div class="greeting-eyebrow">
        <div class="greeting-dot"></div>
        {{ now()->format('l, F j · Y') }}
    </div>
    <h1 class="greeting-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <em>{{ explode(' ', auth()->user()->name)[0] }}</em>
    </h1>
</div>

{{-- ── STAT CARDS ── --}}
@php
    $activePercent   = $totalTenants > 0 ? round(($activeTenants   / $totalTenants) * 100) : 0;
    $inactivePercent = $totalTenants > 0 ? round(($inactiveTenants / $totalTenants) * 100) : 0;
@endphp

<div class="stats-grid">

    {{-- Total Tenants --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="stat-tag">institutions</span>
        </div>
        <div class="stat-num">{{ $totalTenants }}</div>
        <div class="stat-label">Total Tenants</div>
        {{-- Active vs Inactive mini bar --}}
        @if($totalTenants > 0)
        <div class="stat-mini-bar">
            @if($activePercent > 0)
                <div style="width:{{ $activePercent }}%;background:#4ade80;"></div>
            @endif
            @if($inactivePercent > 0)
                <div style="width:{{ $inactivePercent }}%;background:rgba(239,68,68,0.6);"></div>
            @endif
        </div>
        @endif
    </div>

    {{-- Active Tenants ← was broken before --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon green">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag">live</span>
        </div>
        <div class="stat-num green-num">{{ $activeTenants }}</div>
        <div class="stat-label">Active Tenants</div>
    </div>

    {{-- Inactive Tenants ← new, was missing --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon red">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/>
                </svg>
            </div>
            {{-- pulse if any tenants are inactive --}}
            <span class="stat-tag {{ $inactiveTenants > 0 ? 'pulse' : '' }}">
                {{ $inactiveTenants > 0 ? 'suspended' : 'all clear' }}
            </span>
        </div>
        <div class="stat-num {{ $inactiveTenants > 0 ? 'red-num' : '' }}">{{ $inactiveTenants }}</div>
        <div class="stat-label">Inactive Tenants</div>
    </div>

    {{-- Pending Approvals --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag {{ $pendingCount > 0 ? 'pulse' : '' }}">{{ $pendingCount > 0 ? 'needs review' : 'queue' }}</span>
        </div>
        <div class="stat-num gold-num">{{ $pendingCount }}</div>
        <div class="stat-label">Pending Approvals</div>
    </div>

</div>

{{-- ── PLAN STRIP ── --}}
<div class="plan-strip">

    <div class="plan-tile">
        <div>
            <div class="plan-tile-label">Basic</div>
            <div class="plan-tile-val">{{ $basicCount }}</div>
        </div>
        <div class="plan-tile-tag">B</div>
    </div>

    <div class="plan-tile">
        <div>
            <div class="plan-tile-label">Standard</div>
            <div class="plan-tile-val">{{ $standardCount }}</div>
        </div>
        <div class="plan-tile-tag">S</div>
    </div>

    <div class="plan-tile">
        <div>
            <div class="plan-tile-label">Premium</div>
            <div class="plan-tile-val">{{ $premiumCount }}</div>
        </div>
        <div class="plan-tile-tag">P</div>
    </div>

    <div class="plan-tile">
        <div>
            <div class="plan-tile-label">Rejected</div>
            <div class="plan-tile-val" style="color:rgba(140,14,3,0.7)">{{ $rejectedCount }}</div>
        </div>
        <div class="plan-tile-tag crimson-tag">✕</div>
    </div>

</div>

{{-- ── BOTTOM GRID ── --}}
<div class="bottom-grid">

    {{-- ── RECENT TENANTS TABLE ── --}}
    <div class="sa-card">
        <div class="card-header">
            <div>
                <div class="card-title">Recent Tenants</div>
                <div class="card-title-main" style="margin-top:3px;">Latest provisioned environments</div>
            </div>
            <a href="{{ route('super_admin.tenants.index') }}" class="card-action">
                View all
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Domain</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTenants as $tenant)
                    @php
                        $tStatus   = $tenant->status ?? 'active';
                        $tActive   = $tStatus === 'active';
                        $tPlan     = $tenant->plan ?? null;
                        $planColor = match($tPlan) {
                            'basic'    => 'rgba(100,170,240,0.75)',
                            'standard' => 'rgba(200,100,90,0.85)',
                            'premium'  => 'rgba(210,170,70,0.85)',
                            default    => 'rgba(171,171,171,0.2)',
                        };
                    @endphp
                    <tr>
                        {{-- Tenant ID --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="tenant-avatar">{{ strtoupper(substr($tenant->id, 0, 2)) }}</div>
                                <div>
                                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:rgba(171,171,171,0.7);">{{ $tenant->id }}</div>
                                    @if($tenant->name ?? false)
                                        <div style="font-size:12px;color:rgba(171,171,171,0.35);margin-top:1px;">{{ $tenant->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Domain --}}
                        <td>
                            @forelse($tenant->domains as $domain)
                                <span class="domain-pill">
                                    <span style="width:5px;height:5px;border-radius:50%;
                                                 background:{{ $tActive ? '#4ade80' : '#ef4444' }};
                                                 flex-shrink:0;
                                                 {{ $tActive ? 'box-shadow:0 0 4px rgba(74,222,128,0.5);' : '' }}">
                                    </span>
                                    {{ $domain->domain }}
                                </span>
                            @empty
                                <span style="font-size:11px;color:rgba(171,171,171,0.2);font-family:monospace;">—</span>
                            @endforelse
                        </td>

                        {{-- Plan --}}
                        <td>
                            @if($tPlan)
                                <span style="font-family:'DM Mono',monospace;font-size:10px;
                                             letter-spacing:0.1em;text-transform:uppercase;
                                             color:{{ $planColor }};">
                                    {{ ucfirst($tPlan) }}
                                </span>
                            @else
                                <span style="font-size:11px;color:rgba(171,171,171,0.18);font-family:monospace;">—</span>
                            @endif
                        </td>

                        {{-- Status — now reflects real value ── --}}
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
                                         border:1px solid {{ $tActive ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)' }};
                                         background:{{ $tActive ? 'rgba(34,197,94,0.05)' : 'rgba(239,68,68,0.05)' }};
                                         font-family:'DM Mono',monospace;font-size:9px;
                                         letter-spacing:0.12em;text-transform:uppercase;
                                         color:{{ $tActive ? 'rgba(74,222,128,0.8)' : 'rgba(252,165,165,0.7)' }};">
                                <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;
                                             background:{{ $tActive ? '#22c55e' : '#ef4444' }};
                                             {{ $tActive ? 'box-shadow:0 0 5px rgba(34,197,94,0.55);' : '' }}">
                                </span>
                                {{ $tActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:rgba(171,171,171,0.3);">
                            {{ $tenant->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:40px;text-align:center;">
                            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:rgba(171,171,171,0.2);">No tenants yet</div>
                            <a href="{{ route('super_admin.tenants.create') }}" style="display:inline-block;margin-top:10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(140,14,3,0.7);text-decoration:none;">Create first tenant →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="right-col">

        {{-- QUICK ACTIONS --}}
        <div class="sa-card">
            <div class="card-header">
                <div class="card-title">Quick Actions</div>
            </div>
            <div class="qa-grid">

                <a href="{{ route('super_admin.tenants.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <span class="qa-label">New Tenant</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                        </svg>
                    </div>
                    <span class="qa-label">All Tenants</span>
                </a>

                <a href="{{ route('super_admin.approvals.pending') }}" class="qa-btn">
                    @if($pendingCount > 0)
                        <span class="qa-badge">{{ $pendingCount }}</span>
                    @endif
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="qa-label">Approvals</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                        </svg>
                    </div>
                    <span class="qa-label">Domains</span>
                </a>

            </div>
        </div>

        {{-- REGISTRATION STATUS --}}
        <div class="sa-card">
            <div class="card-header">
                <div class="card-title">Registration Status</div>
            </div>

            @if($totalRegs > 0)
            <div class="reg-bar-wrap">
                <div class="reg-bar">
                    @if($approvedCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($approvedCount / $totalRegs) * 100) }}%;background:#4ade80;"></div>
                    @endif
                    @if($pendingCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($pendingCount / $totalRegs) * 100) }}%;background:#fbbf24;"></div>
                    @endif
                    @if($rejectedCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($rejectedCount / $totalRegs) * 100) }}%;background:var(--crimson);"></div>
                    @endif
                </div>
                <div class="reg-legend">
                    <div class="reg-legend-item"><div class="reg-legend-dot" style="background:#4ade80;"></div> Approved</div>
                    <div class="reg-legend-item"><div class="reg-legend-dot" style="background:#fbbf24;"></div> Pending</div>
                    <div class="reg-legend-item"><div class="reg-legend-dot" style="background:var(--crimson);"></div> Rejected</div>
                </div>
            </div>
            @endif

            <div class="reg-rows">
                <div class="reg-row">
                    <span class="reg-row-label">Total registrations</span>
                    <span class="reg-row-val">{{ $totalRegs }}</span>
                </div>
                <div class="reg-row">
                    <span class="reg-row-label">Pending review</span>
                    <span class="reg-row-val pending-val">{{ $pendingCount }}</span>
                </div>
                <div class="reg-row">
                    <span class="reg-row-label">Approved</span>
                    <span class="reg-row-val approved-val">{{ $approvedCount }}</span>
                </div>
                <div class="reg-row">
                    <span class="reg-row-label">Rejected</span>
                    <span class="reg-row-val rejected-val">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>

        {{-- RECENT REGISTRATIONS FEED --}}
        <div class="sa-card">
            <div class="card-header">
                <div class="card-title">Recent Registrations</div>
                <a href="{{ route('super_admin.approvals.pending') }}" class="card-action">
                    View all
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @forelse($recentRegistrations as $reg)
            <div class="feed-item">
                <div class="feed-dot" style="background:{{ $reg->status === 'approved' ? '#4ade80' : ($reg->status === 'rejected' ? 'var(--crimson)' : '#fbbf24') }};"></div>
                <div class="feed-body">
                    <p class="feed-text">
                        <strong>{{ $reg->company_name }}</strong>
                        submitted a
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(171,171,171,0.5);padding:1px 5px;border:1px solid var(--border2);background:rgba(171,171,171,0.04);">{{ $reg->plan }}</span>
                        plan.
                    </p>
                    <p class="feed-time">{{ $reg->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div style="padding:30px 16px;text-align:center;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:rgba(171,171,171,0.18);">
                No registrations yet
            </div>
            @endforelse
        </div>

    </div>{{-- end right col --}}

</div>{{-- end bottom grid --}}

@endsection