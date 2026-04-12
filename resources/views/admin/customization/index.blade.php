@extends('layouts.app')
@section('title', 'Tenant Customization')
@section('page-title', 'Customization')

@section('content')

<div class="greeting" style="margin-bottom:24px;">
  <div class="greeting-sub">Premium Feature</div>
  <div class="greeting-title">Tenant <span>Customization</span></div>
</div>

@if(session('success'))
<div style="background:rgba(45,212,191,0.08);border:1px solid rgba(45,212,191,0.2);color:#2dd4bf;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;">
  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
  {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.25);color:var(--crimson);padding:12px 16px;margin-bottom:20px;font-size:13px;">
  @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
</div>
@endif

{{-- ── Live Preview Strip ──────────────────────────────────────────────────────────────── --}}
<div id="preview-strip" style="border:1px solid var(--border2);margin-bottom:24px;overflow:hidden;">
  <div style="padding:8px 16px;background:var(--surface2);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
    <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">// Live Preview</span>
  </div>
  <div id="preview-navbar" style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:#0E1126;border-bottom:2px solid #8C0E03;">
    <div id="preview-logo-wrap" style="width:32px;height:32px;border:1px solid rgba(140,14,3,0.55);background:rgba(140,14,3,0.1);display:flex;align-items:center;justify-content:center;position:relative;flex-shrink:0;">
      <span id="preview-logo-letter" style="font-family:'Playfair Display',serif;font-weight:900;font-size:14px;color:#8C0E03;">O</span>
    </div>
    <img id="preview-logo-img" src="" alt="" style="height:32px;width:32px;object-fit:contain;display:none;border:1px solid rgba(255,255,255,0.1);">
    <span id="preview-brand-name" style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#fff;">OJTConnect</span>
    <div style="flex:1;"></div>
    <div id="preview-btn" style="display:inline-flex;padding:6px 14px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#fff;background:#8C0E03;cursor:default;">Active Button</div>
  </div>
  <div id="preview-hero" style="padding:20px;background:#0E1126;text-align:center;">
    <div id="preview-badge" style="display:inline-block;padding:4px 12px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.08);font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;color:#8C0E03;margin-bottom:8px;">Premium Plan Active</div>
    <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#fff;">Welcome to <span id="preview-name-display" style="color:#8C0E03;font-style:italic;">OJTConnect</span></div>
  </div>
</div>

<form method="POST" action="{{ route('admin.customization.update') }}" enctype="multipart/form-data">
@csrf

{{-- ─── SECTION 1: BRANDING ──────────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">🎨 Branding</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.1em;">Navbar · Sidebar · PDFs · Emails</span>
  </div>
  <div style="padding:20px;display:grid;gap:20px;">

    {{-- Logo --}}
    <div>
      <label class="form-label">Institution Logo</label>
      @if(!empty($tenantLogoUrl))
        <div style="margin-bottom:12px;display:flex;align-items:center;gap:14px;">
          <img src="{{ $tenantLogoUrl }}" style="height:56px;width:56px;object-fit:contain;border:1px solid var(--border2);padding:4px;background:var(--surface2);" alt="Current Logo">
          <div>
            <div style="font-size:12px;color:var(--text2);margin-bottom:6px;">Current logo</div>
            <form method="POST" action="{{ route('admin.customization.logo.delete') }}" style="display:inline;">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Remove</button>
            </form>
          </div>
        </div>
      @endif
      <input type="file" name="brand_logo" id="logoInput" accept="image/png,image/jpeg,image/webp" class="form-input" style="padding:6px;">
      <div class="form-hint">PNG, JPG or WebP · max 2MB · recommended 200×200 px. Replaces the "O" icon in all sidebars.</div>
      @error('brand_logo') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    {{-- Institution name --}}
    <div>
      <label class="form-label">Institution Display Name</label>
      <input type="text" name="brand_name" id="brandNameInput" class="form-input"
             value="{{ old('brand_name', $settings['brand_name'] ?? '') }}"
             placeholder="e.g. Bukidnon State University — College of Technology" maxlength="120">
      <div class="form-hint">Shown in sidebar, page titles, PDF headers, and email subjects.</div>
    </div>

    {{-- Primary + Secondary colors --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
      <div>
        <label class="form-label">Primary Color
          <span style="color:var(--muted);font-size:9px;font-style:normal;letter-spacing:0;">— buttons · active nav · accents</span>
        </label>
        <div style="display:flex;align-items:center;gap:10px;">
          <input type="color" id="colorPicker" value="#{{ old('brand_color', $settings['brand_color'] ?? '8C0E03') }}"
                 style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
          <input type="text" name="brand_color" id="colorHex" class="form-input"
                 value="#{{ old('brand_color', $settings['brand_color'] ?? '8C0E03') }}"
                 placeholder="#8C0E03" maxlength="7">
        </div>
        @error('brand_color') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <div>
        <label class="form-label">Secondary Color
          <span style="color:var(--muted);font-size:9px;font-style:normal;letter-spacing:0;">— sidebar · hero · dark surfaces</span>
        </label>
        <div style="display:flex;align-items:center;gap:10px;">
          <input type="color" id="colorPickerSecondary" value="#{{ old('brand_color_secondary', $settings['brand_color_secondary'] ?? '0E1126') }}"
                 style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
          <input type="text" name="brand_color_secondary" id="colorHexSecondary" class="form-input"
                 value="#{{ old('brand_color_secondary', $settings['brand_color_secondary'] ?? '0E1126') }}"
                 placeholder="#0E1126" maxlength="7">
        </div>
        @error('brand_color_secondary') <div class="form-error">{{ $message }}</div> @enderror
      </div>
    </div>

    {{-- Font --}}
    <div>
      <label class="form-label">UI Font Family</label>
      <select name="brand_font" id="fontSelect" class="form-input" style="cursor:pointer;">
        @foreach($fontOptions as $f)
          <option value="{{ $f }}" {{ ($settings['brand_font'] ?? 'barlow') === $f ? 'selected' : '' }}>
            {{ ucfirst($f) }}{{ $f === 'barlow' ? ' (default)' : '' }}
          </option>
        @endforeach
      </select>
      <div class="form-hint">Applied to all body text in every role's dashboard.</div>
    </div>

    {{-- Reset --}}
    <div style="padding-top:4px;border-top:1px solid var(--border);">
      <form method="POST" action="{{ route('admin.customization.reset') }}" style="display:inline;">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('Reset all branding to system defaults?')" style="color:var(--muted);">
          ↺ Reset branding to defaults
        </button>
      </form>
    </div>

  </div>
</div>

{{-- ─── SECTION 2: EMAIL TEMPLATES ─────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">📧 Email Templates</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.1em;">All outgoing tenant emails</span>
  </div>
  <div style="padding:20px;display:grid;gap:18px;">

    <div>
      <label class="form-label">Custom Email Greeting</label>
      <input type="text" name="email_greeting" id="greetingInput" class="form-input"
             value="{{ old('email_greeting', $settings['email_greeting'] ?? '') }}"
             placeholder="e.g. Greetings from BukSU — College of Technology!" maxlength="200">
      <div class="form-hint">Shown as a highlighted line below the email header. Leave blank to hide.</div>
    </div>

    <div>
      <label class="form-label">Email Footer / Signature</label>
      <input type="text" name="email_signature" id="signatureInput" class="form-input"
             value="{{ old('email_signature', $settings['email_signature'] ?? '') }}"
             placeholder="e.g. — OJT Office, BukSU College of Technology" maxlength="200">
      <div class="form-hint">Replaces the default "— The OJTConnect Team" in all email footers.</div>
    </div>

    {{-- Email preview --}}
    <div style="border:1px solid var(--border2);overflow:hidden;">
      <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);padding:8px 14px;background:var(--surface2);border-bottom:1px solid var(--border);">// Email preview</div>
      <div id="ep-header" style="padding:14px 20px;font-size:14px;font-weight:700;color:#fff;background:#8C0E03;">
        OJTConnect – <span id="ep-name">{{ $settings['brand_name'] ?? 'Your Institution' }}</span>
      </div>
      <div id="ep-greeting" style="padding:8px 20px;background:#f9f9f9;border-left:3px solid #8C0E03;font-size:13px;font-style:italic;color:#333740;{{ empty($settings['email_greeting'] ?? '') ? 'display:none;' : '' }}">
        {{ $settings['email_greeting'] ?? '' }}
      </div>
      <div style="padding:14px 20px;font-size:13px;color:#333740;background:#fff;">
        Hello <strong>Student Name</strong>, your OJT application has been approved. You may now start logging your daily hours.
      </div>
      <div id="ep-footer" style="padding:12px 20px;text-align:center;background:#0E1126;">
        <div style="font-size:12px;color:#ABABAB;margin-bottom:4px;">Need help? <span style="color:#fff;">support@ojtconnect.com</span></div>
        <div id="ep-sig" style="font-size:11px;color:#555a6a;">{{ $settings['email_signature'] ?? '— The OJTConnect Team' }}</div>
      </div>
    </div>

  </div>
</div>

{{-- ─── SECTION 3: OJT SETTINGS ────────────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">⚙️ OJT Settings</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.1em;">Tenant-wide defaults</span>
  </div>
  <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:18px;">
    <div>
      <label class="form-label">Required OJT Hours</label>
      <input type="number" name="ojt_required_hours" class="form-input"
             value="{{ old('ojt_required_hours', $settings['ojt_required_hours'] ?? 490) }}" min="1" max="2000">
      <div class="form-hint">Tenant-wide default. Can be overridden per student profile.</div>
      @error('ojt_required_hours') <div class="form-error">{{ $message }}</div> @enderror
    </div>
    <div>
      <label class="form-label">Passing Grade Threshold (%)</label>
      <input type="number" name="ojt_passing_grade" class="form-input"
             value="{{ old('ojt_passing_grade', $settings['ojt_passing_grade'] ?? 75) }}" min="1" max="100" step="0.5">
      <div class="form-hint">Minimum overall_grade for a "pass" recommendation.</div>
      @error('ojt_passing_grade') <div class="form-error">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

{{-- ─── SECTION 4: ANNOUNCEMENT BANNER ────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:24px;">
  <div class="card-header">
    <span class="card-title">📢 Announcement Banner</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.1em;">Shown to all roles when active</span>
  </div>
  <div style="padding:20px;display:grid;gap:16px;">
    <div>
      <label class="form-label">Banner Message</label>
      <input type="text" name="announcement_text" id="announcementInput" class="form-input"
             value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}"
             placeholder="e.g. Submit your weekly report by Friday 5 PM." maxlength="300">
      <div class="form-hint">Dismissible banner shown to all tenant users on every page.</div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <input type="hidden" name="announcement_active" value="0">
      <input type="checkbox" name="announcement_active" value="1" id="ann_active"
             {{ ($settings['announcement_active'] ?? '0') === '1' ? 'checked' : '' }}
             style="width:16px;height:16px;accent-color:var(--crimson);cursor:pointer;">
      <label for="ann_active" class="form-label" style="margin:0;cursor:pointer;">Show banner to all users now</label>
    </div>
    <div id="banner-preview" style="{{ empty($settings['announcement_text'] ?? '') ? 'display:none;' : '' }}background:rgba(140,14,3,0.08);border:1px solid rgba(140,14,3,0.25);padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <span id="banner-preview-text" style="font-size:13px;color:var(--text);">📢 {{ $settings['announcement_text'] ?? '' }}</span>
      <span style="color:var(--muted);font-size:16px;cursor:default;">✕</span>
    </div>
  </div>
</div>

<div style="display:flex;gap:10px;align-items:center;">
  <button type="submit" class="btn btn-primary">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
      <polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/>
    </svg>
    Save Customization
  </button>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Cancel</a>
  <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-left:8px;">// Changes take effect immediately</span>
</div>

</form>

@push('scripts')
<script>
function hexToRgb(hex) {
    const h = hex.replace('#','');
    const r = parseInt(h.substring(0,2),16);
    const g = parseInt(h.substring(2,4),16);
    const b = parseInt(h.substring(4,6),16);
    return r+','+g+','+b;
}

function syncPicker(picker, hexInput, onUpdate) {
    picker.addEventListener('input', () => { hexInput.value = picker.value; onUpdate(picker.value); });
    hexInput.addEventListener('input', () => {
        if (/^#[0-9A-Fa-f]{6}$/.test(hexInput.value)) { picker.value = hexInput.value; onUpdate(hexInput.value); }
    });
}

// Primary color
syncPicker(
    document.getElementById('colorPicker'),
    document.getElementById('colorHex'),
    hex => {
        document.getElementById('preview-navbar').style.borderBottomColor = hex;
        document.getElementById('preview-btn').style.background = hex;
        document.getElementById('preview-badge').style.color = hex;
        document.getElementById('preview-badge').style.borderColor = hex+'55';
        document.getElementById('preview-badge').style.background = hex+'15';
        document.getElementById('preview-name-display').style.color = hex;
        document.getElementById('preview-logo-letter').style.color = hex;
        document.getElementById('preview-logo-wrap').style.borderColor = hex+'99';
        document.getElementById('preview-logo-wrap').style.background = hex+'1a';
        document.getElementById('ep-header').style.background = hex;
        document.getElementById('ep-greeting').style.borderLeftColor = hex;
        const rgb = hexToRgb(hex);
        document.getElementById('banner-preview').style.background = 'rgba('+rgb+',0.08)';
        document.getElementById('banner-preview').style.borderColor = 'rgba('+rgb+',0.25)';
    }
);

// Secondary color
syncPicker(
    document.getElementById('colorPickerSecondary'),
    document.getElementById('colorHexSecondary'),
    hex => {
        document.getElementById('preview-navbar').style.background = hex;
        document.getElementById('preview-hero').style.background = hex;
        document.getElementById('ep-footer').style.background = hex;
    }
);

// Brand name
document.getElementById('brandNameInput').addEventListener('input', function() {
    const val = this.value.trim() || 'OJTConnect';
    document.getElementById('preview-brand-name').textContent = val;
    document.getElementById('preview-name-display').textContent = val;
    document.getElementById('ep-name').textContent = val;
});

// Logo preview
document.getElementById('logoInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-logo-img').src = e.target.result;
        document.getElementById('preview-logo-img').style.display = 'block';
        document.getElementById('preview-logo-wrap').style.display = 'none';
    };
    reader.readAsDataURL(file);
});

// Greeting preview
document.getElementById('greetingInput').addEventListener('input', function() {
    const el = document.getElementById('ep-greeting');
    el.textContent = this.value;
    el.style.display = this.value.trim() ? 'block' : 'none';
});

// Signature preview
document.getElementById('signatureInput').addEventListener('input', function() {
    document.getElementById('ep-sig').textContent = this.value.trim() || '— The OJTConnect Team';
});

// Announcement preview
document.getElementById('announcementInput').addEventListener('input', function() {
    const banner = document.getElementById('banner-preview');
    document.getElementById('banner-preview-text').textContent = '📢 ' + this.value;
    banner.style.display = this.value.trim() ? 'flex' : 'none';
});
</script>
@endpush

@endsection