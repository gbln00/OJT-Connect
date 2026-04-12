@php
    $sig   = $tenantSignature  ?? null;
    $color = $tenantBrandColor ?? '0E1126';
@endphp
<tr>
  <td style="background:#{{ $color }};padding:1.5rem 2.5rem;text-align:center;">
    <p style="color:#ABABAB;font-size:13px;margin:0 0 0.25rem;">
      Need help? Contact us at
      <span style="color:#ffffff;">support@ojtconnect.com</span>
    </p>
    <p style="color:#555a6a;font-size:12px;margin:0.5rem 0 0;">
      {{ $sig ?? '— The ' . config('app.name') . ' Team' }}
    </p>
  </td>
</tr>
