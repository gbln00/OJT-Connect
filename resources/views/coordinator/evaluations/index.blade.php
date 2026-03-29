{{-- ═══════════════════════════════════════════════════════════════════
     COORDINATOR / evaluations / index.blade.php
═══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.coordinator-app')
@section('title', 'Evaluations')
@section('page-title', 'Evaluations')
@section('content')

<div class="card fade-up">
    <div class="card-header">
        <div class="card-title">Supervisor evaluations</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">// final intern assessments</span>
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
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                            {{ strtoupper(substr($evaluation->student->name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:500;color:var(--text);">{{ $evaluation->student->name ?? '—' }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $evaluation->student->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px;">{{ $evaluation->supervisor->name ?? '—' }}</td>
                <td>
                    <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:{{ $evaluation->grade_color }};">
                        {{ number_format($evaluation->overall_grade, 2) }}
                    </span>
                </td>
                <td>
                    <span class="status-pill {{ $evaluation->recommendation_class }}">{{ $evaluation->recommendation_label }}</span>
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                    {{ $evaluation->submitted_at?->format('M d, Y') ?? '—' }}
                </td>
                <td>
                    <a href="{{ route('coordinator.evaluations.show', $evaluation->id) }}" class="btn btn-ghost btn-sm">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                    No evaluations submitted yet.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($evaluations->hasPages())
    <div class="pagination">
        <span class="pagination-info">Showing {{ $evaluations->firstItem() }}–{{ $evaluations->lastItem() }} of {{ $evaluations->total() }}</span>
        <div style="display:flex;gap:4px;">
            @if($evaluations->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $evaluations->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($evaluations->hasMorePages())
                <a href="{{ $evaluations->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection


{{-- ═══════════════════════════════════════════════════════════════════
     COORDINATOR / evaluations / show.blade.php
     Save this section as a SEPARATE file: evaluations/show.blade.php
═══════════════════════════════════════════════════════════════════ --}}
{{--
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

        {{-- Header card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                        {{ strtoupper(substr($evaluation->student->name ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <div class="card-title">{{ $evaluation->student->name }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">Evaluated by {{ $evaluation->supervisor->name ?? '—' }}</div>
                    </div>
                </div>
                <span class="status-pill {{ $evaluation->recommendation_class }}">{{ $evaluation->recommendation_label }}</span>
            </div>

            {{-- Rating grid --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;border-bottom:1px solid var(--border);">
                @foreach([['Attendance', $evaluation->attendance_rating], ['Performance', $evaluation->performance_rating], ['Overall Grade', null]] as [$lbl, $val])
                <div style="padding:20px;{{ !$loop->last ? 'border-right:1px solid var(--border);' : '' }}text-align:center;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">{{ $lbl }}</div>
                    @if($lbl === 'Overall Grade')
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:{{ $evaluation->grade_color }};">{{ number_format($evaluation->overall_grade, 2) }}</div>
                    @else
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">{{ $val }}<span style="font-size:14px;color:var(--muted);">/5</span></div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Remarks --}}
            <div style="padding:20px;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Supervisor Remarks</div>
                <div style="padding:14px;background:var(--surface2);border-left:2px solid var(--crimson);font-size:13px;color:var(--text2);line-height:1.6;">
                    {{ $evaluation->remarks ?? 'No remarks provided.' }}
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actions</div>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:10px;">
                <form method="POST" action="{{ route('coordinator.evaluations.complete', $evaluation->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-approve" style="width:100%;justify-content:center;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Mark OJT Complete
                    </button>
                </form>
                <a href="{{ route('coordinator.evaluations.index') }}" class="btn btn-ghost" style="justify-content:center;">← Back to list</a>
            </div>
        </div>
    </div>

</div>

@endsection
--}}