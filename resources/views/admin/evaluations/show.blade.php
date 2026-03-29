@extends('layouts.app')
@section('title', 'Evaluation Detail')
@section('page-title', 'Evaluations')

@section('content')
@php
$pillMap = ['pass'=>'green','fail'=>'crimson'];
$pillCls = $pillMap[$evaluation->recommendation] ?? 'steel';
@endphp

<div style="max-width:1100px;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow + back --}}
    <div class="fade-up" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
                Evaluations / #{{ $evaluation->id }}
            </span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="status-pill {{ $pillCls }}">{{ $evaluation->recommendation_label }}</span>
            <a href="{{ route('admin.evaluations.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 300px;gap:14px;align-items:start;" class="fade-up fade-up-1">

        {{-- LEFT --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Student + context --}}
            <div class="card">
                <div class="card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                            {{ strtoupper(substr($evaluation->student->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="card-title">{{ $evaluation->student->name }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $evaluation->student->email }}</div>
                        </div>
                    </div>
                </div>
                <div style="padding:20px;">
                    <div class="detail-grid" style="grid-template-columns:repeat(3,1fr);">
                        <div class="detail-item">
                            <div class="detail-label">Company</div>
                            <div class="detail-value" style="font-weight:600;">{{ $evaluation->application->company->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Program</div>
                            <div class="detail-value">{{ $evaluation->application->program }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Evaluated by</div>
                            <div class="detail-value">{{ $evaluation->supervisor->name }}</div>
                        </div>
                        <div class="detail-item" style="grid-column:span 2;">
                            <div class="detail-label">Submitted</div>
                            <div class="detail-value" style="font-family:'DM Mono',monospace;font-size:12px;">
                                {{ $evaluation->submitted_at?->format('M d, Y') ?? $evaluation->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ratings --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Performance Ratings</div>
                </div>
                <div style="padding:24px;display:flex;flex-direction:column;gap:28px;">

                    {{-- Attendance --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <div>
                                <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">Attendance</div>
                            </div>
                            <span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--text);">
                                {{ $evaluation->attendance_rating }}<span style="font-size:13px;color:var(--muted);font-weight:400;font-family:'DM Mono',monospace;"> / 5</span>
                            </span>
                        </div>
                        <div style="display:flex;gap:4px;margin-bottom:6px;">
                            @for($i = 1; $i <= 5; $i++)
                            <div style="flex:1;height:8px;background:{{ $i <= $evaluation->attendance_rating ? 'var(--crimson)' : 'var(--border2)' }};transition:background 0.2s;"></div>
                            @endfor
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.1em;">POOR</span>
                            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.1em;">EXCELLENT</span>
                        </div>
                    </div>

                    {{-- Performance --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <div>
                                <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">Performance</div>
                            </div>
                            <span style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--text);">
                                {{ $evaluation->performance_rating }}<span style="font-size:13px;color:var(--muted);font-weight:400;font-family:'DM Mono',monospace;"> / 5</span>
                            </span>
                        </div>
                        <div style="display:flex;gap:4px;margin-bottom:6px;">
                            @for($i = 1; $i <= 5; $i++)
                            <div style="flex:1;height:8px;background:{{ $i <= $evaluation->performance_rating ? '#333740' : 'var(--border2)' }};transition:background 0.2s;"></div>
                            @endfor
                        </div>
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.1em;">POOR</span>
                            <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);letter-spacing:0.1em;">EXCELLENT</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Remarks --}}
            @if($evaluation->remarks)
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Supervisor Remarks</div>
                </div>
                <div style="padding:20px;font-size:14px;color:var(--text);line-height:1.75;white-space:pre-wrap;">{{ $evaluation->remarks }}</div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Grade display --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Grade card --}}
            <div class="card" style="position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--crimson);"></div>
                <div style="padding:32px 24px;text-align:center;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Overall Grade</div>
                    <div style="font-family:'Playfair Display',serif;font-size:64px;font-weight:900;color:{{ $evaluation->grade_color }};line-height:1;margin-bottom:6px;">
                        {{ number_format($evaluation->overall_grade, 1) }}
                    </div>
                    <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:20px;">
                        {{ $evaluation->rating_label }}
                    </div>
                    <span class="status-pill {{ $pillCls }}" style="padding:6px 18px;font-size:12px;">
                        {{ $evaluation->recommendation_label }}
                    </span>
                </div>
            </div>

            {{-- Summary rows --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Summary</div>
                </div>
                <div style="padding:4px 0;">
                    @php
                    $rows = [
                        ['Attendance',      $evaluation->attendance_rating . ' / 5'],
                        ['Performance',     $evaluation->performance_rating . ' / 5'],
                        ['Overall grade',   number_format($evaluation->overall_grade, 1)],
                        ['Recommendation',  $evaluation->recommendation_label],
                    ];
                    @endphp
                    @foreach($rows as [$lbl, $val])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid var(--border);">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);">{{ $lbl }}</span>
                        <span style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--text);">{{ $val }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.detail-label {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 5px;
}
.detail-value { font-size: 13.5px; color: var(--text); }
</style>
@endpush
@endsection