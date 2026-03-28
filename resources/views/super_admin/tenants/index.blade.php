@extends('layouts.superadmin-app')
@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;
              background:#8C0E03;color:rgba(255,255,255,0.92);font-family:'Barlow Condensed',sans-serif;
              font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;
              transition:background 0.2s,transform 0.15s,box-shadow 0.2s;"
       onmouseover="this.style.background='#a81004';this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 32px rgba(140,14,3,0.35)'"
       onmouseout="this.style.background='#8C0E03';this.style.transform='translateY(0)';this.style.boxShadow='none'">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')

@php
    $planColors = [
        'basic'   => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.8)'],
        'standard'     => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
        'premium' => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
    ];
@endphp

{{-- ── Stat Strip ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(171,171,171,0.06);margin-bottom:1px;">
    @foreach([
        [$totalCount,    'Total Tenants', '#fff'],
        [$activeCount,   'Active',        'rgba(34,197,94,0.85)'],
        [$inactiveCount, 'Inactive',      'rgba(239,68,68,0.75)'],
        [$domainCount,   'Domains',       '#fff'],
    ] as [$val, $label, $color])
    <div style="background:#0E1126;padding:18px 22px;transition:background 0.2s;"
         onmouseover="this.style.background='rgba(14,17,38,0.8)'"
         onmouseout="this.style.background='#0E1126'">
        <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:{{ $color }};line-height:1;letter-spacing:-0.02em;">{{ $val }}</div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:6px;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── Main Card ── --}}
<div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">All Tenants</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.35);margin-top:2px;font-family:monospace;">
              // {{ $totalCount }} tenant{{ $totalCount !== 1 ? 's' : '' }} registered
            </div>
        </div>

        {{-- Plan legend --}}
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;">
            @foreach(['basic'=>'Basic','standard'=>'Standard','premium'=>'Premium'] as $key => $label)
            @php $c = $planColors[$key]; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid {{ $c['border'] }};background:{{ $c['bg'] }};
                         font-size:10px;color:{{ $c['color'] }};font-family:monospace;letter-spacing:0.1em;text-transform:uppercase;">
                {{ $label }}
            </span>
            @endforeach
        </div>
    </div>

    @if($tenants->isEmpty())
        {{-- Empty state --}}
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid rgba(171,171,171,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="rgba(171,171,171,0.25)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">No tenants yet</div>
            <div style="font-size:13px;color:rgba(171,171,171,0.35);margin-bottom:24px;font-family:monospace;">// Create your first tenant to get started.</div>
            <a href="{{ route('super_admin.tenants.create') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:10px 22px;background:#8C0E03;color:rgba(255,255,255,0.92);
                      font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;
                      transition:background 0.2s;"
               onmouseover="this.style.background='#a81004'" onmouseout="this.style.background='#8C0E03'">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                </svg>
                Create Tenant
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:760px;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.08);">
                        @foreach(['Tenant', 'Domain(s)', 'Plan', 'Status', 'Created', 'Actions'] as $th)
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;
                                   color:rgba(171,171,171,0.28);font-weight:600;padding:0 14px 12px 0;text-align:left;
                                   {{ $loop->last ? 'padding-right:0;' : '' }}">
                            {{ $th }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    @php
                        $plan   = $tenant->plan   ?? null;
                        $status = $tenant->status ?? 'active';
                        $pc     = $planColors[$plan] ?? ['border'=>'rgba(171,171,171,0.12)','bg'=>'transparent','color'=>'rgba(171,171,171,0.25)'];
                        $isActive = $status === 'active';
                    @endphp
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.05);transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(171,171,171,0.018)'"
                        onmouseout="this.style.background='transparent'">

                        {{-- Tenant ID --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:2px;height:32px;background:#8C0E03;flex-shrink:0;"></div>
                                <div>
                                    <div style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:15px;
                                                color:#fff;letter-spacing:0.05em;line-height:1.2;">
                                        {{ $tenant->id }}
                                    </div>
                                    @if($tenant->name)
                                    <div style="font-size:11px;color:rgba(171,171,171,0.3);font-family:monospace;margin-top:2px;">
                                        {{ $tenant->name }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Domains --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="display:flex;flex-wrap:wrap;gap:5px;">
                                @forelse($tenant->domains as $domain)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
                                                 border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.08);
                                                 font-size:11px;color:rgba(200,100,90,0.85);font-family:monospace;letter-spacing:0.04em;">
                                        <span style="width:4px;height:4px;background:#8C0E03;flex-shrink:0;display:inline-block;border-radius:50%;"></span>
                                        {{ $domain->domain }}
                                    </span>
                                @empty
                                    <span style="font-size:11px;color:rgba(171,171,171,0.2);font-family:monospace;">—</span>
                                @endforelse
                            </div>
                        </td>

                        {{-- Plan --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            @if($plan)
                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                                             border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                                             font-size:10px;color:{{ $pc['color'] }};font-family:monospace;
                                             letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                                    {{ ucfirst($plan) }}
                                </span>
                            @else
                                <span style="font-size:11px;color:rgba(171,171,171,0.2);font-family:monospace;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;
                                         border:1px solid {{ $isActive ? 'rgba(34,197,94,0.25)' : 'rgba(239,68,68,0.2)' }};
                                         background:{{ $isActive ? 'rgba(34,197,94,0.06)' : 'rgba(239,68,68,0.06)' }};
                                         font-size:10px;color:{{ $isActive ? 'rgba(74,222,128,0.85)' : 'rgba(252,165,165,0.75)' }};
                                         font-family:monospace;letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                                <span style="width:5px;height:5px;border-radius:50%;flex-shrink:0;
                                             background:{{ $isActive ? '#22c55e' : '#ef4444' }};
                                             {{ $isActive ? 'box-shadow:0 0 6px rgba(34,197,94,0.6);' : '' }}">
                                </span>
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Created --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="font-size:12px;color:rgba(171,171,171,0.35);font-family:monospace;line-height:1.5;">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </div>
                            <div style="font-size:10px;color:rgba(171,171,171,0.2);font-family:monospace;margin-top:2px;">
                                {{ $tenant->updated_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td style="padding:15px 0;vertical-align:middle;">
                            <div style="display:flex;gap:5px;align-items:center;">
                                <a href="{{ route('super_admin.tenants.show', $tenant) }}"
                                   title="View"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;
                                          border:1px solid rgba(171,171,171,0.1);background:transparent;color:rgba(171,171,171,0.4);
                                          text-decoration:none;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                                   onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.9)';this.style.background='rgba(171,171,171,0.05)'"
                                   onmouseout="this.style.borderColor='rgba(171,171,171,0.1)';this.style.color='rgba(171,171,171,0.4)';this.style.background='transparent'">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('super_admin.tenants.edit', $tenant) }}"
                                   title="Edit"
                                   style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;
                                          border:1px solid rgba(171,171,171,0.1);background:transparent;color:rgba(171,171,171,0.4);
                                          text-decoration:none;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                                   onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.9)';this.style.background='rgba(171,171,171,0.05)'"
                                   onmouseout="this.style.borderColor='rgba(171,171,171,0.1)';this.style.color='rgba(171,171,171,0.4)';this.style.background='transparent'">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>

                                {{-- Quick-toggle status --}}
                                <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}" style="margin:0;">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $isActive ? 'inactive' : 'active' }}">
                                    <input type="hidden" name="plan"   value="{{ $tenant->plan ?? '' }}">
                                    {{-- domain is nullable in update() now, omit it entirely so no validation error --}}
                                    <button type="submit"
                                            title="{{ $isActive ? 'Deactivate' : 'Activate' }}"
                                            style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;
                                                border:1px solid {{ $isActive ? 'rgba(239,68,68,0.2)' : 'rgba(34,197,94,0.2)' }};
                                                background:transparent;
                                                color:{{ $isActive ? 'rgba(252,165,165,0.5)' : 'rgba(74,222,128,0.5)' }};
                                                cursor:pointer;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                                            onmouseover="this.style.borderColor='{{ $isActive ? 'rgba(239,68,68,0.5)' : 'rgba(34,197,94,0.5)' }}';
                                                        this.style.color='{{ $isActive ? 'rgba(252,165,165,0.9)' : 'rgba(74,222,128,0.9)' }}';
                                                        this.style.background='{{ $isActive ? 'rgba(239,68,68,0.06)' : 'rgba(34,197,94,0.06)' }}'"
                                            onmouseout="this.style.borderColor='{{ $isActive ? 'rgba(239,68,68,0.2)' : 'rgba(34,197,94,0.2)' }}';
                                                        this.style.color='{{ $isActive ? 'rgba(252,165,165,0.5)' : 'rgba(74,222,128,0.5)' }}';
                                                        this.style.background='transparent'">
                                        @if($isActive)
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                                <rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>
                                            </svg>
                                        @else
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24">
                                                <polygon points="5,3 19,12 5,21"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                <button onclick="openDeleteModal('{{ $tenant->id }}', '{{ route('super_admin.tenants.destroy', $tenant) }}')"
                                        title="Delete"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;
                                               border:1px solid rgba(140,14,3,0.2);background:transparent;
                                               color:rgba(200,80,70,0.5);cursor:pointer;
                                               transition:border-color 0.2s,color 0.2s,background 0.2s;"
                                        onmouseover="this.style.borderColor='rgba(140,14,3,0.55)';this.style.color='rgba(220,100,90,0.9)';this.style.background='rgba(140,14,3,0.08)'"
                                        onmouseout="this.style.borderColor='rgba(140,14,3,0.2)';this.style.color='rgba(200,80,70,0.5)';this.style.background='transparent'">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="3,6 5,6 21,6"/>
                                        <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                                        <path d="M10,11v6M14,11v6"/>
                                        <path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($tenants->hasPages())
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(171,171,171,0.06);">
                {{ $tenants->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Warning strip --}}
<div style="margin-top:8px;padding:14px 20px;border:1px solid rgba(140,14,3,0.15);background:rgba(140,14,3,0.03);
            font-size:12px;color:rgba(171,171,171,0.35);line-height:1.7;font-family:monospace;">
    <span style="color:rgba(200,90,80,0.6);font-weight:700;">// warning:</span>
    Deleting a tenant permanently drops their isolated database and all associated records. This cannot be undone.
    The <span style="color:rgba(171,171,171,0.5);">⏸ / ▶</span> buttons toggle a tenant's active status inline without leaving the page.
</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.88);z-index:999;align-items:center;justify-content:center;backdrop-filter:blur(2px);">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.1);border-top:2px solid #8C0E03;
                width:100%;max-width:440px;margin:0 20px;padding:28px;
                animation:fadeUp 0.25s cubic-bezier(.22,.61,.36,1) both;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:rgba(171,171,171,0.5);line-height:1.8;margin-bottom:24px;font-family:monospace;">
            // You are about to permanently delete<br>
            <strong id="deleteTenantId" style="color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:15px;letter-spacing:0.05em;"></strong>.<br>
            This will drop their entire database and remove all data.
            <span style="color:rgba(200,80,70,0.8);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeDeleteModal()"
                    style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                           transition:border-color 0.2s,color 0.2s;"
                    onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                    onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                Cancel
            </button>
            <form id="deleteForm" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:8px 18px;border:1px solid rgba(140,14,3,0.5);background:rgba(140,14,3,0.15);
                               color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;letter-spacing:0.1em;
                               text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                               transition:background 0.2s,border-color 0.2s;"
                        onmouseover="this.style.background='rgba(140,14,3,0.28)';this.style.borderColor='rgba(140,14,3,0.75)'"
                        onmouseout="this.style.background='rgba(140,14,3,0.15)';this.style.borderColor='rgba(140,14,3,0.5)'">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<style>
@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
</style>

@endsection

@push('scripts')
<script>
function openDeleteModal(tenantId, actionUrl) {
    document.getElementById('deleteTenantId').textContent = tenantId;
    document.getElementById('deleteForm').action = actionUrl;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush