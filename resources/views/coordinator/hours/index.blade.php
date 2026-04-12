@extends('layouts.coordinator-app')
@section('title', 'Approved Hour Logs')
@section('page-title', 'Approved Hour Logs')
@section('content')

{{-- Info banner --}}
<div class="fade-up" style="background:var(--blue-dim);border:1px solid var(--blue-border);color:var(--blue);padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span>
        Showing supervisor-approved hour logs only. Approval and rejection is handled by company supervisors.
        <span style="font-family:'DM Mono',monospace;font-size:10px;opacity:0.7;margin-left:8px;">
            Total approved: <strong>{{ number_format($totalApprovedHours, 1) }} hrs</strong>
        </span>
    </span>
</div>

{{-- Filters --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('coordinator.hours.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="student_id" class="form-input" style="width:auto;">
            <option value="">All students</option>
            @foreach($interns as $intern)
                <option value="{{ $intern->id }}" {{ request('student_id') == $intern->id ? 'selected' : '' }}>
                    {{ $intern->name }}
                </option>
            @endforeach
        </select>

        <select name="company_id" class="form-input" style="width:auto;">
            <option value="">All companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request()->hasAny(['student_id','company_id']))
            <a href="{{ route('coordinator.hours.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
</div>

<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Approved by</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                            {{ strtoupper(substr($log->student->name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $log->student->name ?? '—' }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $log->student->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px;">{{ $log->application->company->name ?? '—' }}</td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ $log->date->format('M d, Y') }}
                </td>
                <td>
                    <span class="status-pill {{ $log->session === 'morning' ? 'gold' : 'blue' }}">
                        {{ $log->session === 'morning' ? 'AM' : 'PM' }}
                    </span>
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                </td>
                <td>
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--teal);">
                        {{ $log->total_hours }}
                    </span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">hrs</span>
                </td>
                <td style="font-size:12px;color:var(--muted);">
                    {{ $log->approvedBy->name ?? '—' }}
                    @if($log->approved_at)
                        <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:1px;">
                            {{ $log->approved_at->format('M d, Y') }}
                        </div>
                    @endif
                </td>
                <td style="font-size:12px;color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $log->description ?? '—' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                    No approved hour logs found.
                    <span style="font-family:'DM Mono',monospace;font-size:11px;display:block;margin-top:4px;">
                        // Logs will appear here once supervisors approve them.
                    </span>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination">
        <span class="pagination-info">Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
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