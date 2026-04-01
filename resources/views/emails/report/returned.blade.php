@component('mail::message')
# Your Week {{ $weekNumber }} Report Needs Revision

Hi {{ $studentName }},

Your **Week {{ $weekNumber }}** weekly report has been reviewed and returned for revision.

@if($feedback)
**Coordinator Feedback:**
{{ $feedback }}
@endif

@component('mail::button', ['url' => $reportsUrl, 'color' => 'primary'])
View My Reports
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent