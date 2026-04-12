<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Approved</title>
</head>
<body style="margin:0;padding:0;background:#ABABAB;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="padding:2rem 1rem;">
    <tr>
      <td align="center">
        <table width="580" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;max-width:580px;width:100%;">

          {{-- Header --}}

          @include('emails.partials.tenant_header')

          {{-- Hero --}}
          <tr>
            <td style="background:#0E1126;padding:2rem 2.5rem;text-align:center;">
              <div style="width:56px;height:56px;background:#8C0E03;border-radius:50%;margin:0 auto 1rem;line-height:56px;font-size:28px;color:#ffffff;text-align:center;">✓</div>
              <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 0.5rem;">Application Approved!</h1>
              <p style="color:#ABABAB;font-size:14px;margin:0;">Your OJT application has been reviewed and approved</p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:2rem 2.5rem;">
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.25rem;">
                Hello <strong style="color:#0D0D0D;">{{ $studentName }}</strong>,
              </p>
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                Great news — your OJT application has been reviewed and <strong style="color:#0D0D0D;">approved</strong>. You can now start logging your daily hours from your student dashboard.
              </p>

              {{-- Details box --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 1.75rem;">
                <tr>
                  <td style="background:#f9f9f9;border-left:4px solid #8C0E03;border-radius:4px;padding:1rem 1.25rem;">
                    <p style="color:#ABABAB;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 0.75rem;">Application Details</p>
                    <table cellpadding="0" cellspacing="0">
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;padding-bottom:0.4rem;width:120px;">Company</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;padding-bottom:0.4rem;">{{ $companyName }}</td>
                      </tr>
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;padding-bottom:0.4rem;">Program</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;padding-bottom:0.4rem;">{{ $program }}</td>
                      </tr>
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;padding-bottom:0.4rem;">Semester</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;padding-bottom:0.4rem;">{{ $semester }}</td>
                      </tr>
                      <tr>
                        <td style="color:#ABABAB;font-size:13px;">Required Hours</td>
                        <td style="color:#0D0D0D;font-size:14px;font-weight:600;">{{ $requiredHours }} hours</td>
                      </tr>
                    </table>
                    @if($remarks)
                    <p style="color:#ABABAB;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0.75rem 0 0.4rem;">Coordinator Remarks</p>
                    <p style="color:#0D0D0D;font-size:14px;line-height:1.6;margin:0;">{{ $remarks }}</p>
                    @endif
                  </td>
                </tr>
              </table>

              {{-- CTA --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 2rem;">
                <tr>
                  <td align="center">
                    <a href="{{ $dashboardUrl }}" style="display:inline-block;background:#8C0E03;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:0.75rem 2rem;border-radius:6px;letter-spacing:0.3px;">
                      Go to My Dashboard
                    </a>
                  </td>
                </tr>
              </table>

              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-top:1px solid #eeeeee;padding-top:1.5rem;">
                    <p style="color:#333740;font-size:14px;line-height:1.7;margin:0;">
                      If you have questions, please contact your OJT coordinator.
                    </p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          {{-- Footer --}}
          
          @include('emails.partials.tenant_footer')

        </table>
      </td>
    </tr>
  </table>
</body>
</html>