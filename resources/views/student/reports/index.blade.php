{{-- resources/views/student/reports/index.blade.php --}}
@extends('layouts.student-app')
@section('title', 'Weekly Reports')
@section('page-title', 'Weekly Reports')

@section('content')

{{-- TOOLBAR --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;" class="fade-up">
    <div>
        <div style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--text);">{{ $application->company_name }}</div>
        <div class="form-hint" style="margin-top:4px;">
            Internship Period:
            {{ $application->start_date?->format('M d, Y') ?? 'N/A' }}
            –
            {{ $application->end_date?->format('M d, Y') ?? 'N/A' }}
        </div>
    </div>
    <a href="{{ route('student.reports.create') }}" class="btn btn-primary btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Report
    </a>
</div>

{{-- PROGRESS CARD --}}
<div class="card fade-up fade-up-1" style="margin-bottom:16px;padding:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Report Progress</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
            <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ $totalReports }}</span>
            of
            <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ $application->total_weeks }}</span>
            weeks
        </div>
    </div>
    @php $pct = $application->total_weeks > 0 ? min(100, round(($totalReports / $application->total_weeks) * 100)) : 0; @endphp
    <div class="progress-track" style="margin-bottom:12px;"><div class="progress-fill teal" style="width:{{ $pct }}%;"></div></div>
    <div style="display:flex;gap:20px;">
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);">
            <span style="width:7px;height:7px;background:var(--teal-color);display:inline-block;"></span>
            Approved <strong style="color:var(--text);margin-left:4px;">{{ $approvedReports }}</strong>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);">
            <span style="width:7px;height:7px;background:var(--gold-color);display:inline-block;"></span>
            Pending <strong style="color:var(--text);margin-left:4px;">{{ $pendingReports }}</strong>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);">
            <span style="width:7px;height:7px;background:var(--crimson);display:inline-block;"></span>
            Rejected <strong style="color:var(--text);margin-left:4px;">{{ $rejectedReports }}</strong>
        </div>
    </div>
</div>

{{-- REPORT CARDS --}}
@forelse($reports as $report)
<div class="card fade-up fade-up-2" style="margin-bottom:10px;">
    <div style="padding:18px 20px;display:flex;justify-content:space-between;align-items:flex-start;gap:16px;">

        <div style="display:flex;gap:16px;align-items:flex-start;">
            {{-- Week badge --}}
            <div style="min-width:52px;text-align:center;padding:10px 8px;background:var(--surface2);border:1px solid var(--border2);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">WK</div>
                <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--teal-color);line-height:1;">{{ $report->week_number }}</div>
            </div>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">
                    {{ $report->week_start->format('M d') }} – {{ $report->week_end->format('M d, Y') }}
                </div>
                <div style="font-size:12.5px;color:var(--muted2);line-height:1.5;max-width:460px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $report->description }}
                </div>
                @if($report->supervisor_remarks)
                <div style="margin-top:8px;padding:8px 12px;background:var(--surface2);border-left:2px solid var(--blue-color);font-size:12px;color:var(--muted);">
                    <span style="font-weight:600;color:var(--blue-color);">Supervisor: </span>{{ $report->supervisor_remarks }}
                </div>
                @endif
            </div>
        </div>

        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0;">
            @php
                $rCls = match($report->status ?? 'pending') {
                    'approved' => 'green', 'returned' => 'crimson', default => 'gold'
                };
            @endphp
            <span class="status-pill {{ $rCls }}">{{ ucfirst($report->status ?? 'pending') }}</span>
            <div style="display:flex;gap:6px;align-items:center;">
                @if($report->file_path)
                <a href="{{ Storage::url($report->file_path) }}" target="_blank" class="btn btn-ghost btn-sm" title="View attachment">📎</a>
                @endif
                @if($report->status === 'pending')
                <a href="{{ route('student.reports.edit', $report->id) }}" class="btn btn-ghost btn-sm">Edit</a>
                @endif
                <a href="{{ route('student.reports.show', $report->id) }}" class="btn btn-ghost btn-sm" style="color:var(--crimson);border-color:rgba(140,14,3,0.3);">View</a>
            </div>
        </div>

    </div>
</div>
@empty
<div class="card fade-up fade-up-2" style="padding:60px;text-align:center;">
    <div style="font-family:'Playfair Display',serif;font-size:28px;color:var(--muted);margin-bottom:12px;">📋</div>
    <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;">No reports submitted yet</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:20px;">Start documenting your internship experience week by week.</div>
    <a href="{{ route('student.reports.create') }}" class="btn btn-primary btn-sm">Submit First Report</a>
</div>
@endforelse

{{ $reports->links() }}

@endsection