@extends('layouts.app')
@section('title', 'Evaluations')
@section('page-title', 'Evaluations')

@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total evaluations</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['passed'] }}</div>
        <div class="stat-label">Passed</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['failed'] }}</div>
        <div class="stat-label">Failed</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['avg_grade'] }}</div>
        <div class="stat-label">Average grade</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.evaluations.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name or email..."
               style="flex:1;min-width:160px;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">

        <select name="recommendation" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All results</option>
            <option value="pass" {{ request('recommendation') === 'pass' ? 'selected' : '' }}>Pass</option>
            <option value="fail" {{ request('recommendation') === 'fail' ? 'selected' : '' }}>Fail</option>
        </select>

        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">
            Filter
        </button>

        @if(request()->hasAny(['search', 'recommendation']))
            <a href="{{ route('admin.evaluations.index') }}"
               style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Supervisor</th>
                    <th>Attendance</th>
                    <th>Performance</th>
                    <th>Grade</th>
                    <th>Result</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $eval)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($eval->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $eval->student->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $eval->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $eval->application->company->name }}</td>
                    <td style="font-size:13px;color:var(--muted2);">{{ $eval->supervisor->name }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            @for($i = 1; $i <= 5; $i++)
                                <div style="width:8px;height:8px;border-radius:50%;background:{{ $i <= $eval->attendance_rating ? 'var(--gold)' : 'var(--border2)' }};"></div>
                            @endfor
                            <span style="font-size:11px;color:var(--muted);margin-left:2px;">{{ $eval->attendance_rating }}/5</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            @for($i = 1; $i <= 5; $i++)
                                <div style="width:8px;height:8px;border-radius:50%;background:{{ $i <= $eval->performance_rating ? 'var(--blue)' : 'var(--border2)' }};"></div>
                            @endfor
                            <span style="font-size:11px;color:var(--muted);margin-left:2px;">{{ $eval->performance_rating }}/5</span>
                        </div>
                    </td>
                    <td>
                        <span style="font-size:15px;font-weight:600;color:{{ $eval->grade_color }};">
                            {{ number_format($eval->overall_grade, 1) }}
                        </span>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                            background:var(--{{ $eval->recommendation_class }}-dim);color:var(--{{ $eval->recommendation_class }});">
                            {{ $eval->recommendation_label }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.evaluations.show', $eval) }}"
                           style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;"
                           onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
                           View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">
                        No evaluations submitted yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($evaluations->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $evaluations->firstItem() }}–{{ $evaluations->lastItem() }} of {{ $evaluations->total() }}
        </span>
        <div style="display:flex;gap:4px;">
            @if($evaluations->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $evaluations->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($evaluations->hasMorePages())
                <a href="{{ $evaluations->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection