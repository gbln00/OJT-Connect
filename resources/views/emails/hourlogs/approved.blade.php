@component('mail::message')
# Your OJT Evaluation Is Ready


Hi {{ $studentName }},


Your company supervisor at **{{ $companyName }}** has submitted
your final OJT evaluation.


@component('mail::panel')
**Overall Grade:** {{ $overallGrade }}
**Recommendation:** {{ $recommendation }}
**Performance Rating:** {{ $ratingLabel }}
@endcomponent


Log in to view your full evaluation details.


@component('mail::button', ['url' => $dashboardUrl, 'color' => 'success'])
View My Evaluation
@endcomponent


Congratulations on completing your OJT!


Thanks,
{{ config('app.name') }}
@endcomponent

