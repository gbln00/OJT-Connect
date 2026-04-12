{{--
    Tenant-branded email header partial.
    Used inside <table> email layouts — outputs two <tr> rows:
      1. Header bar (primary color background)
      2. Optional greeting row (if set)
--}}
@php
    $color   = $tenantBrandColor          ?? '8C0E03';
    $colorSec= $tenantBrandColorSecondary ?? '0E1126';
    $name    = $tenantBrandName           ?? config('app.name');
    $greet   = $tenantGreeting            ?? null;
    $logoUrl = $tenantLogoUrl             ?? null;
@endphp

{{-- Header bar --}}
<tr>
  <td style="background:#{{ $color }};padding:1.5rem 2.5rem;">
    <table cellpadding="0" cellspacing="0">
      <tr>
        @if($logoUrl)
        <td style="padding-right:12px;vertical-align:middle;">
          <img src="{{ $logoUrl }}" alt="Logo"
               style="height:36px;width:36px;object-fit:contain;
                      border:1px solid rgba(255,255,255,0.2);display:block;">
        </td>
        @endif
        <td style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:0.3px;vertical-align:middle;">
          {{ config('app.name') }}@if($name && $name !== config('app.name')) – {{ $name }}@endif
        </td>
      </tr>
    </table>
  </td>
</tr>

{{-- Optional greeting row --}}
@if($greet)
<tr>
  <td style="background:#f5f5f5;padding:0.65rem 2.5rem;border-left:4px solid #{{ $color }};">
    <p style="color:#333740;font-size:13px;font-style:italic;margin:0;line-height:1.5;">
      {{ $greet }}
    </p>
  </td>
</tr>
@endif