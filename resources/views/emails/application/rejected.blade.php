@component('mail::message')
# Update on Your OJT Application


Hi {{ $studentName }},


Thank you for submitting your OJT application for **{{ $companyName }}**.


After review, your application was not approved at this time.


@if($remarks)
**Reason / Remarks:**
{{ $remarks }}
@endif


You are welcome to revise and resubmit a new application.


@component('mail::button', ['url' => $applyUrl, 'color' => 'primary'])
Apply Again
@endcomponent


If you have questions, please reach out to your OJT coordinator.


Thanks,
{{ config('app.name') }}
@endcomponent

