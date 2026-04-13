{{--
    Tenant CSS + Font injection partial.
    Include in the <head> of EVERY layout, after @stack('styles').

    File: resources/views/layouts/partials/tenant_inject.blade.php

    Usage:  @include('layouts.partials.tenant_inject')
--}}
@php
    $tPrimary   = $tenantBrandColor ?? null;
    $tSecondary = $tenantBrandColorSecondary ?? null;
    $tFont      = $tenantBrandFont ?? 'barlow';
    
    $hexToRgb = fn(string $hex): string =>
        implode(',', array_map('hexdec', str_split(ltrim($hex, '#'), 2)));
@endphp

{{-- Google Font import --}}
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


{{-- Tenant CSS variable override --}}
@if($tPrimary || $tSecondary || ($fontStack ?? null))
@php
    $tPrimary      = $tenantBrandColor ?? null;
    $tPrimaryLight = $settings['brand_color_light'] ?? $tPrimary;
    $tSecondary    = $tenantBrandColorSecondary ?? null;
    $tFont         = $tenantBrandFont ?? 'barlow';
    $lightBg       = $settings['light_bg_color'] ?? null;
    $lightText     = $settings['light_text_color'] ?? null;
    $lightBorder   = $settings['light_border_color'] ?? null;
    $lightSidebar  = $settings['light_sidebar_color'] ?? null;
    $lightSurface  = $settings['light_surface_color'] ?? null;
    $darkText      = $settings['dark_text_color'] ?? null;
    $darkBorder    = $settings['dark_border_color'] ?? null;
    $hexToRgb = fn(string $hex): string =>
        implode(',', array_map('hexdec', str_split(ltrim($hex, '#'), 2)));
@endphp

<style>
    :root,
    [data-theme="dark"] {
        @if($tPrimary)
        --crimson:    #{{ $tPrimary }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimary) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimary) }}, 0.18);
        @endif
        @if($tSecondary)
        --night:   #{{ $tSecondary }};
        --surface: #{{ $tSecondary }};
        @endif
        @if($darkText)
        --text: rgba({{ $hexToRgb($darkText) }}, 0.88);
        @endif
        @if($darkBorder)
        --border:  rgba({{ $hexToRgb($darkBorder) }}, 0.5);
        --border2: rgba({{ $hexToRgb($darkBorder) }}, 0.8);
        @endif
    }

    [data-theme="light"] {
        @if($tPrimaryLight)
        --crimson:    #{{ $tPrimaryLight }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimaryLight) }}, 0.18);
        @endif
        @if($lightBg)
        --bg: #{{ $lightBg }};
        @endif
        @if($lightText)
        --text:  #{{ $lightText }};
        --text2: rgba({{ $hexToRgb($lightText) }}, 0.65);
        @endif
        @if($lightBorder)
        --border:  rgba({{ $hexToRgb($lightBorder) }}, 0.6);
        --border2: rgba({{ $hexToRgb($lightBorder) }}, 0.9);
        @endif
        @if($lightSurface)
        --surface: #{{ $lightSurface }};
        @endif
        @if($lightSidebar)
        --surface: #{{ $lightSidebar }};
        @endif
    }

    @if($lightSidebar)
    [data-theme="light"] .sidebar {
        background: #{{ $lightSidebar }};
    }
    @endif
</style>
@endif