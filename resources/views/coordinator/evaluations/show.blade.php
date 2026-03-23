@extends('layouts.coordinator-app')
@section('title', 'Evaluation Details')
@section('page-title', 'Evaluation Details')

@section('content')

<div class="card" style="padding:24px;">

    {{-- Header --}}
    <div style="margin-bottom:20px;">
        <div style="font-size:18px;font-weight:700;">
            {{ $evaluation->student->name }}
        </div>
        <div style="font-size:12px;color:var(--muted);">
            Evaluated by {{ $evaluation->supervisor->name ?? '—' }}
        </div>
    </div>

    {{-- Ratings --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">

        <div class="card" style="padding:16px;">
            <div style="font-size:12px;color:var(--muted);">Attendance</div>
            <div style="font-size:24px;font-weight:700;">
                {{ $evaluation->attendance_rating }}/5
            </div>
        </div>

        <div class="card" style="padding:16px;">
            <div style="font-size:12px;color:var(--muted);">Performance</div>
            <div style="font-size:24px;font-weight:700;">
                {{ $evaluation->performance_rating }}/5
            </div>
        </div>

        <div class="card" style="padding:16px;">
            <div style="font-size:12px;color:var(--muted);">Overall Grade</div>
            <div style="font-size:24px;font-weight:800;color: {{ $evaluation->grade_color }}">
                {{ number_format($evaluation->overall_grade, 2) }}
            </div>
        </div>

    </div>

    {{-- Recommendation --}}
    <div style="margin-bottom:20px;">
        <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Recommendation</div>
        <span style="
            padding:6px 14px;
            border-radius:20px;
            font-size:12px;
            font-weight:700;
            background: var(--{{ $evaluation->recommendation_class }}-dim);
            color: var(--{{ $evaluation->recommendation_class }});
        ">
            {{ $evaluation->recommendation_label }}
        </span>
    </div>

    {{-- Remarks --}}
    <div style="margin-bottom:24px;">
        <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Supervisor Remarks</div>
        <div style="background:var(--surface2);padding:16px;border-radius:8px;">
            {{ $evaluation->remarks ?? 'No remarks provided.' }}
        </div>
    </div>

    {{-- Actions --}}
    <div style="display:flex;gap:10px;">

        {{-- Mark complete --}}
        <form method="POST" action="{{ route('coordinator.evaluations.complete', $evaluation->id) }}">
            @csrf
            <button type="submit"
                style="background:var(--teal);color:white;padding:10px 16px;
                       border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                Mark OJT Complete
            </button>
        </form>

        <a href="{{ route('coordinator.evaluations.index') }}"
           style="padding:10px 16px;border-radius:8px;border:1px solid var(--border2);
                  text-decoration:none;color:var(--text);">
            Back
        </a>

    </div>

</div>

@endsection