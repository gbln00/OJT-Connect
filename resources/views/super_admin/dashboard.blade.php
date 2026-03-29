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
        color: var(--muted);
        margin-bottom: 6px;
    }
    .greeting-dot {
        width: 5px; height: 5px; background: var(--crimson);
        animation: flicker 8s ease-in-out infinite;
        flex-shrink: 0;
    }
    .greeting-title {
        font-family: 'Playfair Display', serif;
        font-weight: 900; font-size: clamp(1.5rem, 2vw, 1.9rem);
        color: var(--text); line-height: 1.1; letter-spacing: -0.02em;
    }
    .greeting-title em {
        font-style: italic;
        color: var(--crimson);
        font-weight: 400;
    }

    /* ── Stat cards ── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 10px;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 18px 20px;
        position: relative; overflow: hidden;
        transition: border-color .2s, transform .2s;
        cursor: default;
    }
    .stat-card::before {
        content: '';
        position: absolute; top: 0; left: 0; right: 0; height: 2px;
        background: var(--crimson); transform: scaleX(0); transform-origin: left;
        transition: transform 0.3s cubic-bezier(.22,.61,.36,1);
    }
    .stat-card:hover { border-color: var(--border2); transform: translateY(-2px); }
    .stat-card:hover::before { transform: scaleX(1); }

    .stat-card::after {
        content: ''; position: absolute;
        bottom: 0; right: 0;
        width: 40px; height: 40px;
        border-bottom: 1px solid rgba(140,14,3,0.12);
        border-right:  1px solid rgba(140,14,3,0.12);
    }

    .stat-top {
        display: flex; align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-icon {
        width: 36px; height: 36px;
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        color: var(--muted);
        flex-shrink: 0;
    }
    .stat-icon.crimson { border-color: rgba(140,14,3,0.35); background: rgba(140,14,3,0.08); color: var(--crimson); }
    .stat-icon.gold    { border-color: rgba(201,168,76,0.25); background: rgba(201,168,76,0.06); color: #c9a84c; }
    .stat-icon.blue    { border-color: rgba(91,143,185,0.25); background: rgba(91,143,185,0.06); color: #5b8fb9; }
    .stat-icon.green   { border-color: rgba(52,211,153,0.2); background: rgba(52,211,153,0.05); color: #34d399; }
    .stat-icon.red     { border-color: rgba(239,68,68,0.25); background: rgba(239,68,68,0.06); color: #f87171; }

    [data-theme="light"] .stat-icon.green { color: #0f9660; border-color: rgba(15,150,96,0.25); background: rgba(15,150,96,0.06); }

    .stat-tag {
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
        color: var(--muted);
        border: 1px solid var(--border2); padding: 2px 7px;
    }
    .stat-tag.pulse { color: #fbbf24; border-color: rgba(251,191,36,0.25); animation: flicker 3s ease-in-out infinite; }

    .stat-num {
        font-family: 'Playfair Display', serif;
        font-weight: 900; font-size: 2.2rem;
        color: var(--text); line-height: 1;
        margin-bottom: 5px;
        letter-spacing: -0.02em;
    }
    .stat-num.crimson-num { color: var(--crimson); }
    .stat-num.gold-num    { color: #c9a84c; }
    .stat-num.green-num   { color: #34d399; }
    .stat-num.red-num     { color: #f87171; }

    [data-theme="light"] .stat-num.green-num { color: #0f9660; }
    [data-theme="light"] .stat-num.red-num   { color: #c0392b; }

    .stat-label {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px; font-weight: 600;
        letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--muted);
    }

    /* Active/Inactive mini bar */
    .stat-mini-bar {
        display: flex; height: 3px;
        margin-top: 12px; gap: 2px; overflow: hidden;
        background: var(--border2);
    }

    /* ── Plan strip ── */
    .plan-strip {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .plan-tile {
        background: var(--surface);
        border: 1px solid var(--border);
        padding: 12px 16px;
        display: flex; align-items: center;
        justify-content: space-between;
        transition: border-color .2s;
    }
    .plan-tile:hover { border-color: var(--border2); }

    .plan-tile-label {
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.15em; text-transform: uppercase;
        color: var(--muted); margin-bottom: 5px;
    }
    .plan-tile-val {
        font-family: 'Playfair Display', serif;
        font-size: 1.3rem; font-weight: 700;
        color: var(--text2); line-height: 1;
    }
    .plan-tile-tag {
        width: 28px; height: 28px;
        border: 1px solid var(--border2);
        display: grid; place-items: center;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.05em;
        color: var(--muted);
        flex-shrink: 0;
    }
    .plan-tile-tag.crimson-tag {
        border-color: rgba(140,14,3,0.3);
        color: var(--crimson);
        background: rgba(140,14,3,0.06);
    }

    /* ── Bottom grid ── */
    .bottom-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 12px;
    }

    .right-col { display: flex; flex-direction: column; gap: 12px; }

    /* ── Quick actions ── */
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding: 14px;
    }
    .qa-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 8px; padding: 16px 10px;
        background: var(--surface2); border: 1px solid var(--border);
        cursor: pointer; text-decoration: none; text-align: center;
        transition: all 0.2s; position: relative;
    }
    .qa-btn:hover { border-color: rgba(140,14,3,0.35); background: rgba(140,14,3,0.06); }
    .qa-icon {
        width: 30px; height: 30px;
        border: 1px solid var(--border2);
        display: flex; align-items: center; justify-content: center;
        color: var(--text2);
    }
    .qa-btn:hover .qa-icon { border-color: rgba(140,14,3,0.35); color: var(--crimson); }
    .qa-label {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 11px; font-weight: 600;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: var(--text2);
    }
    .qa-btn:hover .qa-label { color: var(--crimson); }
    .qa-badge {
        position: absolute; top: 6px; right: 6px;
        background: var(--crimson); color: #fff;
        font-family: 'DM Mono', monospace;
        font-size: 9px; padding: 1px 5px; line-height: 14px;
    }

    /* ── Registration status ── */
    .reg-bar-wrap { padding: 14px 16px 10px; }
    .reg-bar {
        display: flex; height: 4px;
        background: var(--border2);
        overflow: hidden; margin-bottom: 10px;
    }
    .reg-bar-seg { transition: width .5s; }

    .reg-legend {
        display: flex; gap: 12px;
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
        color: var(--muted);
    }
    .reg-legend-item { display: flex; align-items: center; gap: 4px; }
    .reg-legend-dot { width: 5px; height: 5px; border-radius: 50%; }

    .reg-rows { padding: 0; }
    .reg-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 16px;
        border-top: 1px solid var(--border);
        font-size: 12px;
    }
    .reg-row-label { color: var(--text2); font-weight: 400; }
    .reg-row-val {
        font-family: 'Playfair Display', serif;
        font-weight: 700; font-size: 15px;
        color: var(--text);
    }
    .reg-row-val.pending-val  { color: #fbbf24; }
    .reg-row-val.approved-val { color: #34d399; }
    .reg-row-val.rejected-val { color: var(--crimson); }

    [data-theme="light"] .reg-row-val.approved-val { color: #0f9660; }

    /* ── Feed ── */
    .feed-item {
        display: flex; align-items: flex-start; gap: 10px;
        padding: 10px 16px;
        border-top: 1px solid var(--border);
        transition: background .12s;
    }
    .feed-item:hover { background: var(--surface2); }
    .feed-dot { width: 6px; height: 6px; margin-top: 5px; flex-shrink: 0; }
    .feed-body { flex: 1; min-width: 0; }
    .feed-text { font-size: 12.5px; color: var(--text2); line-height: 1.5; }
    .feed-text strong { color: var(--text); font-weight: 500; }
    .feed-time {
        font-family: 'DM Mono', monospace;
        font-size: 10px; color: var(--muted); margin-top: 2px;
    }

    /* ── Recent tenants table ── */
    .tenant-avatar {
        width: 28px; height: 28px; flex-shrink: 0;
        border: 1px solid rgba(140,14,3,0.3);
        background: rgba(140,14,3,0.07);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Playfair Display', serif;
        font-size: 11px; font-weight: 700;
        color: var(--crimson);
    }

    .domain-pill {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 2px 8px;
        border: 1px solid var(--border2);
        background: var(--surface2);
        font-family: 'DM Mono', monospace;
        font-size: 10.5px;
        color: var(--text2);
    }

    /* ── Plan badge inline in table ── */
    .plan-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
    }
    .plan-label.basic    { color: #5b8fb9; }
    .plan-label.standard { color: #c9a84c; }
    .plan-label.premium  { color: var(--crimson); }
    .plan-label.default  { color: var(--muted); }

    /* ── Status pill (tenant table) ── */
    .tenant-status {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px;
        font-family: 'DM Mono', monospace;
        font-size: 9px; letter-spacing: 0.12em; text-transform: uppercase;
    }
    .tenant-status.active {
        border: 1px solid rgba(52,211,153,0.2);
        background: rgba(52,211,153,0.05);
        color: #34d399;
    }
    .tenant-status.inactive {
        border: 1px solid rgba(239,68,68,0.2);
        background: rgba(239,68,68,0.05);
        color: #f87171;
    }
    .tenant-status-dot {
        width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
    }
    .tenant-status.active .tenant-status-dot {
        background: #22c55e;
        box-shadow: 0 0 5px rgba(34,197,94,0.55);
    }
    .tenant-status.inactive .tenant-status-dot { background: #ef4444; }

    [data-theme="light"] .tenant-status.active  { color: #0f9660; border-color: rgba(15,150,96,0.25); background: rgba(15,150,96,0.06); }
    [data-theme="light"] .tenant-status.inactive { color: #c0392b; border-color: rgba(192,57,43,0.25); background: rgba(192,57,43,0.06); }

    /* ── Responsive ── */
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
<div class="greeting fade-up">
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

<div class="stats-grid fade-up fade-up-1">

    {{-- Total Tenants --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="stat-tag">institutions</span>
        </div>
        <div class="stat-num">{{ $totalTenants }}</div>
        <div class="stat-label">Total Tenants</div>
        @if($totalTenants > 0)
        <div class="stat-mini-bar">
            @if($activePercent > 0)
                <div style="width:{{ $activePercent }}%;background:#34d399;"></div>
            @endif
            @if($inactivePercent > 0)
                <div style="width:{{ $inactivePercent }}%;background:rgba(239,68,68,0.6);"></div>
            @endif
        </div>
        @endif
    </div>

    {{-- Active Tenants --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon green">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag">live</span>
        </div>
        <div class="stat-num green-num">{{ $activeTenants }}</div>
        <div class="stat-label">Active Tenants</div>
    </div>

    {{-- Inactive Tenants --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon red">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/>
                </svg>
            </div>
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
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag {{ $pendingCount > 0 ? 'pulse' : '' }}">
                {{ $pendingCount > 0 ? 'needs review' : 'queue' }}
            </span>
        </div>
        <div class="stat-num gold-num">{{ $pendingCount }}</div>
        <div class="stat-label">Pending Approvals</div>
    </div>

</div>

{{-- ── PLAN STRIP ── --}}
<div class="plan-strip fade-up fade-up-2">

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
            <div class="plan-tile-val" style="color:var(--crimson);">{{ $rejectedCount }}</div>
        </div>
        <div class="plan-tile-tag crimson-tag">✕</div>
    </div>

</div>

{{-- ── BOTTOM GRID ── --}}
<div class="bottom-grid fade-up fade-up-3">

    {{-- ── RECENT TENANTS TABLE ── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Tenants</div>
            <a href="{{ route('super_admin.tenants.index') }}" class="card-action">View all →</a>
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
                        $tStatus = $tenant->status ?? 'active';
                        $tActive = $tStatus === 'active';
                        $tPlan   = $tenant->plan ?? null;
                        $planCls = match($tPlan) {
                            'basic'    => 'basic',
                            'standard' => 'standard',
                            'premium'  => 'premium',
                            default    => 'default',
                        };
                    @endphp
                    <tr>
                        {{-- Tenant --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div class="tenant-avatar">{{ strtoupper(substr($tenant->id, 0, 2)) }}</div>
                                <div>
                                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text);">{{ $tenant->id }}</div>
                                    @if($tenant->name ?? false)
                                        <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $tenant->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Domain --}}
                        <td>
                            @forelse($tenant->domains as $domain)
                                <span class="domain-pill">
                                    <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;
                                                 background:{{ $tActive ? '#22c55e' : '#ef4444' }};
                                                 {{ $tActive ? 'box-shadow:0 0 4px rgba(34,197,94,0.5);' : '' }}">
                                    </span>
                                    {{ $domain->domain }}
                                </span>
                            @empty
                                <span style="font-size:11px;color:var(--muted);font-family:monospace;">—</span>
                            @endforelse
                        </td>

                        {{-- Plan --}}
                        <td>
                            @if($tPlan)
                                <span class="plan-label {{ $planCls }}">{{ ucfirst($tPlan) }}</span>
                            @else
                                <span style="font-size:11px;color:var(--muted);font-family:monospace;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="tenant-status {{ $tActive ? 'active' : 'inactive' }}">
                                <span class="tenant-status-dot"></span>
                                {{ $tActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $tenant->created_at->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:40px;text-align:center;">
                            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:var(--muted);">No tenants yet</div>
                            <a href="{{ route('super_admin.tenants.create') }}" style="display:inline-block;margin-top:10px;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--crimson);text-decoration:none;">Create first tenant →</a>
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
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick actions</div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('super_admin.tenants.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                    </div>
                    <span class="qa-label">New Tenant</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="qa-label">Approvals</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                        </svg>
                    </div>
                    <span class="qa-label">Domains</span>
                </a>
            </div>
        </div>

        {{-- REGISTRATION STATUS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Registration Status</div>
                <span style="width:7px;height:7px;background:#34d399;display:inline-block;" class="flicker"></span>
            </div>

            @if($totalRegs > 0)
            <div class="reg-bar-wrap">
                <div class="reg-bar">
                    @if($approvedCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($approvedCount / $totalRegs) * 100) }}%;background:#34d399;"></div>
                    @endif
                    @if($pendingCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($pendingCount / $totalRegs) * 100) }}%;background:#fbbf24;"></div>
                    @endif
                    @if($rejectedCount > 0)
                        <div class="reg-bar-seg" style="width:{{ round(($rejectedCount / $totalRegs) * 100) }}%;background:var(--crimson);"></div>
                    @endif
                </div>
                <div class="reg-legend">
                    <div class="reg-legend-item"><div class="reg-legend-dot" style="background:#34d399;"></div> Approved</div>
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
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Registrations</div>
                <a href="{{ route('super_admin.approvals.pending') }}" class="card-action">View all →</a>
            </div>

            @forelse($recentRegistrations as $reg)
            <div class="feed-item">
                <div class="feed-dot" style="background:{{ $reg->status === 'approved' ? '#34d399' : ($reg->status === 'rejected' ? 'var(--crimson)' : '#fbbf24') }};"></div>
                <div class="feed-body">
                    <p class="feed-text">
                        <strong>{{ $reg->company_name }}</strong>
                        submitted a
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--text2);padding:1px 5px;border:1px solid var(--border2);background:var(--surface2);">{{ $reg->plan }}</span>
                        plan.
                    </p>
                    <p class="feed-time">{{ $reg->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div style="padding:30px 16px;text-align:center;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:.15em;text-transform:uppercase;color:var(--muted);">
                No registrations yet
            </div>
            @endforelse
        </div>

    </div>{{-- end right col --}}

</div>{{-- end bottom grid --}}

@endsection