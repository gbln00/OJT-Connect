@extends('layouts.coordinator-app')
@section('title', 'Evaluation Details')
@section('page-title', 'Evaluation Details')
@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Evaluations / Detail
    </span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--border2);">·</span>
    <a href="{{ route('coordinator.evaluations.index') }}"
       style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-decoration:none;"
       onmouseover="this.style.color='var(--crimson)'" onmouseout="this.style.color='var(--muted)'">
        ← Back
    </a>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start;" class="fade-up fade-up-1">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                        {{ strtoupper(substr($evaluation->student->name ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <div class="card-title">{{ $evaluation->student->name }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                            Evaluated by <strong style="color:var(--text2);">{{ $evaluation->supervisor->name ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
                <span class="status-pill {{ $evaluation->recommendation_class }}">{{ $evaluation->recommendation_label }}</span>
            </div>

            {{-- Rating grid --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid var(--border);">
                <div style="padding:20px;border-right:1px solid var(--border);text-align:center;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Attendance</div>
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">
                        {{ $evaluation->attendance_rating }}<span style="font-size:14px;color:var(--muted);">/5</span>
                    </div>
                </div>
                <div style="padding:20px;border-right:1px solid var(--border);text-align:center;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Performance</div>
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">
                        {{ $evaluation->performance_rating }}<span style="font-size:14px;color:var(--muted);">/5</span>
                    </div>
                </div>
                <div style="padding:20px;text-align:center;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Overall Grade</div>
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:{{ $evaluation->grade_color }};">
                        {{ number_format($evaluation->overall_grade, 2) }}
                    </div>
                </div>
            </div>

            {{-- Remarks --}}
            <div style="padding:20px;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Supervisor Remarks</div>
                <div style="padding:14px;background:var(--surface2);border-left:2px solid var(--crimson);font-size:13px;color:var(--text2);line-height:1.7;">
                    {{ $evaluation->remarks ?? 'No remarks provided.' }}
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actions</div>
            </div>
            <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
                <form method="POST" action="{{ route('coordinator.evaluations.complete', $evaluation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-approve" style="width:100%;justify-content:center;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Mark OJT Complete
                    </button>
                </form>
                <a href="{{ route('coordinator.evaluations.index') }}" class="btn btn-ghost" style="justify-content:center;">← Back to list</a>
            </div>
        </div>

        {{-- Meta --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Meta</div>
            </div>
            <div style="padding:4px 0;">
                @if($evaluation->submitted_at)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--text2);">Submitted</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $evaluation->submitted_at->format('M d, Y') }}</span>
                </div>
                @endif
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;">
                    <span style="font-size:12.5px;color:var(--text2);">Recommendation</span>
                    <span class="status-pill {{ $evaluation->recommendation_class }}">{{ $evaluation->recommendation_label }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection