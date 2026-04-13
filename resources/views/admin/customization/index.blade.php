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

{{-- ── Tab Navigation ───────────────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:2px;margin-bottom:24px;border-bottom:1px solid var(--border);">
    @foreach([
        ['branding',      '🎨', 'Branding'],
        ['colors',        '🌈', 'Colors'],
        ['typography',    '✏️',  'Typography'],
        ['email',         '📧', 'Email'],
        ['ojt',           '⚙️',  'OJT Settings'],
        ['announcement',  '📢', 'Announcement'],
    ] as [$tab, $icon, $label])
    <button type="button" onclick="switchTab('{{ $tab }}')" id="tab-btn-{{ $tab }}"
            class="tab-btn {{ $tab === 'branding' ? 'active' : '' }}">
        {{ $icon }} {{ $label }}
    </button>
    @endforeach
</div>

{{-- ── Live Preview Strip ───────────────────────────────────────────────────────────── --}}
<div id="preview-strip" style="border:1px solid var(--border2);margin-bottom:24px;overflow:hidden;">
  <div style="padding:8px 16px;background:var(--surface2);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
    <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">// Live Preview</span>
    <div style="display:flex;gap:4px;">
        <button type="button" onclick="setPreviewMode('dark')" id="prev-dark-btn"
                style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.1em;text-transform:uppercase;padding:3px 10px;border:1px solid var(--crimson);background:var(--crimson);color:#fff;cursor:pointer;">Dark</button>
        <button type="button" onclick="setPreviewMode('light')" id="prev-light-btn"
                style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.1em;text-transform:uppercase;padding:3px 10px;border:1px solid var(--border2);background:transparent;color:var(--muted);cursor:pointer;">Light</button>
    </div>
  </div>

  {{-- Dark preview --}}
  <div id="prev-dark">
    <div id="preview-navbar" style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:#0E1126;border-bottom:2px solid #8C0E03;">
        <div id="preview-logo-wrap" style="width:32px;height:32px;border:1px solid rgba(140,14,3,0.55);background:rgba(140,14,3,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
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
    <div style="padding:14px 20px;background:#131929;display:flex;gap:10px;align-items:center;border-top:1px solid rgba(255,255,255,0.05);">
        <div id="preview-dark-card" style="flex:1;background:#0E1126;border:1px solid rgba(171,171,171,0.07);padding:12px 14px;">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(171,171,171,0.35);margin-bottom:6px;">Sample Card</div>
            <div id="preview-dark-text" style="font-size:13px;color:rgba(255,255,255,0.88);margin-bottom:4px;">Card title text</div>
            <div style="font-size:11px;color:rgba(171,171,171,0.55);">Secondary muted text</div>
        </div>
        <div style="flex:1;background:#0E1126;border:1px solid rgba(171,171,171,0.07);padding:12px 14px;">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:rgba(171,171,171,0.35);margin-bottom:6px;">Progress</div>
            <div style="height:4px;background:rgba(171,171,171,0.1);margin-bottom:6px;">
                <div id="preview-progress-dark" style="height:100%;width:62%;background:#8C0E03;"></div>
            </div>
            <div style="font-size:11px;color:rgba(171,171,171,0.55);">62% complete</div>
        </div>
    </div>
  </div>

  {{-- Light preview --}}
  <div id="prev-light" style="display:none;">
    <div id="preview-navbar-light" style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:#FFFFFF;border-bottom:2px solid #8C0E03;box-shadow:0 1px 0 #e5e7eb;">
        <div id="preview-logo-wrap-light" style="width:32px;height:32px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.06);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <span id="preview-logo-letter-light" style="font-family:'Playfair Display',serif;font-weight:900;font-size:14px;color:#8C0E03;">O</span>
        </div>
        <span id="preview-brand-name-light" style="font-family:'Barlow Condensed',sans-serif;font-size:16px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#0D0D0D;">OJTConnect</span>
        <div style="flex:1;"></div>
        <div id="preview-btn-light" style="display:inline-flex;padding:6px 14px;font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:#fff;background:#8C0E03;cursor:default;">Active Button</div>
    </div>
    <div id="preview-hero-light" style="padding:20px;background:#F5F4F0;text-align:center;border-bottom:1px solid #e5e7eb;">
        <div id="preview-badge-light" style="display:inline-block;padding:4px 12px;border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.07);font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.15em;text-transform:uppercase;color:#8C0E03;margin-bottom:8px;">Premium Plan Active</div>
        <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:#0D0D0D;">Welcome to <span id="preview-name-display-light" style="color:#8C0E03;font-style:italic;">OJTConnect</span></div>
    </div>
    <div style="padding:14px 20px;background:#F5F4F0;display:flex;gap:10px;align-items:center;">
        <div style="flex:1;background:#FFFFFF;border:1px solid rgba(51,55,64,0.1);padding:12px 14px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:#8a8e99;margin-bottom:6px;">Sample Card</div>
            <div id="preview-light-text" style="font-size:13px;color:#0D0D0D;margin-bottom:4px;">Card title text</div>
            <div style="font-size:11px;color:#6b7280;">Secondary muted text</div>
        </div>
        <div style="flex:1;background:#FFFFFF;border:1px solid rgba(51,55,64,0.1);padding:12px 14px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:#8a8e99;margin-bottom:6px;">Progress</div>
            <div style="height:4px;background:rgba(51,55,64,0.1);margin-bottom:6px;">
                <div id="preview-progress-light" style="height:100%;width:62%;background:#8C0E03;"></div>
            </div>
            <div style="font-size:11px;color:#6b7280;">62% complete</div>
        </div>
    </div>
  </div>
</div>

{{-- ── MAIN FORM ────────────────────────────────────────────────────────────────────── --}}
<form method="POST" action="{{ route('admin.customization.update') }}" enctype="multipart/form-data">
@csrf

{{-- ═══ TAB: BRANDING ═══════════════════════════════════════════════════════════════ --}}
<div id="tab-branding" class="tab-panel">
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
            <button type="button" class="btn btn-danger btn-sm"
                onclick="document.getElementById('logo-delete-form').submit()">Remove</button>
          </div>
        </div>
      @endif
      <input type="file" name="brand_logo" id="logoInput" accept="image/png,image/jpeg,image/webp" class="form-input" style="padding:6px;">
      <div class="form-hint">PNG, JPG or WebP · max 2MB · recommended 200×200 px</div>
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
      <div class="form-hint">Applied to all body text across every role's dashboard.</div>
    </div>

    {{-- Reset --}}
    <div style="padding-top:4px;border-top:1px solid var(--border);">
      <button type="button" class="btn btn-ghost btn-sm" style="color:var(--muted);"
          onclick="if(confirm('Reset all branding to system defaults?')) document.getElementById('reset-branding-form').submit()">
        ↺ Reset all branding to defaults
      </button>
    </div>
  </div>
</div>
</div>

{{-- ═══ TAB: COLORS ═════════════════════════════════════════════════════════════════ --}}
<div id="tab-colors" class="tab-panel" style="display:none;">
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">🌈 Color Scheme</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Separate controls for dark & light mode</span>
  </div>
  <div style="padding:20px;display:grid;gap:24px;">

    {{-- Mode switcher inside colors tab --}}
    <div style="display:flex;gap:2px;border-bottom:1px solid var(--border);padding-bottom:16px;">
        <button type="button" onclick="switchColorMode('dark')" id="color-mode-dark"
                style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;padding:6px 16px;border:1px solid var(--crimson);background:var(--crimson);color:#fff;cursor:pointer;">
            🌙 Dark Mode Colors
        </button>
        <button type="button" onclick="switchColorMode('light')" id="color-mode-light"
                style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;padding:6px 16px;border:1px solid var(--border2);background:transparent;color:var(--muted);cursor:pointer;">
            ☀️ Light Mode Colors
        </button>
    </div>

    {{-- DARK MODE COLORS --}}
    <div id="color-panel-dark">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;">// Dark Mode</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div>
                <label class="form-label">Primary / Accent Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— buttons · active nav · crimson accents</span>
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
                <label class="form-label">Dark Surface / Sidebar Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— sidebar bg · hero section · dark cards</span>
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

            <div>
                <label class="form-label">Dark Text Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— primary body text in dark mode</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerDarkText" value="#{{ old('dark_text_color', $settings['dark_text_color'] ?? 'E0E0E0') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="dark_text_color" id="colorHexDarkText" class="form-input"
                           value="#{{ old('dark_text_color', $settings['dark_text_color'] ?? 'E0E0E0') }}"
                           placeholder="#E0E0E0" maxlength="7">
                </div>
            </div>

            <div>
                <label class="form-label">Dark Border / Line Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— card borders · dividers · outlines</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerDarkBorder" value="#{{ old('dark_border_color', $settings['dark_border_color'] ?? '252A3D') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="dark_border_color" id="colorHexDarkBorder" class="form-input"
                           value="#{{ old('dark_border_color', $settings['dark_border_color'] ?? '252A3D') }}"
                           placeholder="#252A3D" maxlength="7">
                </div>
            </div>

        </div>
    </div>

    {{-- LIGHT MODE COLORS --}}
    <div id="color-panel-light" style="display:none;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;">// Light Mode</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div>
                <label class="form-label">Primary / Accent Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— buttons · active nav · accents in light mode</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLight" value="#{{ old('brand_color_light', $settings['brand_color_light'] ?? '8C0E03') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="brand_color_light" id="colorHexLight" class="form-input"
                           value="#{{ old('brand_color_light', $settings['brand_color_light'] ?? '8C0E03') }}"
                           placeholder="#8C0E03" maxlength="7">
                </div>
                <div class="form-hint">Often the same as the dark primary, or a slightly lighter shade.</div>
            </div>

            <div>
                <label class="form-label">Light Page Background
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— outermost page background</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLightBg" value="#{{ old('light_bg_color', $settings['light_bg_color'] ?? 'F5F4F0') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="light_bg_color" id="colorHexLightBg" class="form-input"
                           value="#{{ old('light_bg_color', $settings['light_bg_color'] ?? 'F5F4F0') }}"
                           placeholder="#F5F4F0" maxlength="7">
                </div>
            </div>

            <div>
                <label class="form-label">Light Text Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— primary body text in light mode</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLightText" value="#{{ old('light_text_color', $settings['light_text_color'] ?? '0D0D0D') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="light_text_color" id="colorHexLightText" class="form-input"
                           value="#{{ old('light_text_color', $settings['light_text_color'] ?? '0D0D0D') }}"
                           placeholder="#0D0D0D" maxlength="7">
                </div>
            </div>

            <div>
                <label class="form-label">Light Border / Line Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— card borders · dividers in light mode</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLightBorder" value="#{{ old('light_border_color', $settings['light_border_color'] ?? 'D1D5DB') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="light_border_color" id="colorHexLightBorder" class="form-input"
                           value="#{{ old('light_border_color', $settings['light_border_color'] ?? 'D1D5DB') }}"
                           placeholder="#D1D5DB" maxlength="7">
                </div>
            </div>

            <div>
                <label class="form-label">Light Sidebar Color
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— sidebar background in light mode</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLightSidebar" value="#{{ old('light_sidebar_color', $settings['light_sidebar_color'] ?? 'FFFFFF') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="light_sidebar_color" id="colorHexLightSidebar" class="form-input"
                           value="#{{ old('light_sidebar_color', $settings['light_sidebar_color'] ?? 'FFFFFF') }}"
                           placeholder="#FFFFFF" maxlength="7">
                </div>
            </div>

            <div>
                <label class="form-label">Light Card Surface
                    <span style="font-size:9px;color:var(--muted);letter-spacing:0;">— card & panel backgrounds</span>
                </label>
                <div style="display:flex;align-items:center;gap:10px;">
                    <input type="color" id="colorPickerLightSurface" value="#{{ old('light_surface_color', $settings['light_surface_color'] ?? 'FFFFFF') }}"
                           style="width:44px;height:36px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;padding:2px;flex-shrink:0;">
                    <input type="text" name="light_surface_color" id="colorHexLightSurface" class="form-input"
                           value="#{{ old('light_surface_color', $settings['light_surface_color'] ?? 'FFFFFF') }}"
                           placeholder="#FFFFFF" maxlength="7">
                </div>
            </div>

        </div>
    </div>

    {{-- Preset swatches --}}
    <div style="border-top:1px solid var(--border);padding-top:16px;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">// Quick Presets</div>
        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach([
                ['Crimson',   '8C0E03', '0E1126'],
                ['Navy',      '1E3A5F', '0A1628'],
                ['Forest',    '14532D', '0A2418'],
                ['Violet',    '4C1D95', '1E0A3C'],
                ['Slate',     '1E293B', '0F172A'],
                ['Emerald',   '065F46', '022C22'],
                ['Bronze',    '78350F', '1C0A00'],
                ['Indigo',    '312E81', '1E1B4B'],
            ] as [$name, $primary, $secondary])
            <button type="button" onclick="applyPreset('#{{ $primary }}', '#{{ $secondary }}')"
                    style="display:flex;align-items:center;gap:7px;padding:5px 12px;border:1px solid var(--border2);background:var(--surface2);cursor:pointer;font-family:'DM Mono',monospace;font-size:10px;color:var(--text2);transition:border-color 0.15s;"
                    onmouseover="this.style.borderColor='#{{ $primary }}'"
                    onmouseout="this.style.borderColor='var(--border2)'">
                <span style="display:flex;gap:2px;">
                    <span style="width:12px;height:12px;background:#{{ $primary }};display:inline-block;"></span>
                    <span style="width:12px;height:12px;background:#{{ $secondary }};display:inline-block;"></span>
                </span>
                {{ $name }}
            </button>
            @endforeach
        </div>
    </div>

  </div>
</div>
</div>

{{-- ═══ TAB: TYPOGRAPHY ═════════════════════════════════════════════════════════════ --}}
<div id="tab-typography" class="tab-panel" style="display:none;">
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">✏️ Typography</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Font size · weight · spacing</span>
  </div>
  <div style="padding:20px;display:grid;gap:20px;">

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
        <div>
            <label class="form-label">Base Font Size</label>
            <select name="font_size_base" class="form-input" style="cursor:pointer;">
                @foreach(['13px'=>'Small (13px)', '14px'=>'Default (14px)', '15px'=>'Medium (15px)', '16px'=>'Large (16px)'] as $val => $label)
                <option value="{{ $val }}" {{ ($settings['font_size_base'] ?? '14px') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-hint">Body text size across all pages.</div>
        </div>
        <div>
            <label class="form-label">UI Density</label>
            <select name="ui_density" class="form-input" style="cursor:pointer;">
                @foreach(['compact'=>'Compact', 'default'=>'Default', 'comfortable'=>'Comfortable'] as $val => $label)
                <option value="{{ $val }}" {{ ($settings['ui_density'] ?? 'default') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-hint">Controls padding and spacing in tables and cards.</div>
        </div>
        <div>
            <label class="form-label">Heading Style</label>
            <select name="heading_style" class="form-input" style="cursor:pointer;">
                @foreach(['serif'=>'Playfair (serif)', 'condensed'=>'Condensed', 'mono'=>'Monospace'] as $val => $label)
                <option value="{{ $val }}" {{ ($settings['heading_style'] ?? 'serif') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-hint">Style for page titles and card headings.</div>
        </div>
    </div>

    {{-- Typography preview --}}
    <div style="border:1px solid var(--border2);padding:20px;background:var(--surface2);">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:14px;">// Typography Preview</div>
        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text);margin-bottom:6px;">Page Title — Playfair Display</div>
        <div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">SECTION LABEL · DM MONO TRACKING</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.6;margin-bottom:8px;">Body text appears at this size. Intern name, application status, date labels, and form field values all use the selected body font family above.</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">// Monospace labels · codes · timestamps · IDs</div>
    </div>

  </div>
</div>
</div>

{{-- ═══ TAB: EMAIL ══════════════════════════════════════════════════════════════════ --}}
<div id="tab-email" class="tab-panel" style="display:none;">
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">📧 Email Templates</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">All outgoing tenant emails</span>
  </div>
  <div style="padding:20px;display:grid;gap:18px;">
    <div>
      <label class="form-label">Custom Email Greeting</label>
      <input type="text" name="email_greeting" id="greetingInput" class="form-input"
             value="{{ old('email_greeting', $settings['email_greeting'] ?? '') }}"
             placeholder="e.g. Greetings from BukSU — College of Technology!" maxlength="200">
      <div class="form-hint">Highlighted line below the email header. Leave blank to hide.</div>
    </div>
    <div>
      <label class="form-label">Email Footer / Signature</label>
      <input type="text" name="email_signature" id="signatureInput" class="form-input"
             value="{{ old('email_signature', $settings['email_signature'] ?? '') }}"
             placeholder="e.g. — OJT Office, BukSU College of Technology" maxlength="200">
      <div class="form-hint">Replaces "— The OJTConnect Team" in all email footers.</div>
    </div>
    <div>
      <label class="form-label">Reply-To Email Address</label>
      <input type="email" name="email_reply_to" class="form-input"
             value="{{ old('email_reply_to', $settings['email_reply_to'] ?? '') }}"
             placeholder="e.g. ojt-office@yourinstitution.edu.ph">
      <div class="form-hint">Students and supervisors replying to system emails will reach this address.</div>
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
        <div id="ep-sig" style="font-size:11px;color:#9ca3af;">{{ $settings['email_signature'] ?? '— The OJTConnect Team' }}</div>
      </div>
    </div>
  </div>
</div>
</div>

{{-- ═══ TAB: OJT SETTINGS ═══════════════════════════════════════════════════════════ --}}
<div id="tab-ojt" class="tab-panel" style="display:none;">
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">⚙️ OJT Settings</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Tenant-wide defaults</span>
  </div>
  <div style="padding:20px;display:grid;gap:20px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
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
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label class="form-label">Default Session: Morning Start</label>
          <input type="time" name="session_morning_start" class="form-input"
                 value="{{ old('session_morning_start', $settings['session_morning_start'] ?? '08:00') }}">
          <div class="form-hint">Pre-filled default for AM time-in in the log form.</div>
        </div>
        <div>
          <label class="form-label">Default Session: Morning End</label>
          <input type="time" name="session_morning_end" class="form-input"
                 value="{{ old('session_morning_end', $settings['session_morning_end'] ?? '12:00') }}">
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
          <label class="form-label">Default Session: Afternoon Start</label>
          <input type="time" name="session_afternoon_start" class="form-input"
                 value="{{ old('session_afternoon_start', $settings['session_afternoon_start'] ?? '13:00') }}">
        </div>
        <div>
          <label class="form-label">Default Session: Afternoon End</label>
          <input type="time" name="session_afternoon_end" class="form-input"
                 value="{{ old('session_afternoon_end', $settings['session_afternoon_end'] ?? '17:00') }}">
        </div>
    </div>
    <div style="border-top:1px solid var(--border);padding-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <div>
            <label class="form-label">Allow Students to Edit Rejected Logs</label>
            <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                <input type="hidden" name="allow_edit_rejected" value="0">
                <input type="checkbox" name="allow_edit_rejected" value="1" id="allow_edit"
                       {{ ($settings['allow_edit_rejected'] ?? '1') === '1' ? 'checked' : '' }}
                       style="width:16px;height:16px;accent-color:var(--crimson);cursor:pointer;">
                <label for="allow_edit" style="font-size:13px;color:var(--text2);cursor:pointer;">Enabled</label>
            </div>
        </div>
        <div>
            <label class="form-label">Require Description on Hour Logs</label>
            <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
                <input type="hidden" name="require_log_description" value="0">
                <input type="checkbox" name="require_log_description" value="1" id="req_desc"
                       {{ ($settings['require_log_description'] ?? '0') === '1' ? 'checked' : '' }}
                       style="width:16px;height:16px;accent-color:var(--crimson);cursor:pointer;">
                <label for="req_desc" style="font-size:13px;color:var(--text2);cursor:pointer;">Required</label>
            </div>
        </div>
    </div>
  </div>
</div>
</div>

{{-- ═══ TAB: ANNOUNCEMENT ════════════════════════════════════════════════════════════ --}}
<div id="tab-announcement" class="tab-panel" style="display:none;">
<div class="card" style="margin-bottom:20px;">
  <div class="card-header">
    <span class="card-title">📢 Announcement Banner</span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Shown to all roles when active</span>
  </div>
  <div style="padding:20px;display:grid;gap:16px;">
    <div>
      <label class="form-label">Banner Message</label>
      <input type="text" name="announcement_text" id="announcementInput" class="form-input"
             value="{{ old('announcement_text', $settings['announcement_text'] ?? '') }}"
             placeholder="e.g. Submit your weekly report by Friday 5 PM." maxlength="300">
      <div class="form-hint">Dismissible banner shown to all tenant users on every page.</div>
    </div>
    <div>
      <label class="form-label">Banner Type</label>
      <select name="announcement_type" class="form-input" style="cursor:pointer;">
        @foreach(['info'=>'Info (blue)', 'warning'=>'Warning (amber)', 'success'=>'Success (teal)', 'danger'=>'Danger (crimson)'] as $val => $label)
        <option value="{{ $val }}" {{ ($settings['announcement_type'] ?? 'info') === $val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <input type="hidden" name="announcement_active" value="0">
      <input type="checkbox" name="announcement_active" value="1" id="ann_active"
             {{ ($settings['announcement_active'] ?? '0') === '1' ? 'checked' : '' }}
             style="width:16px;height:16px;accent-color:var(--crimson);cursor:pointer;">
      <label for="ann_active" class="form-label" style="margin:0;cursor:pointer;">Show banner to all users now</label>
    </div>
    <div id="banner-preview" style="{{ empty($settings['announcement_text'] ?? '') ? 'display:none;' : '' }}background:rgba(140,14,3,0.08);border:1px solid rgba(140,14,3,0.25);border-left:3px solid var(--crimson);padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
      <span id="banner-preview-text" style="font-size:13px;color:var(--text);">📢 {{ $settings['announcement_text'] ?? '' }}</span>
      <span style="color:var(--muted);font-size:16px;cursor:default;">✕</span>
    </div>
  </div>
</div>
</div>

{{-- ── Save Button ──────────────────────────────────────────────────────────────── --}}
<div style="display:flex;gap:10px;align-items:center;padding:20px 0;border-top:1px solid var(--border);margin-top:8px;">
  <button type="submit" class="btn btn-primary">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
      <polyline points="17,21 17,13 7,13 7,21"/><polyline points="7,3 7,8 15,8"/>
    </svg>
    Save Customization
  </button>
  <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Cancel</a>
  <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-left:8px;">// Changes take effect immediately for all users</span>
</div>

</form>

{{-- Hidden standalone forms --}}
<form id="logo-delete-form" method="POST" action="{{ route('admin.customization.logo.delete') }}" style="display:none;">
  @csrf @method('DELETE')
</form>
<form id="reset-branding-form" method="POST" action="{{ route('admin.customization.reset') }}" style="display:none;">
  @csrf
</form>

@push('styles')
<style>
.tab-btn {
    font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;
    padding:8px 16px;border:none;border-bottom:2px solid transparent;
    background:transparent;color:var(--muted);cursor:pointer;
    transition:color 0.15s,border-color 0.15s;
    white-space:nowrap;
}
.tab-btn:hover { color:var(--text2); }
.tab-btn.active { color:var(--crimson);border-bottom-color:var(--crimson); }
</style>
@endpush

@push('scripts')
<script>
function hexToRgb(hex) {
    const h = hex.replace('#','');
    return parseInt(h.slice(0,2),16)+','+parseInt(h.slice(2,4),16)+','+parseInt(h.slice(4,6),16);
}

function syncPicker(pickerId, hexId, onUpdate) {
    const picker = document.getElementById(pickerId);
    const hex    = document.getElementById(hexId);
    if (!picker || !hex) return;
    picker.addEventListener('input', () => { hex.value = picker.value; onUpdate(picker.value); });
    hex.addEventListener('input', () => {
        if (/^#[0-9A-Fa-f]{6}$/.test(hex.value)) { picker.value = hex.value; onUpdate(hex.value); }
    });
}

// ── Tab switching ─────────────────────────────────────────────────────────────────
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).style.display = 'block';
    document.getElementById('tab-btn-' + tab).classList.add('active');
}

// ── Preview mode toggle ────────────────────────────────────────────────────────────
function setPreviewMode(mode) {
    const isDark = mode === 'dark';
    document.getElementById('prev-dark').style.display  = isDark ? 'block' : 'none';
    document.getElementById('prev-light').style.display = isDark ? 'none' : 'block';
    document.getElementById('prev-dark-btn').style.background  = isDark ? 'var(--crimson)' : 'transparent';
    document.getElementById('prev-dark-btn').style.color       = isDark ? '#fff' : 'var(--muted)';
    document.getElementById('prev-dark-btn').style.borderColor = isDark ? 'var(--crimson)' : 'var(--border2)';
    document.getElementById('prev-light-btn').style.background  = isDark ? 'transparent' : 'var(--crimson)';
    document.getElementById('prev-light-btn').style.color       = isDark ? 'var(--muted)' : '#fff';
    document.getElementById('prev-light-btn').style.borderColor = isDark ? 'var(--border2)' : 'var(--crimson)';
}

// ── Color mode tab inside Colors panel ────────────────────────────────────────────
function switchColorMode(mode) {
    const isDark = mode === 'dark';
    document.getElementById('color-panel-dark').style.display  = isDark ? 'block' : 'none';
    document.getElementById('color-panel-light').style.display = isDark ? 'none' : 'block';
    document.getElementById('color-mode-dark').style.background  = isDark ? 'var(--crimson)' : 'transparent';
    document.getElementById('color-mode-dark').style.color       = isDark ? '#fff' : 'var(--muted)';
    document.getElementById('color-mode-dark').style.borderColor = isDark ? 'var(--crimson)' : 'var(--border2)';
    document.getElementById('color-mode-light').style.background  = isDark ? 'transparent' : 'var(--crimson)';
    document.getElementById('color-mode-light').style.color       = isDark ? 'var(--muted)' : '#fff';
    document.getElementById('color-mode-light').style.borderColor = isDark ? 'var(--border2)' : 'var(--crimson)';
}

// ── Preset swatches ────────────────────────────────────────────────────────────────
function applyPreset(primary, secondary) {
    ['colorPicker','colorHex'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = primary;
    });
    ['colorPickerSecondary','colorHexSecondary'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = secondary;
    });
    updatePrimaryPreview(primary);
    updateSecondaryPreview(secondary);
    // Also switch to branding tab and show live preview
    switchTab('colors');
}

// ── Primary color live update ──────────────────────────────────────────────────────
function updatePrimaryPreview(hex) {
    const rgb = hexToRgb(hex);
    // Dark preview
    const nb = document.getElementById('preview-navbar');
    if (nb) nb.style.borderBottomColor = hex;
    const pb = document.getElementById('preview-btn');
    if (pb) pb.style.background = hex;
    const badge = document.getElementById('preview-badge');
    if (badge) { badge.style.color = hex; badge.style.borderColor = hex+'55'; badge.style.background = hex+'15'; }
    const nd = document.getElementById('preview-name-display');
    if (nd) nd.style.color = hex;
    const ll = document.getElementById('preview-logo-letter');
    if (ll) ll.style.color = hex;
    const lw = document.getElementById('preview-logo-wrap');
    if (lw) { lw.style.borderColor = hex+'99'; lw.style.background = hex+'1a'; }
    const pp = document.getElementById('preview-progress-dark');
    if (pp) pp.style.background = hex;
    // Light preview
    const nbl = document.getElementById('preview-navbar-light');
    if (nbl) nbl.style.borderBottomColor = hex;
    const pbl = document.getElementById('preview-btn-light');
    if (pbl) pbl.style.background = hex;
    const badgel = document.getElementById('preview-badge-light');
    if (badgel) { badgel.style.color = hex; badgel.style.borderColor = hex+'44'; badgel.style.background = hex+'12'; }
    const ndl = document.getElementById('preview-name-display-light');
    if (ndl) ndl.style.color = hex;
    const lll = document.getElementById('preview-logo-letter-light');
    if (lll) lll.style.color = hex;
    const lwl = document.getElementById('preview-logo-wrap-light');
    if (lwl) { lwl.style.borderColor = hex+'66'; lwl.style.background = hex+'10'; }
    const ppl = document.getElementById('preview-progress-light');
    if (ppl) ppl.style.background = hex;
    // Email
    const eph = document.getElementById('ep-header');
    if (eph) eph.style.background = hex;
    const epg = document.getElementById('ep-greeting');
    if (epg) epg.style.borderLeftColor = hex;
    // Banner
    const bann = document.getElementById('banner-preview');
    if (bann) { bann.style.background = 'rgba('+rgb+',0.08)'; bann.style.borderColor = 'rgba('+rgb+',0.25)'; bann.style.borderLeftColor = hex; }
    // Apply to live CSS vars
    document.documentElement.style.setProperty('--crimson', hex);
    document.documentElement.style.setProperty('--crimson-lo', 'rgba('+rgb+',0.08)');
    document.documentElement.style.setProperty('--crimson-md', 'rgba('+rgb+',0.18)');
}

function updateSecondaryPreview(hex) {
    const nb = document.getElementById('preview-navbar');
    if (nb) nb.style.background = hex;
    const ph = document.getElementById('preview-hero');
    if (ph) ph.style.background = hex;
    const epf = document.getElementById('ep-footer');
    if (epf) epf.style.background = hex;
}

function updateLightBgPreview(hex) {
    const ph = document.getElementById('preview-hero-light');
    if (ph) ph.style.background = hex;
}

function updateLightSidebarPreview(hex) {
    const nb = document.getElementById('preview-navbar-light');
    if (nb) nb.style.background = hex;
}

function updateLightSurfacePreview(hex) {
    // Simulate card bg change in light preview
}

function updateLightTextPreview(hex) {
    const el = document.getElementById('preview-light-text');
    if (el) el.style.color = hex;
}

// ── Wire up all pickers ────────────────────────────────────────────────────────────
syncPicker('colorPicker',            'colorHex',             updatePrimaryPreview);
syncPicker('colorPickerSecondary',   'colorHexSecondary',    updateSecondaryPreview);
syncPicker('colorPickerDarkText',    'colorHexDarkText',     hex => { document.getElementById('preview-dark-text') && (document.getElementById('preview-dark-text').style.color = hex); });
syncPicker('colorPickerDarkBorder',  'colorHexDarkBorder',   hex => {});
syncPicker('colorPickerLight',       'colorHexLight',        hex => {
    // Light primary updates the same preview elements as dark primary when in light preview mode
    const pbl = document.getElementById('preview-btn-light');
    if (pbl) pbl.style.background = hex;
});
syncPicker('colorPickerLightBg',     'colorHexLightBg',      updateLightBgPreview);
syncPicker('colorPickerLightText',   'colorHexLightText',    updateLightTextPreview);
syncPicker('colorPickerLightBorder', 'colorHexLightBorder',  hex => {});
syncPicker('colorPickerLightSidebar','colorHexLightSidebar', updateLightSidebarPreview);
syncPicker('colorPickerLightSurface','colorHexLightSurface', updateLightSurfacePreview);

// ── Brand name ─────────────────────────────────────────────────────────────────────
document.getElementById('brandNameInput').addEventListener('input', function() {
    const val = this.value.trim() || 'OJTConnect';
    ['preview-brand-name','preview-brand-name-light','preview-name-display','preview-name-display-light','ep-name']
        .forEach(id => { const el = document.getElementById(id); if (el) el.textContent = val; });
});

// ── Logo preview ───────────────────────────────────────────────────────────────────
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

// ── Email previews ─────────────────────────────────────────────────────────────────
document.getElementById('greetingInput').addEventListener('input', function() {
    const el = document.getElementById('ep-greeting');
    if (el) { el.textContent = this.value; el.style.display = this.value.trim() ? 'block' : 'none'; }
});
document.getElementById('signatureInput').addEventListener('input', function() {
    const el = document.getElementById('ep-sig');
    if (el) el.textContent = this.value.trim() || '— The OJTConnect Team';
});

// ── Announcement preview ───────────────────────────────────────────────────────────
document.getElementById('announcementInput').addEventListener('input', function() {
    const banner = document.getElementById('banner-preview');
    const text   = document.getElementById('banner-preview-text');
    if (text)   text.textContent = '📢 ' + this.value;
    if (banner) banner.style.display = this.value.trim() ? 'flex' : 'none';
});

// ── Font preview ───────────────────────────────────────────────────────────────────
document.getElementById('fontSelect').addEventListener('change', function() {
    const fontName = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    ['preview-brand-name','preview-name-display'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.fontFamily = fontName + ', sans-serif';
    });
});
</script>
@endpush

@endsection