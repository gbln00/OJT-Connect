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
    // Inline closure — safe for repeated includes (no function re-declaration)
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
<style>
    :root {
        @if($tPrimary)
        --crimson:    #{{ $tPrimary }};
        --crimson-lo: rgba({{ $hexToRgb($tPrimary) }}, 0.08);
        --crimson-md: rgba({{ $hexToRgb($tPrimary) }}, 0.18);
        @endif
        @if($tSecondary)
        --night:   #{{ $tSecondary }};
        --surface: #{{ $tSecondary }};
        @endif
    }
    @if($fontStack ?? null)
    html, body, .nav-item, .btn, .form-input, .form-select, .form-textarea,
    .dropdown-item, .sidebar-user-name, .card-action, .activity-text,
    .topbar-user-name, .qa-label, .stat-label {
        font-family: {{ $fontStack }} !important;
    }
    @endif
</style>
@endif