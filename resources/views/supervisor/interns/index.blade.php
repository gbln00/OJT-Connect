{{-- resources/views/supervisor/interns/index.blade.php --}}
@extends('layouts.supervisor-app')
@section('page-title', 'My Interns')

@section('content')

@php
    $evaluated    = $interns->filter(fn($a) => $a->evaluation !== null)->count();
    $notEvaluated = $interns->filter(fn($a) => $a->evaluation === null)->count();
    $total        = $interns->count();
    $company      = $interns->first()?->company;
    $pct          = $total > 0 ? round(($evaluated / $total) * 100) : 0;
@endphp

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Management / My Interns
    </span>
</div>

{{-- Page heading --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px;" class="fade-up">
    <div>
        <p style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin:0;">
            @if($company)
                <span style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:5px;height:5px;background:var(--teal);display:inline-block;"></span>
                    {{ $company->name }}
                </span>
            @else
                All active interns assigned to your company
            @endif
        </p>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <span class="stat-tag">active</span>
        </div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Total interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">pending</span>
        </div>
        <div class="stat-num" style="{{ $notEvaluated > 0 ? 'color:#D97706;' : '' }}">{{ $notEvaluated }}</div>
        <div class="stat-label">Need evaluation</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <span class="stat-tag">done</span>
        </div>
        <div class="stat-num">{{ $evaluated }}</div>
        <div class="stat-label">Evaluated</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
            </div>
            <span class="stat-tag">progress</span>
        </div>
        <div class="stat-num" style="color:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};">{{ $pct }}%</div>
        <div class="stat-label">Evaluation rate</div>
    </div>

</div>

{{-- Progress bar --}}
@if($total > 0)
<div class="card fade-up fade-up-1" style="margin-bottom:20px;">
    <div style="padding:14px 20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">Overall Evaluation Progress</span>
            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ $evaluated }} / {{ $total }} evaluated</span>
        </div>
        <div style="height:5px;background:var(--surface2);border:1px solid var(--border);overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#34d399' : ($pct >= 50 ? '#60a5fa' : 'var(--crimson)') }};transition:width 0.6s ease;"></div>
        </div>
    </div>
</div>
@endif

{{-- Interns Table --}}
<div class="card fade-up fade-up-2">

    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:8px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:var(--muted);">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
            </svg>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Intern Roster</span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $total }} intern{{ $total !== 1 ? 's' : '' }}</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Course / Program</th>
                    <th>Req. Hours</th>
                    <th>Hour Logs</th>
                    <th>Evaluation</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($interns as $intern)
                @php
                    $totalApproved = $intern->student?->applications
                        ->where('id', $intern->id)
                        ->first()?->id ?? null;
                @endphp
                <tr style="transition:background 0.15s;" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background=''">

                    {{-- Student info --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:12px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($intern->student->name ?? 'S', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;color:var(--text);font-size:13px;line-height:1.3;">{{ $intern->student->name ?? '—' }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $intern->student->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Course --}}
                    <td>
                        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ $intern->program ?? '—' }}</div>
                        @if($intern->student?->studentProfile?->course)
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $intern->student->studentProfile->course }}</div>
                        @endif
                    </td>

                    {{-- Required hours --}}
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;color:var(--blue);font-size:15px;">{{ number_format($intern->required_hours) }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                    </td>

                    {{-- Hour logs link --}}
                    <td>
                        <a href="{{ route('supervisor.hours.show', $intern->student_id) }}"
                           style="display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);text-decoration:none;padding:3px 8px;border:1px solid var(--border);background:var(--surface2);transition:all 0.15s;"
                           onmouseover="this.style.borderColor='rgba(140,14,3,0.3)';this.style.color='var(--crimson)'"
                           onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--muted)'">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                            </svg>
                            View logs
                        </a>
                    </td>

                    {{-- Evaluation status --}}
                    <td>
                        @if($intern->evaluation)
                            <div>
                                <span class="log-badge approved" style="margin-bottom:3px;"><span class="badge-dot"></span>Evaluated</span>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                                    Score: <span style="color:var(--text2);font-weight:600;">{{ $intern->evaluation->overall_grade }}/100</span>
                                </div>
                            </div>
                        @else
                            <span class="log-badge pending"><span class="badge-dot"></span>Pending</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:right;">
                        <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                            <a href="{{ route('supervisor.hours.show', $intern->student_id) }}" class="btn btn-ghost btn-sm">
                                Hours
                            </a>
                            @if(!$intern->evaluation)
                                <a href="{{ route('supervisor.evaluations.create', $intern->id) }}" class="btn btn-gold btn-sm" style="display:flex;align-items:center;gap:5px;">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Evaluate
                                </a>
                            @else
                                <a href="{{ route('supervisor.evaluations.create', $intern->id) }}" class="btn btn-ghost btn-sm" style="color:var(--muted);">
                                    View eval
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:64px 32px;">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                            <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="color:var(--muted);opacity:0.35;">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                            </svg>
                            <div style="font-family:'Playfair Display',serif;font-size:18px;color:var(--text2);">No interns assigned</div>
                            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                                No approved intern applications have been assigned to your company yet.
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Table footer summary --}}
    @if($total > 0)
    <div style="padding:12px 20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                <span style="display:inline-block;width:6px;height:6px;background:#34d399;margin-right:5px;"></span>
                {{ $evaluated }} evaluated
            </span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                <span style="display:inline-block;width:6px;height:6px;background:#D97706;margin-right:5px;"></span>
                {{ $notEvaluated }} pending
            </span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $total }} intern{{ $total !== 1 ? 's' : '' }} total</span>
    </div>
    @endif
</div>

@push('styles')
<style>
.log-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace; font-size: 10px;
    letter-spacing: 0.1em; text-transform: uppercase; padding: 3px 8px; border: 1px solid;
}
.log-badge .badge-dot { width: 5px; height: 5px; display: inline-block; flex-shrink: 0; }
.log-badge.pending  { color: #D97706; border-color: rgba(217,119,6,0.3); background: rgba(254,243,199,0.08); }
.log-badge.pending .badge-dot  { background: #D97706; }
.log-badge.approved { color: #34d399; border-color: rgba(52,211,153,0.3); background: rgba(52,211,153,0.06); }
.log-badge.approved .badge-dot { background: #34d399; }
</style>
@endpush

@endsection