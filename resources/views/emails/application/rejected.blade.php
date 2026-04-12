<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Update</title>
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
              <div style="width:56px;height:56px;background:#333740;border-radius:50%;margin:0 auto 1rem;line-height:56px;font-size:24px;color:#ABABAB;text-align:center;">✕</div>
              <h1 style="color:#ffffff;font-size:22px;font-weight:700;margin:0 0 0.5rem;">Application Not Approved</h1>
              <p style="color:#ABABAB;font-size:14px;margin:0;">Your OJT application has been reviewed by your coordinator</p>
            </td>
          </tr>

          {{-- Body --}}
          <tr>
            <td style="padding:2rem 2.5rem;">
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.25rem;">
                Hello <strong style="color:#0D0D0D;">{{ $studentName }}</strong>,
              </p>
              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                Thank you for submitting your OJT application for <strong style="color:#0D0D0D;">{{ $companyName }}</strong>. After review, your application was not approved at this time.
              </p>

              @if($remarks)
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 1.75rem;">
                <tr>
                  <td style="background:#f9f9f9;border-left:4px solid #333740;border-radius:4px;padding:1rem 1.25rem;">
                    <p style="color:#ABABAB;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin:0 0 0.5rem;">Reason / Remarks</p>
                    <p style="color:#0D0D0D;font-size:14px;line-height:1.6;margin:0;">{{ $remarks }}</p>
                  </td>
                </tr>
              </table>
              @endif

              <p style="color:#333740;font-size:15px;line-height:1.7;margin:0 0 1.5rem;">
                You are welcome to revise and resubmit a new application.
              </p>

              {{-- CTA --}}
              <table width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 2rem;">
                <tr>
                  <td align="center">
                    <a href="{{ $applyUrl }}" style="display:inline-block;background:#0E1126;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:0.75rem 2rem;border-radius:6px;letter-spacing:0.3px;">
                      Apply Again
                    </a>
                  </td>
                </tr>
              </table>

              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="border-top:1px solid #eeeeee;padding-top:1.5rem;">
                    <p style="color:#333740;font-size:14px;line-height:1.7;margin:0;">
                      If you have questions, please reach out to your OJT coordinator.
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