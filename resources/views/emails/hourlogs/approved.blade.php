<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hour Logs Approved</title>
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
                    {{ config('app.name') }} - {{ tenant('name') }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Hero --}}
          <tr>
            <td style="background:#0E1126;padding:2rem 2.5rem;text-align:center;">
              <div style="width:56px;height:56px;background:#8C0E03;border-radius:50%;margin:0 auto 1rem;line-height:56px;font-size:28px;color:#ffffff;text-align:center;">✓</div>
              <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 0.5rem;">Hour Logs Approved!</h1>
              <p style="color:#ABABAB;font-size:14px;margin:0;">Your supervisor has approved your submitted hour logs</p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:2rem 2.5rem;">
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.25rem;">
                Hello <strong style="color:#0D0D0D;">{{ $studentName }}</strong>,
              </p>
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                Your supervisor has approved <strong style="color:#0D0D0D;">{{ $approvedCount }} hour log(s)</strong>. Keep logging your daily sessions to reach your required hours target.
              </p>

              {{-- Stats box --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 1.75rem;">
                <tr>
                  <td style="background:#f9f9f9;border-left:4px solid #8C0E03;border-radius:4px;padding:1rem 1.25rem;">
                    <p style="color:#ABABAB;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 0.75rem;">Hours Summary</p>
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;padding-bottom:0.4rem;width:180px;">Logs Approved This Batch</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;padding-bottom:0.4rem;">{{ $approvedCount }}</td>
                      </tr>
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;">Total Approved Hours So Far</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;">{{ $totalApprovedHours }} hrs</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>

              {{-- CTA --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 2rem;">
                <tr>
                  <td align="center">
                    <a href="{{ $logsUrl }}" style="display:inline-block;background:#8C0E03;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:0.75rem 2rem;border-radius:6px;letter-spacing:0.3px;">
                      View My Hour Logs
                    </a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Footer --}}
          <tr>
            <td style="background:#0E1126;padding:1.5rem 2.5rem;text-align:center;">
              <p style="color:#ABABAB;font-size:13px;margin:0 0 0.25rem;">
                Need help? Contact us at <span style="color:#ffffff;">support@ojtconnect.com</span>
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