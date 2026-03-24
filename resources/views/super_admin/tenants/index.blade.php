@extends('layouts.superadmin-app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')

    <div class="card">
        <div class="section-header">
            <div>
                <div class="section-title">All Tenants</div>
                <div style="font-size:13px; color:var(--muted); margin-top:2px;">
                    {{ $tenants->total() }} tenant{{ $tenants->total() !== 1 ? 's' : '' }} registered
                </div>
            </div>
        </div>

        @if($tenants->isEmpty())
            <div class="empty">
                <div class="empty-icon">🏢</div>
                <div class="empty-title">No tenants found</div>
                <div class="empty-text">Create your first tenant to get started.</div>
                <br>
                <a href="{{ route('super_admin.tenants.create') }}" class="btn btn-primary">Create Tenant</a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tenant ID</th>
                            <th>Domain(s)</th>
                            <th>Created</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                        <tr>
                            <td>
                                <span style="font-family:'Syne',sans-serif; font-weight:700; font-size:15px;">
                                    {{ $tenant->id }}
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                    @forelse($tenant->domains as $domain)
                                        <span class="badge badge-purple">{{ $domain->domain }}</span>
                                    @empty
                                        <span class="badge badge-muted">No domain</span>
                                    @endforelse
                                </div>
                            </td>
                            <td style="color:var(--muted); font-size:13px;">
                                {{ $tenant->created_at->format('M d, Y') }}
                            </td>
                            <td style="color:var(--muted); font-size:13px;">
                                {{ $tenant->updated_at->diffForHumans() }}
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">View</a>
                                    <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm"
                                            onclick="openDeleteModal('{{ $tenant->id }}', '{{ route('super_admin.tenants.destroy', $tenant) }}')">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($tenants->hasPages())
                <div class="pagination">
                    {{ $tenants->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal-backdrop" id="deleteModal">
        <div class="modal">
            <div class="modal-title">Delete Tenant?</div>
            <div class="modal-text">
                You are about to permanently delete tenant <strong id="deleteTenantId" style="color:var(--text);"></strong>.
                This will drop their entire database and remove all associated data. This action <strong style="color:var(--danger);">cannot be undone</strong>.
            </div>
            <div class="modal-actions">
                <button class="btn btn-ghost" onclick="closeDeleteModal()">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
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
        document.getElementById('deleteModal').classList.add('open');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
    }

    // Close on backdrop click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush