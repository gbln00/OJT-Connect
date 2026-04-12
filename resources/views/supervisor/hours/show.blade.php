{{-- resources/views/supervisor/hours/show.blade.php --}}
@extends('layouts.supervisor-app')
@section('title', $student->name . ' — Hour Logs')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.css' rel='stylesheet'>
<style>
/* ── Calendar skin ────────────────────────────────────────────── */
#cal-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    padding: 20px;
}
.fc .fc-toolbar-title {
    font-family: 'Playfair Display', serif;
    font-size: 15px; font-weight: 700;
    color: var(--text); letter-spacing: 0.02em;
}
.fc .fc-button {
    background: var(--surface2) !important;
    border: 1px solid var(--border) !important;
    color: var(--text) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important; font-weight: 600 !important;
    letter-spacing: 0.1em !important; text-transform: uppercase !important;
    border-radius: 0 !important; padding: 5px 12px !important;
    box-shadow: none !important; transition: background 0.15s;
}
.fc .fc-button:hover { background: var(--border) !important; border-color: var(--muted) !important; }
.fc .fc-button-active,
.fc .fc-button:focus {
    background: var(--crimson) !important;
    border-color: var(--crimson) !important;
    color: #fff !important; outline: none !important; box-shadow: none !important;
}
.fc .fc-col-header-cell-cushion {
    font-family: 'DM Mono', monospace; font-size: 9px;
    font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase;
    color: var(--muted); padding: 8px 4px; text-decoration: none !important;
}
.fc .fc-daygrid-day-number {
    font-family: 'DM Mono', monospace; font-size: 11px;
    color: var(--text2); text-decoration: none !important; padding: 6px 8px;
}
.fc .fc-day-today .fc-daygrid-day-number { color: var(--crimson); font-weight: 700; }
.fc .fc-day-today { background: rgba(140,14,3,0.04) !important; }
.fc td, .fc th { border-color: var(--border) !important; }
.fc .fc-scrollgrid { border-color: var(--border) !important; }
.fc .fc-daygrid-day-frame { min-height: 64px; }
.fc-event {
    border-radius: 0 !important; border: none !important;
    font-family: 'DM Mono', monospace !important; font-size: 9px !important;
    font-weight: 600 !important; letter-spacing: 0.06em !important;
    padding: 2px 5px !important; cursor: default !important; margin-bottom: 2px !important;
}
.fc-event.ev-morning   { background: rgba(217,119,6,0.18)  !important; color: #D97706 !important; }
.fc-event.ev-afternoon { background: rgba(59,130,246,0.15) !important; color: #3B82F6 !important; }
.fc-event.ev-approved  { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; }
.fc-event.ev-rejected  { background: rgba(239,68,68,0.15)  !important; color: #f87171 !important; }
.fc-event-dot { display: none !important; }

/* ── Session pills & badges ───────────────────────────────────── */
.session-pill {
    display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px;
    font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase; border: 1px solid;
}
.session-pill.morning         { background: rgba(254,243,199,0.12); color: #D97706; border-color: rgba(217,119,6,0.3); }
.session-pill.afternoon       { background: rgba(219,234,254,0.12); color: #3B82F6; border-color: rgba(59,130,246,0.3); }
.session-pill.morning.muted   { background: transparent; color: var(--muted); border-color: var(--border); }
.session-pill.afternoon.muted { background: transparent; color: var(--muted); border-color: var(--border); }

.log-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase; padding: 3px 8px; border: 1px solid;
}
.log-badge .badge-dot { width: 5px; height: 5px; display: inline-block; flex-shrink: 0; }
.log-badge.pending  { color: #D97706; border-color: rgba(217,119,6,0.3); background: rgba(254,243,199,0.08); }
.log-badge.pending .badge-dot  { background: #D97706; }
.log-badge.approved { color: #34d399; border-color: rgba(52,211,153,0.3); background: rgba(52,211,153,0.06); }
.log-badge.approved .badge-dot { background: #34d399; }
.log-badge.rejected { color: var(--crimson); border-color: rgba(140,14,3,0.3); background: rgba(140,14,3,0.06); }
.log-badge.rejected .badge-dot { background: var(--crimson); }

/* Cal legend */
.cal-legend {
    display: flex; gap: 16px; flex-wrap: wrap;
    margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);
}
.cal-legend-item {
    display: flex; align-items: center; gap: 6px;
    font-family: 'DM Mono', monospace; font-size: 9px; font-weight: 600;
    letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);
}
.cal-legend-dot { width: 8px; height: 8px; flex-shrink: 0; }

/* ── Back button ──────────────────────────────────────────────── */
.back-btn {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: 'DM Mono', monospace; font-size: 11px; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase; text-decoration: none;
    padding: 9px 20px; border: 1px solid var(--border2);
    color: var(--text2); background: var(--surface2);
    transition: border-color 0.15s, color 0.15s, background 0.15s;
    flex-shrink: 0;
}
.back-btn:hover {
    border-color: var(--crimson);
    color: var(--crimson);
    background: rgba(140,14,3,0.06);
}
</style>
@endpush

@section('content')

{{-- Eyebrow / Breadcrumb + Back button --}}
<div class="fade-up" style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Hour Logs</span>
        <span style="color:var(--border2);font-size:10px;">/</span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Intern Detail</span>
    </div>

    <a href="{{ route('supervisor.hours.index') }}" class="back-btn">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to Hour Logs
    </a>
</div>

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;" class="fade-up fade-up-1">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:52px;height:52px;flex-shrink:0;border:1.5px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.08);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--crimson);">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div>
            <h1 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text);margin:0 0 3px;line-height:1.2;">{{ $student->name }}</h1>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span>{{ $application->company->name }}</span>
                @if($application->program)
                    <span style="color:var(--border2);">·</span>
                    <span>{{ $application->program }}</span>
                @endif
            </div>
        </div>
    </div>

    @if ($stats['pending_count'] > 0)
        <form method="POST" action="{{ route('supervisor.hours.approve-all', $student) }}" style="flex-shrink:0;">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-approve btn-sm" style="display:flex;align-items:center;gap:6px;">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Approve All Pending
                <span style="font-family:'DM Mono',monospace;font-size:9px;background:rgba(255,255,255,0.15);padding:1px 5px;">{{ $stats['pending_count'] }}</span>
            </button>
        </form>
    @endif
</div>

{{-- Stats row --}}
@php
    $pct = $application->required_hours > 0
        ? min(100, round(($stats['total_approved'] / $application->required_hours) * 100))
        : 0;
    $pctColor = $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)');
@endphp
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(4,1fr);margin-bottom:16px;">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon night"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg></div><span class="stat-tag">logged</span></div>
        <div class="stat-num">{{ number_format($stats['total_logged'], 1) }}</div>
        <div class="stat-label">Total hrs logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon teal"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg></div><span class="stat-tag">done</span></div>
        <div class="stat-num">{{ number_format($stats['total_approved'], 1) }}</div>
        <div class="stat-label">Approved hrs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon gold"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg></div><span class="stat-tag">queue</span></div>
        <div class="stat-num" style="{{ $stats['pending_count'] > 0 ? 'color:#D97706;' : '' }}">{{ $stats['pending_count'] }}</div>
        <div class="stat-label">Pending logs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon steel"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="stat-tag">progress</span></div>
        <div class="stat-num" style="color:{{ $pctColor }};">{{ $pct }}%</div>
        <div class="stat-label">Of {{ number_format($application->required_hours) }} req. hrs</div>
    </div>
</div>

{{-- Progress bar --}}
<div class="card fade-up fade-up-2" style="margin-bottom:20px;">
    <div style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">Completion Progress</span>
            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">
                {{ number_format($stats['total_approved'], 1) }} / {{ number_format($application->required_hours) }} hrs
                <span style="color:{{ $pctColor }};margin-left:8px;font-weight:600;">{{ $pct }}%</span>
            </span>
        </div>
        <div style="height:6px;background:var(--surface2);border:1px solid var(--border);overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pctColor }};transition:width 0.6s ease;"></div>
        </div>
        @if($pct >= 100)
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;margin-top:8px;">✓ Required hours completed</div>
        @elseif($pct > 0)
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:8px;">{{ number_format($application->required_hours - $stats['total_approved'], 1) }} hrs remaining</div>
        @endif
    </div>
</div>

{{-- ── CALENDAR ──────────────────────────────────────────────────── --}}
<div class="card fade-up fade-up-2" style="margin-bottom:20px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
        <span style="width:3px;height:16px;background:var(--crimson);display:inline-block;flex-shrink:0;"></span>
        <span style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">Monthly Overview</span>
    </div>
    <div style="padding:20px 24px;">
        <div id="cal-wrap">
            <div id="calendar"></div>
            <div class="cal-legend">
                <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#D97706;"></div> AM · Pending</div>
                <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#3B82F6;"></div> PM · Pending</div>
                <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#34d399;"></div> Approved</div>
                <div class="cal-legend-item"><div class="cal-legend-dot" style="background:#f87171;"></div> Rejected</div>
            </div>
        </div>
    </div>
</div>

{{-- Status filter tabs --}}
<div style="display:flex;gap:4px;margin-bottom:16px;" class="fade-up fade-up-2">
    @foreach ([''=>'All', 'pending'=>'Pending', 'approved'=>'Approved', 'rejected'=>'Rejected'] as $s => $label)
        <a href="{{ route('supervisor.hours.show', [$student, 'status' => $s]) }}"
           class="btn btn-sm {{ request('status', '') === $s ? 'btn-primary' : 'btn-ghost' }}">
            @if($s === 'pending' && $stats['pending_count'] > 0 && request('status','') !== 'pending')
                {{ $label }} <span style="font-family:'DM Mono',monospace;font-size:9px;background:rgba(217,119,6,0.15);color:#D97706;padding:1px 5px;border:1px solid rgba(217,119,6,0.3);margin-left:4px;">{{ $stats['pending_count'] }}</span>
            @else
                {{ $label }}
            @endif
        </a>
    @endforeach
</div>

@if (session('success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.25);color:#34d399;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;display:flex;align-items:center;gap:8px;" class="fade-up">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- Section label --}}
<div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:12px;" class="fade-up fade-up-2">
    Log History
</div>

{{-- Daily grouped logs --}}
@forelse ($groupedByDate as $date => $sessions)
    @php
        $dateLabel   = \Carbon\Carbon::parse($date)->format('l, F j, Y');
        $dayTotal    = collect($sessions)->sum('total_hours');
        $allApproved = collect($sessions)->every(fn($l) => $l->status === 'approved');
        $hasPending  = collect($sessions)->some(fn($l) => $l->status === 'pending');
    @endphp
    <div class="card fade-up fade-up-2" style="margin-bottom:10px;" data-date="{{ $date }}">
        <div style="padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:3px;height:22px;background:{{ $hasPending ? '#D97706' : ($allApproved ? '#34d399' : 'var(--crimson)') }};display:inline-block;flex-shrink:0;"></span>
                <div>
                    <span style="font-family:'Barlow',sans-serif;font-weight:600;font-size:13.5px;color:var(--text);">{{ $dateLabel }}</span>
                    @if($allApproved)
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;margin-left:8px;">✓ All approved</span>
                    @elseif($hasPending)
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:#D97706;margin-left:8px;">⏳ Pending review</span>
                    @endif
                </div>
            </div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);background:var(--surface2);border:1px solid var(--border);padding:2px 10px;letter-spacing:0.06em;">
                {{ number_format($dayTotal, 1) }} hrs
            </span>
        </div>
        <div class="table-wrap" style="margin:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width:140px;">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['morning', 'afternoon'] as $session)
                        @if (isset($sessions[$session]))
                            @php $log = $sessions[$session]; @endphp
                            <tr style="transition:background 0.15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background=''">
                                <td><span class="session-pill {{ $session }}">{{ $session === 'morning' ? '☀ AM · Morning' : '◑ PM · Afternoon' }}</span></td>
                                <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                                <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                                <td>
                                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</span>
                                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                                </td>
                                <td style="font-size:12px;color:var(--muted);max-width:180px;">{{ $log->description ?? '—' }}</td>
                                <td>
                                    @if ($log->status === 'approved')
                                        <span class="log-badge approved"><span class="badge-dot"></span>Approved</span>
                                    @elseif ($log->status === 'rejected')
                                        <span class="log-badge rejected"><span class="badge-dot"></span>Rejected</span>
                                        @if($log->rejection_reason)
                                            <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:3px;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $log->rejection_reason }}">{{ $log->rejection_reason }}</div>
                                        @endif
                                    @else
                                        <span class="log-badge pending"><span class="badge-dot"></span>Pending</span>
                                    @endif
                                </td>
                                <td style="text-align:right;">
                                    @if ($log->isPending())
                                        <div style="display:flex;gap:4px;justify-content:flex-end;">
                                            <form method="POST" action="{{ route('supervisor.hours.approve', $log) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-approve btn-sm">✓ Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('supervisor.hours.reject', $log) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">✕</button>
                                            </form>
                                        </div>
                                    @elseif ($log->isApproved())
                                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;letter-spacing:0.06em;">✓ Approved</span>
                                    @else
                                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--crimson);letter-spacing:0.06em;">✕ Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr style="opacity:0.5;">
                                <td><span class="session-pill {{ $session }} muted">{{ $session === 'morning' ? '☀ AM · Morning' : '◑ PM · Afternoon' }}</span></td>
                                <td colspan="6" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No {{ $session }} session submitted</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="card fade-up fade-up-2" style="padding:64px 32px;text-align:center;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="color:var(--muted);opacity:0.35;"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            <div style="font-family:'Playfair Display',serif;font-size:18px;color:var(--text2);">No logs found</div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                @if(request('status'))
                    No {{ request('status') }} logs for this intern.
                    <a href="{{ route('supervisor.hours.show', $student) }}" style="color:var(--crimson);text-decoration:none;">Clear filter →</a>
                @else
                    This intern has not submitted any hour logs yet.
                @endif
            </div>
        </div>
    </div>
@endforelse

@if ($logs->hasPages())
<div class="pagination fade-up" style="margin-top:8px;">
    <span class="pagination-info">Showing <strong>{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</strong> of <strong>{{ $logs->total() }}</strong></span>
    <div style="display:flex;gap:4px;">
        @if ($logs->onFirstPage())
            <span class="page-link disabled">← Prev</span>
        @else
            <a href="{{ $logs->previousPageUrl() }}" class="page-link">← Prev</a>
        @endif
        @if ($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}" class="page-link">Next →</a>
        @else
            <span class="page-link disabled">Next →</span>
        @endif
    </div>
</div>
@endif

@php
    $calendarEvents = collect($groupedByDate)->flatMap(function($sessions, $date) {
        return collect($sessions)->map(function($log) use ($date) {
            return [
                'title' => ($log->session === 'morning' ? 'AM' : 'PM') . ' · ' . number_format($log->total_hours, 1) . 'h',
                'start' => $date,
                'extendedProps' => [
                    'session' => $log->session,
                    'status'  => $log->status,
                    'hours'   => number_format($log->total_hours, 1),
                ],
            ];
        });
    })->values();
@endphp

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const events = @json($calendarEvents);

    const cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next', center: 'title', right: 'today' },
        height: 'auto',
        firstDay: 0,
        eventDisplay: 'block',
        dayMaxEvents: 3,
        events: events,
        eventClassNames: function (info) {
            const status  = info.event.extendedProps.status  ?? 'pending';
            const session = info.event.extendedProps.session ?? '';
            if (status === 'approved') return ['fc-event', 'ev-approved'];
            if (status === 'rejected') return ['fc-event', 'ev-rejected'];
            return ['fc-event', session === 'morning' ? 'ev-morning' : 'ev-afternoon'];
        },
        eventContent: function (info) {
            const session = info.event.extendedProps.session ?? '';
            const hours   = info.event.extendedProps.hours   ?? '';
            const label   = session === 'morning' ? 'AM' : 'PM';
            return {
                html: `<div style="display:flex;align-items:center;gap:4px;overflow:hidden;">
                           <span>${label}</span>
                           <span style="opacity:0.7;">${hours}h</span>
                       </div>`
            };
        },
        dateClick: function (info) {
            const card = document.querySelector(`[data-date="${info.dateStr}"]`);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.style.outline = '2px solid var(--crimson)';
                setTimeout(() => card.style.outline = '', 1800);
            }
        },
    });
    cal.render();
});
</script>
@endpush

@endsection