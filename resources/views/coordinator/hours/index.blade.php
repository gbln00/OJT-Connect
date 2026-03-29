{{-- ═══════════════════════════════════════════════════════════════════
     COORDINATOR / hours / index.blade.php
═══════════════════════════════════════════════════════════════════ --}}
@extends('layouts.coordinator-app')
@section('title', 'Hour Logs')
@section('page-title', 'Hour Logs')
@section('content')

@if($pending > 0)
<div class="fade-up" style="background:var(--gold-dim);border:1px solid var(--gold-border);color:var(--gold);padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span><strong>{{ $pending }}</strong> hour {{ Str::plural('log', $pending) }} waiting for your approval.</span>
</div>
@endif

{{-- Filter --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('coordinator.hours.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="status" class="form-input" style="width:auto;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request('status'))
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
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
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
                    {{ $log->date instanceof \Carbon\Carbon ? $log->date->format('M d, Y') : \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                </td>
                <td style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:var(--blue);">
                    {{ $log->total_hours }}
                    <span style="font-family:'DM Mono',monospace;font-size:9px;font-weight:400;color:var(--muted);">hrs</span>
                </td>
                <td style="font-size:12px;color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $log->description ?? '—' }}
                </td>
                <td>
                    <span class="status-dot {{ $log->status }}">{{ ucfirst($log->status) }}</span>
                </td>
                <td>
                    @if($log->status === 'pending')
                    <div style="display:flex;gap:4px;">
                        <form method="POST" action="{{ route('coordinator.hours.approve', $log->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-approve btn-sm"
                                    onclick="return confirm('Approve this hour log?')">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('coordinator.hours.reject', $log->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-reject btn-sm"
                                    onclick="return confirm('Reject this hour log?')">Reject</button>
                        </form>
                    </div>
                    @else
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                    No hour logs found.
                    <span style="font-family:'DM Mono',monospace;font-size:11px;display:block;margin-top:4px;">Try adjusting your filters.</span>
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