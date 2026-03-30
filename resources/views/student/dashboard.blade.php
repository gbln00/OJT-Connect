@extends('layouts.student-app')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- GREETING --}}
<div class="greeting fade-up">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);">

    {{-- Application Status --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon {{ $application ? ($application->status === 'approved' ? 'teal' : ($application->status === 'rejected' ? 'coral' : 'gold')) : 'steel' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <span class="stat-tag">application</span>
        </div>
        @if($application)
            <div class="stat-num" style="font-size:16px;margin-bottom:6px;">{{ $application->company->name }}</div>
            @php
                $sCls = match($application->status) {
                    'approved' => 'green', 'rejected' => 'crimson', default => 'gold'
                };
            @endphp
            <span class="status-pill {{ $sCls }}">{{ $application->status_label }}</span>
        @else
            <div class="stat-num" style="font-size:16px;color:var(--muted);">No application</div>
            <a href="{{ route('student.application.create') }}" style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--crimson);text-decoration:none;">Apply now →</a>
        @endif
    </div>

    {{-- Hours Progress --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <span class="stat-tag">hours</span>
        </div>
        <div class="stat-num">{{ number_format($totalLogged, 1) }}</div>
        <div class="stat-label" style="margin-bottom:10px;">of {{ $requiredHours }} hours</div>
        <div class="progress-track">
            <div class="progress-fill blue" style="width:{{ $progressPct }}%;"></div>
        </div>
        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:6px;">{{ $progressPct }}% complete</div>
    </div>

    {{-- Student Info --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <span class="stat-tag">student</span>
        </div>
        @if($profile)
            <div class="stat-num" style="font-size:18px;">{{ $profile->student_id }}</div>
            <div class="stat-label" style="margin-bottom:4px;">{{ $profile->course }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $profile->year_level }} — {{ $profile->section }}</div>
        @else
            <div class="stat-num" style="font-size:16px;color:var(--muted);">No profile</div>
            <div class="stat-label">Contact your coordinator</div>
        @endif
    </div>

</div>

{{-- BOTTOM GRID --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;" class="fade-up fade-up-2">

    {{-- RECENT HOUR LOGS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Hour Logs</div>
            @if($application && $application->isApproved())
                <a href="{{ route('student.hours.index') }}" class="card-action">View all →</a>
            @endif
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}</td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                        <td style="font-weight:600;color:var(--blue-color);">{{ $log->total_hours }} hrs</td>
                        <td>
                            @php
                                $cls = match($log->status ?? 'pending') {
                                    'approved' => 'green', 'rejected' => 'crimson', default => 'gold'
                                };
                            @endphp
                            <span class="status-pill {{ $cls }}">{{ ucfirst($log->status ?? 'pending') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:36px;color:var(--muted);">
                            @if(!$application)
                                No application submitted yet.
                            @elseif($application->isPending())
                                Waiting for application approval.
                            @elseif($application->isRejected())
                                Application was rejected.
                            @else
                                No hour logs yet.
                                <a href="{{ route('student.hours.create') }}" style="color:var(--crimson);text-decoration:none;font-weight:500;">Log hours →</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- QUICK ACTIONS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick actions</div>
            </div>
            <div class="quick-actions">
                <a href="{{ route('student.application.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                    </div>
                    <span class="qa-label">Application</span>
                </a>
                <a href="{{ route('student.hours.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                    </div>
                    <span class="qa-label">Log Hours</span>
                </a>
                <a href="{{ route('student.reports.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                        </svg>
                    </div>
                    <span class="qa-label">New Report</span>
                </a>
                <a href="{{ route('student.evaluation.show') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                            <polyline points="22,4 12,14.01 9,11.01"/>
                        </svg>
                    </div>
                    <span class="qa-label">Evaluation</span>
                </a>
            </div>
        </div>

        {{-- RECENT WEEKLY REPORTS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Weekly reports</div>
                @if($application && $application->isApproved())
                    <a href="{{ route('student.reports.index') }}" class="card-action">View all →</a>
                @endif
            </div>
            <div class="activity-list">
                @forelse($recentReports as $report)
                <div class="activity-item">
                    @php
                        $rDot = match($report->status ?? 'pending') {
                            'approved' => 'green', 'returned' => 'crimson', default => 'gold'
                        };
                    @endphp
                    <div class="activity-dot {{ $rDot }}" style="margin-top:5px;"></div>
                    <div>
                        <div class="activity-text">Week {{ $report->week_number }}</div>
                        <div class="activity-time">{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }} · {{ ucfirst($report->status ?? 'pending') }}</div>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <div class="activity-dot steel" style="margin-top:5px;"></div>
                    <div>
                        <div class="activity-text" style="color:var(--muted);">No reports yet.</div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- APPLICATION STATUS BANNERS --}}
@if($application && $application->isPending())
<div class="alert warning fade-up fade-up-3" style="margin-top:16px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>Application pending review.</strong>
        Your application to <strong>{{ $application->company->name }}</strong> is awaiting coordinator approval. Hour logging will be unlocked once approved.
    </div>
</div>
@endif

@if($application && $application->isRejected())
<div style="margin-top:16px;display:flex;align-items:center;justify-content:space-between;gap:16px;" class="alert error fade-up fade-up-3">
    <div style="display:flex;align-items:center;gap:12px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <div>
            <strong>Application rejected.</strong>
            @if($application->remarks) Reason: {{ $application->remarks }} @endif
        </div>
    </div>
    <a href="{{ route('student.application.create') }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">Re-apply</a>
</div>
@endif

@endsection