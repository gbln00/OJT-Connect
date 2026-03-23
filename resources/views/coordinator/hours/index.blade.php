@extends('layouts.coordinator-app')
@section('title', 'Hour Logs')
@section('page-title', 'Hour Logs')
@section('content')

{{-- Pending banner --}}
@if($pending > 0)
<div style="background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);border-radius:8px;padding:12px 16px;
            margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--gold);">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span><strong>{{ $pending }}</strong> hour {{ Str::plural('log', $pending) }} waiting for your approval.</span>
</div>
@endif

{{-- Filter bar --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <form method="GET" action="{{ route('coordinator.hours.index') }}"
          style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

        <select name="status"
                style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);
                       color:var(--text);font-size:13px;outline:none;font-family:inherit;cursor:pointer;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit"
                style="padding:8px 18px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;
                       font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
            Filter
        </button>

        @if(request('status'))
        <a href="{{ route('coordinator.hours.index') }}"
           style="padding:8px 14px;border:1px solid var(--border2);border-radius:8px;font-size:13px;color:var(--muted);text-decoration:none;">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
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
                    <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $log->student->name ?? '—' }}</div>
                    <div style="font-size:11.5px;color:var(--muted);">{{ $log->student->email ?? '' }}</div>
                </td>
                <td style="font-size:13px;color:var(--text);">
                    {{ $log->application->company->name ?? '—' }}
                </td>
                <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                    {{ $log->date instanceof \Carbon\Carbon ? $log->date->format('M d, Y') : \Carbon\Carbon::parse($log->date)->format('M d, Y') }}
                </td>
                <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}
                </td>
                <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                    {{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}
                </td>
                <td style="font-weight:700;color:var(--blue);font-size:13px;">
                    {{ $log->total_hours }} hrs
                </td>
                <td style="font-size:12px;color:var(--muted);max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ $log->description ?? '—' }}
                </td>
                <td>
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                        background:var(--{{ $log->status === 'approved' ? 'teal' : ($log->status === 'rejected' ? 'coral' : 'gold') }}-dim);
                        color:var(--{{ $log->status === 'approved' ? 'teal' : ($log->status === 'rejected' ? 'coral' : 'gold') }});">
                        {{ ucfirst($log->status) }}
                    </span>
                </td>
                <td>
                    @if($log->status === 'pending')
                    <div style="display:flex;gap:6px;">
                        <form method="POST" action="{{ route('coordinator.hours.approve', $log->id) }}">
                            @csrf
                            <button type="submit"
                                    style="padding:4px 12px;background:var(--teal-dim);color:var(--teal);border:1px solid var(--teal);
                                           border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;"
                                    onclick="return confirm('Approve this hour log?')">
                                Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('coordinator.hours.reject', $log->id) }}">
                            @csrf
                            <button type="submit"
                                    style="padding:4px 12px;background:var(--coral-dim);color:var(--coral);border:1px solid var(--coral);
                                           border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;"
                                    onclick="return confirm('Reject this hour log?')">
                                Reject
                            </button>
                        </form>
                    </div>
                    @else
                    <span style="font-size:12px;color:var(--muted);">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:50px;color:var(--muted);">
                    <div style="font-size:28px;margin-bottom:10px;">🕐</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">No hour logs found</div>
                    <div style="font-size:12px;">Try adjusting your filters.</div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="padding:16px 20px;border-top:1px solid var(--border2);">
        {{ $logs->links() }}
    </div>
</div>

@endsection