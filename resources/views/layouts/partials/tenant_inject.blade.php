{{--
    Tenant CSS + Font injection partial.
    Include in the <head> of EVERY layout, AFTER the main <style> block.
--}}
@php
    $tPrimary      = $tenantBrandColor ?? null;
    $tSecondary    = $tenantBrandColorSecondary ?? null;
    $tFont         = $tenantBrandFont ?? 'barlow';
    $settings      = $tenantSettings ?? [];

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

    $darkenHex = function(string $hex, int $amount = 8): string {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        // Slightly lighten (add brightness) for surface2, keep it dark
        $r = min(255, $r + $amount);
        $g = min(255, $g + $amount);
        $b = min(255, $b + $amount);
        return sprintf('%02x%02x%02x', $r, $g, $b);
    };

    $rawFontSize  = $settings['font_size_base'] ?? null;
    $cssFontSize  = $rawFontSize
        ? (str_ends_with((string)$rawFontSize, 'px') ? $rawFontSize : $rawFontSize . 'px')
        : null;

    $uiDensity    = $settings['ui_density'] ?? null;
    $headingStyle = $settings['heading_style'] ?? null;

    $hasColorOverride  = $tPrimary || $tSecondary || $darkText || $darkBorder;
    $hasLightOverride  = $lightBg || $lightText || $lightBorder || $lightSidebar || $lightSurface || $tPrimaryLight;
    $hasFontOverride   = $tFont && $tFont !== 'barlow';
    $hasSizeOverride   = (bool)$cssFontSize;
    $hasDensityOverride = (bool)$uiDensity;
    $hasHeadingOverride = (bool)$headingStyle;
    $hasAnyOverride    = $hasColorOverride || $hasLightOverride || $hasFontOverride || $hasSizeOverride || $hasDensityOverride || $hasHeadingOverride;
@endphp

{{-- ── Google Font import (only if font changed) ───────────────────────────── --}}
@if($hasFontOverride)
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

{{-- Only inject override styles if something actually changed --}}
@if($hasAnyOverride)
<style>

    /* ═══════════════════════════════════════════════════════════════
       DARK MODE tenant overrides — only emitted when values exist
    ═══════════════════════════════════════════════════════════════ */
    @if($hasColorOverride)
    [data-theme="dark"] {
        @if($tPrimary)
        --crimson:    #{{ $tPrimary }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimary) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimary) }}, 0.18);
        @endif


        @if($tSecondary)
        --night:    #{{ $tSecondary }};
        --surface:  #{{ $tSecondary }};
        --surface2: #{{ $darkenHex($tSecondary, 8) }};
        --surface3: #{{ $darkenHex($tSecondary, 16) }};
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
    @endif

    /* ═══════════════════════════════════════════════════════════════
       LIGHT MODE tenant overrides — only emitted when values exist
    ═══════════════════════════════════════════════════════════════ */
    @if($hasLightOverride)
    [data-theme="light"] {
        @if($tPrimaryLight)
        --crimson:    #{{ $tPrimaryLight }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.18);
        @endif

        @if($lightBg)
        --bg:      #{{ $lightBg }};
        @endif

        @if($lightSurface)
        --surface: #{{ $lightSurface }};
        @endif

        {{-- Derive surface2/surface3 from lightBg or lightSurface for light mode --}}
        @if($lightBg && !$lightSurface)
        --surface2: color-mix(in srgb, #{{ $lightBg }} 60%, #ffffff 40%);
        --surface3: color-mix(in srgb, #{{ $lightBg }} 35%, #ffffff 65%);
        @elseif($lightSurface)
        --surface2: color-mix(in srgb, #{{ $lightSurface }} 88%, #000000 12%);
        --surface3: color-mix(in srgb, #{{ $lightSurface }} 75%, #000000 25%);
        @endif

        @if($lightText)
        --text:    #{{ $lightText }};
        --text2:   rgba({{ $hexToRgb($lightText) }}, 0.65);
        --muted:   rgba({{ $hexToRgb($lightText) }}, 0.42);
        --muted2:  rgba({{ $hexToRgb($lightText) }}, 0.55);
        @endif

        @if($lightBorder)
        --border:  rgba({{ $hexToRgb($lightBorder) }}, 0.55);
        --border2: rgba({{ $hexToRgb($lightBorder) }}, 0.85);
        @endif
    }

    @if($lightSidebar)
    [data-theme="light"] .sidebar,
    [data-theme="light"] .sidebar-brand,
    [data-theme="light"] .sidebar-footer {
        background: #{{ $lightSidebar }};
    }
    @endif

    @if($lightBg)
    [data-theme="light"] body,
    [data-theme="light"] .layout-body,
    [data-theme="light"] .topbar,
    [data-theme="light"] .main-content {
        background: #{{ $lightBg }};
    }

    [data-theme="light"] thead th {
        background: color-mix(in srgb, #{{ $lightBg }} 60%, #ffffff 40%);
    }
    [data-theme="light"] tbody tr:hover {
        background: color-mix(in srgb, #{{ $lightBg }} 40%, #ffffff 60%);
    }
    @endif

    @if($lightSurface)
    [data-theme="light"] .card,
    [data-theme="light"] .stat-card {
        background: #{{ $lightSurface }};
        @if($lightBorder)
        border-color: rgba({{ $hexToRgb($lightBorder) }}, 0.55);
        @endif
    }
    @endif
    @endif

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
@endif