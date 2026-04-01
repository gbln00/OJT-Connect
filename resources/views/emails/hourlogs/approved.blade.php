@component('mail::message')
# Hour Logs Approved

Hi {{ $studentName }},

Your supervisor has approved **{{ $approvedCount }} hour log(s)**.

@component('mail::panel')
**Total Approved Hours So Far:** {{ $totalApprovedHours }} hours
@endcomponent

@component('mail::button', ['url' => $logsUrl, 'color' => 'primary'])
View My Hour Logs
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent