@extends('layouts.student-app')
@section('title', 'Weekly Reports')
@section('page-title', 'Weekly Reports')
@section('content')

{{-- Header row --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
    <div>
        <div style="font-size:13px;color:var(--muted);">
            {{ $application->company_name }}
        </div>

        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
            Internship Period:
            {{ $application->start_date?->format('M d, Y') ?? 'N/A' }}
            –
            {{ $application->end_date?->format('M d, Y') ?? 'N/A' }}
        </div>
    </div>
    <a href="{{ route('student.reports.create') }}"
       style="padding:9px 20px;background:var(--gold);color:var(--bg);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;">
        + New Report
    </a>
</div>

{{-- Progress bar card --}}
<div class="card" style="padding:20px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-size:12px;font-weight:600;color:var(--muted);letter-spacing:.5px;">WEEKLY REPORT PROGRESS</div>
        <div style="font-size:12px;color:var(--muted);">
            <span style="font-weight:700;color:var(--text);">{{ $totalReports }}</span> of
            <span style="font-weight:700;color:var(--text);">{{ $application->total_weeks }}</span> weeks submitted
        </div>
    </div>
    @php $pct = $application->total_weeks > 0 ? min(100, round(($totalReports / $application->total_weeks) * 100)) : 0; @endphp
    <div style="height:8px;background:var(--border2);border-radius:6px;">
        <div style="height:100%;width:{{ $pct }}%;background:var(--teal);border-radius:6px;transition:width .4s;"></div>
    </div>
    <div style="display:flex;gap:24px;margin-top:14px;">
        <div style="font-size:12px;color:var(--muted);">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--teal);margin-right:5px;"></span>
            Approved &nbsp;<strong style="color:var(--text);">{{ $approvedReports }}</strong>
        </div>
        <div style="font-size:12px;color:var(--muted);">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--gold);margin-right:5px;"></span>
            Pending &nbsp;<strong style="color:var(--text);">{{ $pendingReports }}</strong>
        </div>
        <div style="font-size:12px;color:var(--muted);">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--red);margin-right:5px;"></span>
            Rejected &nbsp;<strong style="color:var(--text);">{{ $rejectedReports }}</strong>
        </div>
    </div>
</div>

{{-- Report cards --}}
@forelse($reports as $report)
<div class="card" style="padding:20px;margin-bottom:12px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;">

        {{-- Left: week badge + meta --}}
        <div style="display:flex;gap:16px;align-items:flex-start;">
            <div style="min-width:52px;text-align:center;padding:10px 8px;background:var(--surface2);border-radius:10px;border:1px solid var(--border2);">
                <div style="font-size:10px;color:var(--muted);letter-spacing:.5px;">WK</div>
                <div style="font-size:22px;font-weight:800;color:var(--teal);line-height:1;">{{ $report->week_number }}</div>
            </div>
            <div>
                <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">
                    {{ $report->week_start->format('M d') }} – {{ $report->week_end->format('M d, Y') }}
                </div>
                <div style="font-size:12px;color:var(--muted);line-height:1.5;max-width:480px;
                            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $report->description }}
                </div>
                @if($report->supervisor_remarks)
                <div style="margin-top:8px;padding:8px 12px;background:var(--surface2);border-left:3px solid var(--blue);border-radius:0 6px 6px 0;font-size:12px;color:var(--muted);">
                    <span style="font-weight:600;color:var(--blue);">Supervisor: </span>{{ $report->supervisor_remarks }}
                </div>
                @endif
            </div>
        </div>

        {{-- Right: status + actions --}}
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;flex-shrink:0;">
            <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                background:var(--{{ $report->status_class }}-dim);color:var(--{{ $report->status_class }});">
                {{ ucfirst($report->status) }}
            </span>
            <div style="display:flex;gap:10px;align-items:center;">
                @if($report->file_path)
                <a href="{{ Storage::url($report->file_path) }}" target="_blank"
                   style="font-size:12px;color:var(--muted);text-decoration:none;" title="View attachment">
                    📎
                </a>
                @endif
                @if($report->status === 'pending')
                <a href="{{ route('student.reports.edit', $report->id) }}"
                   style="font-size:12px;color:var(--muted);text-decoration:none;border:1px solid var(--border2);
                          padding:4px 12px;border-radius:6px;">Edit</a>
                @endif
                <a href="{{ route('student.reports.show', $report->id) }}"
                   style="font-size:12px;color:var(--teal);text-decoration:none;border:1px solid var(--teal);
                          padding:4px 12px;border-radius:6px;font-weight:600;">View</a>
            </div>
        </div>

    </div>
</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">📋</div>
    <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px;">No reports submitted yet</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:20px;">Start documenting your internship experience week by week.</div>
    <a href="{{ route('student.reports.create') }}"
       style="padding:10px 24px;background:var(--gold);color:var(--bg);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
        Submit First Report
    </a>
</div>
@endforelse

{{ $reports->links() }}

@endsection