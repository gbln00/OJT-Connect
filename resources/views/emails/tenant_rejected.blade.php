<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration Update</title>
</head>
<body style="margin:0;padding:0;background:#ABABAB;font-family:Arial,sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="padding:2rem 1rem;">
    <tr>
      <td align="center">
        <table width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:580px;width:100%;">

          {{-- Header --}}
          <tr>
            <td style="background:#8C0E03;padding:1.75rem 2.5rem;">
              <table cellpadding="0" cellspacing="0">
                <tr>
                  <td style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:0.3px;">
                    {{ config('app.name') }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Hero --}}
          <tr>
            <td style="background:#0E1126;padding:2rem 2.5rem;text-align:center;">
              <div style="width:56px;height:56px;background:#333740;border-radius:50%;margin:0 auto 1rem;line-height:56px;font-size:24px;color:#ABABAB;text-align:center;">✕</div>
              <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 0.5rem;">Registration Not Approved</h1>
              <p style="color:#ABABAB;font-size:14px;margin:0;">Your registration has been reviewed by our team</p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:2rem 2.5rem;">
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.25rem;">
                Hello <strong style="color:#0D0D0D;">{{ $registration->company_name }}</strong>,
              </p>
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                Thank you for your interest in <strong style="color:#0D0D0D;">{{ config('app.name') }}</strong>. After reviewing your registration, we regret to inform you that we are unable to approve your account at this time.
              </p>

              {{-- Rejection reason box (only shown if reason exists) --}}
              @if($registration->rejection_reason)
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 1.75rem;">
                <tr>
                  <td style="background:#f9f9f9;border-left:4px solid #333740;border-radius:4px;padding:1rem 1.25rem;">
                    <p style="color:#ABABAB;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 0.5rem;">Reason for rejection</p>
                    <p style="color:#0D0D0D;font-size:14px;line-height:1.6;margin:0;">{{ $registration->rejection_reason }}</p>
                  </td>
                </tr>
              </table>
              @endif

              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                If you believe this decision was made in error or you have addressed the concern above, you are welcome to resubmit your registration.
              </p>

              {{-- CTA Button --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 2rem;">
                <tr>
                  <td align="center">
                    <a href="{{ url('/register') }}"
                       style="display:inline-block;background:#0E1126;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:0.75rem 2rem;border-radius:6px;letter-spacing:0.3px;">
                      Resubmit Registration
                    </a>
                  </td>
                </tr>
              </table>

              {{-- Closing note --}}
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-top:1px solid #eeeeee;padding-top:1.5rem;">
                    <p style="color:#333740;font-size:14px;line-height:1.7;margin:0;">
                      If you have any questions or need clarification, please don't hesitate to reach out to our support team.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="background:#0E1126;padding:1.5rem 2.5rem;text-align:center;">
              <p style="color:#ABABAB;font-size:13px;margin:0 0 0.25rem;">
                Need help? Contact us at <span style="color:#ffffff;">support@{{ config('app.base_domain', 'yourapp.com') }}</span>
              </p>
              <p style="color:#555a6a;font-size:12px;margin:0.5rem 0 0;">— The {{ config('app.name') }} Team</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>