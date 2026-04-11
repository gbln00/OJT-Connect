@component('mail::message')
# Hi {{ $studentName }},

This is a reminder to log your OJT hours for today.

**Your progress so far:** {{ $totalApprovedHours }} / {{ $requiredHours }} hours approved.

@component('mail::button', ['url' => $logsUrl])
Log My Hours
@endcomponent

Keep up the great work!

Thanks,
{{ config('app.name') }}
@endcomponent