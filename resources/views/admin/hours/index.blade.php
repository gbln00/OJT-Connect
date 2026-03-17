@extends('layouts.app')
@section('title', 'Hours Monitoring')
@section('page-title', 'Hours Monitoring')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Active interns</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ number_format($stats['total_hours'], 1) }}</div>
        <div class="stat-label">Total hours logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $stats['total_logs'] }}</div>
        <div class="stat-label">Total log entries</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $stats['pending_logs'] }}</div>
        <div class="stat-label">Pending approval</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.hours.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name or email..."
               style="flex:1;min-width:160px;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">

        <select name="company_id" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">
            Filter
        </button>

        @if(request()->hasAny(['search', 'company_id']))
            <a href="{{ route('admin.hours.index') }}"
               style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- STUDENTS TABLE WITH PROGRESS --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Student hour progress</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Required</th>
                    <th>Logged</th>
                    <th>Approved</th>
                    <th>Progress</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                @php
                    $required  = $app->required_hours;
                    $logged    = round($app->hour_logs_sum_total_hours ?? 0, 1);
                    $approved  = \App\Models\HourLog::where('application_id', $app->id)->where('status', 'approved')->sum('total_hours');
                    $pct       = $required > 0 ? min(100, round(($approved / $required) * 100)) : 0;
                    $barColor  = $pct >= 100 ? 'var(--teal)' : ($pct >= 50 ? 'var(--blue)' : 'var(--gold)');
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($app->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $app->student->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $app->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $app->company->name }}</td>
                    <td style="font-size:13px;">{{ number_format($required) }} hrs</td>
                    <td style="font-size:13px;color:var(--blue);">{{ $logged }} hrs</td>
                    <td style="font-size:13px;color:var(--teal);">{{ round($approved, 1) }} hrs</td>
                    <td style="min-width:140px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;border-radius:4px;background:var(--border2);overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:4px;transition:width 0.3s;"></div>
                            </div>
                            <span style="font-size:11px;color:var(--muted);min-width:32px;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('admin.hours.show', $app->student) }}"
                           style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;"
                           onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
                           View logs
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        No approved interns found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }}
        </span>
        <div style="display:flex;gap:4px;">
            @if($applications->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection