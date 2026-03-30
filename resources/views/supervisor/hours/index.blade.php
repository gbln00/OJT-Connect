@extends('layouts.supervisor-app')
@section('title', 'Hour Logs')
@section('page-title', 'Hour Logs')

@section('content')

{{-- EYEBROW --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Hour Logs / Review
    </span>
</div>

{{-- PENDING BANNER --}}
@if($pending > 0)
<div style="background:var(--gold-dim);border:1px solid var(--gold-border);padding:12px 16px;
            margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--gold);"
     class="fade-up">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span>
        <strong>{{ $pending }}</strong> hour {{ Str::plural('log', $pending) }} waiting for your approval.
    </span>
</div>
@endif

{{-- FILTER BAR --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;"
     class="fade-up fade-up-1">
    <form method="GET" action="{{ route('supervisor.hours.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;">

        <select name="status" class="form-input" style="width:auto;cursor:pointer;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>

        @if(request('status'))
        <a href="{{ route('supervisor.hours.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>

    @if($pending > 0)
    <span class="status-pill gold">{{ $pending }} pending</span>
    @endif
</div>

{{-- TABLE --}}
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
                        <div style="width:28px;height:28px;flex-shrink:0;border:1px solid var(--border2);
                                    background:var(--surface2);display:flex;align-items:center;justify-content:center;
                                    font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--text2);">
                            {{ strtoupper(substr($log->student->name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $log->student->name ?? '—' }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $log->student->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td style="font-size:13px;color:var(--text2);">{{ $log->application->company->name ?? '—' }}</td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ $log->date instanceof \Carbon\Carbon ? $log->date->format('M d, Y') : \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                </td>
                <td>
                    <span style="font-family:'Playfair Display',serif;font-weight:700;color:var(--blue);font-size:15px;">
                        {{ $log->total_hours }}
                    </span>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">hrs</span>
                </td>
                <td style="font-size:12px;color:var(--muted);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $log->description ?? '—' }}
                </td>
                <td>
                    @php
                        $statusClass = match($log->status) {
                            'approved' => 'teal',
                            'rejected' => 'coral',
                            default    => 'gold',
                        };
                    @endphp
                    <span class="status-pill {{ $statusClass }}">{{ ucfirst($log->status) }}</span>
                </td>
                <td>
                    @if($log->status === 'pending')
                    <div style="display:flex;gap:4px;">
                        <form method="POST" action="{{ route('supervisor.hours.approve', $log->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-teal btn-sm"
                                    onclick="return confirm('Approve this hour log?')">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('supervisor.hours.reject', $log->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-coral btn-sm"
                                    onclick="return confirm('Reject this hour log?')">
                                Reject
                            </button>
                        </form>
                    </div>
                    @else
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:52px;color:var(--muted);">
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--border2);margin-bottom:10px;">—</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">No hour logs found</div>
                    <div style="font-size:12px;">Try adjusting your filters.</div>
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