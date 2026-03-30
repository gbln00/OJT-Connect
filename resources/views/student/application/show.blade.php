@extends('layouts.student-app')
@section('title', 'My Application')
@section('page-title', 'My Application')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Application / Status
        </span>
    </div>

    @php
        $statusMap = [
            'pending'  => ['cls' => 'gold',    'pill' => 'gold',    'label' => 'Pending Review',  'msg' => 'Your application has been submitted and is awaiting review by the OJT coordinator.'],
            'approved' => ['cls' => 'teal',    'pill' => 'green',   'label' => 'Approved',         'msg' => 'Congratulations! Your OJT application has been approved. You may now begin your internship.'],
            'rejected' => ['cls' => 'crimson', 'pill' => 'crimson', 'label' => 'Rejected',         'msg' => 'Your application was not approved. Please review the remarks below and contact your coordinator.'],
        ];
        $s = $statusMap[$application->status] ?? $statusMap['pending'];
    @endphp

    {{-- STATUS HERO --}}
    <div class="card fade-up fade-up-1" style="border-color:var(--{{ $s['cls'] }}-border, var(--border));">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="stat-icon {{ $s['cls'] }}" style="width:44px;height:44px;font-size:20px;font-family:'Playfair Display',serif;font-weight:900;">
                    @if($application->status === 'approved') ✓
                    @elseif($application->status === 'rejected') ✕
                    @else ⏳
                    @endif
                </div>
                <div>
                    <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);">{{ $s['label'] }}</div>
                    <div style="font-size:12.5px;color:var(--muted);margin-top:3px;line-height:1.5;max-width:440px;">{{ $s['msg'] }}</div>
                </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div class="form-hint">Submitted</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);margin-top:2px;">{{ $application->created_at->format('M d, Y') }}</div>
            </div>
        </div>

        @if($application->remarks)
        <div style="padding:16px 24px;border-bottom:1px solid var(--border);background:var(--surface2);">
            <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Coordinator Remarks</div>
            <div style="font-size:13px;color:var(--text);line-height:1.6;border-left:2px solid var(--crimson);padding-left:12px;">{{ $application->remarks }}</div>
            @if($application->reviewed_at)
            <div class="form-hint" style="margin-top:6px;">Reviewed {{ $application->reviewed_at->format('M d, Y \a\t h:i A') }}</div>
            @endif
        </div>
        @endif

        <div style="padding:24px;">
            <div style="font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;">Application Details</div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Company</div>
                    <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $application->company->name ?? '—' }}</div>
                    @if($application->company?->address)
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $application->company->address }}</div>
                    @endif
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Program</div>
                    <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $application->program }}</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">School Year</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->school_year }}</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Semester</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->semester }}</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Required Hours</div>
                    <div style="font-size:14px;font-weight:700;color:var(--blue-color);">{{ number_format($application->required_hours) }} hrs</div>
                </div>
                <div>
                    <div class="form-hint" style="margin-bottom:5px;">Status</div>
                    <span class="status-pill {{ $s['pill'] }}">{{ ucfirst($application->status) }}</span>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:20px;">
                <div class="form-hint" style="margin-bottom:8px;">Supporting Document</div>
                @if($application->document_path)
                <a href="{{ Storage::url($application->document_path) }}" target="_blank" class="btn btn-ghost btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    View Document
                </a>
                @else
                <div style="font-size:13px;color:var(--muted);">No document uploaded.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- NEXT STEPS (approved) --}}
    @if($application->status === 'approved')
    <div class="card fade-up fade-up-2">
        <div class="card-header">
            <div class="card-title" style="color:var(--teal-color);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;margin-right:6px;"><polyline points="9,11 12,14 22,4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                What's Next
            </div>
        </div>
        <div style="padding:14px;display:flex;flex-direction:column;gap:8px;">
            <a href="{{ route('student.hours.create') }}" class="qa-btn" style="flex-direction:row;text-align:left;padding:14px;">
                <div class="qa-icon" style="flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
                </div>
                <div>
                    <div class="qa-label" style="text-align:left;">Start Logging Hours</div>
                    <div class="form-hint" style="text-transform:none;letter-spacing:0;">Record your daily time in and time out.</div>
                </div>
            </a>
            <a href="{{ route('student.reports.index') }}" class="qa-btn" style="flex-direction:row;text-align:left;padding:14px;">
                <div class="qa-icon" style="flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
                </div>
                <div>
                    <div class="qa-label" style="text-align:left;">Submit Weekly Reports</div>
                    <div class="form-hint" style="text-transform:none;letter-spacing:0;">Document your weekly internship activities.</div>
                </div>
            </a>
        </div>
    </div>
    @endif

    {{-- REJECTED: re-apply --}}
    @if($application->status === 'rejected')
    <div class="alert error fade-up fade-up-2" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Need to re-apply? Contact your OJT coordinator or submit a new application.
        </div>
        <a href="{{ route('student.application.create') }}" class="btn btn-primary btn-sm" style="white-space:nowrap;flex-shrink:0;">Re-apply</a>
    </div>
    @endif

</div>
@endsection