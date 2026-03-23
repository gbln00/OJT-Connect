@extends('layouts.coordinator-app')
@section('title', 'Evaluations')
@section('page-title', 'Evaluations')

@section('content')

<div class="card">
    <div class="card-header">
        <div class="card-title">Supervisor Evaluations</div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Supervisor</th>
                    <th>Grade</th>
                    <th>Recommendation</th>
                    <th>Submitted</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($evaluations as $evaluation)
                <tr>
                    <td>
                        <div style="font-weight:600;">
                            {{ $evaluation->student->name ?? '—' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            {{ $evaluation->student->email ?? '' }}
                        </div>
                    </td>

                    <td>
                        {{ $evaluation->supervisor->name ?? '—' }}
                    </td>

                    <td style="font-weight:700; color: {{ $evaluation->grade_color }}">
                        {{ number_format($evaluation->overall_grade, 2) }}
                    </td>

                    <td>
                        <span style="
                            padding:4px 10px;
                            border-radius:20px;
                            font-size:11px;
                            font-weight:700;
                            background: var(--{{ $evaluation->recommendation_class }}-dim);
                            color: var(--{{ $evaluation->recommendation_class }});
                        ">
                            {{ $evaluation->recommendation_label }}
                        </span>
                    </td>

                    <td style="font-size:12px;color:var(--muted);">
                        {{ $evaluation->submitted_at?->format('M d, Y') ?? '—' }}
                    </td>

                    <td>
                        <a href="{{ route('coordinator.evaluations.show', $evaluation->id) }}"
                           style="font-size:12px;color:var(--teal);border:1px solid var(--teal);
                                  padding:4px 12px;border-radius:6px;text-decoration:none;">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                        No evaluations submitted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:16px;">
        {{ $evaluations->links() }}
    </div>
</div>

@endsection