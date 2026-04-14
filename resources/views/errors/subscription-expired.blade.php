{{-- resources/views/errors/subscription-expired.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscription Expired</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body {
    background: #0f0f0f;
    color: #e8e6e0;
    font-family: 'Barlow', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
}
.container {
    max-width: 520px;
    width: 100%;
    text-align: center;
}
.badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border: 1px solid rgba(239,68,68,0.4);
    background: rgba(239,68,68,0.08);
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: #ef4444;
    margin-bottom: 24px;
}
.badge-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #ef4444;
    animation: pulse 2s ease-in-out infinite;
}
h1 {
    font-family: 'Playfair Display', serif;
    font-size: 40px;
    font-weight: 900;
    color: #e8e6e0;
    line-height: 1.1;
    margin-bottom: 16px;
}
.subtitle {
    font-size: 15px;
    color: #6a6762;
    line-height: 1.7;
    margin-bottom: 32px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}
.info-card {
    border: 1px solid rgba(255,255,255,0.08);
    background: #161616;
    padding: 20px 24px;
    margin-bottom: 24px;
    text-align: left;
}
.info-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    font-family: 'DM Mono', monospace;
    font-size: 11px;
}
.info-row:last-child { border-bottom: none; }
.info-row .label { color: #6a6762; }
.info-row .val   { color: #e8e6e0; }
.info-row .val.red { color: #ef4444; }
.actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    cursor: pointer;
    border: none;
    transition: all 0.15s;
}
.btn-primary {
    background: rgba(140,14,3,0.8);
    border: 1px solid rgba(140,14,3,0.5);
    color: #e8c8c6;
}
.btn-primary:hover { background: rgba(140,14,3,1); }
.btn-ghost {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    color: #6a6762;
}
.btn-ghost:hover { border-color: rgba(255,255,255,0.2); color: #b0ada6; }
.divider {
    display: flex; align-items: center; gap: 12px;
    margin: 28px 0 20px;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.07); }
.divider span {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.16em;
    text-transform: uppercase; color: #6a6762;
}
@keyframes pulse {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(239,68,68,0.4); }
    50% { opacity: 0.8; box-shadow: 0 0 0 4px rgba(239,68,68,0); }
}
</style>
</head>
<body>
<div class="container">

    <div class="badge">
        <span class="badge-dot"></span>
        Subscription Expired
    </div>

    <h1>Access<br>Suspended</h1>

    <p class="subtitle">
        Your institution's subscription has expired and the grace period has ended.
        Please contact your system administrator to renew access.
    </p>

    <div class="info-card">
        <div class="info-row">
            <span class="label">Institution</span>
            <span class="val">{{ $tenant?->id ?? 'Unknown' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Subscription expired</span>
            <span class="val red">
                {{ $expiredAt instanceof \Carbon\Carbon ? $expiredAt->format('M d, Y') : ($expiredAt ?? 'Unknown') }}
            </span>
        </div>
        <div class="info-row">
            <span class="label">Grace period ended</span>
            <span class="val red">
                {{ $graceEndedAt instanceof \Carbon\Carbon ? $graceEndedAt->format('M d, Y') : ($graceEndedAt ?? 'Unknown') }}
            </span>
        </div>
        <div class="info-row">
            <span class="label">Status</span>
            <span class="val red">Access blocked · pending renewal</span>
        </div>
    </div>

    <div class="actions">
        <a href="mailto:support@ojtconnect.com?subject=Subscription renewal — {{ $tenant?->id }}&body=Institution: {{ $tenant?->id }}%0AExpired: {{ $expiredAt instanceof \Carbon\Carbon ? $expiredAt->format('Y-m-d') : '' }}%0A%0APlease renew our subscription."
           class="btn btn-primary">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,12 2,6"/>
            </svg>
            Contact Support
        </a>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="btn btn-ghost">Sign Out</button>
        </form>
    </div>

    <div class="divider"><span>or</span></div>

    <p style="font-family:'DM Mono',monospace;font-size:11px;color:#6a6762;">
        If you believe this is an error, ask your system administrator to check
        <code style="background:rgba(255,255,255,0.05);padding:2px 6px;border:1px solid rgba(255,255,255,0.07);">plan_expires_at</code>
        in the tenant record.
    </p>

</div>
</body>
</html>