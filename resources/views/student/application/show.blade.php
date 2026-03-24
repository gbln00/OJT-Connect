@extends('layouts.student-app')
@section('title', 'My Application')
@section('page-title', 'My Application')
@section('content')

<div style="max-width:720px;">

    {{-- Back link --}}
    <a href="{{ route('student.dashboard') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:20px;">
        ← Back to Dashboard
    </a>

    {{-- Status hero card --}}
    @php
        $statusMap = [
            'pending'  => ['color' => 'gold',  'icon' => '⏳', 'label' => 'Pending Review',  'message' => 'Your application has been submitted and is awaiting review by the OJT coordinator.'],
            'approved' => ['color' => 'teal',  'icon' => '✅', 'label' => 'Approved',         'message' => 'Congratulations! Your OJT application has been approved. You may now begin your internship.'],
            'rejected' => ['color' => 'coral', 'icon' => '✕',  'label' => 'Rejected',         'message' => 'Your application was not approved. Please review the remarks below and contact your coordinator.'],
        ];
        $s = $statusMap[$application->status] ?? $statusMap['pending'];
    @endphp

    <div class="card" style="padding:24px;margin-bottom:16px;border-color:var(--{{ $s['color'] }});
                              background:var(--{{ $s['color'] }}-dim);">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="font-size:32px;line-height:1;">{{ $s['icon'] }}</div>
            <div>
                <div style="font-size:16px;font-weight:700;color:var(--{{ $s['color'] }});">
                    {{ $s['label'] }}
                </div>
                <div style="font-size:12.5px;color:var(--muted);margin-top:3px;line-height:1.5;">
                    {{ $s['message'] }}
                </div>
            </div>
            <div style="margin-left:auto;text-align:right;flex-shrink:0;">
                <div style="font-size:10.5px;color:var(--muted);letter-spacing:.5px;">SUBMITTED</div>
                <div style="font-size:13px;color:var(--muted2);margin-top:2px;font-weight:500;">
                    {{ $application->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Coordinator remarks --}}
        @if($application->remarks)
        <div style="margin-top:16px;padding:12px 14px;background:var(--surface);border-radius:8px;
                    border-left:3px solid var(--{{ $s['color'] }});">
            <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                COORDINATOR REMARKS
            </div>
            <div style="font-size:13px;color:var(--text);line-height:1.6;">
                {{ $application->remarks }}
            </div>
            @if($application->reviewed_at)
            <div style="font-size:11.5px;color:var(--muted);margin-top:6px;">
                Reviewed on {{ $application->reviewed_at->format('M d, Y \a\t h:i A') }}
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- Application details --}}
    <div class="card" style="padding:24px;margin-bottom:16px;">
        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:18px;
                    display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:7px;background:var(--gold-dim);
                        display:flex;align-items:center;justify-content:center;color:var(--gold);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            Application Details
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">

            {{-- Company --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    COMPANY
                </div>
                <div style="font-size:13.5px;color:var(--text);font-weight:600;">
                    {{ $application->company->name ?? '—' }}
                </div>
                @if($application->company?->address)
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    {{ $application->company->address }}
                </div>
                @endif
            </div>

            {{-- Program --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    PROGRAM
                </div>
                <div style="font-size:13.5px;color:var(--text);font-weight:600;">
                    {{ $application->program }}
                </div>
            </div>

            {{-- School Year --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    SCHOOL YEAR
                </div>
                <div style="font-size:13.5px;color:var(--text);">
                    {{ $application->school_year }}
                </div>
            </div>

            {{-- Semester --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    SEMESTER
                </div>
                <div style="font-size:13.5px;color:var(--text);">
                    {{ $application->semester }}
                </div>
            </div>

            {{-- Required Hours --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    REQUIRED HOURS
                </div>
                <div style="font-size:13.5px;color:var(--blue);font-weight:700;">
                    {{ number_format($application->required_hours) }} hrs
                </div>
            </div>

            {{-- Status --}}
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:5px;">
                    STATUS
                </div>
                <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;
                    background:var(--{{ $s['color'] }}-dim);color:var(--{{ $s['color'] }});">
                    {{ ucfirst($application->status) }}
                </span>
            </div>

        </div>

        {{-- Divider --}}
        <div style="border-top:1px solid var(--border2);margin:20px 0;"></div>

        {{-- Document --}}
        <div>
            <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:8px;">
                SUPPORTING DOCUMENT
            </div>
            @if($application->document_path)
            <a href="{{ Storage::url($application->document_path) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:8px;
                      border:1px solid var(--border2);background:var(--surface2);color:var(--text);
                      text-decoration:none;font-size:13px;font-weight:500;"
               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border2)'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                View Submitted Document
            </a>
            @else
            <div style="font-size:13px;color:var(--muted);">No document uploaded.</div>
            @endif
        </div>
    </div>

    {{-- Next steps (only shown when approved) --}}
    @if($application->status === 'approved')
    <div class="card" style="padding:20px;border-color:var(--teal);">
        <div style="font-size:13px;font-weight:700;color:var(--teal);margin-bottom:14px;
                    display:flex;align-items:center;gap:8px;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="9,11 12,14 22,4"/>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
            What's Next
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <a href="{{ route('student.hours.create') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;"
               onmouseover="this.style.borderColor='var(--teal)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--teal);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Start Logging Hours</div>
                    <div style="font-size:11.5px;color:var(--muted);">Record your daily time in and time out.</div>
                </div>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                     style="margin-left:auto;color:var(--muted);">
                    <polyline points="9,18 15,12 9,6"/>
                </svg>
            </a>
            <a href="{{ route('student.reports.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;"
               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--gold-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Submit Weekly Reports</div>
                    <div style="font-size:11.5px;color:var(--muted);">Document your weekly internship activities.</div>
                </div>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                     style="margin-left:auto;color:var(--muted);">
                    <polyline points="9,18 15,12 9,6"/>
                </svg>
            </a>
        </div>
    </div>
    @endif

    {{-- Rejected: re-apply hint --}}
    @if($application->status === 'rejected')
    <div style="padding:14px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                font-size:12.5px;color:var(--muted);line-height:1.6;">
        Need to re-apply? Contact your OJT coordinator directly, or
        <a href="mailto:" style="color:var(--gold);text-decoration:none;font-weight:600;">send them an email</a>
        to discuss your options.
    </div>
    @endif

</div>
@endsection