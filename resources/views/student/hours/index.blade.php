{{-- resources/views/student/hours/index.blade.php --}}
@extends('layouts.student-app')
@section('title', 'My Hour Logs')

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
                <div style="font-family:'Playfair Display',serif;font-size:36px;font-weight:700;color:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};">{{ $pct }}%</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">complete</div>
            </div>
        </div>
        <div style="height:6px;background:var(--surface2);border:1px solid var(--border);position:relative;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};transition:width 0.6s ease;"></div>
        </div>
        @if ($pct >= 100)
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;margin-top:8px;letter-spacing:0.08em;">✓ Required hours completed</div>
        @else
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:8px;letter-spacing:0.04em;">{{ number_format($required - $totalApproved, 1) }} hrs remaining</div>
        @endif
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
                        <th style="width:100px;">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
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
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <span class="session-pill {{ $session }} muted">
                                        {{ $session === 'morning' ? 'AM · Morning' : 'PM · Afternoon' }}
                                    </span>
                                </td>
                                <td colspan="5" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No {{ $session }} session logged</td>
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

@push('styles')
<style>
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
.status-dot.pending {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase;
    color: #D97706;
}
.status-dot.pending::before {
    content: ''; width: 5px; height: 5px;
    background: #D97706; display: inline-block; border-radius: 0;
}
</style>
@endpush

@endsection