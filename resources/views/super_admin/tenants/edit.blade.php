@extends('layouts.superadmin-app')

@section('title', 'Edit Tenant — ' . $tenant->id)
@section('page-title', 'Edit Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

<div style="max-width: 560px;">
    <div class="card">
        <div style="margin-bottom: 24px;">
            <div class="section-title">Edit Tenant</div>
            <div style="font-size:13px; color:var(--muted); margin-top:4px;">
                Tenant ID is immutable. Only the domain can be changed.
            </div>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}">
            @csrf @method('PUT')

            {{-- Tenant ID (read-only) --}}
            <div class="form-group">
                <label>Tenant ID</label>
                <input
                    type="text"
                    value="{{ $tenant->id }}"
                    disabled
                    style="opacity:.5; cursor:not-allowed;"
                >
                <div class="input-hint">Tenant ID cannot be changed after creation.</div>
            </div>

            {{-- Domain --}}
            <div class="form-group">
                <label for="domain">Domain</label>
                <input
                    type="text"
                    id="domain"
                    name="domain"
                    value="{{ old('domain', $tenant->domains->first()?->domain) }}"
                    placeholder="e.g. school.ojthub.com"
                    autofocus
                >
                @error('domain')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <hr class="divider">

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection