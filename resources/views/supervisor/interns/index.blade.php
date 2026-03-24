@extends('layouts.supervisor-app')
@section('title', 'My Interns')
@section('page-title', 'My Interns')
@section('content')

@php
    $evaluated    = $interns->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $interns->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $interns->count();
    $company      = $interns->first()?->company;
@endphp

{{-- Header --}}
<div style="margin-bottom:24px;">
    <div style="font-size:17px;font-weight:700;color:var(--text);letter-spacing:-0.3px;">
        @if($company) {{ $company->name }} @endif
    </div>
    <div style="font-size:12.5px;color:var(--muted);margin-top:3px;">
        {{ $total }} active intern{{ $total !== 1 ? 's' : '' }} assigned to your company
    </div>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                    <path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
        </div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Total interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
        </div>
        <div class="stat-num">{{ $notEvaluated }}</div>
        <div class="stat-label">Pending evaluation</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
            </div>
        </div>
        <div class="stat-num">{{ $evaluated }}</div>
        <div class="stat-label">Evaluated</div>
    </div>

</div>

{{-- Intern cards --}}
@forelse($interns as $intern)
<div class="card" style="padding:22px;margin-bottom:12px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">

        {{-- Left: student info --}}
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--gold-dim);
                        border:2px solid rgba(240,180,41,0.3);display:flex;align-items:center;
                        justify-content:center;font-size:16px;font-weight:700;color:var(--gold);flex-shrink:0;">
                {{ strtoupper(substr($intern->student->name ?? 'S', 0, 2)) }}
            </div>
            <div>
                <div style="font-size:14px;font-weight:700;color:var(--text);">
                    {{ $intern->student->name ?? '—' }}
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    {{ $intern->student->email ?? '' }}
                </div>
                <div style="display:flex;align-items:center;gap:12px;margin-top:6px;flex-wrap:wrap;">
                    <span style="font-size:11.5px;color:var(--muted2);">
                        <span style="color:var(--muted);">Program</span> · {{ $intern->program ?? '—' }}
                    </span>
                    <span style="font-size:11.5px;color:var(--muted2);">
                        <span style="color:var(--muted);">Semester</span> · {{ $intern->semester }} {{ $intern->school_year }}
                    </span>
                    <span style="font-size:11.5px;color:var(--blue);font-weight:600;">
                        {{ number_format($intern->required_hours) }} hrs required
                    </span>
                </div>
            </div>
        </div>

        {{-- Right: evaluation status + action --}}
        <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
            @if($intern->evaluation)
                {{-- Already evaluated --}}
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="text-align:center;">
                        <div style="font-size:10px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:3px;">GRADE</div>
                        <div style="font-size:22px;font-weight:800;color:var(--blue);line-height:1;">
                            {{ number_format($intern->evaluation->overall_grade, 1) }}
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:10px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:3px;">RESULT</div>
                        <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                            background:var(--{{ $intern->evaluation->recommendation === 'pass' ? 'teal' : 'coral' }}-dim);
                            color:var(--{{ $intern->evaluation->recommendation === 'pass' ? 'teal' : 'coral' }});">
                            {{ ucfirst($intern->evaluation->recommendation) }}
                        </span>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:10px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:3px;">ATTENDANCE</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);">
                            {{ $intern->evaluation->attendance_rating }}/5
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <div style="font-size:10px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:3px;">PERFORMANCE</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);">
                            {{ $intern->evaluation->performance_rating }}/5
                        </div>
                    </div>
                </div>
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                    background:var(--teal-dim);color:var(--teal);">
                    ✓ Evaluated
                </span>
            @else
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                    background:var(--gold-dim);color:var(--gold);">
                    Pending
                </span>
                <a href="{{ route('supervisor.evaluations.create', $intern->id) }}"
                   style="padding:8px 18px;background:var(--gold);color:var(--bg);border-radius:8px;
                          font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    Evaluate
                </a>
            @endif
        </div>

    </div>

    {{-- Expandable details --}}
    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border2);
                display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">

        {{-- Hours progress --}}
        @php
            $approved = \App\Models\HourLog::where('application_id', $intern->id)
                ->where('status', 'approved')
                ->sum('total_hours');
            $pct = $intern->required_hours > 0
                ? min(100, round(($approved / $intern->required_hours) * 100))
                : 0;
            $barColor = $pct >= 100 ? 'var(--teal)' : ($pct >= 50 ? 'var(--blue)' : 'var(--gold)');
        @endphp
        <div style="flex:1;min-width:200px;">
            <div style="display:flex;justify-content:space-between;font-size:11.5px;color:var(--muted);margin-bottom:5px;">
                <span>Hours progress</span>
                <span style="color:var(--text);font-weight:600;">
                    {{ number_format($approved, 1) }} / {{ number_format($intern->required_hours) }} hrs
                </span>
            </div>
            <div style="height:6px;background:var(--border2);border-radius:4px;overflow:hidden;">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:4px;transition:width 0.4s;"></div>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px;">{{ $pct }}% complete</div>
        </div>

        {{-- Evaluation remarks preview --}}
        @if($intern->evaluation?->remarks)
        <div style="flex:2;min-width:240px;padding:9px 12px;background:var(--surface2);
                    border-left:3px solid var(--border2);border-radius:0 6px 6px 0;
                    font-size:12px;color:var(--muted);line-height:1.5;max-width:480px;">
            <span style="font-weight:600;color:var(--muted2);">Evaluation remarks: </span>
            {{ Str::limit($intern->evaluation->remarks, 120) }}
        </div>
        @endif

        {{-- Applied date --}}
        <div style="font-size:11.5px;color:var(--muted);flex-shrink:0;">
            Joined {{ $intern->created_at->format('M d, Y') }}
        </div>

    </div>
</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">👥</div>
    <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px;">No active interns yet</div>
    <div style="font-size:13px;color:var(--muted);">
        Interns will appear here once their OJT applications are approved by the coordinator.
    </div>
</div>
@endforelse

@endsection