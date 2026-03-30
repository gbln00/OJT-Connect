@extends('layouts.supervisor-app')
@section('title', 'Evaluations')
@section('page-title', 'Evaluations')

@section('content')

@php
    $evaluated    = $applications->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $applications->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $applications->count();
    $pct          = $total > 0 ? round(($evaluated / $total) * 100) : 0;
@endphp

{{-- EYEBROW --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Evaluations / Overview
    </span>
</div>

{{-- STAT STRIP --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);margin-bottom:16px;">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon teal">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
            </svg>
        </div><span class="stat-tag">total</span></div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Total interns</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon gold">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div><span class="stat-tag">pending</span></div>
        <div class="stat-num">{{ $notEvaluated }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon blue">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <polyline points="20,6 9,17 4,12"/>
            </svg>
        </div><span class="stat-tag">done</span></div>
        <div class="stat-num">{{ $evaluated }}</div>
        <div class="stat-label">Evaluated</div>
    </div>
</div>

{{-- PROGRESS CARD --}}
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <div class="card-header">
        <div class="card-title">Evaluation progress</div>
        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
            <strong style="color:var(--text);">{{ $evaluated }}</strong> of <strong style="color:var(--text);">{{ $total }}</strong> completed
        </span>
    </div>
    <div style="padding:16px 20px;">
        <div class="progress-track" style="height:8px;">
            <div class="progress-fill teal" style="width:{{ $pct }}%;"></div>
        </div>
        <div style="display:flex;gap:24px;margin-top:12px;">
            <div style="display:flex;align-items:center;gap:7px;">
                <span style="width:8px;height:8px;background:var(--teal);display:inline-block;"></span>
                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    Evaluated <strong style="color:var(--text);">{{ $evaluated }}</strong>
                </span>
            </div>
            <div style="display:flex;align-items:center;gap:7px;">
                <span style="width:8px;height:8px;background:var(--gold);display:inline-block;"></span>
                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    Pending <strong style="color:var(--text);">{{ $notEvaluated }}</strong>
                </span>
            </div>
            <div style="display:flex;align-items:center;gap:7px;">
                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    {{ $pct }}% complete
                </span>
            </div>
        </div>
    </div>
</div>

{{-- EVALUATION ROWS --}}
<div class="fade-up fade-up-3">
@forelse($applications as $app)
<div class="card" style="margin-bottom:10px;">

    <div style="padding:20px;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">

        {{-- Student info --}}
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;flex-shrink:0;border:1px solid var(--gold-border);
                        background:var(--gold-dim);display:flex;align-items:center;justify-content:center;
                        font-family:'Playfair Display',serif;font-size:16px;font-weight:900;color:var(--gold);">
                {{ strtoupper(substr($app->student->name ?? 'S', 0, 2)) }}
            </div>
            <div>
                <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $app->student->name ?? '—' }}</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:2px;">{{ $app->student->email ?? '' }}</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--blue);margin-top:3px;">
                    {{ number_format($app->required_hours) }} hrs required
                    <span style="color:var(--muted);margin-left:8px;">· Since {{ $app->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Right: status + action --}}
        <div style="display:flex;align-items:center;gap:14px;flex-shrink:0;flex-wrap:wrap;">
            @if($app->evaluation)
                <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Grade</div>
                        <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--blue);">
                            {{ number_format($app->evaluation->overall_grade, 1) }}
                        </div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Result</div>
                        <span class="status-pill {{ $app->evaluation->recommendation === 'pass' ? 'teal' : 'coral' }}">
                            {{ ucfirst($app->evaluation->recommendation) }}
                        </span>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Attendance</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $app->evaluation->attendance_rating }}/5</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Performance</div>
                        <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $app->evaluation->performance_rating }}/5</div>
                    </div>
                </div>
                <span class="status-pill teal">✓ Evaluated</span>
            @else
                <span class="status-pill gold">Pending</span>
                <a href="{{ route('supervisor.evaluations.create', $app->id) }}" class="btn btn-gold btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                    </svg>
                    Evaluate
                </a>
            @endif
        </div>

    </div>

    {{-- Remarks + submitted date --}}
    @if($app->evaluation)
    <div style="padding:10px 20px;border-top:1px solid var(--border);background:var(--surface2);
                display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        @if($app->evaluation->remarks)
        <div style="font-size:12px;color:var(--muted);border-left:2px solid var(--border2);padding-left:12px;line-height:1.5;flex:1;min-width:200px;">
            <span style="font-weight:600;color:var(--muted2);">Remarks: </span>{{ $app->evaluation->remarks }}
        </div>
        @endif
        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);flex-shrink:0;letter-spacing:0.05em;">
            Submitted {{ $app->evaluation->submitted_at?->format('M d, Y') ?? '—' }}
        </div>
    </div>
    @endif

</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:var(--border2);margin-bottom:12px;">—</div>
    <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;">
        No active interns yet
    </div>
    <div style="font-size:13px;color:var(--muted);">
        Interns will appear here once their applications are approved by the coordinator.
    </div>
</div>
@endforelse
</div>

@endsection