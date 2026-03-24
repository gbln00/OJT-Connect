@extends('layouts.student-app')
@section('title', 'Hour Logs')
@section('page-title', 'Hour Logs')
@section('content')

{{-- Stats row --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:20px;">
    <div class="card" style="padding:18px;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:8px;">APPROVED HOURS</div>
        <div style="font-size:28px;font-weight:700;color:var(--teal);">{{ number_format($totalApproved,1) }}</div>
        <div style="font-size:12px;color:var(--muted);">of {{ $application->required_hours }} required</div>
    </div>
    <div class="card" style="padding:18px;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:8px;">REMAINING</div>
        <div style="font-size:28px;font-weight:700;color:var(--gold);">{{ number_format($application->remaining_hours,1) }}</div>
        <div style="font-size:12px;color:var(--muted);">hours left</div>
    </div>
    <div class="card" style="padding:18px;">
        <div style="font-size:11px;color:var(--muted);margin-bottom:8px;">PROGRESS</div>
        @php $pct = $application->required_hours > 0 ? min(100, round(($totalApproved / $application->required_hours)*100)) : 0; @endphp
        <div style="font-size:28px;font-weight:700;color:var(--blue);">{{ $pct }}%</div>
        <div style="height:6px;background:var(--border2);border-radius:4px;margin-top:6px;">
            <div style="height:100%;width:{{ $pct }}%;background:var(--blue);border-radius:4px;"></div>
        </div>
    </div>
</div>
{{-- Log button + table --}}
<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
    <a href="{{ route('student.hours.create') }}" style="padding:9px 20px;background:var(--gold);color:var(--bg);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
        + Log Hours
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Date</th><th>Time In</th><th>Time Out</th>
                <th>Hours</th><th>Description</th><th>Status</th>
            </tr></thead>
            <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->date->format('M d, Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                <td style="font-weight:600;color:var(--blue);">{{ $log->total_hours }} hrs</td>
                <td>{{ $log->description ?? '—' }}</td>
                <td><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                    background:var(--{{ $log->status_class }}-dim);color:var(--{{ $log->status_class }});">
                    {{ ucfirst($log->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                No hour logs yet. <a href="{{ route('student.hours.create') }}" style="color:var(--gold);">Log your first hours →</a>
            </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    {{ $logs->links() }}
</div>
@endsection
