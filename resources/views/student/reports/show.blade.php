@extends('layouts.student-app')
@section('title', 'View Weekly Report')
@section('page-title', 'Weekly Report Details')
@section('content')

<div style="max-width:720px;">

{{-- Back link --}}
<a href="{{ route('student.reports.index') }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:20px;">
    ← Back to Weekly Reports
</a>

<div class="card" style="padding:28px;">

    {{-- Header --}}
    <div style="font-size:15px;font-weight:700;margin-bottom:4px;">
        Weekly Report - Week {{ $report->week_number }}
    </div>
    <div style="font-size:12px;color:var(--muted);margin-bottom:24px;">
        Submitted on {{ $report->created_at->format('M d, Y') }}
    </div>

    {{-- Status --}}
    <div style="margin-bottom:18px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
            STATUS
        </label>
        <div style="display:inline-block;padding:6px 12px;border-radius:999px;
                    font-size:11px;font-weight:600;
                    background:
                    @if($report->status === 'approved') var(--green-dim)
                    @elseif($report->status === 'rejected') var(--red-dim)
                    @else var(--yellow-dim)
                    @endif;
                    color:
                    @if($report->status === 'approved') var(--green)
                    @elseif($report->status === 'rejected') var(--red)
                    @else var(--yellow)
                    @endif;">
            {{ ucfirst($report->status) }}
        </div>
    </div>

    {{-- Week info --}}
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px;">
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
                WEEK NUMBER
            </label>
            <div style="font-size:13px;">{{ $report->week_number }}</div>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
                DATE FROM
            </label>
            <div style="font-size:13px;">{{ $report->week_start }}</div>
        </div>
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
                DATE TO
            </label>
            <div style="font-size:13px;">{{ $report->week_end }}</div>
        </div>
    </div>

    {{-- Hours --}}
    @if(isset($report->hours_this_week))
    <div style="margin-bottom:18px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
            HOURS RENDERED THIS WEEK
        </label>
        <div style="font-size:13px;">{{ $report->hours_this_week }}</div>
    </div>
    @endif

    {{-- Activities --}}
    <div style="margin-bottom:18px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
            ACTIVITIES SUMMARY
        </label>
        <div style="font-size:13px;line-height:1.6;">
            {{ $report->description }}
        </div>
    </div>

    {{-- Learnings --}}
    @if($report->learnings)
    <div style="margin-bottom:18px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
            LEARNINGS & REFLECTIONS
        </label>
        <div style="font-size:13px;line-height:1.6;">
            {{ $report->learnings }}
        </div>
    </div>
    @endif

    {{-- Challenges --}}
    @if($report->challenges)
    <div style="margin-bottom:18px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
            CHALLENGES ENCOUNTERED
        </label>
        <div style="font-size:13px;line-height:1.6;">
            {{ $report->challenges }}
        </div>
    </div>
    @endif

    {{-- Attachment --}}
    @if($report->file_path)
    <div style="margin-bottom:28px;">
        <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;">
            ATTACHMENT
        </label>
        <a href="{{ asset('storage/' . $report->file_path) }}" target="_blank"
           style="font-size:13px;color:var(--gold);text-decoration:none;font-weight:600;">
            View / Download File
        </a>
    </div>
    @endif

    {{-- Feedback (for rejected) --}}
    @if($report->status === 'rejected' && $report->feedback)
    <div style="background:var(--red-dim);border:1px solid var(--red);border-radius:8px;padding:12px 16px;margin-bottom:20px;">
        <div style="font-size:12px;font-weight:600;color:var(--red);margin-bottom:6px;">
            Coordinator Feedback
        </div>
        <div style="font-size:12px;color:var(--red);">
            {{ $report->feedback }}
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div style="display:flex;gap:12px;">
        @if($report->status === 'rejected')
            <a href="{{ route('student.reports.edit', $report->id) }}"
               style="padding:10px 20px;background:var(--gold);color:var(--bg);border-radius:8px;
                      font-size:13px;font-weight:600;text-decoration:none;">
                Edit & Resubmit
            </a>
        @endif

        <a href="{{ route('student.reports.index') }}"
           style="padding:10px 20px;background:transparent;color:var(--muted);border:1px solid var(--border2);
                  border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
            Back
        </a>
    </div>

</div>

</div>
@endsection
