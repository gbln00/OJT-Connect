@component('mail::message')
# Your OJT Application Has Been Approved


Hi {{ $studentName }},


Great news! Your OJT application has been reviewed and **approved**.


**Company:** {{ $companyName }}
**Program:** {{ $program }}
**Semester:** {{ $semester }}
**Required Hours:** {{ $requiredHours }} hours


@if($remarks)
**Coordinator Remarks:**
{{ $remarks }}
@endif


You can now start logging your daily hours from your student dashboard.


@component('mail::button', ['url' => $dashboardUrl, 'color' => 'success'])
Go to My Dashboard
@endcomponent


If you have questions, please contact your OJT coordinator.


Thanks,
{{ config('app.name') }}
@endcomponent

