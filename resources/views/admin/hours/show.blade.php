@extends('layouts.app')
@section('title', 'Hours — ' . $student->name)
@section('page-title', 'Hours Monitoring')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- BACK --}}
<a href="{{ route('admin.hours.index') }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;margin-bottom:20px;transition:color 0.15s;"
   onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
    Back to hours overview
</a>

{{-- STUDENT + PROGRESS HEADER --}}
<div class="card fade-up" style="margin-bottom:16px;">
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="width:48px;height:48px;border-radius:50%;background:rgba(240,180,41,0.1);border:2px solid rgba(240,180,41,0.25);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--gold);flex-shrink:0;">
            {{ strtoupper(substr($student->name, 0, 2)) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:16px;font-weight:600;color:var(--text);">{{ $student->name }}</div>
            <div style="font-size:12.5px;color:var(--muted);margin-top:2px;display:flex;align-items:center;gap:8px;">
                <span style="font-family:'DM Mono',monospace;">{{ $student->email }}</span>
                <span style="color:var(--border2);">·</span>
                <span>{{ $application->company->name }}</span>
            </div>
        </div>
        @if(\App\Models\HourLog::where('student_id', $student->id)->where('status', 'pending')->exists())
        <form method="POST" action="{{ route('admin.hours.approve-all', $student) }}">
            @csrf
            <button type="submit"
                style="padding:9px 18px;background:var(--teal);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px;transition:opacity 0.15s;"
                onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                Approve all pending
            </button>
        </form>
        @endif
    </div>

    {{-- Stats strip --}}
    @php
        $required = $application->required_hours;
        $loggedPct  = $required > 0 ? min(100, round(($totalLogged / $required) * 100)) : 0;
        $approvedPct = $required > 0 ? min(100, round(($totalApproved / $required) * 100)) : 0;
    @endphp

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;">
        <div style="padding:20px 24px;border-right:1px solid var(--border);">
            <div style="font-size:10px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Required</div>
            <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--text);line-height:1;">{{ number_format($required) }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;">hours total</div>
        </div>
        <div style="padding:20px 24px;border-right:1px solid var(--border);">
            <div style="font-size:10px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Total logged</div>
            <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--blue);line-height:1;">{{ number_format($totalLogged, 1) }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $loggedPct }}% of required</div>
        </div>
        <div style="padding:20px 24px;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Approved</div>
            <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--teal);line-height:1;">{{ number_format($totalApproved, 1) }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $approvedPct }}% of required</div>
        </div>
    </div>

    {{-- Progress bars --}}
    <div style="padding:16px 24px 20px;">
        <div style="margin-bottom:12px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
                <span style="display:flex;align-items:center;gap:5px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--teal);display:inline-block;"></span>
                    Approved progress
                </span>
                <span style="font-family:'DM Mono',monospace;font-size:11px;">{{ $approvedPct }}%</span>
            </div>
            <div style="height:8px;border-radius:4px;background:var(--border2);overflow:hidden;">
                <div style="height:100%;width:{{ $approvedPct }}%;background:{{ $approvedPct >= 100 ? 'var(--teal)' : ($approvedPct >= 50 ? 'var(--blue)' : 'var(--gold)') }};border-radius:4px;transition:width 0.4s ease;"></div>
            </div>
        </div>
        <div>
            <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-bottom:6px;">
                <span style="display:flex;align-items:center;gap:5px;">
                    <span style="width:8px;height:8px;border-radius:50%;background:var(--blue);opacity:0.5;display:inline-block;"></span>
                    Total logged (incl. pending)
                </span>
                <span style="font-family:'DM Mono',monospace;font-size:11px;">{{ $loggedPct }}%</span>
            </div>
            <div style="height:4px;border-radius:4px;background:var(--border2);overflow:hidden;">
                <div style="height:100%;width:{{ $loggedPct }}%;background:var(--blue);opacity:0.5;border-radius:4px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.hours.show', $student) }}" style="display:flex;gap:8px;align-items:center;">
        <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;">
            <option value="">All logs</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
        </select>
        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;font-family:inherit;transition:border-color 0.15s;"
            onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">Filter</button>
        @if(request('status'))
            <a href="{{ route('admin.hours.show', $student) }}" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;transition:color 0.15s;"
               onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">Clear ×</a>
        @endif
    </form>
</div>

{{-- LOGS TABLE --}}
<div class="card fade-up fade-up-1">
    <div class="card-header">
        <div class="card-title">Daily hour logs</div>
        @if(request('status'))
            <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted);">
                Filtered: {{ ucfirst(request('status')) }}
            </span>
        @endif
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
                    <td>
                        <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $log->date->format('M d, Y') }}</div>
                        <div style="font-size:11px;color:var(--muted);">{{ $log->date->format('l') }}</div>
                    </td>
                    <td style="font-size:13px;font-family:'DM Mono',monospace;">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td style="font-size:13px;font-family:'DM Mono',monospace;">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--blue);">{{ number_format($log->total_hours, 1) }}</span>
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
                                style="padding:5px 12px;border-radius:6px;border:1px solid var(--teal);background:none;cursor:pointer;font-size:11px;color:var(--teal);font-family:inherit;transition:background 0.15s;"
                                onmouseover="this.style.background='var(--teal-dim)'" onmouseout="this.style.background='none'">
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
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">
                        <div style="margin-bottom:8px;">
                            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="opacity:0.3;margin:0 auto;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
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
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;opacity:0.5;">← Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;transition:border-color 0.15s;"
                   onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">← Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;transition:border-color 0.15s;"
                   onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;opacity:0.5;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection