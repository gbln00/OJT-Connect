@extends('layouts.student-app')
@section('title', 'My Evaluation')
@section('page-title', 'My Evaluation')

@section('content')

{{-- PAGE HEADER --}}
<div class="greeting fade-up">
    <div class="greeting-sub">Performance Review</div>
    <div class="greeting-title">
        My <span>Evaluation</span>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     STATE 1 — No application at all
══════════════════════════════════════════════════ --}}
@if(! $application)
    <div class="card fade-up fade-up-1" style="padding:56px 32px;text-align:center;">
        <div style="width:52px;height:52px;border:1px solid var(--border2);background:var(--surface2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--muted);">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14,2 14,8 20,8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
        </div>
        <div style="font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--text);margin-bottom:8px;">No Active Application</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:24px;max-width:380px;margin-left:auto;margin-right:auto;">
            You need an approved OJT application before an evaluation can be submitted by your supervisor.
        </div>
        <a href="{{ route('student.application.create') }}" class="btn btn-primary">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Apply Now
        </a>
    </div>

{{-- ══════════════════════════════════════════════════
     STATE 2 — Application exists but no evaluation yet
══════════════════════════════════════════════════ --}}
@elseif(! $evaluation)

    {{-- Application info strip --}}
    <div class="card fade-up fade-up-1" style="margin-bottom:16px;">
        <div style="padding:16px 20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <div class="stat-icon teal" style="flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $application->company->name }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;margin-top:2px;">
                        {{ $application->company->address }}
                    </div>
                </div>
            </div>
            @php
                $sCls = match($application->status) {
                    'approved'  => 'green',
                    'rejected'  => 'crimson',
                    'completed' => 'teal',
                    default     => 'gold',
                };
            @endphp
            <span class="status-pill {{ $sCls }}">{{ $application->status_label }}</span>
        </div>
    </div>

    <div class="card fade-up fade-up-2" style="padding:56px 32px;text-align:center;">
        <div style="width:52px;height:52px;border:1px solid var(--gold-border);background:var(--gold-dim);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--gold-color);">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12,6 12,12 16,14"/>
            </svg>
        </div>
        <div style="font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--text);margin-bottom:8px;">Evaluation Pending</div>
        <div style="font-size:13px;color:var(--muted);max-width:420px;margin:0 auto;">
            Your supervisor has not submitted an evaluation yet. You'll see your performance scores and feedback here once they do.
        </div>
    </div>

{{-- ══════════════════════════════════════════════════
     STATE 3 — Evaluation submitted
══════════════════════════════════════════════════ --}}
@else

    @php
        /*
         | attendance_rating  — integer 1–5
         | performance_rating — integer 1–5
         | overall_grade      — decimal (0–100)
         | recommendation     — 'pass' | 'fail'
         | remarks            — string|null
         */

        $ratingLabel = function($val) {
            return match(true) {
                $val >= 5 => ['Excellent', 'teal'],
                $val >= 4 => ['Very Good', 'blue'],
                $val >= 3 => ['Good',      'green'],
                $val >= 2 => ['Fair',      'gold'],
                default   => ['Poor',      'crimson'],
            };
        };

        $gradeColor = match(true) {
            $evaluation->overall_grade >= 90 => 'teal',
            $evaluation->overall_grade >= 75 => 'blue',
            $evaluation->overall_grade >= 60 => 'gold',
            default                          => 'crimson',
        };

        // CSS variable names for the segment bars
        $colorVar = [
            'teal'    => 'var(--teal-color)',
            'blue'    => 'var(--blue-color)',
            'green'   => '#34d399',
            'gold'    => 'var(--gold-color)',
            'crimson' => 'var(--crimson)',
        ];

        [$attendLabel, $attendCls] = $ratingLabel($evaluation->attendance_rating);
        [$perfLabel,   $perfCls]   = $ratingLabel($evaluation->performance_rating);

        $recCls   = $evaluation->recommendation === 'pass' ? 'teal' : 'coral';
        $recLabel = $evaluation->recommendation_label;

        $grade  = (float) $evaluation->overall_grade;
        $dash   = round(2 * 3.14159 * 54);
        $filled = round($dash * $grade / 100);
    @endphp

    {{-- ── TOP META STRIP ──────────────────────────────────── --}}
    <div class="card fade-up fade-up-1" style="margin-bottom:16px;">
        <div style="padding:16px 20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0;">
                <div class="stat-icon teal" style="flex-shrink:0;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/>
                        <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $application->company->name }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:0.08em;margin-top:2px;">
                        Evaluated by: {{ $evaluation->supervisor->name }}
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
                @if($evaluation->submitted_at)
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                        {{ $evaluation->submitted_at->format('M d, Y') }}
                    </span>
                @endif
                <span class="status-pill green">Evaluated</span>
            </div>
        </div>
    </div>

    {{-- ── MAIN GRID ────────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:start;" class="fade-up fade-up-2">

        {{-- LEFT — Overall grade + recommendation --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            <div class="card" style="padding:28px 20px;text-align:center;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;right:0;height:3px;background:var(--crimson);"></div>

                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:18px;">
                    Overall Grade
                </div>

                {{-- Score ring --}}
                <div style="position:relative;width:128px;height:128px;margin:0 auto 18px;">
                    <svg width="128" height="128" viewBox="0 0 128 128" style="transform:rotate(-90deg);">
                        <circle cx="64" cy="64" r="54" fill="none" stroke="var(--border2)" stroke-width="8"/>
                        <circle cx="64" cy="64" r="54" fill="none" stroke="var(--crimson)" stroke-width="8"
                                stroke-dasharray="{{ $filled }} {{ $dash - $filled }}"
                                stroke-linecap="square"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:var(--text);line-height:1;">
                            {{ number_format($grade, 1) }}
                        </div>
                        <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:3px;">/ 100</div>
                    </div>
                </div>

                {{-- Recommendation badge --}}
                <span class="status-pill {{ $recCls }}" style="display:inline-flex;margin-bottom:18px;font-size:12px;padding:5px 14px;">
                    {{ $recLabel }}
                </span>

                <div style="height:1px;background:var(--border);margin-bottom:14px;"></div>

                {{-- Quick summary --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:11px;color:var(--text2);">Attendance</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:var(--text);">
                        {{ $evaluation->attendance_rating }}/5
                    </span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:11px;color:var(--text2);">Performance</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:var(--text);">
                        {{ $evaluation->performance_rating }}/5
                    </span>
                </div>
            </div>

            {{-- Remarks --}}
            @if($evaluation->remarks)
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Supervisor Remarks</div>
                </div>
                <div style="padding:16px 20px;">
                    <div style="font-size:13px;color:var(--text2);line-height:1.7;font-style:italic;border-left:2px solid var(--crimson);padding-left:14px;">
                        "{{ $evaluation->remarks }}"
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT — Individual rating cards --}}
        <div style="display:flex;flex-direction:column;gap:12px;">

            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);padding:0 2px 4px;">
                Rating Details
            </div>

            {{-- Attendance Rating --}}
            <div class="card" style="padding:20px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div class="stat-icon {{ $attendCls }}" style="width:36px;height:36px;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M9 16l2 2 4-4"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text);">
                            Attendance &amp; Punctuality
                        </div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            Consistency and timeliness in reporting for duty
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                        <span class="status-pill {{ $attendCls }}">{{ $attendLabel }}</span>
                        <span style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">
                            {{ $evaluation->attendance_rating }}
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);align-self:flex-end;padding-bottom:4px;">/5</span>
                    </div>
                </div>
                {{-- Segment bar --}}
                <div style="display:flex;gap:4px;margin-bottom:6px;">
                    @for($i = 1; $i <= 5; $i++)
                        <div style="flex:1;height:5px;background:{{ $i <= $evaluation->attendance_rating ? $colorVar[$attendCls] : 'var(--border2)' }};"></div>
                    @endfor
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">Poor</span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">Excellent</span>
                </div>
            </div>

            {{-- Performance Rating --}}
            <div class="card" style="padding:20px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div class="stat-icon {{ $perfCls }}" style="width:36px;height:36px;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text);">
                            Work Performance
                        </div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            Quality of work, initiative, and overall output
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                        <span class="status-pill {{ $perfCls }}">{{ $perfLabel }}</span>
                        <span style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">
                            {{ $evaluation->performance_rating }}
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);align-self:flex-end;padding-bottom:4px;">/5</span>
                    </div>
                </div>
                <div style="display:flex;gap:4px;margin-bottom:6px;">
                    @for($i = 1; $i <= 5; $i++)
                        <div style="flex:1;height:5px;background:{{ $i <= $evaluation->performance_rating ? $colorVar[$perfCls] : 'var(--border2)' }};"></div>
                    @endfor
                </div>
                <div style="display:flex;justify-content:space-between;">
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">Poor</span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">Excellent</span>
                </div>
            </div>

            {{-- Overall Grade --}}
            <div class="card" style="padding:20px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                    <div class="stat-icon {{ $gradeColor }}" style="width:36px;height:36px;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                            <polyline points="22,4 12,14.01 9,11.01"/>
                        </svg>
                    </div>
                    <div style="flex:1;">
                        <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text);">
                            Computed Overall Grade
                        </div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            Final grade as computed by your supervisor
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                        <span style="font-family:'Playfair Display',serif;font-size:28px;font-weight:900;color:var(--text);">
                            {{ number_format($evaluation->overall_grade, 2) }}
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);align-self:flex-end;padding-bottom:4px;">/100</span>
                    </div>
                </div>
                <div class="progress-track">
                    <div class="progress-fill {{ $gradeColor }}" style="width:{{ min($evaluation->overall_grade, 100) }}%;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:5px;">
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">0</span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">100</span>
                </div>
            </div>

        </div>
    </div>

@endif

@endsection