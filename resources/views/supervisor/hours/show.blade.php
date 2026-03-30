{{-- resources/views/supervisor/hours/show.blade.php --}}
@extends('layouts.supervisor-app')
@section('title', $student->name . ' — Hour Logs')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Hour Logs / Intern Detail
    </span>
</div>

{{-- Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;" class="fade-up fade-up-1">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:48px;height:48px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:var(--crimson);">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div>
            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--text);line-height:1.2;">{{ $student->name }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                {{ $application->company->name }}
                @if($application->program)
                    <span style="color:var(--border2);margin:0 6px;">·</span>{{ $application->program }}
                @endif
            </div>
            <a href="{{ route('supervisor.hours.index') }}" style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-decoration:none;letter-spacing:0.04em;margin-top:4px;display:inline-block;">
                ← All interns
            </a>
        </div>
    </div>

    @if ($stats['pending_count'] > 0)
        <form method="POST" action="{{ route('supervisor.hours.approveAll', $student) }}" style="flex-shrink:0;">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-approve btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Approve All Pending ({{ $stats['pending_count'] }})
            </button>
        </form>
    @endif
</div>

{{-- Stats row --}}
@php
    $pct = $application->required_hours > 0
        ? min(100, round(($stats['total_approved'] / $application->required_hours) * 100))
        : 0;
@endphp
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <span class="stat-tag">logged</span>
        </div>
        <div class="stat-num">{{ number_format($stats['total_logged'], 1) }}</div>
        <div class="stat-label">Total hrs logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
            <span class="stat-tag">done</span>
        </div>
        <div class="stat-num">{{ number_format($stats['total_approved'], 1) }}</div>
        <div class="stat-label">Approved hrs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <span class="stat-tag">queue</span>
        </div>
        <div class="stat-num">{{ $stats['pending_count'] }}</div>
        <div class="stat-label">Pending logs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <span class="stat-tag">progress</span>
        </div>
        <div class="stat-num" style="color:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};">{{ $pct }}%</div>
        <div class="stat-label">Of {{ number_format($application->required_hours) }} hrs</div>
    </div>
</div>

{{-- Progress bar --}}
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <div style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">Hours Completion</span>
            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ number_format($stats['total_approved'], 1) }} / {{ number_format($application->required_hours) }} hrs</span>
        </div>
        <div style="height:6px;background:var(--surface2);border:1px solid var(--border);overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};transition:width 0.5s ease;"></div>
        </div>
    </div>
</div>

{{-- Status filter tabs --}}
<div style="display:flex;gap:4px;margin-bottom:16px;" class="fade-up fade-up-2">
    @foreach ([''=>'All', 'pending'=>'Pending', 'approved'=>'Approved', 'rejected'=>'Rejected'] as $s => $label)
        <a href="{{ route('supervisor.hours.show', [$student, 'status' => $s]) }}"
           class="btn btn-sm {{ request('status', '') === $s ? 'btn-primary' : 'btn-ghost' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

@if (session('success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- Daily grouped logs --}}
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
                        <th style="width:120px;">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
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
                                <td style="font-size:12px;color:var(--muted);max-width:180px;">{{ $log->description ?? '—' }}</td>
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
                                    @if ($log->isPending())
                                        <div style="display:flex;gap:4px;">
                                            <form method="POST" action="{{ route('supervisor.hours.approve', $log) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-approve btn-sm">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('supervisor.hours.reject', $log) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
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
                            <tr>
                                <td>
                                    <span class="session-pill {{ $session }} muted">
                                        {{ $session === 'morning' ? 'AM · Morning' : 'PM · Afternoon' }}
                                    </span>
                                </td>
                                <td colspan="6" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No {{ $session }} session submitted</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="card fade-up fade-up-2" style="padding:56px;text-align:center;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;color:var(--text2);margin-bottom:8px;">No logs found</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
            @if(request('status'))
                No {{ request('status') }} logs for this intern.
                <a href="{{ route('supervisor.hours.show', $student) }}" style="color:var(--crimson);text-decoration:none;">Clear filter →</a>
            @else
                This intern has not submitted any hour logs yet.
            @endif
        </div>
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
    display: inline-block; padding: 3px 10px;
    font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase; border: 1px solid;
}
.session-pill.morning         { background: rgba(254,243,199,0.12); color: #D97706; border-color: rgba(217,119,6,0.3); }
.session-pill.afternoon       { background: rgba(219,234,254,0.12); color: #3B82F6; border-color: rgba(59,130,246,0.3); }
.session-pill.morning.muted   { background: transparent; color: var(--muted); border-color: var(--border); }
.session-pill.afternoon.muted { background: transparent; color: var(--muted); border-color: var(--border); }
.status-dot.pending {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase; color: #D97706;
}
.status-dot.pending::before {
    content: ''; width: 5px; height: 5px; background: #D97706;
    display: inline-block; border-radius: 0;
}
</style>
@endpush

@endsection