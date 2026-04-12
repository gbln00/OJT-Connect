{{--
    Tenant-branded email footer partial.
    Outputs one <tr> row with the secondary brand color as background.
--}}
@php
    $colorSec = $tenantBrandColorSecondary ?? '0E1126';
    $sig      = $tenantSignature           ?? null;
@endphp
<tr>
  <td style="background:#{{ $colorSec }};padding:1.5rem 2.5rem;text-align:center;">
    <p style="color:#ABABAB;font-size:13px;margin:0 0 0.25rem;">
      Need help? Contact us at
      <span style="color:#ffffff;">support@ojtconnect.com</span>
    </p>
    <p style="color:#6b7280;font-size:12px;margin:0.4rem 0 0;">
      {{ $sig ?? ('— The ' . config('app.name') . ' Team') }}
    </p>
  </td>
</tr>