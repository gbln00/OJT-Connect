@extends('layouts.supervisor-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

@php
    $evaluated    = $interns->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $interns->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $interns->count();
    $company      = $interns->first()?->company;
    $hour          = now()->hour;
@endphp

{{-- GREETING --}}
<div class="greeting fade-up">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">
        Good {{ $hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
    @if($company)
    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;
                color:var(--muted);margin-top:6px;display:flex;align-items:center;gap:6px;">
        <span style="width:5px;height:5px;background:var(--teal);display:inline-block;"></span>
        {{ $company->name }}
    </div>
    @endif
</div>

{{-- STAT CARDS --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <span class="stat-tag">active</span>
        </div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Active Interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">pending</span>
        </div>
        <div class="stat-num">{{ $notEvaluated }}</div>
        <div class="stat-label">Pending Evaluations</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <span class="stat-tag">done</span>
        </div>
        <div class="stat-num">{{ $evaluated }}</div>
        <div class="stat-label">Evaluated</div>
        @php $pct = $total > 0 ? round(($evaluated / $total) * 100) : 0; @endphp
        <div class="progress-track" style="margin-top:12px;">
            <div class="progress-fill blue" style="width:{{ $pct }}%;"></div>
        </div>
        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:5px;letter-spacing:0.05em;">
            {{ $pct }}% complete
        </div>
    </div>

</div>

{{-- BOTTOM GRID --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;" class="fade-up fade-up-2">

    {{-- INTERN TABLE --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">My Interns</div>
            <a href="{{ route('supervisor.interns.index') }}" class="card-action">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Program</th>
                        <th>Req. Hours</th>
                        <th>Evaluation</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($interns->take(6) as $intern)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:28px;height:28px;flex-shrink:0;border:1px solid var(--gold-border);
                                        background:var(--gold-dim);display:flex;align-items:center;justify-content:center;
                                        font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--gold);">
                                {{ strtoupper(substr($intern->student->name ?? 'S', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $intern->student->name ?? '—' }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $intern->student->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $intern->program ?? '—' }}</td>
                    <td style="font-family:'Playfair Display',serif;font-weight:700;color:var(--blue);font-size:14px;">
                        {{ number_format($intern->required_hours) }}
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);font-weight:400;">hrs</span>
                    </td>
                    <td>
                        @if($intern->evaluation)
                            <span class="status-pill teal">✓ Done</span>
                        @else
                            <span class="status-pill gold">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if(!$intern->evaluation)
                        <a href="{{ route('supervisor.evaluations.create', $intern->id) }}" class="btn btn-gold btn-sm">
                            Evaluate
                        </a>
                        @else
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $intern->evaluation->overall_grade }}/100
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                        No active interns assigned yet.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- QUICK ACTIONS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick actions</div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:14px;">

                <a href="{{ route('supervisor.interns.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
                          padding:16px 10px;background:var(--surface2);border:1px solid var(--border);
                          text-decoration:none;text-align:center;transition:all 0.2s;"
                   onmouseover="this.style.borderColor='var(--teal-border)';this.style.background='var(--teal-dim)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface2)'">
                    <div style="width:30px;height:30px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--teal);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                    </div>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text2);">My Interns</span>
                </a>

                <a href="{{ route('supervisor.evaluations.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
                          padding:16px 10px;background:var(--surface2);border:1px solid var(--border);
                          text-decoration:none;text-align:center;transition:all 0.2s;"
                   onmouseover="this.style.borderColor='var(--gold-border)';this.style.background='var(--gold-dim)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface2)'">
                    <div style="width:30px;height:30px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                    </div>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text2);">Evaluations</span>
                </a>

                <a href="{{ route('supervisor.hours.index') }}"
                   style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
                          padding:16px 10px;background:var(--surface2);border:1px solid var(--border);
                          text-decoration:none;text-align:center;transition:all 0.2s;"
                   onmouseover="this.style.borderColor='var(--blue-border)';this.style.background='var(--blue-dim)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface2)'">
                    <div style="width:30px;height:30px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--blue);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                        </svg>
                    </div>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text2);">Hour Logs</span>
                </a>

                <a href="{{ route('supervisor.profile.settings') }}"
                   style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;
                          padding:16px 10px;background:var(--surface2);border:1px solid var(--border);
                          text-decoration:none;text-align:center;transition:all 0.2s;"
                   onmouseover="this.style.borderColor='var(--border2)';this.style.background='var(--surface3)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--surface2)'">
                    <div style="width:30px;height:30px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--muted);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                        </svg>
                    </div>
                    <span style="font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--text2);">Settings</span>
                </a>

            </div>
        </div>

        {{-- SUMMARY STATUS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Summary</div>
                <span style="width:7px;height:7px;background:#34d399;display:inline-block;" class="flicker"></span>
            </div>
            <div style="padding:4px 0;">
                @php
                $rows = [
                    ['Active interns',       $total,        'var(--teal)'],
                    ['Pending evaluations',  $notEvaluated, 'var(--gold)'],
                    ['Completed evaluations',$evaluated,    'var(--blue)'],
                    ['Evaluation progress',  $pct . '%',    'var(--crimson)'],
                ];
                @endphp
                @foreach($rows as [$lbl, $val, $clr])
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--text2);">{{ $lbl }}</span>
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:{{ $clr }};">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection