@extends('layouts.student-app')
@section('title', 'View Weekly Report')
@section('page-title', 'Weekly Report Details')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Reports / Week {{ $report->week_number }}
        </span>
    </div>

    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;flex-shrink:0;border:1px solid var(--teal-border);background:var(--teal-dim);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--teal-color);">
                    {{ $report->week_number }}
                </div>
                <div>
                    <div class="card-title">Week {{ $report->week_number }} Report</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                        {{ $report->week_start?->format('M d') }} – {{ $report->week_end?->format('M d, Y') }}
                        · Submitted {{ $report->created_at->format('M d, Y') }}
                    </div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                @php
                    $sCls = match($report->status ?? 'pending') {
                        'approved' => 'green', 'rejected' => 'crimson', 'returned' => 'crimson', default => 'gold'
                    };
                @endphp
                <span class="status-pill {{ $sCls }}">{{ ucfirst($report->status ?? 'pending') }}</span>
                <a href="{{ route('student.reports.index') }}" class="btn btn-ghost btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div style="padding:24px;">

            {{-- Week Info --}}
            <div class="form-section-divider"><span>Week Information</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:28px;">
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Week Number</div>
                    <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--text);">{{ $report->week_number }}</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Date From</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->week_start?->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Date To</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->week_end?->format('M d, Y') }}</div>
                </div>
            </div>

            @if(isset($report->hours_this_week))
            <div style="margin-bottom:28px;">
                <div class="form-hint" style="margin-bottom:5px;">Hours Rendered</div>
                <div style="font-family:'Playfair Display',serif;font-size:24px;font-weight:900;color:var(--blue-color);">{{ $report->hours_this_week }} hrs</div>
            </div>
            @endif

            {{-- Content --}}
            <div class="form-section-divider"><span>Report Content</span></div>

            <div style="margin-bottom:20px;">
                <div class="form-hint" style="margin-bottom:8px;">Activities Summary</div>
                <div style="font-size:13px;color:var(--text);line-height:1.7;background:var(--surface2);border:1px solid var(--border2);padding:14px;border-left:2px solid var(--crimson);">{{ $report->description }}</div>
            </div>

            @if($report->learnings)
            <div style="margin-bottom:20px;">
                <div class="form-hint" style="margin-bottom:8px;">Learnings & Reflections</div>
                <div style="font-size:13px;color:var(--text);line-height:1.7;background:var(--surface2);border:1px solid var(--border2);padding:14px;border-left:2px solid var(--teal-color);">{{ $report->learnings }}</div>
            </div>
            @endif

            @if($report->challenges)
            <div style="margin-bottom:28px;">
                <div class="form-hint" style="margin-bottom:8px;">Challenges Encountered</div>
                <div style="font-size:13px;color:var(--text);line-height:1.7;background:var(--surface2);border:1px solid var(--border2);padding:14px;border-left:2px solid var(--gold-color);">{{ $report->challenges }}</div>
            </div>
            @endif

            {{-- Attachment --}}
            @if($report->file_path)
            <div style="border-top:1px solid var(--border);padding-top:20px;margin-bottom:20px;">
                <div class="form-hint" style="margin-bottom:8px;">Attachment</div>
                <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank" class="btn btn-ghost btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    View / Download File
                </a>
            </div>
            @endif

            {{-- Feedback --}}
            @if(in_array($report->status, ['rejected','returned']) && $report->feedback)
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.25);padding:13px 16px;margin-bottom:20px;">
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--crimson);margin-bottom:6px;">Coordinator Feedback</div>
                <div style="font-size:13px;color:var(--text);line-height:1.6;">{{ $report->feedback }}</div>
            </div>
            @endif

            {{-- Actions --}}
            <div style="display:flex;gap:8px;">
                @if(in_array($report->status, ['rejected','returned','pending']))
                <a href="{{ route('student.reports.edit', $report->id) }}" class="btn btn-primary btn-sm">Edit & Resubmit</a>
                @endif
                <a href="{{ route('student.reports.index') }}" class="btn btn-ghost btn-sm">Back to Reports</a>
            </div>

        </div>
    </div>
</div>
@endsection