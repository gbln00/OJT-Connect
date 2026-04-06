@component('mail::message')
# Weekly Report Approved

Hi {{ $studentName }},

Your **Week {{ $weekNumber }}** report has been approved.

@if($feedback)
**Feedback from your coordinator:**
{{ $feedback }}
@endif

@component('mail::button', ['url' => $reportsUrl])
View Your Reports
@endcomponent

Keep up the good work!

**OJTConnect**
@endcomponent