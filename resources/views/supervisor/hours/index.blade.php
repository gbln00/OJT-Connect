{{-- resources/views/supervisor/hours/index.blade.php --}}
@extends('layouts.supervisor-app')
@section('title', 'Hour Logs')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Management / Intern Hour Logs
    </span>
</div>

{{-- Stats --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <span class="stat-tag">review</span>
        </div>
        <div class="stat-num">{{ $pending }}</div>
        <div class="stat-label">Pending logs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
            <span class="stat-tag">done</span>
        </div>
        <div class="stat-num">{{ $approved }}</div>
        <div class="stat-label">Approved logs</div>
    </div>
</div>

{{-- Toolbar / Filters --}}
<div class="card fade-up fade-up-1" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('supervisor.hours.index') }}" style="padding:16px 20px;">
        <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:160px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Filter by intern</label>
                <select name="student_id" class="form-select">
                    <option value="">All interns</option>
                    @foreach ($interns as $intern)
                        <option value="{{ $intern->id }}" {{ request('student_id') == $intern->id ? 'selected' : '' }}>
                            {{ $intern->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:140px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Status</label>
                <select name="status" class="form-select">
                    <option value="">All statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div style="display:flex;gap:6px;">
                <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
                @if(request()->hasAny(['student_id','status']))
                    <a href="{{ route('supervisor.hours.index') }}" class="btn btn-ghost btn-sm">Clear</a>
                @endif
            </div>
        </div>
    </form>
</div>


{{-- Logs table --}}
<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Intern</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>
                            <a href="{{ route('supervisor.hours.show', $log->student_id) }}"
                               style="color:var(--text);font-weight:500;text-decoration:none;display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:10px;font-weight:700;color:var(--crimson);">
                                    {{ strtoupper(substr($log->student->name, 0, 2)) }}
                                </div>
                                {{ $log->student->name }}
                            </a>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $log->date->format('M d, Y') }}</td>
                        <td>
                            <span class="session-pill {{ $log->session }}">
                                {{ $log->session === 'morning' ? 'AM' : 'PM' }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:12px;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                        <td style="font-family:'DM Mono',monospace;font-size:12px;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                        <td style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</td>
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
                                       @csrf
                                        <button type="submit" class="btn btn-approve btn-sm">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('supervisor.hours.reject', $log) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:48px;color:var(--muted);">
                            No logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($logs->hasPages())
    <div class="pagination">
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
</div>

@push('styles')
<style>
.session-pill {
    display: inline-block; padding: 2px 10px;
    font-family: 'DM Mono', monospace; font-size: 10px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase; border: 1px solid;
}
.session-pill.morning   { background: rgba(254,243,199,0.12); color: #D97706; border-color: rgba(217,119,6,0.3); }
.session-pill.afternoon { background: rgba(219,234,254,0.12); color: #3B82F6; border-color: rgba(59,130,246,0.3); }
.status-dot.pending {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase; color: #D97706;
}
.status-dot.pending::before {
    content: ''; width: 5px; height: 5px; background: #D97706;
    display: inline-block; border-radius: 0;
}
.form-label {
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted);
}
.form-select {
    width: 100%; padding: 9px 14px;
    background: var(--surface2); border: 1px solid var(--border2);
    color: var(--text); font-size: 13px; font-family: 'Barlow', sans-serif;
    outline: none; transition: border-color 0.15s;
    border-radius: 0; appearance: none;
}
.form-select:focus { border-color: var(--crimson); }
</style>
@endpush

@endsection