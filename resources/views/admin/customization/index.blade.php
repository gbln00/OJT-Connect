@extends('layouts.app')
@section('title', 'Tenant Customization')
@section('page-title', 'Customization')
 
@section('content')
<div class="greeting" style="margin-bottom:24px;">
  <div class="greeting-sub">Premium Feature</div>
  <div class="greeting-title">Tenant <span>Customization</span></div>
</div>
 
@if(session("success"))
  <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.2);
              color:#34d399;padding:12px 16px;margin-bottom:20px;font-size:13px;">
    ✓ {{ session("success") }}
  </div>
@endif
 
<form method="POST" action="{{ route('admin.customization.update') }}"
      enctype="multipart/form-data">
  @csrf
 
  {{-- ── Section 1: Branding ──────────────────────────────────── --}}
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <span class="card-title">🎨 Branding</span>
    </div>
    <div style="padding:20px;display:grid;gap:18px;">
 
      {{-- Logo upload --}}
      <div>
        <label class="form-label">Institution Logo</label>
        @if($tenantLogoUrl)
          <div style="margin-bottom:10px;display:flex;align-items:center;gap:12px;">
            <img src="{{ $tenantLogoUrl }}" style="height:48px;border:1px solid var(--border2);" alt="Logo">
            <form method="POST" action="{{ route('admin.customization.logo.delete') }}" style="display:inline;">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Remove Logo</button>
            </form>
          </div>
        @endif
        <input type="file" name="brand_logo" accept="image/png,image/jpeg"
               class="form-input" style="padding:6px;">
        <div class="form-hint">PNG or JPG, max 2MB. Recommended 200×200 px.</div>
        @error("brand_logo") <div class="form-error">{{ $message }}</div> @enderror
      </div>
 
      {{-- Institution name --}}
      <div>
        <label class="form-label">Institution Display Name</label>
        <input type="text" name="brand_name" class="form-input"
               value="{{ old('brand_name', $settings['brand_name'] ?? '') }}"
               placeholder="e.g. Bukidnon State University — College of Technology">
        <div class="form-hint">Shown in navbar, PDF headers, and email subjects.</div>
      </div>
 
      {{-- Primary color --}}
      <div>
        <label class="form-label">Primary Brand Color</label>
        <div style="display:flex;align-items:center;gap:10px;">
          <input type="color" name="brand_color_picker" id="colorPicker"
                 value="#{{ old('brand_color', $settings['brand_color'] ?? '8C0E03') }}"
                 style="width:44px;height:36px;border:1px solid var(--border2);
                        background:var(--surface2);cursor:pointer;padding:2px;">
          <input type="text" name="brand_color" id="colorHex" class="form-input"
                 value="#{{ old('brand_color', $settings['brand_color'] ?? '8C0E03') }}"
                 placeholder="#8C0E03" style="max-width:140px;" maxlength="7">
          <span style="font-size:12px;color:var(--muted);">Drives navbar, buttons, and PDF accent bars.</span>
        </div>
        @error("brand_color") <div class="form-error">{{ $message }}</div> @enderror
      </div>
 
    </div>
  </div>
 
  {{-- ── Section 2: Email Templates ───────────────────────────── --}}
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <span class="card-title">📧 Email Templates</span>
    </div>
    <div style="padding:20px;display:grid;gap:18px;">
 
      <div>
        <label class="form-label">Custom Email Greeting</label>
        <input type="text" name="email_greeting" class="form-input"
               value="{{ old('email_greeting', $settings['email_greeting'] ?? '') }}"
               placeholder="e.g. Greetings from BukSU — College of Technology!">
        <div class="form-hint">Prepended to all outgoing system emails.</div>
      </div>
 
      <div>
        <label class="form-label">Email Signature / Footer Line</label>
        <input type="text" name="email_signature" class="form-input"
               value="{{ old('email_signature', $settings['email_signature'] ?? '') }}"
               placeholder="e.g. — OJT Office, BukSU College of Technology">
        <div class="form-hint">Replaces the default "— The OJTConnect Team" footer.</div>
      </div>
 
    </div>
  </div>
 
  {{-- ── Section 3: OJT Settings ──────────────────────────────── --}}
  <div class="card" style="margin-bottom:20px;">
    <div class="card-header">
      <span class="card-title">⚙️ OJT Settings</span>
    </div>
    <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:18px;">
 
      <div>
        <label class="form-label">Required OJT Hours</label>
        <input type="number" name="ojt_required_hours" class="form-input"
               value="{{ old('ojt_required_hours', $settings['ojt_required_hours'] ?? 490) }}"
               min="1" max="2000">
        <div class="form-hint">Tenant-wide default (can still be overridden per student).</div>
      </div>
 
      <div>
        <label class="form-label">Passing Grade Threshold (%)</label>
        <input type="number" name="ojt_passing_grade" class="form-input"
               value="{{ old('ojt_passing_grade', $settings['ojt_passing_grade'] ?? 75) }}"
               min="1" max="100" step="0.5">
        <div class="form-hint">Minimum overall_grade for a "pass" recommendation.</div>
      </div>
 
    </div>
  </div>
 
  {{-- ── Section 4: Announcement Banner ───────────────────────── --}}
  <div class="card" style="margin-bottom:24px;">
    <div class="card-header">
      <span class="card-title">📢 Announcement Banner</span>
    </div>
    <div style="padding:20px;display:grid;gap:18px;">
 
      <div>
        <label class="form-label">Banner Message</label>
        <input type="text" name="announcement_text" class="form-input"
               value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}"
               placeholder="e.g. Submit your weekly report by Friday 5 PM." maxlength="300">
        <div class="form-hint">Shown as a dismissible banner to all tenant users.</div>
      </div>
 
      <div style="display:flex;align-items:center;gap:10px;">
        <input type="hidden" name="announcement_active" value="0">
        <input type="checkbox" name="announcement_active" value="1" id="ann_active"
               {{ ($settings['announcement_active'] ?? '0') === '1' ? 'checked' : '' }}
               style="width:16px;height:16px;accent-color:var(--crimson);">
        <label for="ann_active" class="form-label" style="margin:0;cursor:pointer;">
          Show banner to all users
        </label>
      </div>
 
    </div>
  </div>
 
  <div style="display:flex;gap:10px;">
    <button type="submit" class="btn btn-primary">Save Customization</button>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Cancel</a>
  </div>
 
</form>
 
@push("scripts")
<script>
// Sync color picker ↔ hex input
const picker = document.getElementById('colorPicker');
const hex    = document.getElementById('colorHex');
picker.addEventListener('input', () => hex.value = picker.value);
hex.addEventListener('input',   () => {
    if (/^#[0-9A-Fa-f]{6}$/.test(hex.value)) picker.value = hex.value;
});
</script>
@endpush
 
@endsection