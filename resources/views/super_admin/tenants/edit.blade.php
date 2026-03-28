@extends('layouts.superadmin-app')
@section('title', 'Edit Tenant — ' . $tenant->id)
@section('page-title', 'Edit Tenant')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.show', $tenant) }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid rgba(171,171,171,0.15);
              background:transparent;color:rgba(171,171,171,0.6);font-size:12px;font-weight:700;
              letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back
    </a>
@endsection

@section('content')

@php
$inputStyle = "width:100%;padding:10px 14px;background:#0D0D0D;border:1px solid rgba(171,171,171,0.12);
               color:#fff;font-size:14px;font-family:'Barlow',sans-serif;outline:none;
               transition:border-color 0.2s,box-shadow 0.2s;";
$labelStyle = "display:block;font-size:10px;color:rgba(171,171,171,0.4);letter-spacing:0.18em;
               text-transform:uppercase;font-family:monospace;margin-bottom:8px;";
@endphp

<div style="max-width:560px;">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:28px;">

        <div style="margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Edit Tenant</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;">
                // Tenant ID is immutable. Only the domain can be changed.
            </div>
        </div>

        <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}">
            @csrf @method('PUT')

            <div style="margin-bottom:18px;">
                <label style="{{ $labelStyle }}">Tenant ID</label>
                <input type="text" value="{{ $tenant->id }}" disabled
                       style="{{ $inputStyle }} opacity:0.35;cursor:not-allowed;">
                <div style="font-size:12px;color:rgba(171,171,171,0.2);margin-top:6px;font-family:monospace;">// Cannot be changed after creation.</div>
            </div>

            <div style="margin-bottom:28px;">
                <label style="{{ $labelStyle }}">Domain</label>
                <input type="text" name="domain"
                       value="{{ old('domain', $tenant->domains->first()?->domain) }}"
                       placeholder="e.g. school.ojthub.com"
                       style="{{ $inputStyle }}"
                       onfocus="this.style.borderColor='rgba(140,14,3,0.7)';this.style.boxShadow='0 0 0 3px rgba(140,14,3,0.1)'"
                       onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'"
                       autofocus>
                @error('domain')
                    <div style="font-size:12px;color:rgba(220,100,90,0.8);margin-top:6px;font-family:monospace;">{{ $message }}</div>
                @enderror
            </div>

            <div style="height:1px;background:rgba(171,171,171,0.06);margin-bottom:20px;"></div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('super_admin.tenants.show', $tenant) }}"
                   style="display:inline-flex;align-items:center;padding:9px 20px;border:1px solid rgba(171,171,171,0.15);
                          background:transparent;color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;
                          letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
                    Cancel
                </a>
                <button type="submit"
                        style="padding:9px 20px;background:#8C0E03;color:rgba(255,255,255,0.92);border:none;cursor:pointer;
                               font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;font-family:'Barlow Condensed',sans-serif;
                               transition:background 0.2s;"
                        onmouseover="this.style.background='#a81004'"
                        onmouseout="this.style.background='#8C0E03'">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection