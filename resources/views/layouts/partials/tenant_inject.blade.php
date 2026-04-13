{{--
    Tenant CSS + Font injection partial.
    Include in the <head> of EVERY layout, AFTER the main <style> block.

    File: resources/views/layouts/partials/tenant_inject.blade.php
--}}
@php
    $tPrimary      = $tenantBrandColor ?? null;
    $tSecondary    = $tenantBrandColorSecondary ?? null;
    $tFont         = $tenantBrandFont ?? 'barlow';
    $tPrimaryLight = $settings['brand_color_light'] ?? $tPrimary;
    $lightBg       = $settings['light_bg_color'] ?? null;
    $lightText     = $settings['light_text_color'] ?? null;
    $lightBorder   = $settings['light_border_color'] ?? null;
    $lightSidebar  = $settings['light_sidebar_color'] ?? null;
    $lightSurface  = $settings['light_surface_color'] ?? null;
    $darkText      = $settings['dark_text_color'] ?? null;
    $darkBorder    = $settings['dark_border_color'] ?? null;

    $hexToRgb = fn(string $hex): string =>
        implode(',', array_map('hexdec', str_split(ltrim($hex, '#'), 2)));

    $rawFontSize  = $settings['font_size_base'] ?? null;
    $cssFontSize  = $rawFontSize
        ? (str_ends_with((string)$rawFontSize, 'px') ? $rawFontSize : $rawFontSize . 'px')
        : null;

    $uiDensity    = $settings['ui_density'] ?? null;
    $headingStyle = $settings['heading_style'] ?? null;
@endphp

{{-- ── Google Font import ───────────────────────────────────────────────────── --}}
@if($tFont && $tFont !== 'barlow')
@php
    $fontImports = [
        'inter'   => 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        'poppins' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
        'roboto'  => 'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap',
    ];
    $fontStacks = [
        'inter'   => "'Inter', sans-serif",
        'poppins' => "'Poppins', sans-serif",
        'roboto'  => "'Roboto', sans-serif",
    ];
    $importUrl = $fontImports[$tFont] ?? null;
    $fontStack = $fontStacks[$tFont] ?? null;
@endphp
@if($importUrl ?? false)
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="{{ $importUrl }}" rel="stylesheet">
@endif
@else
@php $fontStack = null; @endphp
@endif

{{--
    CRITICAL SPECIFICITY FIX:
    ─────────────────────────
    The base stylesheet defines dark defaults on :root AND [data-theme="dark"].
    Tenant overrides must ALSO target [data-theme="dark"] — never bare :root —
    so they don't bleed into light mode.

    Load order in app.blade.php head:
      1. Base <style> block  (:root dark defaults + [data-theme="light"] overrides)
      2. @stack('styles')
      3. @include('layouts.partials.tenant_inject')   ← this file, comes LAST
      4. The inline brand_color <style> block that was in app.blade.php
         → that block must be REMOVED from app.blade.php (we handle it here)
--}}
<style>

    /* ═══════════════════════════════════════════════════════════════
       DARK MODE tenant overrides
       Selector [data-theme="dark"] matches the default state of <html>
       and has identical specificity to [data-theme="light"], so order
       in the file controls which wins — light overrides come after.
    ═══════════════════════════════════════════════════════════════ */
    [data-theme="dark"] {
        @if($tPrimary)
        --crimson:    #{{ $tPrimary }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimary) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimary) }}, 0.18);
        @endif

        @if($tSecondary)
        --night:    #{{ $tSecondary }};
        --surface:  #{{ $tSecondary }};
        --surface2: color-mix(in srgb, #{{ $tSecondary }} 78%, #ffffff 22%);
        --surface3: color-mix(in srgb, #{{ $tSecondary }} 60%, #ffffff 40%);
        @endif

        @if($darkText)
        --text:   rgba({{ $hexToRgb($darkText) }}, 0.88);
        --text2:  rgba({{ $hexToRgb($darkText) }}, 0.55);
        --muted:  rgba({{ $hexToRgb($darkText) }}, 0.35);
        --muted2: rgba({{ $hexToRgb($darkText) }}, 0.50);
        @endif

        @if($darkBorder)
        --border:  rgba({{ $hexToRgb($darkBorder) }}, 0.45);
        --border2: rgba({{ $hexToRgb($darkBorder) }}, 0.75);
        @endif
    }

    /* ═══════════════════════════════════════════════════════════════
       LIGHT MODE tenant overrides
       Comes AFTER the dark block → same specificity, later position wins.
    ═══════════════════════════════════════════════════════════════ */
    [data-theme="light"] {
        @if($tPrimaryLight)
        --crimson:    #{{ $tPrimaryLight }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.18);
        @endif

        /* Always restore light base variables even without tenant overrides */
        --bg:      {{ $lightBg ? '#'.$lightBg : '#F5F4F0' }};
        --surface: {{ $lightSurface ? '#'.$lightSurface : '#FFFFFF' }};
        --surface2: {{ $lightBg ? 'color-mix(in srgb, #'.$lightBg.' 60%, #ffffff 40%)' : '#F0EEE9' }};
        --surface3: {{ $lightBg ? 'color-mix(in srgb, #'.$lightBg.' 35%, #ffffff 65%)' : '#E8E5DE' }};
        --text:    {{ $lightText ? '#'.$lightText : '#0D0D0D' }};
        --text2:   {{ $lightText ? 'rgba('.$hexToRgb($lightText).', 0.65)' : '#333740' }};
        --muted:   {{ $lightText ? 'rgba('.$hexToRgb($lightText).', 0.42)' : '#8a8e99' }};
        --muted2:  {{ $lightText ? 'rgba('.$hexToRgb($lightText).', 0.55)' : '#6b7280' }};
        --border:  {{ $lightBorder ? 'rgba('.$hexToRgb($lightBorder).', 0.55)' : 'rgba(51,55,64,0.1)' }};
        --border2: {{ $lightBorder ? 'rgba('.$hexToRgb($lightBorder).', 0.85)' : 'rgba(51,55,64,0.18)' }};
    }

    /* Sidebar in light mode: its own background separate from --surface */
    [data-theme="light"] .sidebar,
    [data-theme="light"] .sidebar-brand,
    [data-theme="light"] .sidebar-footer {
        background: {{ $lightSidebar ? '#'.$lightSidebar : '#FFFFFF' }};
    }

    /* ── Body / page background in light mode ───────────────────── */
    [data-theme="light"] body,
    [data-theme="light"] .layout-body,
    [data-theme="light"] .topbar,
    [data-theme="light"] .main-content {
        background: {{ $lightBg ? '#'.$lightBg : '#F5F4F0' }};
    }

    /* ── Cards in light mode ────────────────────────────────────── */
    [data-theme="light"] .card,
    [data-theme="light"] .stat-card {
        background: {{ $lightSurface ? '#'.$lightSurface : '#FFFFFF' }};
        border-color: {{ $lightBorder ? 'rgba('.$hexToRgb($lightBorder ?? 'D1D5DB').', 0.55)' : 'rgba(51,55,64,0.1)' }};
    }

    /* ── Tables in light mode ────────────────────────────────────── */
    [data-theme="light"] thead th {
        background: {{ $lightBg ? 'color-mix(in srgb, #'.$lightBg.' 60%, #ffffff 40%)' : '#F0EEE9' }};
    }
    [data-theme="light"] tbody tr:hover {
        background: {{ $lightBg ? 'color-mix(in srgb, #'.$lightBg.' 40%, #ffffff 60%)' : '#F0EEE9' }};
    }

    /* ── Typography ─────────────────────────────────────────────── */
    @if($fontStack ?? false)
    html, body, .nav-item, .btn,
    .form-input, .form-select, .form-textarea,
    tbody td, .dropdown-item {
        font-family: {{ $fontStack }};
    }
    @endif

    @if($cssFontSize)
    html, body { font-size: {{ $cssFontSize }}; }
    tbody td, .form-input, .form-select, .dropdown-item { font-size: {{ $cssFontSize }}; }
    @endif

    /* ── UI Density ─────────────────────────────────────────────── */
    @if($uiDensity === 'compact')
    .main-content { padding: 16px 20px; }
    .card-header  { padding: 10px 16px 9px; }
    .stat-card    { padding: 12px 14px; }
    tbody td      { padding: 8px 12px; }
    thead th      { padding: 7px 12px; }
    .btn          { padding: 6px 12px; }
    .nav-item     { padding: 6px 10px; }
    @elseif($uiDensity === 'comfortable')
    .main-content { padding: 36px 40px; }
    .card-header  { padding: 20px 24px 18px; }
    .stat-card    { padding: 24px 26px; }
    tbody td      { padding: 16px 20px; }
    thead th      { padding: 14px 20px; }
    .btn          { padding: 10px 20px; }
    .nav-item     { padding: 10px 12px; }
    @endif

    /* ── Heading style ──────────────────────────────────────────── */
    @if($headingStyle === 'condensed')
    .card-title, .greeting-title, .modal-title, h1, h2, h3 {
        font-family: 'Barlow Condensed', sans-serif;
        font-style: normal;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }
    @elseif($headingStyle === 'mono')
    .card-title, .greeting-title, .modal-title, h1, h2, h3 {
        font-family: 'DM Mono', monospace;
        font-style: normal;
        letter-spacing: 0.04em;
    }
    @endif

</style>