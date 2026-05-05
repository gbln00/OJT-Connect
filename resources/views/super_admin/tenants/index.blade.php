@extends('layouts.superadmin-app')
@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}" class="btn btn-primary btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')

@php
    use App\Models\PlanRequest;

    $planColors = [
        'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.8)'],
        'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
        'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
    ];

    // Load all pending plan requests with tenant info
    $pendingRequests = PlanRequest::with('tenant')
        ->where('status', 'pending')
        ->latest()
        ->get();

    // Map tenant_id => pending requests for quick lookup
    $pendingByTenant = $pendingRequests->groupBy('tenant_id');

    // Subscription urgency helper
    $getUrgency = function($tenant) {
        if (!$tenant->plan_expires_at) return null;
        $expired  = $tenant->checkSubscriptionExpired();
        $inGrace  = $tenant->checkInGracePeriod();
        $days     = $tenant->checkDaysUntilExpiry();
        if ($expired && !$inGrace) return ['label'=>'BLOCKED',      'color'=>'#ef4444', 'bg'=>'rgba(239,68,68,0.12)',  'border'=>'rgba(239,68,68,0.4)'];
        if ($inGrace)              return ['label'=>'GRACE',        'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.12)', 'border'=>'rgba(245,158,11,0.4)'];
        if ($days !== null && $days <= 7)  return ['label'=>$days.'d LEFT',   'color'=>'#f87171', 'bg'=>'rgba(248,113,113,0.1)', 'border'=>'rgba(248,113,113,0.35)'];
        if ($days !== null && $days <= 30) return ['label'=>$days.'d LEFT',   'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.08)', 'border'=>'rgba(245,158,11,0.3)'];
        return ['label'=>$days.'d',        'color'=>'#34d399', 'bg'=>'rgba(52,211,153,0.06)', 'border'=>'rgba(52,211,153,0.2)'];
    };
@endphp

{{-- ════════════════════════════════════════════════════
     PENDING PLAN REQUESTS — top-level alert panel
════════════════════════════════════════════════════ --}}
@if($pendingRequests->count() > 0)
<div class="fade-up" style="margin-bottom:20px;border:1px solid rgba(201,168,76,0.35);background:rgba(201,168,76,0.04);overflow:hidden;">

    {{-- Panel header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid rgba(201,168,76,0.2);background:rgba(201,168,76,0.06);">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="width:7px;height:7px;border-radius:50%;background:#c9a84c;display:inline-block;animation:flicker 3s ease-in-out infinite;"></span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;color:#c9a84c;">
                {{ $pendingRequests->count() }} Pending Plan Request{{ $pendingRequests->count() !== 1 ? 's' : '' }}
            </span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:rgba(201,168,76,0.6);">// Awaiting your action</span>
        </div>
        <button onclick="togglePendingPanel()" id="pending-toggle-btn"
                style="background:none;border:1px solid rgba(201,168,76,0.3);color:#c9a84c;padding:4px 12px;cursor:pointer;
                       font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.08em;transition:all 0.15s;"
                onmouseover="this.style.background='rgba(201,168,76,0.1)'"
                onmouseout="this.style.background='none'">
            <span id="pending-toggle-label">Hide ↑</span>
        </button>
    </div>

    {{-- Request rows --}}
    <div id="pending-panel">
        @foreach($pendingRequests as $pr)
        @php
            $prTenant  = $pr->tenant;
            $isUpgrade = $pr->request_type === 'upgrade';
        @endphp
        <div style="display:grid;grid-template-columns:1fr auto;gap:0;border-bottom:1px solid rgba(201,168,76,0.12);" id="pr-row-{{ $pr->id }}">

            {{-- Left: Request info --}}
            <div style="padding:14px 20px;display:flex;align-items:center;gap:18px;flex-wrap:wrap;">

                {{-- Tenant ID --}}
                <div style="display:flex;align-items:center;gap:8px;min-width:140px;">
                    <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);
                                display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                        {{ strtoupper(substr($pr->tenant_id, 0, 2)) }}
                    </div>
                    <div>
                        <a href="{{ route('super_admin.tenants.show', $pr->tenant_id) }}"
                           style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);font-weight:500;text-decoration:none;transition:color 0.15s;"
                           onmouseover="this.style.color='var(--crimson)'"
                           onmouseout="this.style.color='var(--text)'">
                            {{ $pr->tenant_id }}
                        </a>
                        @if($prTenant?->name)
                        <div style="font-size:10px;color:var(--muted);margin-top:1px;">{{ $prTenant->name }}</div>
                        @endif
                    </div>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:32px;background:var(--border);flex-shrink:0;"></div>

                {{-- Request type + plan change --}}
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="display:inline-flex;align-items:center;padding:3px 10px;
                                 border:1px solid {{ $isUpgrade ? 'rgba(52,211,153,0.3)' : 'rgba(248,113,113,0.3)' }};
                                 background:{{ $isUpgrade ? 'rgba(52,211,153,0.06)' : 'rgba(248,113,113,0.06)' }};
                                 font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                                 letter-spacing:0.1em;text-transform:uppercase;
                                 color:{{ $isUpgrade ? '#34d399' : '#f87171' }};">
                        {{ $isUpgrade ? '↑ Upgrade' : '↓ Downgrade' }}
                    </span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);padding:3px 8px;border:1px solid var(--border);background:var(--surface2);">
                        {{ ucfirst($pr->current_plan) }}
                    </span>
                    <span style="color:var(--muted);font-size:13px;">→</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:700;color:#c9a84c;padding:3px 8px;border:1px solid rgba(201,168,76,0.3);background:rgba(201,168,76,0.07);">
                        {{ ucfirst($pr->requested_plan) }}
                    </span>
                </div>

                {{-- Divider --}}
                <div style="width:1px;height:32px;background:var(--border);flex-shrink:0;"></div>

                {{-- Message excerpt --}}
                @if($pr->message)
                <div style="font-size:12px;color:var(--muted);max-width:280px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;font-style:italic;">
                    "{{ $pr->message }}"
                </div>
                @endif

                {{-- Time --}}
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);opacity:0.6;margin-left:auto;white-space:nowrap;">
                    {{ $pr->created_at->diffForHumans() }}
                </div>
            </div>

            {{-- Right: Inline action buttons --}}
            <div style="display:flex;align-items:stretch;border-left:1px solid rgba(201,168,76,0.15);">
                {{-- Approve --}}
                <form method="POST" action="{{ url('super-admin/plan-requests') }}/{{ $pr->id }}/approve" style="margin:0;display:flex;">
                    @csrf
                    <button type="submit"
                            style="padding:0 18px;background:rgba(52,211,153,0.06);border:none;border-right:1px solid rgba(201,168,76,0.15);
                                   color:#34d399;cursor:pointer;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                                   letter-spacing:0.12em;text-transform:uppercase;display:flex;align-items:center;gap:6px;
                                   transition:all 0.15s;white-space:nowrap;"
                            onmouseover="this.style.background='rgba(52,211,153,0.14)'"
                            onmouseout="this.style.background='rgba(52,211,153,0.06)'">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Approve
                    </button>
                </form>
                {{-- Reject --}}
                <button onclick="openRejectModal({{ $pr->id }}, '{{ $pr->tenant_id }}', '{{ url('super-admin/plan-requests') }}/{{ $pr->id }}/reject')"
                        style="padding:0 18px;background:rgba(248,113,113,0.06);border:none;
                               color:#f87171;cursor:pointer;font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
                               letter-spacing:0.12em;text-transform:uppercase;display:flex;align-items:center;gap:6px;
                               transition:all 0.15s;white-space:nowrap;"
                        onmouseover="this.style.background='rgba(248,113,113,0.14)'"
                        onmouseout="this.style.background='rgba(248,113,113,0.06)'">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reject
                </button>
                {{-- View --}}
                <a href="{{ route('super_admin.tenants.show', $pr->tenant_id) }}"
                   style="padding:0 14px;background:transparent;border-left:1px solid rgba(201,168,76,0.15);
                          color:var(--muted);font-family:'DM Mono',monospace;font-size:10px;
                          display:flex;align-items:center;gap:5px;text-decoration:none;
                          transition:all 0.15s;white-space:nowrap;"
                   onmouseover="this.style.color='var(--text)';this.style.background='var(--surface2)'"
                   onmouseout="this.style.color='var(--muted)';this.style.background='transparent'">
                    Full ↗
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(session('error'))
<div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.3);color:#ef4444;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;display:flex;align-items:center;gap:8px;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('error') }}
</div>
@endif

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(5,1fr);margin-bottom:20px;">
    @foreach([
        [$totalCount,                    'Total Tenants',   'stat-icon crimson', '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
        [$activeCount,                   'Active',          'stat-icon green',   '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$inactiveCount,                 'Inactive',        'stat-icon red',     '<circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/>'],
        [$domainCount,                   'Domains',         'stat-icon blue',    '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
        [$pendingRequests->count(),      'Pending Requests','stat-icon gold',    '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
    ] as [$val, $label, $iconClass, $iconSvg])
    <div class="stat-card" style="{{ $label === 'Pending Requests' && $val > 0 ? 'border-color:rgba(201,168,76,0.35);' : '' }}">
        <div class="stat-top">
            <div class="{{ $iconClass }}" style="{{ $label === 'Pending Requests' && $val > 0 ? 'border-color:rgba(201,168,76,0.3);background:rgba(201,168,76,0.1);color:#c9a84c;' : '' }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
            </div>
            <span class="stat-tag" style="{{ $label === 'Pending Requests' && $val > 0 ? 'color:#c9a84c;border-color:rgba(201,168,76,0.3);' : '' }}">{{ strtolower($label) }}</span>
        </div>
        <div class="stat-num" style="{{ $label === 'Pending Requests' && $val > 0 ? 'color:#c9a84c;' : '' }}">{{ $val }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── Main Card ── --}}
<div class="card fade-up fade-up-1">
    <div class="card-header">
        <div>
            <div class="card-title-main">All Tenants</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                // {{ $totalCount }} tenant{{ $totalCount !== 1 ? 's' : '' }} registered
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            @foreach(['basic'=>'Basic','standard'=>'Standard','premium'=>'Premium'] as $key => $label)
            @php $c = $planColors[$key]; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid {{ $c['border'] }};background:{{ $c['bg'] }};
                         font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;
                         letter-spacing:0.1em;text-transform:uppercase;color:{{ $c['color'] }};">
                {{ $label }}
            </span>
            @endforeach
        </div>
    </div>

    @if($tenants->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:8px;">No tenants yet</div>
            <div style="font-size:13px;color:var(--muted);font-family:'DM Mono',monospace;margin-bottom:24px;">// Create your first tenant to get started.</div>
            <a href="{{ route('super_admin.tenants.create') }}" class="btn btn-primary btn-sm">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                </svg>
                Create Tenant
            </a>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Domain(s)</th>
                        <th>Plan</th>
                        <th>Subscription</th>
                        <th>Requests</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    @php
                        $plan           = $tenant->plan ?? null;
                        $status         = $tenant->status ?? 'active';
                        $pc             = $planColors[$plan] ?? null;
                        $isActive       = $status === 'active';
                        $tenantPending  = $pendingByTenant[$tenant->id] ?? collect();
                        $hasPending     = $tenantPending->count() > 0;
                        $urgency        = $getUrgency($tenant);
                    @endphp
                    <tr style="{{ $hasPending ? 'border-left:2px solid rgba(201,168,76,0.5);' : '' }}">
                        {{-- Tenant identity --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);
                                            display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:12px;font-weight:700;color:var(--crimson);">
                                    {{ strtoupper(substr($tenant->id, 0, 2)) }}
                                </div>
                                <div>
                                    <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);font-weight:500;">{{ $tenant->id }}</div>
                                    @if($tenant->name)
                                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">{{ $tenant->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Domains --}}
                        <td>
                            <div style="display:flex;flex-wrap:wrap;gap:5px;">
                                @forelse($tenant->domains as $domain)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;
                                                 border:1px solid var(--border2);background:var(--surface2);
                                                 font-family:'DM Mono',monospace;font-size:10.5px;color:var(--text2);">
                                        <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;
                                                     background:{{ $isActive ? '#22c55e' : '#ef4444' }};
                                                     {{ $isActive ? 'box-shadow:0 0 4px rgba(34,197,94,0.5);' : '' }}"></span>
                                        {{ $domain->domain }}
                                    </span>
                                @empty
                                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Plan --}}
                        <td>
                            @if($plan && $pc)
                                <span style="display:inline-flex;padding:3px 9px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                                             font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:{{ $pc['color'] }};">
                                    {{ ucfirst($plan) }}
                                </span>
                            @else
                                <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                            @endif
                        </td>

                        {{-- Subscription expiry ── NEW COLUMN ── --}}
                        <td>
                            @if($urgency)
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
                                             border:1px solid {{ $urgency['border'] }};background:{{ $urgency['bg'] }};
                                             font-family:'DM Mono',monospace;font-size:10px;color:{{ $urgency['color'] }};
                                             letter-spacing:0.06em;text-transform:uppercase;">
                                    {{ $urgency['label'] }}
                                </span>
                            @else
                                <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);opacity:0.5;">No expiry</span>
                            @endif
                        </td>

                        {{-- Plan requests ── NEW COLUMN ── --}}
                        <td>
                            @if($hasPending)
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
                                             border:1px solid rgba(201,168,76,0.35);background:rgba(201,168,76,0.1);
                                             font-family:'DM Mono',monospace;font-size:10px;color:#c9a84c;
                                             letter-spacing:0.06em;">
                                    <span style="width:5px;height:5px;border-radius:50%;background:#c9a84c;display:inline-block;animation:flicker 3s ease-in-out infinite;"></span>
                                    {{ $tenantPending->count() }} pending
                                </span>
                            @else
                                <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);opacity:0.4;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="status-dot {{ $isActive ? 'active' : 'inactive' }}">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                        </td>

                        {{-- Created --}}
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $tenant->created_at->format('M d, Y') }}<br>
                            <span style="font-size:10px;opacity:0.6;">{{ $tenant->updated_at->diffForHumans() }}</span>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                <a href="{{ route('super_admin.tenants.show', $tenant) }}"
                                   class="btn btn-ghost btn-sm"
                                   style="{{ $hasPending ? 'color:#c9a84c;border-color:rgba(201,168,76,0.35);' : '' }}">
                                    @if($hasPending)
                                    <span style="width:5px;height:5px;border-radius:50%;background:#c9a84c;display:inline-block;animation:flicker 3s ease-in-out infinite;flex-shrink:0;"></span>
                                    @endif
                                    View
                                </a>
                                <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-ghost btn-sm">Edit</a>

                                <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}" style="margin:0;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status"      value="{{ $isActive ? 'inactive' : 'active' }}">
                                    <input type="hidden" name="plan"        value="{{ $tenant->plan ?? '' }}">
                                    <input type="hidden" name="redirect_to" value="index">
                                    <button type="submit" class="btn btn-sm {{ $isActive ? 'btn-danger' : 'btn-approve' }}">
                                        {{ $isActive ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                <button onclick="openDeleteModal('{{ $tenant->id }}', '{{ route('super_admin.tenants.destroy', $tenant) }}')"
                                        class="btn btn-ghost btn-sm" style="color:var(--muted);">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
        <div class="pagination">
            <span class="pagination-info">Showing {{ $tenants->firstItem() }}–{{ $tenants->lastItem() }} of {{ $tenants->total() }} tenants</span>
            <div style="display:flex;gap:4px;">
                @if($tenants->onFirstPage())
                    <span class="page-link disabled">← Prev</span>
                @else
                    <a href="{{ $tenants->previousPageUrl() }}" class="page-link">← Prev</a>
                @endif
                @if($tenants->hasMorePages())
                    <a href="{{ $tenants->nextPageUrl() }}" class="page-link">Next →</a>
                @else
                    <span class="page-link disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

<div class="flash flash-info fade-up fade-up-2" style="margin-top:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Deleting a tenant permanently drops their isolated database and all associated records. This cannot be undone.
</div>


{{-- ════════════════════════════════════════════════════
     DELETE MODAL
════════════════════════════════════════════════════ --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(3px);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeDeleteModal()">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid var(--crimson);width:100%;max-width:440px;margin:0 20px;padding:28px;animation:fadeUp 0.2s ease both;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.8;margin-bottom:24px;">
            You are about to permanently delete <strong id="deleteTenantId" style="color:var(--text);"></strong>. This will drop their entire database and remove all data.
            <span style="color:var(--crimson);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeDeleteModal()" class="btn btn-ghost btn-sm">Cancel</button>
            <form id="deleteForm" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════════
     REJECT MODAL — with admin notes
════════════════════════════════════════════════════ --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);backdrop-filter:blur(3px);z-index:999;align-items:center;justify-content:center;" onclick="if(event.target===this)closeRejectModal()">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid rgba(248,113,113,0.7);width:100%;max-width:460px;margin:0 20px;padding:28px;animation:fadeUp 0.2s ease both;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:6px;">Reject Plan Request</div>
        <div id="rejectSubtitle" style="font-size:13px;color:var(--muted);margin-bottom:20px;font-family:'DM Mono',monospace;font-size:11px;"></div>
        <form id="rejectForm" method="POST" style="margin:0;">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Reason / Admin Notes <span style="opacity:0.5;">(optional — sent to tenant)</span></label>
                <textarea name="admin_notes" rows="4" class="form-input"
                          style="resize:vertical;font-family:'Barlow',sans-serif;"
                          placeholder="Explain why the request is being rejected..."></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Delete modal ──────────────────────────────────
function openDeleteModal(tenantId, actionUrl) {
    document.getElementById('deleteTenantId').textContent = tenantId;
    document.getElementById('deleteForm').action = actionUrl;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// ── Reject modal ──────────────────────────────────
function openRejectModal(prId, tenantId, actionUrl) {
    document.getElementById('rejectSubtitle').textContent = '// Tenant: ' + tenantId + ' · Request #' + prId;
    document.getElementById('rejectForm').action = actionUrl;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// ── Pending panel toggle ──────────────────────────
let pendingVisible = true;
function togglePendingPanel() {
    const panel = document.getElementById('pending-panel');
    const label = document.getElementById('pending-toggle-label');
    pendingVisible = !pendingVisible;
    panel.style.display = pendingVisible ? 'block' : 'none';
    label.textContent   = pendingVisible ? 'Hide ↑' : 'Show ↓';
}
</script>
@endpush