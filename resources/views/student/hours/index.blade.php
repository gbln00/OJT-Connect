{{-- resources/views/student/hours/index.blade.php --}}
@extends('layouts.student-app')
@section('title', 'Hour Logs')
@section('page-title', 'Hour Logs')

@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon teal">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        </div><span class="stat-tag">approved</span></div>
        <div class="stat-num">{{ number_format($totalApproved, 1) }}</div>
        <div class="stat-label">Approved Hours</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon gold">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div><span class="stat-tag">remaining</span></div>
        <div class="stat-num">{{ number_format($application->remaining_hours, 1) }}</div>
        <div class="stat-label">Hours Remaining</div>
    </div>
    <div class="stat-card">
        @php $pct = $application->required_hours > 0 ? min(100, round(($totalApproved / $application->required_hours)*100)) : 0; @endphp
        <div class="stat-top"><div class="stat-icon blue">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
        </div><span class="stat-tag">progress</span></div>
        <div class="stat-num">{{ $pct }}%</div>
        <div class="stat-label" style="margin-bottom:8px;">Complete</div>
        <div class="progress-track"><div class="progress-fill blue" style="width:{{ $pct }}%;"></div></div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;justify-content:flex-end;margin-bottom:16px;" class="fade-up fade-up-1">
    <a href="{{ route('student.hours.create') }}" class="btn btn-primary btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Log Hours
    </a>
</div>

{{-- TABLE --}}
<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ $log->date->format('M d, Y') }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                    <td style="font-weight:600;color:var(--blue-color);">{{ $log->total_hours }} hrs</td>
                    <td style="color:var(--muted2);">{{ $log->description ?? '—' }}</td>
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
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                        No hour logs yet.
                        <a href="{{ route('student.hours.create') }}" style="color:var(--crimson);text-decoration:none;font-weight:500;">Log your first hours →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination">
        <span class="pagination-info">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} logs</span>
        <div style="display:flex;gap:4px;">
            @if($logs->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection