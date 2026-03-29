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
