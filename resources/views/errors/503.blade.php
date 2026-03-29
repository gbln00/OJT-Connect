<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Disabled</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;700;900&family=Barlow:wght@400;500&family=Playfair+Display:wght@700&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #080910;
            color: #fff;
            font-family: 'Barlow', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Subtle grid background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(140,14,3,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(140,14,3,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .card {
            position: relative;
            background: #0E1126;
            border: 1px solid rgba(171,171,171,0.08);
            border-top: 2px solid #8C0E03;
            max-width: 480px;
            width: 100%;
            padding: 40px;
        }

        /* Glow behind card */
        .card::before {
            content: '';
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            width: 220px;
            height: 60px;
            background: rgba(140,14,3,0.25);
            filter: blur(40px);
            pointer-events: none;
        }

        .code-line {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: rgba(140,14,3,0.6);
            letter-spacing: 0.12em;
            margin-bottom: 20px;
        }

        .status-number {
            font-family: 'Playfair Display', serif;
            font-size: 72px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            letter-spacing: -0.03em;
            margin-bottom: 4px;
        }

        .status-number span {
            color: #8C0E03;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: #8C0E03;
            margin: 20px 0;
        }

        .title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff;
            margin-bottom: 12px;
        }

        .message {
            font-size: 13px;
            color: rgba(171,171,171,0.45);
            line-height: 1.8;
            font-family: 'JetBrains Mono', monospace;
        }

        .message strong {
            color: rgba(171,171,171,0.7);
            font-weight: 500;
        }

        .support-strip {
            margin-top: 28px;
            padding: 14px 16px;
            border: 1px solid rgba(140,14,3,0.18);
            background: rgba(140,14,3,0.04);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: rgba(171,171,171,0.3);
            line-height: 1.7;
        }

        .support-strip a {
            color: rgba(200,100,90,0.7);
            text-decoration: none;
            transition: color 0.2s;
        }

        .support-strip a:hover {
            color: rgba(220,120,110,1);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code-line">// HTTP 503 — SERVICE UNAVAILABLE</div>

        <div class="status-number">5<span>0</span>3</div>

        <div class="divider"></div>

        <div class="title">Account Disabled</div>

        <p class="message">
            This institution's account has been <strong>deactivated</strong>
            by the system administrator.<br><br>
            // Access to all services has been suspended.<br>
            // No data has been deleted.
        </p>

        <div class="support-strip">
            <span style="color:rgba(200,90,80,0.6);font-weight:700;">// contact:</span>
            If you believe this is an error, please reach out to
            <a href="mailto:support@ojtconnect.com">support@ojtconnect.com</a>
            to have your account reviewed.
        </div>
    </div>
</body>
</html>