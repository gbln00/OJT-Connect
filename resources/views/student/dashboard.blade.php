@extends('layouts.student-app')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Greeting --}}
<div class="greeting" style="margin-bottom:24px;">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
</div>

{{-- ── TOP STAT CARDS ── --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">

    {{-- Application Status --}}
    <div class="card" style="padding:20px;">
        <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">
            Application
        </div>
        @if($application)
            @php
                $statusColor = match($application->status) {
                    'approved' => 'var(--color-text-success)',
                    'rejected' => 'var(--coral)',
                    default    => 'var(--color-text-warning)',
                };
                $statusBg = match($application->status) {
                    'approved' => 'var(--color-background-success)',
                    'rejected' => 'var(--coral-dim)',
                    default    => 'var(--color-background-warning)',
                };
            @endphp
            <div style="font-size:22px;font-weight:700;color:var(--text);margin-bottom:6px;">
                {{ $application->company->name }}
            </div>
            <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:10px;background:{{ $statusBg }};color:{{ $statusColor }};">
                {{ $application->status_label }}
            </span>
            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                {{ $application->program }} · {{ $application->semester }} {{ $application->school_year }}
            </div>
        @else
            <div style="font-size:15px;font-weight:600;color:var(--muted);margin-bottom:8px;">No application yet</div>
            <a href="{{ route('student.application.create') }}"
               style="font-size:12px;font-weight:500;color:var(--gold);text-decoration:none;">
                Apply now →
            </a>
        @endif
    </div>

    {{-- Hours Progress --}}
    <div class="card" style="padding:20px;">
        <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">
            OJT Hours
        </div>
        <div style="display:flex;align-items:baseline;gap:6px;margin-bottom:10px;">
            <div style="font-size:28px;font-weight:700;color:var(--text);line-height:1;">
                {{ number_format($totalLogged, 1) }}
            </div>
            <div style="font-size:13px;color:var(--muted);">/ {{ $requiredHours }} hrs</div>
        </div>
        {{-- Progress bar --}}
        <div style="height:6px;background:var(--border2);border-radius:4px;overflow:hidden;margin-bottom:6px;">
            <div style="height:100%;width:{{ $progressPct }}%;background:var(--color-text-success);border-radius:4px;transition:width 0.8s ease;"></div>
        </div>
        <div style="font-size:11px;color:var(--muted);">
            {{ $progressPct }}% complete
            @if($application && $application->isApproved())
                · {{ number_format($application->remaining_hours, 1) }} hrs remaining
            @endif
        </div>
    </div>

    {{-- Student Info --}}
    <div class="card" style="padding:20px;">
        <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:12px;">
            Student Info
        </div>
        @if($profile)
            <div style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:8px;">
                {{ $profile->student_id }}
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;">
                <div style="font-size:12px;color:var(--muted2);">
                    <span style="color:var(--muted);">Course</span> · {{ $profile->course }}
                </div>
                <div style="font-size:12px;color:var(--muted2);">
                    <span style="color:var(--muted);">Year & Section</span> · {{ $profile->year_level }} — {{ $profile->section }}
                </div>
            </div>
        @else
            <div style="font-size:13px;color:var(--muted);">No profile found.</div>
        @endif
    </div>

</div>

{{-- ── BOTTOM ROW ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">

    {{-- Recent Hour Logs --}}
    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <div class="card-title">Recent Hour Logs</div>
            @if($application && $application->isApproved())
                <a href="{{ route('student.hours.index') }}"
                   style="font-size:12px;color:var(--gold);text-decoration:none;">
                    View all →
                </a>
            @endif
        </div>

        <div style="padding:0 16px 16px;">
            @if($recentLogs->isEmpty())
                <div style="padding:24px 0;text-align:center;color:var(--muted);font-size:13px;">
                    @if(!$application)
                        No application submitted yet.
                    @elseif($application->isPending())
                        Waiting for application approval before logging hours.
                    @elseif($application->isRejected())
                        Application was rejected. Please re-apply.
                    @else
                        No hour logs yet.
                        <a href="{{ route('student.hours.index') }}"
                           style="display:block;margin-top:8px;color:var(--gold);font-size:12px;font-weight:500;text-decoration:none;">
                            Log your first hours →
                        </a>
                    @endif
                </div>
            @else
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border2);">
                            <th style="text-align:left;padding:8px 0;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);">Date</th>
                            <th style="text-align:left;padding:8px 0;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);">Hours</th>
                            <th style="text-align:left;padding:8px 0;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                        <tr style="border-bottom:1px solid var(--border2);">
                            <td style="padding:10px 0;color:var(--text);">
                                {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                            </td>
                            <td style="padding:10px 0;color:var(--text);font-weight:500;">
                                {{ $log->total_hours }} hrs
                            </td>
                            <td style="padding:10px 0;">
                                @php
                                    $logColor = match($log->status ?? 'pending') {
                                        'approved' => ['bg' => 'var(--color-background-success)', 'text' => 'var(--color-text-success)'],
                                        'rejected' => ['bg' => 'var(--coral-dim)',                'text' => 'var(--coral)'],
                                        default    => ['bg' => 'var(--color-background-warning)', 'text' => 'var(--color-text-warning)'],
                                    };
                                @endphp
                                <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:{{ $logColor['bg'] }};color:{{ $logColor['text'] }};">
                                    {{ ucfirst($log->status ?? 'pending') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Recent Weekly Reports --}}
    <div class="card">
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <div class="card-title">Weekly Reports</div>
            @if($application && $application->isApproved())
                <a href="{{ route('student.reports.index') }}"
                   style="font-size:12px;color:var(--gold);text-decoration:none;">
                    View all →
                </a>
            @endif
        </div>

        <div style="padding:0 16px 16px;">
            @if($recentReports->isEmpty())
                <div style="padding:24px 0;text-align:center;color:var(--muted);font-size:13px;">
                    @if(!$application)
                        No application submitted yet.
                    @elseif($application->isPending())
                        Waiting for application approval.
                    @elseif($application->isRejected())
                        Application was rejected. Please re-apply.
                    @else
                        No reports submitted yet.
                        <a href="{{ route('student.reports.index') }}"
                           style="display:block;margin-top:8px;color:var(--gold);font-size:12px;font-weight:500;text-decoration:none;">
                            Submit your first report →
                        </a>
                    @endif
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:8px;">
                    @foreach($recentReports as $report)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border2);">
                        <div>
                            <div style="font-size:13px;font-weight:500;color:var(--text);">
                                Week {{ $report->week_number }}
                            </div>
                            <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                                {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}
                            </div>
                        </div>
                        @php
                            $rColor = match($report->status ?? 'pending') {
                                'approved' => ['bg' => 'var(--color-background-success)', 'text' => 'var(--color-text-success)'],
                                'returned' => ['bg' => 'var(--coral-dim)',                'text' => 'var(--coral)'],
                                default    => ['bg' => 'var(--color-background-warning)', 'text' => 'var(--color-text-warning)'],
                            };
                        @endphp
                        <span style="font-size:10px;font-weight:600;padding:2px 8px;border-radius:10px;background:{{ $rColor['bg'] }};color:{{ $rColor['text'] }};">
                            {{ ucfirst($report->status ?? 'pending') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── APPLICATION STATUS BANNER (if pending/rejected) ── --}}
@if($application && $application->isPending())
    <div style="margin-top:14px;background:var(--color-background-warning);border:1px solid var(--color-text-warning);border-radius:8px;padding:14px 18px;display:flex;align-items:center;gap:12px;font-size:13px;color:var(--color-text-warning);">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div>
            <span style="font-weight:600;">Application pending review.</span>
            Your application to <strong>{{ $application->company->name }}</strong> is waiting for coordinator approval.
            Hour logging will be unlocked once approved.
        </div>
    </div>
@endif

@if($application && $application->isRejected())
    <div style="margin-top:14px;background:var(--coral-dim);border:1px solid var(--coral);border-radius:8px;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:13px;color:var(--coral);">
        <div style="display:flex;align-items:center;gap:12px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <div>
                <span style="font-weight:600;">Application rejected.</span>
                @if($application->remarks)
                    Reason: {{ $application->remarks }}
                @endif
            </div>
        </div>
        <a href="{{ route('student.application.create') }}"
           style="white-space:nowrap;font-size:12px;font-weight:600;padding:6px 14px;background:var(--coral);color:#fff;border-radius:6px;text-decoration:none;">
            Re-apply
        </a>
    </div>
@endif

@endsection