@extends('layouts.supervisor-app')
@section('title', 'Evaluations')
@section('page-title', 'Evaluations')
@section('content')

@php
    $evaluated    = $applications->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $applications->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $applications->count();
@endphp

{{-- Progress card --}}
<div class="card" style="padding:20px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-size:12px;font-weight:600;color:var(--muted);letter-spacing:.5px;">EVALUATION PROGRESS</div>
        <div style="font-size:12px;color:var(--muted);">
            <span style="font-weight:700;color:var(--text);">{{ $evaluated }}</span> of
            <span style="font-weight:700;color:var(--text);">{{ $total }}</span> interns evaluated
        </div>
    </div>
    @php $pct = $total > 0 ? round(($evaluated / $total) * 100) : 0; @endphp
    <div style="height:8px;background:var(--border2);border-radius:6px;">
        <div style="height:100%;width:{{ $pct }}%;background:var(--teal);border-radius:6px;transition:width .4s;"></div>
    </div>
    <div style="display:flex;gap:24px;margin-top:14px;">
        <div style="font-size:12px;color:var(--muted);">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--teal);margin-right:5px;"></span>
            Evaluated &nbsp;<strong style="color:var(--text);">{{ $evaluated }}</strong>
        </div>
        <div style="font-size:12px;color:var(--muted);">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--gold);margin-right:5px;"></span>
            Pending &nbsp;<strong style="color:var(--text);">{{ $notEvaluated }}</strong>
        </div>
    </div>
</div>

{{-- Intern cards --}}
@forelse($applications as $app)
<div class="card" style="padding:20px;margin-bottom:12px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">

        {{-- Left: student info --}}
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;border-radius:50%;background:var(--gold-dim);
                        border:2px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;
                        font-size:15px;font-weight:700;color:var(--gold);flex-shrink:0;">
                {{ strtoupper(substr($app->student->name ?? 'S', 0, 2)) }}
            </div>
            <div>
                <div style="font-size:13px;font-weight:700;color:var(--text);">
                    {{ $app->student->name ?? '—' }}
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    {{ $app->student->email ?? '' }}
                </div>
                <div style="font-size:11.5px;color:var(--muted);margin-top:3px;">
                    Required: <span style="color:var(--blue);font-weight:600;">{{ number_format($app->required_hours) }} hrs</span>
                    &nbsp;·&nbsp; Since {{ $app->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Right: status + action --}}
        <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
            @if($app->evaluation)
                {{-- Already evaluated --}}
                <div style="text-align:right;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div>
                            <div style="font-size:11px;color:var(--muted);letter-spacing:.5px;">GRADE</div>
                            <div style="font-size:18px;font-weight:800;color:var(--blue);">
                                {{ number_format($app->evaluation->overall_grade, 1) }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--muted);letter-spacing:.5px;">RESULT</div>
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                background:var(--{{ $app->evaluation->recommendation === 'pass' ? 'teal' : 'coral' }}-dim);
                                color:var(--{{ $app->evaluation->recommendation === 'pass' ? 'teal' : 'coral' }});">
                                {{ ucfirst($app->evaluation->recommendation) }}
                            </span>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--muted);letter-spacing:.5px;">ATTENDANCE</div>
                            <div style="font-size:13px;font-weight:600;color:var(--text);">
                                {{ $app->evaluation->attendance_rating }}/5
                            </div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:var(--muted);letter-spacing:.5px;">PERFORMANCE</div>
                            <div style="font-size:13px;font-weight:600;color:var(--text);">
                                {{ $app->evaluation->performance_rating }}/5
                            </div>
                        </div>
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:6px;text-align:right;">
                        Submitted {{ $app->evaluation->submitted_at?->format('M d, Y') ?? '—' }}
                    </div>
                </div>
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                    background:var(--teal-dim);color:var(--teal);">
                    ✓ Evaluated
                </span>
            @else
                {{-- Not yet evaluated --}}
                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                    background:var(--gold-dim);color:var(--gold);">
                    Pending
                </span>
                <a href="{{ route('supervisor.evaluations.create', $app->id) }}"
                   style="padding:8px 18px;background:var(--gold);color:var(--bg);border-radius:8px;
                          font-size:13px;font-weight:600;text-decoration:none;white-space:nowrap;">
                    Evaluate
                </a>
            @endif
        </div>

    </div>

    {{-- Remarks preview (if evaluated and has remarks) --}}
    @if($app->evaluation?->remarks)
    <div style="margin-top:14px;padding:10px 14px;background:var(--surface2);border-left:3px solid var(--border2);
                border-radius:0 6px 6px 0;font-size:12px;color:var(--muted);line-height:1.5;">
        <span style="font-weight:600;color:var(--muted2);">Remarks: </span>{{ $app->evaluation->remarks }}
    </div>
    @endif

</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-size:32px;margin-bottom:12px;">👥</div>
    <div style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px;">No active interns yet</div>
    <div style="font-size:13px;color:var(--muted);">
        Interns will appear here once their applications are approved by the coordinator.
    </div>
</div>
@endforelse

@endsection