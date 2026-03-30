{{-- resources/views/supervisor/hours/index.blade.php --}}
@extends('layouts.supervisor-app')

@section('title', 'Hour Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Intern Hour Logs</h1>
        <p class="text-muted mb-0">Review and approve time-in/out entries from your interns</p>
    </div>
    <div class="d-flex gap-2 text-center">
        <div class="stat-chip pending">
            <div class="fw-bold">{{ $pending }}</div>
            <div class="small">Pending</div>
        </div>
        <div class="stat-chip approved">
            <div class="fw-bold">{{ $approved }}</div>
            <div class="small">Approved</div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="card mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-sm-5">
                <label class="form-label small">Filter by intern</label>
                <select name="student_id" class="form-select form-select-sm">
                    <option value="">All interns</option>
                    @foreach ($interns as $intern)
                        <option value="{{ $intern->id }}" {{ request('student_id') == $intern->id ? 'selected' : '' }}>
                            {{ $intern->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-sm-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </div>
    </div>
</form>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Log table --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Intern</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>
                            <a href="{{ route('supervisor.hours.show', $log->student_id) }}" class="text-decoration-none fw-semibold">
                                {{ $log->student->name }}
                            </a>
                        </td>
                        <td>{{ $log->date->format('M d, Y') }}</td>
                        <td>
                            <span class="session-badge {{ $log->session }}">
                                {{ $log->session === 'morning' ? 'AM' : 'PM' }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                        <td>{{ number_format($log->total_hours, 1) }}</td>
                        <td>
                            <span class="badge
                                @if($log->status === 'approved') bg-success
                                @elseif($log->status === 'rejected') bg-danger
                                @else bg-warning text-dark
                                @endif
                            ">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td>
                            @if ($log->isPending())
                                <div class="d-flex gap-1">
                                    <form method="POST" action="{{ route('supervisor.hours.approve', $log) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs btn-success">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('supervisor.hours.reject', $log) }}">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-xs btn-outline-danger">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $logs->withQueryString()->links() }}</div>

<style>
.stat-chip { padding: 8px 16px; border-radius: 10px; min-width: 80px; }
.stat-chip.pending  { background: #FEF9C3; color: #713F12; }
.stat-chip.approved { background: #D1FAE5; color: #064E3B; }
.session-badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:12px; font-weight:600; }
.session-badge.morning   { background:#FEF3C7; color:#92400E; }
.session-badge.afternoon { background:#DBEAFE; color:#1E40AF; }
.btn-xs { padding: 2px 8px; font-size: 12px; }
</style>
@endsection