@extends('layouts.superadmin-app')
@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;
              background:#8C0E03;color:rgba(255,255,255,0.92);font-family:'Barlow Condensed',sans-serif;
              font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;
              transition:background 0.2s,transform 0.15s;"
       onmouseover="this.style.background='#a81004';this.style.transform='translateY(-1px)'"
       onmouseout="this.style.background='#8C0E03';this.style.transform='translateY(0)'">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')

{{-- Stat strip --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(171,171,171,0.06);margin-bottom:1px;">
    @php $total = $tenants->total(); @endphp
    <div style="background:#0E1126;padding:16px 20px;">
        <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:#fff;line-height:1;">{{ $total }}</div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:4px;">Total Tenants</div>
    </div>
    <div style="background:#0E1126;padding:16px 20px;">
        <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:#fff;line-height:1;">{{ $tenants->sum(fn($t) => $t->domains->count()) }}</div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:4px;">Active Domains</div>
    </div>
    <div style="background:#0E1126;padding:16px 20px;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#fff;line-height:1;margin-top:4px;">
            {{ $tenants->first() ? $tenants->first()->created_at->format('M Y') : '—' }}
        </div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:4px;">Latest Created</div>
    </div>
</div>

<div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">All Tenants</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.35);margin-top:2px;">
                {{ $tenants->total() }} tenant{{ $tenants->total() !== 1 ? 's' : '' }} registered
            </div>
        </div>
    </div>

    @if($tenants->isEmpty())
        <div style="text-align:center;padding:60px 20px;">
            <div style="width:48px;height:48px;border:1px solid rgba(171,171,171,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="20" height="20" fill="none" stroke="rgba(171,171,171,0.3)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;margin-bottom:6px;">No tenants found</div>
            <div style="font-size:13px;color:rgba(171,171,171,0.35);margin-bottom:20px;">Create your first tenant to get started.</div>
            <a href="{{ route('super_admin.tenants.create') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;background:#8C0E03;color:rgba(255,255,255,0.92);
                      font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;">
                Create Tenant
            </a>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.08);">
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);font-weight:600;padding:0 16px 12px 0;text-align:left;">Tenant ID</th>
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);font-weight:600;padding:0 16px 12px 0;text-align:left;">Domain(s)</th>
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);font-weight:600;padding:0 16px 12px 0;text-align:left;">Created</th>
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);font-weight:600;padding:0 16px 12px 0;text-align:left;">Last Updated</th>
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(171,171,171,0.3);font-weight:600;padding:0 0 12px 0;text-align:left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.05);transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(171,171,171,0.02)'"
                        onmouseout="this.style.background='transparent'">
                        <td style="padding:14px 16px 14px 0;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:2px;height:28px;background:#8C0E03;flex-shrink:0;"></div>
                                <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:15px;color:#fff;letter-spacing:0.05em;">
                                    {{ $tenant->id }}
                                </span>
                            </div>
                        </td>
                        <td style="padding:14px 16px 14px 0;vertical-align:middle;">
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @forelse($tenant->domains as $domain)
                                    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                                                 border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.1);
                                                 font-size:11px;color:rgba(200,100,90,0.9);font-family:monospace;letter-spacing:0.05em;">
                                        <span style="width:5px;height:5px;background:#8C0E03;flex-shrink:0;display:inline-block;"></span>
                                        {{ $domain->domain }}
                                    </span>
                                @empty
                                    <span style="font-size:11px;color:rgba(171,171,171,0.25);font-family:monospace;">No domain</span>
                                @endforelse
                            </div>
                        </td>
                        <td style="padding:14px 16px 14px 0;vertical-align:middle;">
                            <span style="font-size:12px;color:rgba(171,171,171,0.35);font-family:monospace;">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td style="padding:14px 16px 14px 0;vertical-align:middle;">
                            <span style="font-size:12px;color:rgba(171,171,171,0.35);font-family:monospace;">
                                {{ $tenant->updated_at->diffForHumans() }}
                            </span>
                        </td>
                        <td style="padding:14px 0;vertical-align:middle;">
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('super_admin.tenants.show', $tenant) }}"
                                   style="display:inline-flex;align-items:center;padding:5px 12px;border:1px solid rgba(171,171,171,0.12);
                                          background:transparent;color:rgba(171,171,171,0.5);font-size:11px;font-weight:700;
                                          letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;
                                          transition:border-color 0.2s,color 0.2s;"
                                   onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.9)'"
                                   onmouseout="this.style.borderColor='rgba(171,171,171,0.12)';this.style.color='rgba(171,171,171,0.5)'">
                                    View
                                </a>
                                <a href="{{ route('super_admin.tenants.edit', $tenant) }}"
                                   style="display:inline-flex;align-items:center;padding:5px 12px;border:1px solid rgba(171,171,171,0.12);
                                          background:transparent;color:rgba(171,171,171,0.5);font-size:11px;font-weight:700;
                                          letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;
                                          transition:border-color 0.2s,color 0.2s;"
                                   onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.9)'"
                                   onmouseout="this.style.borderColor='rgba(171,171,171,0.12)';this.style.color='rgba(171,171,171,0.5)'">
                                    Edit
                                </a>
                                <button onclick="openDeleteModal('{{ $tenant->id }}', '{{ route('super_admin.tenants.destroy', $tenant) }}')"
                                        style="display:inline-flex;align-items:center;padding:5px 12px;border:1px solid rgba(140,14,3,0.25);
                                               background:transparent;color:rgba(200,80,70,0.6);font-size:11px;font-weight:700;
                                               letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                                               transition:border-color 0.2s,color 0.2s,background 0.2s;"
                                        onmouseover="this.style.borderColor='rgba(140,14,3,0.6)';this.style.color='rgba(220,100,90,0.9)';this.style.background='rgba(140,14,3,0.08)'"
                                        onmouseout="this.style.borderColor='rgba(140,14,3,0.25)';this.style.color='rgba(200,80,70,0.6)';this.style.background='transparent'">
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
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(171,171,171,0.06);">
                {{ $tenants->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Warning strip --}}
<div style="margin-top:12px;padding:14px 20px;border:1px solid rgba(140,14,3,0.18);background:rgba(140,14,3,0.04);
            font-size:12px;color:rgba(171,171,171,0.4);line-height:1.7;font-family:monospace;">
    <span style="color:rgba(200,90,80,0.65);font-weight:700;">// warning:</span>
    Deleting a tenant permanently drops their isolated database and all associated records. This cannot be undone.
</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.85);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.1);border-top:2px solid #8C0E03;width:100%;max-width:440px;margin:0 20px;padding:28px;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:rgba(171,171,171,0.5);line-height:1.7;margin-bottom:24px;">
            You are about to permanently delete tenant
            <strong id="deleteTenantId" style="color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:15px;"></strong>.
            This will drop their entire database and remove all data.
            <span style="color:rgba(200,80,70,0.8);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="closeDeleteModal()"
                    style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                Cancel
            </button>
            <form id="deleteForm" method="POST" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:8px 18px;border:1px solid rgba(140,14,3,0.5);background:rgba(140,14,3,0.15);
                               color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;letter-spacing:0.1em;
                               text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                    Yes, Delete
                </button>
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