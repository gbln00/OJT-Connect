@extends('layouts.superadmin-app')
@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid rgba(171,171,171,0.15);
              background:transparent;color:rgba(171,171,171,0.6);font-size:12px;font-weight:700;
              letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Tenants
    </a>
@endsection

@section('content')

@php
$inputStyle = "width:100%;padding:10px 14px;background:#0D0D0D;border:1px solid rgba(171,171,171,0.12);
               color:#fff;font-size:14px;font-family:'Barlow',sans-serif;outline:none;
               transition:border-color 0.2s,box-shadow 0.2s;";
$labelStyle = "display:block;font-size:10px;color:rgba(171,171,171,0.4);letter-spacing:0.18em;
               text-transform:uppercase;font-family:monospace;margin-bottom:8px;";
$hintStyle  = "font-size:12px;color:rgba(171,171,171,0.25);margin-top:6px;font-family:monospace;line-height:1.5;";
$errorStyle = "font-size:12px;color:rgba(220,100,90,0.8);margin-top:6px;font-family:monospace;";
@endphp

<div style="max-width:560px;display:flex;flex-direction:column;gap:12px;">

    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:28px;">

        <div style="margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">New Tenant</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;line-height:1.6;">
                // Each tenant gets an isolated database. Migrations run automatically on creation.
            </div>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.store') }}">
            @csrf

            {{-- Tenant Identity --}}
            <div style="margin-bottom:18px;">
                <label style="{{ $labelStyle }}">Tenant ID</label>
                <input type="text" name="id" value="{{ old('id') }}"
                       placeholder="e.g. university-of-manila"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                <div style="{{ $hintStyle }}">// Lowercase letters, numbers, and hyphens only. Cannot be changed later.</div>
                @error('id')<div style="{{ $errorStyle }}">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:24px;">
                <label style="{{ $labelStyle }}">Domain</label>
                <input type="text" name="domain" value="{{ old('domain') }}"
                       placeholder="e.g. university-of-manila.ojthub.com"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                <div style="{{ $hintStyle }}">// Full subdomain or domain this tenant will be served on.</div>
                @error('domain')<div style="{{ $errorStyle }}">{{ $message }}</div>@enderror
            </div>

            <div style="height:1px;background:rgba(171,171,171,0.06);margin-bottom:20px;"></div>

            {{-- Admin Account --}}
            <div style="margin-bottom:16px;">
                <div style="font-size:10px;color:rgba(140,14,3,0.8);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;font-weight:700;">Admin Account</div>
                <div style="font-size:12px;color:rgba(171,171,171,0.25);font-family:monospace;margin-top:4px;">
                    // Seeded into the tenant's database. Used to log in to their dashboard.
                </div>
            </div>

            <div style="margin-bottom:18px;">
                <label style="{{ $labelStyle }}">Admin Name</label>
                <input type="text" name="admin_name" value="{{ old('admin_name') }}"
                       placeholder="e.g. Juan dela Cruz"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                @error('admin_name')<div style="{{ $errorStyle }}">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="{{ $labelStyle }}">Admin Email</label>
                <input type="email" name="admin_email" value="{{ old('admin_email') }}"
                       placeholder="e.g. admin@university-of-manila.com"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                @error('admin_email')<div style="{{ $errorStyle }}">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:18px;">
                <label style="{{ $labelStyle }}">Admin Password</label>
                <input type="password" name="admin_password"
                       placeholder="Minimum 8 characters"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                @error('admin_password')<div style="{{ $errorStyle }}">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:28px;">
                <label style="{{ $labelStyle }}">Confirm Password</label>
                <input type="password" name="admin_password_confirmation"
                       placeholder="Re-enter the password"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
            </div>

            <div style="height:1px;background:rgba(171,171,171,0.06);margin-bottom:20px;"></div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.index') }}"
                   style="display:inline-flex;align-items:center;padding:9px 20px;border:1px solid rgba(171,171,171,0.15);
                          background:transparent;color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;
                          letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
                    Cancel
                </a>
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:7px;padding:9px 20px;
                               background:#8C0E03;color:rgba(255,255,255,0.92);border:none;cursor:pointer;
                               font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;font-family:'Barlow Condensed',sans-serif;
                               transition:background 0.2s;"
                        onmouseover="this.style.background='#a81004'"
                        onmouseout="this.style.background='#8C0E03'">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                    </svg>
                    Create Tenant
                </button>
            </div>
        </form>
    </div>

    {{-- Info box --}}
    <div style="padding:16px 20px;border:1px solid rgba(140,14,3,0.18);background:rgba(140,14,3,0.04);
                font-size:12px;color:rgba(171,171,171,0.4);line-height:1.8;font-family:monospace;">
        <span style="color:rgba(200,90,80,0.65);font-weight:700;">// on creation:</span><br>
        A new isolated database is provisioned → all tenant migrations run automatically → domain is registered for traffic routing → admin account is seeded into tenant database.
    </div>
</div>
@endsection