@extends('layouts.supervisor-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

@php
    $evaluated    = $interns->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $interns->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $interns->count();
    $company      = $interns->first()?->company;
@endphp

{{-- Welcome bar --}}
<div style="margin-bottom:24px;">
    <div style="font-size:18px;font-weight:700;color:var(--text);letter-spacing:-0.3px;">
        Welcome back, {{ auth()->user()->name }} 👋
    </div>
    <div style="font-size:12.5px;color:var(--muted);margin-top:3px;">
        {{ now()->format('l, F d, Y') }}
        @if($company)
            · {{ $company->name }}
        @endif
    </div>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;">

    {{-- Total interns --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--teal-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">ACTIVE INTERNS</div>
        <div style="font-size:32px;font-weight:800;color:var(--teal);line-height:1;">{{ $total }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">currently assigned</div>
        <a href="{{ route('supervisor.interns.index') }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--teal);text-decoration:none;font-weight:600;">
            View all →
        </a>
    </div>

    {{-- Pending evaluations --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--gold-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">PENDING EVALUATIONS</div>
        <div style="font-size:32px;font-weight:800;color:var(--gold);line-height:1;">{{ $notEvaluated }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">interns to evaluate</div>
        <a href="{{ route('supervisor.evaluations.index') }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--gold);text-decoration:none;font-weight:600;">
            Evaluate →
        </a>
    </div>

    {{-- Completed evaluations --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--blue-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">EVALUATED</div>
        <div style="font-size:32px;font-weight:800;color:var(--blue);line-height:1;">{{ $evaluated }}</div>
        @php $pct = $total > 0 ? round(($evaluated / $total) * 100) : 0; @endphp
        <div style="height:5px;background:var(--border2);border-radius:4px;margin-top:10px;">
            <div style="height:100%;width:{{ $pct }}%;background:var(--blue);border-radius:4px;"></div>
        </div>
        <div style="font-size:11.5px;color:var(--muted);margin-top:5px;">{{ $pct }}% complete</div>
    </div>

</div>

{{-- Bottom section --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;">

    {{-- Intern list --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--teal-dim);
                                display:flex;align-items:center;justify-content:center;color:var(--teal);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                            <path d="M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    My Interns
                </div>
            </div>
            <a href="{{ route('supervisor.interns.index') }}"
               style="font-size:12px;color:var(--teal);text-decoration:none;font-weight:600;">View all</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Required Hours</th>
                        <th>Evaluation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($interns->take(5) as $intern)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--gold-dim);
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:12px;font-weight:700;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($intern->student->name ?? 'S', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:600;color:var(--text);">
                                    {{ $intern->student->name ?? '—' }}
                                </div>
                                <div style="font-size:11.5px;color:var(--muted);">
                                    {{ $intern->student->email ?? '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $intern->program ?? '—' }}</td>
                    <td style="font-size:13px;font-weight:600;color:var(--blue);">
                        {{ number_format($intern->required_hours) }} hrs
                    </td>
                    <td>
                        @if($intern->evaluation)
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                background:var(--teal-dim);color:var(--teal);">✓ Done</span>
                        @else
                            <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                                background:var(--gold-dim);color:var(--gold);">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if(!$intern->evaluation)
                        <a href="{{ route('supervisor.evaluations.create', $intern->id) }}"
                           style="font-size:12px;color:var(--gold);text-decoration:none;border:1px solid var(--gold);
                                  padding:4px 12px;border-radius:6px;font-weight:600;white-space:nowrap;">
                            Evaluate
                        </a>
                        @else
                        <span style="font-size:12px;color:var(--muted);">
                            {{ $intern->evaluation->overall_grade }}/100
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                        <div style="font-size:24px;margin-bottom:8px;">👥</div>
                        No active interns yet.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="card" style="padding:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:16px;
                    display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:7px;background:var(--gold-dim);
                        display:flex;align-items:center;justify-content:center;color:var(--gold);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            Quick Actions
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;">

            <a href="{{ route('supervisor.interns.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--teal)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--teal);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">My Interns</div>
                    <div style="font-size:11.5px;color:var(--muted);">View all assigned interns</div>
                </div>
                @if($total > 0)
                <span style="margin-left:auto;padding:2px 8px;background:var(--teal-dim);color:var(--teal);
                             border-radius:20px;font-size:11px;font-weight:700;">{{ $total }}</span>
                @endif
            </a>

            <a href="{{ route('supervisor.evaluations.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--gold-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Evaluations</div>
                    <div style="font-size:11.5px;color:var(--muted);">Submit intern evaluations</div>
                </div>
                @if($notEvaluated > 0)
                <span style="margin-left:auto;padding:2px 8px;background:var(--gold-dim);color:var(--gold);
                             border-radius:20px;font-size:11px;font-weight:700;">{{ $notEvaluated }}</span>
                @endif
            </a>

            <a href="{{ route('supervisor.profile.settings') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--surface2);border:1px solid var(--border2);
                            display:flex;align-items:center;justify-content:center;color:var(--muted);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Settings</div>
                    <div style="font-size:11.5px;color:var(--muted);">Profile & password</div>
                </div>
            </a>

        </div>
    </div>

</div>

@endsection