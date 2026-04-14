@extends('layouts.student-app')
@section('title', 'Student Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    /* ── Unified color tokens matching super admin ── */
    .stat-icon.crimson { border-color: rgba(140,14,3,0.35);   background: rgba(140,14,3,0.08);    color: var(--crimson); }
    .stat-icon.steel   { border-color: rgba(171,171,171,0.2); background: rgba(171,171,171,0.06); color: var(--ash); }
    .stat-icon.gold    { border-color: rgba(201,168,76,0.25); background: rgba(201,168,76,0.08);  color: #c9a84c; }
    .stat-icon.teal    { border-color: rgba(45,212,191,0.2);  background: rgba(45,212,191,0.06);  color: #2dd4bf; }
    .stat-icon.blue    { border-color: rgba(96,165,250,0.2);  background: rgba(96,165,250,0.06);  color: #60a5fa; }
    .stat-icon.coral   { border-color: rgba(248,113,113,0.2); background: rgba(248,113,113,0.06); color: #f87171; }

    .status-pill.gold    { background: rgba(201,168,76,0.1);   color: #c9a84c; border:1px solid rgba(201,168,76,0.25); }
    .status-pill.green   { background: rgba(52,211,153,0.08);  color: #34d399; border:1px solid rgba(52,211,153,0.2); }
    .status-pill.crimson { background: rgba(140,14,3,0.1);     color: #c0392b; border:1px solid rgba(140,14,3,0.2); }
    .status-pill.blue    { background: rgba(96,165,250,0.08);  color: #60a5fa; border:1px solid rgba(96,165,250,0.2); }
    .status-pill.steel   { background: rgba(171,171,171,0.07); color: var(--ash); border:1px solid var(--border2); }

    [data-theme="light"] .status-pill.green   { color: #0f9660; background: rgba(16,155,96,0.08); border-color: rgba(16,155,96,0.2); }
    [data-theme="light"] .status-pill.crimson { color: #8C0E03; }
    [data-theme="light"] .status-pill.gold    { color: #9a6f00; background: rgba(154,111,0,0.08); border-color: rgba(154,111,0,0.2); }
    [data-theme="light"] .stat-icon.teal      { color: #0f9e8e; }
    [data-theme="light"] .stat-icon.blue      { color: #1d4ed8; }
    [data-theme="light"] .stat-icon.coral     { color: #e05252; }
    [data-theme="light"] .stat-icon.gold      { color: #9a6f00; }

    /* ── Quick actions — matches super admin qa-btn ── */
    .qa-btn {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 8px; padding: 16px 10px;
        background: var(--surface2);
        border: 1px solid var(--border);
        cursor: pointer; text-decoration: none; text-align: center;
        transition: all 0.2s;
    }
    .qa-btn:hover { border-color: rgba(140,14,3,0.35); background: rgba(140,14,3,0.06); }
    .qa-icon { width: 30px; height: 30px; border: 1px solid var(--border2); display: flex; align-items: center; justify-content: center; color: var(--text2); }
    .qa-btn:hover .qa-icon { border-color: rgba(140,14,3,0.35); color: var(--crimson); }
    .qa-label { font-family: 'Barlow Condensed', sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text2); }
    .qa-btn:hover .qa-label { color: var(--crimson); }

    /* ── OJT Progress ring ── */
    .ring-wrap { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
    .ring-wrap svg { transform: rotate(-90deg); }
    .ring-bg   { fill: none; stroke: var(--border2); stroke-width: 6; }
    .ring-fill { fill: none; stroke: var(--crimson); stroke-width: 6; stroke-linecap: butt; transition: stroke-dashoffset 0.6s cubic-bezier(.22,.61,.36,1); }
    .ring-fill.complete { stroke: #34d399; }
    .ring-text {
        position: absolute; inset: 0; display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }
    .ring-pct { font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 900; color: var(--text); line-height: 1; }
    .ring-sub { font-family: 'DM Mono', monospace; font-size: 9px; color: var(--muted); letter-spacing: 0.06em; }

    /* ── Timeline / Activity ── */
    .timeline { padding: 8px 0; }
    .tl-item { display: flex; gap: 14px; padding: 10px 20px; position: relative; }
    .tl-item + .tl-item::before { content: ''; position: absolute; left: 26px; top: 0; width: 1px; height: 10px; background: var(--border2); }
    .tl-dot { width: 8px; height: 8px; flex-shrink: 0; margin-top: 4px; border: 1px solid; }
    .tl-dot.green   { background: rgba(52,211,153,0.15);  border-color: #34d399; }
    .tl-dot.crimson { background: rgba(140,14,3,0.12);    border-color: var(--crimson); }
    .tl-dot.gold    { background: rgba(201,168,76,0.12);  border-color: #c9a84c; }
    .tl-dot.steel   { background: var(--surface2);        border-color: var(--border2); }
    .tl-text { font-size: 12.5px; color: var(--text2); line-height: 1.45; }
    .tl-text strong { color: var(--text); font-weight: 500; }
    .tl-time { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 2px; }

    /* ── Session badge ── */
    .session-badge { font-family: 'DM Mono', monospace; font-size: 9px; font-weight: 500; padding: 1px 6px; letter-spacing: 0.06em; text-transform: uppercase; }
    .session-badge.am { background: rgba(96,165,250,0.08); color: #60a5fa; border: 1px solid rgba(96,165,250,0.2); }
    .session-badge.pm { background: rgba(201,168,76,0.08); color: #c9a84c; border: 1px solid rgba(201,168,76,0.2); }
    [data-theme="light"] .session-badge.am { color: #1d4ed8; }
    [data-theme="light"] .session-badge.pm { color: #9a6f00; }

    /* ── Countdown banner ── */
    .countdown-banner {
        display: flex; align-items: center; gap: 14px;
        padding: 10px 16px; margin-bottom: 16px;
        background: rgba(201,168,76,0.06); border: 1px solid rgba(201,168,76,0.2);
        border-left: 2px solid #c9a84c;
    }
    .countdown-banner.done { background: rgba(52,211,153,0.06); border-color: rgba(52,211,153,0.2); border-left-color: #34d399; }

    /* ── Milestone strip ── */
    .milestone-strip { display: flex; gap: 0; padding: 12px 16px; border-top: 1px solid var(--border); }
    .milestone { flex: 1; text-align: center; position: relative; }
    .milestone + .milestone::before { content: ''; position: absolute; left: 0; top: 10px; height: 1px; width: 100%; background: var(--border2); z-index: 0; }
    .ms-dot {
        width: 20px; height: 20px; border-radius: 50%; border: 1px solid var(--border2);
        background: var(--surface2); margin: 0 auto 4px;
        display: flex; align-items: center; justify-content: center;
        position: relative; z-index: 1;
        font-size: 9px; color: var(--muted);
    }
    .ms-dot.reached { background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.4); color: #34d399; }
    .ms-dot.reached::after { content: '✓'; font-size: 8px; }
    .ms-dot.active  { background: rgba(140,14,3,0.1);   border-color: var(--crimson);            color: var(--crimson); }
    .ms-label { font-family: 'DM Mono', monospace; font-size: 9px; color: var(--muted); letter-spacing: 0.06em; }

    /* ── Stats badge numbers ── */
    .stat-num-hrs { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 900; color: var(--text); line-height: 1; }

    /* ── Responsive two-col ── */
    @media (max-width: 900px) {
        .dash-grid { grid-template-columns: 1fr !important; }
        .stats-3col { grid-template-columns: 1fr 1fr !important; }
    }
    @media (max-width: 560px) {
        .stats-3col { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')

@php
    $requiredHours  = $requiredHours ?? 490;
    $totalLogged    = $totalLogged ?? 0;
    $progressPct    = $progressPct ?? 0;
    $remaining      = max(0, $requiredHours - $totalLogged);

    // Milestones: 25%, 50%, 75%, 100%
    $milestones = [
        ['pct' => 25,  'hrs' => round($requiredHours * 0.25)],
        ['pct' => 50,  'hrs' => round($requiredHours * 0.50)],
        ['pct' => 75,  'hrs' => round($requiredHours * 0.75)],
        ['pct' => 100, 'hrs' => $requiredHours],
    ];

    // Days left estimate (assuming ~8 hrs/day)
    $avgHrsPerDay   = 8;
    $daysEstimate   = $remaining > 0 ? ceil($remaining / $avgHrsPerDay) : 0;

    // Count approved hour logs
    $approvedLogs = $recentLogs->where('status', 'approved')->count();
    $pendingLogs  = $recentLogs->where('status', 'pending')->count();
@endphp

{{-- ── GREETING ── --}}
<div class="greeting fade-up">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }} · @yield('page-title', 'Student Portal')</div>
    <div class="greeting-title">
        {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
</div>

{{-- ── COUNTDOWN BANNER ── --}}
@if($application && $application->isApproved())
    @if($progressPct >= 100)
    <div class="countdown-banner done fade-up">
        <svg width="14" height="14" fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        <div>
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#34d399;">OJT Complete</span>
            <span style="font-size:12px;color:var(--text2);margin-left:8px;">You've reached {{ $requiredHours }} required hours. 🎉 Awaiting final evaluation.</span>
        </div>
    </div>
    @elseif($remaining > 0)
    <div class="countdown-banner fade-up">
        <svg width="14" height="14" fill="none" stroke="#c9a84c" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        <div style="flex:1;">
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:#c9a84c;">
                {{ number_format($remaining, 1) }} hours remaining
            </span>
            <span style="font-size:12px;color:var(--text2);margin-left:8px;">
                ~{{ $daysEstimate }} workday{{ $daysEstimate !== 1 ? 's' : '' }} at {{ $avgHrsPerDay }}h/day
            </span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $progressPct }}% done</span>
    </div>
    @endif
@endif

{{-- ── TOP STAT CARDS ── --}}
<div class="stats-grid stats-3col fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">

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
            <div class="stat-num" style="font-size:15px;margin-bottom:6px;line-height:1.3;">{{ $application->company->name }}</div>
            @php $sCls = match($application->status) { 'approved' => 'green', 'rejected' => 'crimson', default => 'gold' }; @endphp
            <span class="status-pill {{ $sCls }}">{{ $application->status_label }}</span>
        @else
            <div class="stat-num" style="font-size:15px;color:var(--muted);">No application</div>
            <a href="{{ route('student.application.create') }}" class="card-action" style="display:inline-flex;margin-top:6px;">Apply now →</a>
        @endif
    </div>

    {{-- Hours Progress with ring ── --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <span class="stat-tag">hours logged</span>
        </div>
        <div style="display:flex;align-items:center;gap:16px;">
            {{-- Progress ring --}}
            @php $r = 34; $circ = 2 * M_PI * $r; $offset = $circ * (1 - min(1, $progressPct / 100)); @endphp
            <div class="ring-wrap">
                <svg width="80" height="80" viewBox="0 0 80 80">
                    <circle class="ring-bg"   cx="40" cy="40" r="{{ $r }}" />
                    <circle class="ring-fill {{ $progressPct >= 100 ? 'complete' : '' }}" cx="40" cy="40" r="{{ $r }}"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $offset }}" />
                </svg>
                <div class="ring-text">
                    <span class="ring-pct">{{ $progressPct }}%</span>
                    <span class="ring-sub">done</span>
                </div>
            </div>
            <div>
                <div class="stat-num-hrs">{{ number_format($totalLogged, 1) }}</div>
                <div class="stat-label">of {{ $requiredHours }} hrs</div>
                @if($remaining > 0)
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:4px;">{{ number_format($remaining,1) }} to go</div>
                @else
                    <span class="status-pill green" style="margin-top:6px;display:inline-flex;">Complete</span>
                @endif
            </div>
        </div>
        {{-- Milestones --}}
        <div class="milestone-strip" style="margin: 12px -0px -0px;">
            @foreach($milestones as $ms)
                @php
                    $reached = $progressPct >= $ms['pct'];
                    $active  = !$reached && ($loop->first || $progressPct >= $milestones[$loop->index - 1]['pct']);
                    $dotCls  = $reached ? 'reached' : ($active ? 'active' : '');
                @endphp
                <div class="milestone">
                    <div class="ms-dot {{ $dotCls }}" title="{{ $ms['hrs'] }}h">
                        @if(!$reached) <span>{{ $ms['pct'] }}</span> @endif
                    </div>
                    <div class="ms-label">{{ $ms['hrs'] }}h</div>
                </div>
            @endforeach
        </div>
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
            <div class="stat-num" style="font-size:17px;">{{ $profile->student_id }}</div>
            <div class="stat-label" style="margin-bottom:4px;">{{ $profile->course }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $profile->year_level }} · {{ $profile->section }}</div>
            <div style="margin-top:10px;display:flex;gap:6px;flex-wrap:wrap;">
                @if($recentLogs->count())
                    <span class="stat-tag">{{ $approvedLogs }} approved</span>
                @endif
                @if($pendingLogs)
                    <span class="stat-tag" style="border-color:rgba(201,168,76,0.3);color:#c9a84c;">{{ $pendingLogs }} pending</span>
                @endif
            </div>
        @else
            <div class="stat-num" style="font-size:15px;color:var(--muted);">No profile</div>
            <div class="stat-label">Contact your coordinator</div>
        @endif
    </div>

</div>

{{-- ── MAIN GRID ── --}}
<div class="dash-grid fade-up fade-up-2" style="display:grid;grid-template-columns:1fr 300px;gap:16px;">

    {{-- LEFT: Recent Hour Logs --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Hour Logs</div>
                <div style="display:flex;align-items:center;gap:10px;">
                    @if($application && $application->isApproved())
                        <a href="{{ route('student.hours.create') }}" class="btn btn-primary btn-sm">+ Log Hours</a>
                        <a href="{{ route('student.hours.index') }}"  class="card-action">View all →</a>
                    @endif
                </div>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Session</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text);">
                                {{ \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                            </td>
                            <td>
                                @if(isset($log->session))
                                    <span class="session-badge {{ $log->session === 'morning' ? 'am' : 'pm' }}">
                                        {{ $log->session === 'morning' ? 'AM' : 'PM' }}
                                    </span>
                                @else
                                    <span class="session-badge am">—</span>
                                @endif
                            </td>
                            <td style="font-family:'DM Mono',monospace;font-size:11px;">
                                {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                            </td>
                            <td style="font-family:'DM Mono',monospace;font-size:11px;">
                                {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                            </td>
                            <td style="font-weight:600;color:#60a5fa;font-family:'DM Mono',monospace;font-size:12px;">
                                {{ number_format($log->total_hours, 2) }}h
                            </td>
                            <td>
                                @php $cls = match($log->status ?? 'pending') { 'approved' => 'green', 'rejected' => 'crimson', default => 'gold' }; @endphp
                                <span class="status-pill {{ $cls }}">{{ ucfirst($log->status ?? 'pending') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center;padding:36px;color:var(--muted);font-family:'DM Mono',monospace;font-size:11px;">
                                @if(!$application) // no application yet
                                @elseif($application->isPending()) // awaiting approval
                                @elseif($application->isRejected()) // application rejected
                                @else // no logs yet — <a href="{{ route('student.hours.create') }}" style="color:var(--crimson);">log hours</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentLogs->count())
            <div style="padding:10px 16px;border-top:1px solid var(--border);display:flex;gap:14px;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    Showing {{ $recentLogs->count() }} of latest entries
                </div>
                @php $totalHrsShown = $recentLogs->sum('total_hours'); @endphp
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--text2);margin-left:auto;">
                    Subtotal: <span style="color:#60a5fa;">{{ number_format($totalHrsShown, 2) }}h</span>
                </div>
            </div>
            @endif
        </div>

        {{-- Weekly Reports mini list --}}
        @if($recentReports->count())
        <div class="card">
            <div class="card-header">
                <div class="card-title">Weekly Reports</div>
                <a href="{{ route('student.reports.index') }}" class="card-action">View all →</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Period</th>
                            <th>Submitted</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                        <tr>
                            <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text);font-weight:500;">
                                Week {{ $report->week_number }}
                            </td>
                            <td style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                                {{ \Carbon\Carbon::parse($report->week_start)->format('M d') }} –
                                {{ \Carbon\Carbon::parse($report->week_end)->format('M d') }}
                            </td>
                            <td style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                                {{ $report->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                @php $rCls = match($report->status ?? 'pending') { 'approved' => 'green', 'rejected' => 'crimson', default => 'gold' }; @endphp
                                <span class="status-pill {{ $rCls }}">{{ ucfirst($report->status ?? 'pending') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Quick Actions --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick actions</div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:12px;">
                <a href="{{ route('student.application.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                            <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
                        </svg>
                    </div>
                    <span class="qa-label">Application</span>
                </a>
                <a href="{{ route('student.hours.create') }}" class="qa-btn">
                    <div class="qa-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
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

        {{-- OJT Summary Card --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">OJT Summary</div>
                <span class="stat-tag">{{ now()->format('Y') }}</span>
            </div>
            <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
                {{-- Hours bar --}}
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">Progress</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--text2);">{{ number_format($totalLogged,1) }} / {{ $requiredHours }}h</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill {{ $progressPct >= 100 ? 'green' : '' }}" style="width:{{ $progressPct }}%;"></div>
                    </div>
                </div>
                {{-- Stats row --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:4px;">
                    <div style="background:var(--surface2);border:1px solid var(--border);padding:10px 12px;">
                        <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:4px;">Approved</div>
                        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:#34d399;">{{ number_format($totalLogged, 0) }}h</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);padding:10px 12px;">
                        <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:4px;">Remaining</div>
                        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:{{ $remaining > 0 ? '#c9a84c' : '#34d399' }};">{{ number_format($remaining, 0) }}h</div>
                    </div>
                </div>
                {{-- Company --}}
                @if($application && $application->isApproved())
                <div style="border-top:1px solid var(--border);padding-top:10px;display:flex;align-items:center;gap:8px;">
                    <svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span style="font-size:12px;color:var(--text2);">{{ $application->company->name }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Activity Feed --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Activity</div>
            </div>
            <div class="timeline">
                @if($application && $application->isApproved() && $recentLogs->count())
                    @foreach($recentLogs->take(4) as $log)
                    @php
                        $dot = match($log->status ?? 'pending') { 'approved' => 'green', 'rejected' => 'crimson', default => 'gold' };
                        $session = isset($log->session) ? ($log->session === 'morning' ? 'AM' : 'PM') : '';
                    @endphp
                    <div class="tl-item">
                        <div class="tl-dot {{ $dot }}"></div>
                        <div>
                            <div class="tl-text">
                                <strong>{{ \Carbon\Carbon::parse($log->date)->format('M d') }}</strong>
                                {{ $session ? "· $session ·" : '·' }}
                                {{ number_format($log->total_hours, 1) }}h
                                <span class="status-pill {{ $dot }}" style="font-size:9px;padding:1px 5px;margin-left:2px;">{{ ucfirst($log->status ?? 'pending') }}</span>
                            </div>
                            <div class="tl-time">{{ \Carbon\Carbon::parse($log->date)->diffForHumans() }}</div>
                        </div>
                    </div>
                    @endforeach
                @elseif($application && $application->isPending())
                    <div class="tl-item">
                        <div class="tl-dot gold"></div>
                        <div>
                            <div class="tl-text">Application submitted to <strong>{{ $application->company->name }}</strong></div>
                            <div class="tl-time">Awaiting coordinator approval</div>
                        </div>
                    </div>
                @else
                    <div class="tl-item">
                        <div class="tl-dot steel"></div>
                        <div>
                            <div class="tl-text" style="color:var(--muted);">No activity yet.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ── STATUS BANNERS ── --}}
@if($application && $application->isPending())
<div class="alert warning fade-up fade-up-3" style="margin-top:16px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <strong>Application pending review.</strong>
        Your application to <strong>{{ $application->company->name }}</strong> is awaiting coordinator approval.
        Hour logging unlocks once approved.
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