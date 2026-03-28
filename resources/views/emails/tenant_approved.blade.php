<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <h2>Welcome, {{ $registration->company_name }}!</h2>

    <p>Your tenant account has been approved. You can now log in at:</p>

    <p>
        <a href="https://{{ $registration->subdomain }}.{{ config('app.base_domain', 'yourapp.com') }}">
            {{ $registration->subdomain }}.{{ config('app.base_domain', 'yourapp.com') }}
        </a>
    </p>

    <p>If you have any questions, feel free to reach out to support.</p>

    <p>— The {{ config('app.name') }} Team</p>
</body>
</html>