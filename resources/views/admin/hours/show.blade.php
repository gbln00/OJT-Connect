@extends('layouts.app')
@section('title', 'Hours — ' . $student->name)
@section('page-title', 'Hours Monitoring')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- BACK --}}
<a href="{{ route('admin.hours.index') }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;margin-bottom:20px;"
   onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
    Back to hours overview
</a>

{{-- STUDENT + PROGRESS HEADER --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;flex-wrap:wrap;">
        <div style="width:52px;height:52px;border-radius:50%;background:var(--gold-dim);border:2px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:600;color:var(--gold);flex-shrink:0;">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div>
            <div style="font-size:17px;font-weight:600;color:var(--text);">{{ $student->name }}</div>
            <div style="font-size:12.5px;color:var(--muted);margin-top:2px;">{{ $student->email }} · {{ $application->company->name }}</div>
        </div>
        <div style="margin-left:auto;display:flex;gap:8px;">
            {{-- Approve all pending --}}
            @if(\App\Models\HourLog::where('student_id', $student->id)->where('status', 'pending')->exists())
            <form method="POST" action="{{ route('admin.hours.approveAll', $student) }}">
                @csrf
                <button type="submit"
                    style="padding:8px 16px;background:var(--teal);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                    Approve all pending
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Progress bars --}}
    @php
        $required = $application->required_hours;
        $loggedPct  = $required > 0 ? min(100, round(($totalLogged / $required) * 100)) : 0;
        $approvedPct = $required > 0 ? min(100, round(($totalApproved / $required) * 100)) : 0;
    @endphp

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Required hours</div>
            <div style="font-size:22px;font-weight:600;color:var(--text);">{{ number_format($required) }}</div>
        </div>
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Total logged</div>
            <div style="font-size:22px;font-weight:600;color:var(--blue);">{{ number_format($totalLogged, 1) }}</div>
        </div>
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Approved hours</div>
            <div style="font-size:22px;font-weight:600;color:var(--teal);">{{ number_format($totalApproved, 1) }}</div>
        </div>
    </div>

    {{-- Progress bar --}}
    <div style="margin-bottom:8px;">
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
            <span>Approved progress</span>
            <span>{{ $approvedPct }}% of {{ number_format($required) }} hrs</span>
        </div>
        <div style="height:8px;border-radius:4px;background:var(--border2);overflow:hidden;">
            <div style="height:100%;width:{{ $approvedPct }}%;background:{{ $approvedPct >= 100 ? 'var(--teal)' : ($approvedPct >= 50 ? 'var(--blue)' : 'var(--gold)') }};border-radius:4px;transition:width 0.3s;"></div>
        </div>
    </div>
    <div>
        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
            <span>Total logged (incl. pending)</span>
            <span>{{ $loggedPct }}%</span>
        </div>
        <div style="height:4px;border-radius:4px;background:var(--border2);overflow:hidden;">
            <div style="height:100%;width:{{ $loggedPct }}%;background:var(--blue);opacity:0.5;border-radius:4px;"></div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.hours.show', $student) }}" style="display:flex;gap:8px;">
        <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All logs</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
        </select>
        <button type="submit" style="padding:8px 14px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">Filter</button>
        @if(request('status'))
            <a href="{{ route('admin.hours.show', $student) }}" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;">Clear</a>
        @endif
    </form>
</div>

{{-- LOGS TABLE --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Daily hour logs</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time in</th>
                    <th>Time out</th>
                    <th>Hours</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:13px;color:var(--text);font-weight:500;">
                        {{ $log->date->format('M d, Y') }}
                        <div style="font-size:11px;color:var(--muted);">{{ $log->date->format('l') }}</div>
                    </td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                    <td>
                        <span style="font-size:13px;font-weight:600;color:var(--blue);">{{ number_format($log->total_hours, 1) }}</span>
                        <span style="font-size:11px;color:var(--muted);"> hrs</span>
                    </td>
                    <td style="font-size:12.5px;color:var(--muted2);max-width:220px;">
                        {{ $log->description ? Str::limit($log->description, 60) : '—' }}
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                            background:var(--{{ $log->status_class }}-dim);color:var(--{{ $log->status_class }});">
                            {{ ucfirst($log->status) }}
                        </span>
                    </td>
                    <td>
                        @if($log->isPending())
                        <form method="POST" action="{{ route('admin.hours.approve', $log) }}">
                            @csrf
                            <button type="submit"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--teal);font-family:inherit;">
                                Approve
                            </button>
                        </form>
                        @else
                        <span style="font-size:11px;color:var(--muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        No hour logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} logs
        </span>
        <div style="display:flex;gap:4px;">
            @if($logs->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection