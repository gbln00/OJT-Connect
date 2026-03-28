<!DOCTYPE html>
<html>
<body>
    <h2>Hello, {{ $registration->company_name }}</h2>

    <p>Unfortunately, your registration has not been approved at this time.</p>

    @if($registration->rejection_reason)
        <p><strong>Reason:</strong> {{ $registration->rejection_reason }}</p>
    @endif

    <p>Please contact support if you believe this is an error.</p>

    <p>— The {{ config('app.name') }} Team</p>
</body>
</html>