@extends('layouts.superadmin-app')

@section('title', 'Tenant — ' . $tenant->id)
@section('page-title', $tenant->id)

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-ghost">Edit</a>
        <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
@endsection

@section('content')

<div style="max-width: 640px; display:flex; flex-direction:column; gap:16px;">

    {{-- Tenant Info --}}
    <div class="card">
        <div class="section-title" style="margin-bottom:16px;">Tenant Details</div>

        <div class="detail-row">
            <div class="detail-key">Tenant ID</div>
            <div class="detail-val" style="font-family:'Syne',sans-serif; font-weight:700; font-size:15px;">{{ $tenant->id }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-key">Created</div>
            <div class="detail-val">{{ $tenant->created_at->format('F d, Y \a\t g:i A') }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-key">Last Updated</div>
            <div class="detail-val">{{ $tenant->updated_at->format('F d, Y \a\t g:i A') }}</div>
        </div>
    </div>

    {{-- Domains --}}
    <div class="card">
        <div class="section-title" style="margin-bottom:16px;">Domains</div>

        @forelse($tenant->domains as $domain)
            <div class="detail-row">
                <div class="detail-key">Domain</div>
                <div class="detail-val">
                    <span class="badge badge-purple" style="font-size:13px; padding:4px 12px;">{{ $domain->domain }}</span>
                </div>
            </div>
        @empty
            <div style="color:var(--muted); font-size:14px; padding:12px 0;">No domains configured.</div>
        @endforelse
    </div>

    {{-- Danger Zone --}}
    <div class="card" style="border-color:rgba(255,77,109,.25);">
        <div class="section-title" style="color:var(--danger); margin-bottom:8px;">Danger Zone</div>
        <div style="font-size:13px; color:var(--muted); margin-bottom:16px; line-height:1.6;">
            Deleting this tenant will permanently drop their database and remove all associated data including users, applications, hour logs, and evaluations.
        </div>
        <button class="btn btn-danger" onclick="document.getElementById('deleteModal').classList.add('open')">
            Delete Tenant
        </button>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal">
        <div class="modal-title">Delete Tenant?</div>
        <div class="modal-text">
            You are about to permanently delete <strong style="color:var(--text);">{{ $tenant->id }}</strong>.
            This will drop their entire database. This action <strong style="color:var(--danger);">cannot be undone</strong>.
        </div>
        <div class="modal-actions">
            <button class="btn btn-ghost" onclick="document.getElementById('deleteModal').classList.remove('open')">Cancel</button>
            <form method="POST" action="{{ route('super_admin.tenants.destroy', $tenant) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection