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

            {{-- Tenant Identity --}}
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

            {{-- Admin Credentials --}}
            <div style="margin-bottom: 16px;">
                <div style="font-size:13px; font-weight:600; color:var(--text); margin-bottom:4px;">Admin Account</div>
                <div style="font-size:12px; color:var(--muted); line-height:1.6;">
                    This account will be created inside the tenant's database and used to log in to their dashboard.
                </div>
            </div>

            <div class="form-group">
                <label for="admin_name">Admin Name</label>
                <input
                    type="text"
                    id="admin_name"
                    name="admin_name"
                    value="{{ old('admin_name') }}"
                    placeholder="e.g. Juan dela Cruz"
                    autocomplete="off"
                >
                @error('admin_name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input
                    type="email"
                    id="admin_email"
                    name="admin_email"
                    value="{{ old('admin_email') }}"
                    placeholder="e.g. admin@university-of-manila.com"
                    autocomplete="off"
                >
                @error('admin_email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="admin_password">Admin Password</label>
                <input
                    type="password"
                    id="admin_password"
                    name="admin_password"
                    placeholder="Minimum 8 characters"
                    autocomplete="new-password"
                >
                @error('admin_password')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="admin_password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="admin_password_confirmation"
                    name="admin_password_confirmation"
                    placeholder="Re-enter the password"
                    autocomplete="new-password"
                >
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
        A new isolated database is provisioned for this tenant, all tenant migrations are run automatically, the domain is registered so traffic is routed correctly, and the admin account is seeded into the tenant's database.
    </div>
</div>

@endsection