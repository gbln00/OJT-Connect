{{-- resources/views/student/hours/index.blade.php --}}
@extends('layouts.student-app')
@section('title', 'My Hour Logs')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.css' rel='stylesheet'>
<style>
/* ── Calendar skin ────────────────────────────────────────────────── */
#cal-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    padding: 20px;
    margin-bottom: 24px;
}

/* Toolbar */
.fc .fc-toolbar-title {
    font-family: 'Playfair Display', serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: 0.02em;
}
.fc .fc-button {
    background: var(--surface2) !important;
    border: 1px solid var(--border) !important;
    color: var(--text) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important;
    font-weight: 600 !important;
    letter-spacing: 0.1em !important;
    text-transform: uppercase !important;
    border-radius: 0 !important;
    padding: 5px 12px !important;
    box-shadow: none !important;
    transition: background 0.15s, border-color 0.15s;
}
.fc .fc-button:hover {
    background: var(--border) !important;
    border-color: var(--muted) !important;
}
.fc .fc-button-active,
.fc .fc-button:focus {
    background: var(--crimson) !important;
    border-color: var(--crimson) !important;
    color: #fff !important;
    outline: none !important;
    box-shadow: none !important;
}
.fc .fc-button-primary:not(:disabled):active {
    background: var(--crimson) !important;
    border-color: var(--crimson) !important;
}

/* Grid */
.fc .fc-col-header-cell-cushion {
    font-family: 'DM Mono', monospace;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--muted);
    padding: 8px 4px;
    text-decoration: none !important;
}
.fc .fc-daygrid-day-number {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    color: var(--text2);
    text-decoration: none !important;
    padding: 6px 8px;
}
.fc .fc-day-today .fc-daygrid-day-number {
    color: var(--crimson);
    font-weight: 700;
}
.fc .fc-day-today {
    background: rgba(var(--crimson-rgb, 180,0,0), 0.04) !important;
}
.fc td, .fc th {
    border-color: var(--border) !important;
}
.fc .fc-scrollgrid {
    border-color: var(--border) !important;
}
.fc .fc-daygrid-day-frame {
    min-height: 68px;
}

/* Events */
.fc-event {
    border-radius: 0 !important;
    border: none !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 9px !important;
    font-weight: 600 !important;
    letter-spacing: 0.06em !important;
    padding: 2px 5px !important;
    cursor: default !important;
    margin-bottom: 2px !important;
}
.fc-event.ev-morning   { background: rgba(217,119,6,0.18) !important; color: #D97706 !important; }
.fc-event.ev-afternoon { background: rgba(59,130,246,0.15) !important; color: #3B82F6 !important; }
.fc-event.ev-approved  { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; }
.fc-event.ev-rejected  { background: rgba(239,68,68,0.15)  !important; color: #f87171 !important; }
.fc-event .fc-event-title { font-weight: 600 !important; }
.fc-event-dot { display: none !important; }

/* Day dots strip */
.cal-dot-strip {
    display: flex;
    gap: 2px;
    padding: 0 4px 4px;
    flex-wrap: wrap;
}
.cal-dot {
    width: 5px; height: 5px;
    flex-shrink: 0;
}
.cal-dot.morning   { background: #D97706; }
.cal-dot.afternoon { background: #3B82F6; }
.cal-dot.approved  { background: #34d399; }
.cal-dot.rejected  { background: #f87171; }

/* Legend */
.cal-legend {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}
.cal-legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-family: 'DM Mono', monospace;
    font-size: 9px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--muted);
}
.cal-legend-dot {
    width: 8px; height: 8px; flex-shrink: 0;
}

/* ── Session pills ──────────────────────────────────────────────── */
.session-pill {
    display: inline-block;
    padding: 3px 10px;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border: 1px solid;
}
.session-pill.morning         { background: rgba(254,243,199,0.12); color: #D97706; border-color: rgba(217,119,6,0.3); }
.session-pill.afternoon       { background: rgba(219,234,254,0.12); color: #3B82F6; border-color: rgba(59,130,246,0.3); }
.session-pill.morning.muted   { background: transparent; color: var(--muted); border-color: var(--border); }
.session-pill.afternoon.muted { background: transparent; color: var(--muted); border-color: var(--border); }

/* ── Status dots ────────────────────────────────────────────────── */
.status-dot {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase;
}
.status-dot::before {
    content: ''; width: 5px; height: 5px;
    display: inline-block; border-radius: 0;
}
.status-dot.active   { color: #34d399; } .status-dot.active::before  { background: #34d399; }
.status-dot.inactive { color: #f87171; } .status-dot.inactive::before { background: #f87171; }
.status-dot.pending  { color: #D97706; } .status-dot.pending::before { background: #D97706; }
</style>
@endpush

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        OJT Tracker / Hour Logs
    </span>
</div>

{{-- Header row --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;" class="fade-up fade-up-1">
    <div>
        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text);line-height:1.2;">Hour Logs</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:4px;">{{ $application->company->name }}</div>
    </div>
    <a href="{{ route('student.hours.create') }}" class="btn btn-primary btn-sm" style="flex-shrink:0;">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Log Today's Hours
    </a>
</div>

{{-- Progress card --}}
@php
    $required = $application->required_hours;
    $pct      = $required > 0 ? min(100, round(($totalApproved / $required) * 100)) : 0;
    $pctColor = $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)');
@endphp
<div class="card fade-up fade-up-2" style="margin-bottom:20px;">
    <div style="padding:20px 24px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <div>
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Approved Hours Progress</div>
                <div style="display:flex;align-items:baseline;gap:6px;">
                    <span style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--text);">{{ number_format($totalApproved, 1) }}</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">/ {{ number_format($required) }} hrs required</span>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="font-family:'Playfair Display',serif;font-size:36px;font-weight:700;color:{{ $pctColor }};">{{ $pct }}%</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">complete</div>
            </div>
        </div>
        <div style="height:6px;background:var(--surface2);border:1px solid var(--border);position:relative;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pctColor }};transition:width 0.6s ease;"></div>
        </div>
        @if ($pct >= 100)
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;margin-top:8px;letter-spacing:0.08em;">✓ Required hours completed</div>
        @else
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:8px;letter-spacing:0.04em;">{{ number_format($required - $totalApproved, 1) }} hrs remaining</div>
        @endif
    </div>
</div>

{{-- Calendar --}}
<div class="card fade-up fade-up-2" style="margin-bottom:24px;">
    <div style="padding:20px 24px 0;display:flex;align-items:center;gap:8px;border-bottom:1px solid var(--border);padding-bottom:14px;">
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

{{-- Flash messages --}}
@if (session('success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('success') }}
    </div>
@endif
@if (session('info'))
    <div style="background:rgba(96,165,250,0.08);border:1px solid rgba(96,165,250,0.25);color:#60a5fa;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ⓘ {{ session('info') }}
    </div>
@endif

{{-- Section label --}}
<div style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:12px;" class="fade-up fade-up-2">
    Log History
</div>

{{-- Daily logs --}}
@forelse ($groupedByDate as $date => $sessions)
    @php
        $dateLabel = \Carbon\Carbon::parse($date)->format('l, F j, Y');
        $dayTotal  = collect($sessions)->sum('total_hours');
    @endphp
    <div class="card fade-up fade-up-2" style="margin-bottom:12px;">
        <div style="padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="width:3px;height:20px;background:var(--crimson);display:inline-block;flex-shrink:0;"></span>
                <span style="font-family:'Barlow',sans-serif;font-weight:600;font-size:13.5px;color:var(--text);">{{ $dateLabel }}</span>
            </div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);background:var(--surface2);border:1px solid var(--border);padding:2px 10px;letter-spacing:0.06em;">
                {{ number_format($dayTotal, 1) }} hrs
            </span>
        </div>
        <div class="table-wrap" style="margin:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width:130px;">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['morning', 'afternoon'] as $session)
                        @if (isset($sessions[$session]))
                            @php $log = $sessions[$session]; @endphp
                            <tr>
                                <td>
                                    <span class="session-pill {{ $session }}">
                                        {{ $session === 'morning' ? 'AM · Morning' : 'PM · Afternoon' }}
                                    </span>
                                </td>
                                <td style="font-family:'DM Mono',monospace;font-size:12px;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                                <td style="font-family:'DM Mono',monospace;font-size:12px;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                                <td style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</td>
                                <td style="font-size:12px;color:var(--muted);max-width:200px;">{{ $log->description ?? '—' }}</td>
                                <td>
                                    @if ($log->status === 'approved')
                                        <span class="status-dot active">Approved</span>
                                    @elseif ($log->status === 'rejected')
                                        <span class="status-dot inactive">Rejected</span>
                                    @else
                                        <span class="status-dot pending">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($log->isRejected())
                                        <a href="{{ route('student.hours.edit', $log) }}"
                                           style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.08em;text-transform:uppercase;color:var(--crimson);text-decoration:none;border-bottom:1px solid rgba(var(--crimson-rgb,180,0,0),0.3);">
                                            Fix &rarr;
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <span class="session-pill {{ $session }} muted">
                                        {{ $session === 'morning' ? 'AM · Morning' : 'PM · Afternoon' }}
                                    </span>
                                </td>
                                <td colspan="6" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No {{ $session }} session logged</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="card fade-up fade-up-2" style="padding:56px;text-align:center;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;color:var(--text2);margin-bottom:8px;">No hour logs yet</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-bottom:20px;">Start tracking your OJT hours today.</div>
        <a href="{{ route('student.hours.create') }}" class="btn btn-primary btn-sm" style="display:inline-flex;">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Log your first day
        </a>
    </div>
@endforelse

@if ($logs->hasPages())
<div class="pagination fade-up" style="margin-top:8px;">
    <span class="pagination-info">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
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

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cal = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next',
            center: 'title',
            right:  'today'
        },
        height: 'auto',
        firstDay: 0,
        eventDisplay: 'block',
        dayMaxEvents: 3,
        events: {
            url: '{{ route("student.hours.calendar") }}',
            failure: function () {
                console.warn('Calendar data could not be loaded.');
            }
        },
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
        // Clicking a day scrolls down to that date's log card
        dateClick: function (info) {
            const key  = info.dateStr;                         // YYYY-MM-DD
            const card = document.querySelector(`[data-date="${key}"]`);
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