@component('mail::message')
# Your OJT Evaluation Is Ready

Hi {{ $studentName }},

Your company supervisor at **{{ $companyName }}** has submitted your final OJT evaluation.

@component('mail::panel')
**Overall Grade:** {{ $overallGrade }}
**Recommendation:** {{ $recommendation }}
**Performance Rating:** {{ $ratingLabel }}
@endcomponent

@component('mail::button', ['url' => $dashboardUrl, 'color' => 'success'])
View My Evaluation
@endcomponent

Thanks,
{{ config('app.name') }}
@endcomponent