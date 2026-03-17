@extends('layouts.app')
@section('title', 'Evaluation Detail')
@section('page-title', 'Evaluations')

@section('content')

{{-- BACK + RESULT --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <a href="{{ route('admin.evaluations.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
        Back to evaluations
    </a>
    <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:500;
        background:var(--{{ $evaluation->recommendation_class }}-dim);color:var(--{{ $evaluation->recommendation_class }});">
        {{ $evaluation->recommendation_label }}
    </span>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Student information</div>
            </div>
            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Full name</div>
                    <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $evaluation->student->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Email</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $evaluation->student->email }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Company</div>
                    <div style="font-size:13px;color:var(--text);">{{ $evaluation->application->company->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Program</div>
                    <div style="font-size:13px;color:var(--text);">{{ $evaluation->application->program }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Evaluated by</div>
                    <div style="font-size:13px;color:var(--text);">{{ $evaluation->supervisor->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Submitted</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $evaluation->submitted_at?->format('M d, Y') ?? $evaluation->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Ratings --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Performance ratings</div>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:20px;">

                {{-- Attendance --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:500;color:var(--text);">Attendance</span>
                        <span style="font-size:13px;color:var(--gold);font-weight:600;">{{ $evaluation->attendance_rating }} / 5</span>
                    </div>
                    <div style="display:flex;gap:8px;">
                        @for($i = 1; $i <= 5; $i++)
                        <div style="flex:1;height:10px;border-radius:4px;background:{{ $i <= $evaluation->attendance_rating ? 'var(--gold)' : 'var(--border2)' }};"></div>
                        @endfor
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:4px;">
                        <span style="font-size:10px;color:var(--muted);">Poor</span>
                        <span style="font-size:10px;color:var(--muted);">Excellent</span>
                    </div>
                </div>

                {{-- Performance --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <span style="font-size:13px;font-weight:500;color:var(--text);">Performance</span>
                        <span style="font-size:13px;color:var(--blue);font-weight:600;">{{ $evaluation->performance_rating }} / 5</span>
                    </div>
                    <div style="display:flex;gap:8px;">
                        @for($i = 1; $i <= 5; $i++)
                        <div style="flex:1;height:10px;border-radius:4px;background:{{ $i <= $evaluation->performance_rating ? 'var(--blue)' : 'var(--border2)' }};"></div>
                        @endfor
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-top:4px;">
                        <span style="font-size:10px;color:var(--muted);">Poor</span>
                        <span style="font-size:10px;color:var(--muted);">Excellent</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Remarks --}}
        @if($evaluation->remarks)
        <div class="card">
            <div class="card-header">
                <div class="card-title">Supervisor remarks</div>
            </div>
            <div style="padding:20px;font-size:13.5px;color:var(--text);line-height:1.7;white-space:pre-wrap;">{{ $evaluation->remarks }}</div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Grade card --}}
        <div class="card">
            <div style="padding:28px 20px;text-align:center;">
                <div style="font-size:12px;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.08em;">Overall grade</div>
                <div style="font-size:52px;font-weight:700;color:{{ $evaluation->grade_color }};line-height:1;margin-bottom:8px;">
                    {{ number_format($evaluation->overall_grade, 1) }}
                </div>
                <div style="font-size:13px;color:var(--muted2);margin-bottom:16px;">{{ $evaluation->rating_label }}</div>
                <span style="display:inline-flex;align-items:center;padding:6px 18px;border-radius:20px;font-size:13px;font-weight:500;
                    background:var(--{{ $evaluation->recommendation_class }}-dim);color:var(--{{ $evaluation->recommendation_class }});">
                    {{ $evaluation->recommendation_label }}
                </span>
            </div>
        </div>

        {{-- Summary --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Summary</div>
            </div>
            <div style="padding:4px 0;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Attendance</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--gold);">{{ $evaluation->attendance_rating }} / 5</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Performance</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--blue);">{{ $evaluation->performance_rating }} / 5</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Overall grade</span>
                    <span style="font-size:12.5px;font-weight:500;color:{{ $evaluation->grade_color }};">{{ number_format($evaluation->overall_grade, 1) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;">
                    <span style="font-size:12.5px;color:var(--muted2);">Recommendation</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--{{ $evaluation->recommendation_class }});">{{ $evaluation->recommendation_label }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection