{{-- resources/views/supervisor/hours/show.blade.php --}}
@extends('layouts.supervisor-app')

@section('title', $student->name . ' — Hour Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('supervisor.hours.index') }}" class="text-muted small text-decoration-none">← All interns</a>
        <h1 class="h4 mb-0 mt-1">{{ $student->name }}</h1>
        <p class="text-muted mb-0 small">{{ $application->company->name }} · {{ $application->program }}</p>
    </div>

    @if ($stats['pending_count'] > 0)
        <form method="POST" action="{{ route('supervisor.hours.approveAll', $student) }}">
            @csrf @method('PATCH')
            <button class="btn btn-success btn-sm">
                Approve All Pending ({{ $stats['pending_count'] }})
            </button>
        </form>
    @endif
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div class="h5 mb-0">{{ number_format($stats['total_approved'], 1) }}</div>
            <div class="text-muted small">Approved hrs</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div class="h5 mb-0">{{ number_format($application->required_hours) }}</div>
            <div class="text-muted small">Required hrs</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            @php $pct = $application->required_hours > 0 ? min(100, round(($stats['total_approved'] / $application->required_hours) * 100)) : 0; @endphp
            <div class="h5 mb-0">{{ $pct }}%</div>
            <div class="text-muted small">Progress</div>
        </div>
    </div>
</div>

{{-- Status filter --}}
<form method="GET" class="mb-3 d-flex gap-2">
    @foreach (['', 'pending', 'approved', 'rejected'] as $s)
        <a
            href="{{ route('supervisor.hours.show', [$student, 'status' => $s]) }}"
            class="btn btn-sm {{ request('status', '') === $s ? 'btn-primary' : 'btn-outline-secondary' }}"
        >
            {{ $s === '' ? 'All' : ucfirst($s) }}
        </a>
    @endforeach
</form>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Daily grouped logs --}}
@forelse ($groupedByDate as $date => $sessions)
    @php
        $dateLabel = \Carbon\Carbon::parse($date)->format('l, F j, Y');
        $dayTotal  = collect($sessions)->sum('total_hours');
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold">{{ $dateLabel }}</span>
            <span class="text-muted small">{{ number_format($dayTotal, 1) }} hrs logged</span>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:110px">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['morning', 'afternoon'] as $session)
                        @if (isset($sessions[$session]))
                            @php $log = $sessions[$session]; @endphp
                            <tr>
                                <td>
                                    <span class="session-badge {{ $session }}">
                                        {{ $session === 'morning' ? 'Morning' : 'Afternoon' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                                <td>{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                                <td>{{ number_format($log->total_hours, 1) }}</td>
                                <td class="text-muted small">{{ $log->description ?? '—' }}</td>
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
                                    @elseif ($log->isApproved())
                                        <span class="text-success small">✓ Approved</span>
                                    @else
                                        <span class="text-danger small">✗ Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr class="text-muted">
                                <td>
                                    <span class="session-badge {{ $session }} faded">
                                        {{ $session === 'morning' ? 'Morning' : 'Afternoon' }}
                                    </span>
                                </td>
                                <td colspan="6" class="small">No {{ $session }} session submitted</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">No logs found for the selected filter.</div>
@endforelse

<div class="mt-3">{{ $logs->withQueryString()->links() }}</div>

<style>
.session-badge { display:inline-block; padding:2px 9px; border-radius:20px; font-size:12px; font-weight:600; }
.session-badge.morning         { background:#FEF3C7; color:#92400E; }
.session-badge.afternoon       { background:#DBEAFE; color:#1E40AF; }
.session-badge.faded.morning   { background:#FEF9EC; color:#C7A560; }
.session-badge.faded.afternoon { background:#EFF6FF; color:#93C5FD; }
.btn-xs { padding:2px 8px; font-size:12px; }
</style>
@endsection