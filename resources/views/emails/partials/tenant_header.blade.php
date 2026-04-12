@php
    $color   = $tenantBrandColor ?? '8C0E03';
    $name    = $tenantBrandName  ?? config('app.name');
    $greet   = $tenantGreeting   ?? null;
@endphp
<tr>
  <td style="background:#{{ $color }};padding:1.75rem 2.5rem;">
    <table cellpadding="0" cellspacing="0"><tr>
      <td style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:0.3px;">
        {{ config('app.name') }} – {{ $name }}
      </td>
    </tr></table>
  </td>
</tr>
@if($greet)
<tr>
  <td style="background:#f9f9f9;padding:0.75rem 2.5rem;
              border-left:3px solid #{{ $color }};">
    <p style="color:#333740;font-size:13px;font-style:italic;margin:0;">
      {{ $greet }}
    </p>
  </td>
</tr>
@endif
