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
    $planColors = [
        'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.8)'],
        'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
        'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
    ];
@endphp

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(4,1fr);">
    @foreach([
        [$totalCount,    'Total Tenants', 'stat-icon crimson', '<path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
        [$activeCount,   'Active',        'stat-icon green',   '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$inactiveCount, 'Inactive',      'stat-icon red',     '<circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12" stroke-linecap="round"/>'],
        [$domainCount,   'Domains',       'stat-icon blue',    '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
    ] as [$val, $label, $iconClass, $iconSvg])
    <div class="stat-card">
        <div class="stat-top">
            <div class="{{ $iconClass }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
            </div>
            <span class="stat-tag">{{ strtolower($label) }}</span>
        </div>
        <div class="stat-num">{{ $val }}</div>
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
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    @php
                        $plan     = $tenant->plan ?? null;
                        $status   = $tenant->status ?? 'active';
                        $pc       = $planColors[$plan] ?? null;
                        $isActive = $status === 'active';
                    @endphp
                    <tr>
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
                        <td>
                            <span class="status-dot {{ $isActive ? 'active' : 'inactive' }}">{{ $isActive ? 'Active' : 'Inactive' }}</span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $tenant->created_at->format('M d, Y') }}<br>
                            <span style="font-size:10px;opacity:0.6;">{{ $tenant->updated_at->diffForHumans() }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:4px;">
                                <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">View</a>
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

{{-- Delete Modal --}}
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

@endsection

@push('scripts')
<script>
function openDeleteModal(tenantId, actionUrl) {
    document.getElementById('deleteTenantId').textContent = tenantId;
    document.getElementById('deleteForm').action = actionUrl;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>
@endpush