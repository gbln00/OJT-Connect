@extends('layouts.superadmin-app')
@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Tenants
    </a>
@endsection

@section('content')

<div style="max-width: 560px;">
    <div class="card">
        <div style="margin-bottom: 24px;">
            <div class="section-title">New Tenant</div>
            <div style="font-size:13px; color:var(--muted); margin-top:4px; line-height:1.6;">
                Each tenant gets an isolated database. A migration will run automatically after creation.
            </div>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.store') }}">
            @csrf

            <div class="form-group">
                <label for="id">Tenant ID</label>
                <input
                    type="text"
                    id="id"
                    name="id"
                    value="{{ old('id') }}"
                    placeholder="e.g. university-of-manila"
                    autocomplete="off"
                    autofocus
                >
                <div class="input-hint">Lowercase letters, numbers, and hyphens only. This cannot be changed later.</div>
                @error('id')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="domain">Domain</label>
                <input
                    type="text"
                    id="domain"
                    name="domain"
                    value="{{ old('domain') }}"
                    placeholder="e.g. university-of-manila.ojthub.com"
                    autocomplete="off"
                >
                <div class="input-hint">The full subdomain or domain this tenant will be served on.</div>
                @error('domain')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <hr class="divider">

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Tenant</button>
            </div>
        </form>
    </div>

    {{-- Info box --}}
    <div style="margin-top:16px; padding:16px 18px; border-radius:10px; border:1px solid rgba(108,99,255,.25); background:rgba(108,99,255,.06); font-size:13px; color:var(--muted); line-height:1.7;">
        <strong style="color:#a09aff;">What happens on creation:</strong><br>
        A new isolated database is provisioned for this tenant, all tenant migrations are run automatically, and the domain is registered so traffic is routed correctly.
    </div>
</div>

@endsection