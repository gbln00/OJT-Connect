{{-- resources/views/student/hours/index.blade.php --}}
@extends('layouts.student-app')

@section('title', 'My Hour Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">Hour Logs</h1>
        <p class="text-muted mb-0">{{ $application->company->name }}</p>
    </div>
    <a href="{{ route('student.hours.create') }}" class="btn btn-primary">
        + Log Today's Hours
    </a>
</div>

{{-- Progress summary --}}
@php
    $required  = $application->required_hours;
    $pct       = $required > 0 ? min(100, round(($totalApproved / $required) * 100)) : 0;
@endphp
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between mb-1">
            <span class="fw-semibold">Approved Hours Progress</span>
            <span class="text-muted small">{{ number_format($totalApproved, 1) }} / {{ number_format($required) }} hrs ({{ $pct }}%)</span>
        </div>
        <div class="progress" style="height: 10px;">
            <div
                class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-primary' : 'bg-warning') }}"
                style="width: {{ $pct }}%"
            ></div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
@endif

{{-- Daily grouped logs --}}
@forelse ($groupedByDate as $date => $sessions)
    @php $dateLabel = \Carbon\Carbon::parse($date)->format('l, F j, Y'); @endphp
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold">{{ $dateLabel }}</span>
            @php
                $dayTotal = collect($sessions)->sum('total_hours');
            @endphp
            <span class="badge bg-secondary">{{ number_format($dayTotal, 1) }} hrs</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-borderless mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:110px">Session</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Hours</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (['morning', 'afternoon'] as $session)
                        @if (isset($sessions[$session]))
                            @php $log = $sessions[$session]; @endphp
                            <tr>
                                <td>
                                    <span class="session-badge {{ $session }}">
                                        {{ $session === 'morning' ? 'AM' : 'PM' }}
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
                            </tr>
                        @else
                            <tr class="text-muted">
                                <td>
                                    <span class="session-badge {{ $session }} faded">
                                        {{ $session === 'morning' ? 'AM' : 'PM' }}
                                    </span>
                                </td>
                                <td colspan="5" class="small">No {{ $session }} session logged</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@empty
    <div class="text-center text-muted py-5">
        <p class="mb-2">No hour logs yet.</p>
        <a href="{{ route('student.hours.create') }}" class="btn btn-primary btn-sm">Log your first day</a>
    </div>
@endforelse

{{ $logs->links() }}

<style>
.session-badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.session-badge.morning         { background: #FEF3C7; color: #92400E; }
.session-badge.afternoon       { background: #DBEAFE; color: #1E40AF; }
.session-badge.faded.morning   { background: #FEF9EC; color: #C7A560; }
.session-badge.faded.afternoon { background: #EFF6FF; color: #93C5FD; }
</style>
@endsection