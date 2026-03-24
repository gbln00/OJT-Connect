@extends('layouts.coordinator-app')
@section('title', 'Application — ' . $application->student->name)
@section('page-title', 'Application Detail')
@section('content')

@php
    $statusMap = [
        'pending'  => ['color' => 'gold',  'label' => 'Pending Review'],
        'approved' => ['color' => 'teal',  'label' => 'Approved'],
        'rejected' => ['color' => 'coral', 'label' => 'Rejected'],
    ];
    $s = $statusMap[$application->status] ?? $statusMap['pending'];
@endphp

{{-- Back link --}}
<a href="{{ route('coordinator.applications.index') }}"
   style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:20px;">
    ← Back to Applications
</a>

<div style="display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start;">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info card --}}
        <div class="card" style="padding:24px;">
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                <div style="width:52px;height:52px;border-radius:50%;background:var(--gold-dim);
                            border:2px solid rgba(240,180,41,0.3);display:flex;align-items:center;
                            justify-content:center;font-size:18px;font-weight:700;color:var(--gold);flex-shrink:0;">
                    {{ strtoupper(substr($application->student->name ?? 'S', 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:16px;font-weight:700;color:var(--text);letter-spacing:-0.3px;">
                        {{ $application->student->name ?? '—' }}
                    </div>
                    <div style="font-size:12.5px;color:var(--muted);margin-top:2px;">
                        {{ $application->student->email ?? '' }}
                    </div>
                    <div style="margin-top:6px;">
                        <span class="role-badge student">Student Intern</span>
                    </div>
                </div>
                <div style="margin-left:auto;">
                    <span style="padding:5px 14px;border-radius:20px;font-size:12px;font-weight:600;
                        background:var(--{{ $s['color'] }}-dim);color:var(--{{ $s['color'] }});">
                        {{ $s['label'] }}
                    </span>
                </div>
            </div>

            {{-- Divider --}}
            <div style="border-top:1px solid var(--border2);margin-bottom:20px;"></div>

            {{-- Application fields --}}
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:16px;
                        display:flex;align-items:center;gap:8px;">
                <div style="width:26px;height:26px;border-radius:7px;background:var(--gold-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--gold);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                Application Details
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">COMPANY</div>
                    <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $application->company->name ?? '—' }}</div>
                    @if($application->company?->address)
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $application->company->address }}</div>
                    @endif
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">PROGRAM</div>
                    <div style="font-size:13.5px;color:var(--text);">{{ $application->program }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">SCHOOL YEAR</div>
                    <div style="font-size:13.5px;color:var(--text);">{{ $application->school_year }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">SEMESTER</div>
                    <div style="font-size:13.5px;color:var(--text);">{{ $application->semester }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">REQUIRED HOURS</div>
                    <div style="font-size:13.5px;font-weight:700;color:var(--blue);">{{ number_format($application->required_hours) }} hrs</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">SUBMITTED</div>
                    <div style="font-size:13.5px;color:var(--text);">{{ $application->created_at->format('M d, Y') }}</div>
                </div>
            </div>

            {{-- Document --}}
            <div style="border-top:1px solid var(--border2);margin-top:20px;padding-top:18px;">
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
                    View Document
                </a>
                @else
                <div style="font-size:13px;color:var(--muted);">No document uploaded.</div>
                @endif
            </div>
        </div>

        {{-- Reviewer info (if already reviewed) --}}
        @if($application->reviewed_at)
        <div class="card" style="padding:20px;">
            <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px;
                        display:flex;align-items:center;gap:8px;">
                <div style="width:26px;height:26px;border-radius:7px;background:var(--teal-dim);
                            display:flex;align-items:center;justify-content:center;color:var(--teal);">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                Review Record
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">REVIEWED BY</div>
                    <div style="font-size:13px;color:var(--text);font-weight:600;">{{ $application->reviewer->name ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:4px;">REVIEWED ON</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->reviewed_at->format('M d, Y \a\t h:i A') }}</div>
                </div>
            </div>
            @if($application->remarks)
            <div>
                <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:6px;">REMARKS</div>
                <div style="padding:10px 14px;background:var(--surface2);border-left:3px solid var(--{{ $s['color'] }});
                            border-radius:0 6px 6px 0;font-size:13px;color:var(--text);line-height:1.6;">
                    {{ $application->remarks }}
                </div>
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN: actions --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        @if($application->status === 'pending')

        {{-- Approve card --}}
        <div class="card" style="padding:20px;border-color:rgba(45,212,191,0.3);">
            <div style="font-size:13px;font-weight:700;color:var(--teal);margin-bottom:14px;
                        display:flex;align-items:center;gap:7px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                Approve Application
            </div>
            <form method="POST" action="{{ route('coordinator.applications.approve', $application->id) }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                                  letter-spacing:.5px;margin-bottom:6px;">
                        REMARKS <span style="font-weight:400;">(optional)</span>
                    </label>
                    <textarea name="remarks" rows="3" placeholder="Add a note for the student…"
                              style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);
                                     background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;
                                     outline:none;font-family:inherit;box-sizing:border-box;"
                              onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                </div>
                <button type="submit"
                        style="width:100%;padding:10px;background:var(--teal);color:var(--bg);border:none;
                               border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    ✓ Confirm Approve
                </button>
            </form>
        </div>

        {{-- Reject card --}}
        <div class="card" style="padding:20px;border-color:rgba(248,113,113,0.3);">
            <div style="font-size:13px;font-weight:700;color:var(--coral);margin-bottom:14px;
                        display:flex;align-items:center;gap:7px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                Reject Application
            </div>
            <form method="POST" action="{{ route('coordinator.applications.reject', $application->id) }}">
                @csrf
                <div style="margin-bottom:12px;">
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                                  letter-spacing:.5px;margin-bottom:6px;">
                        REASON <span style="color:var(--coral);">*</span>
                    </label>
                    <textarea name="remarks" rows="3" placeholder="State the reason for rejection…" required
                              style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid rgba(248,113,113,0.35);
                                     background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;
                                     outline:none;font-family:inherit;box-sizing:border-box;"
                              onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='rgba(248,113,113,0.35)'"></textarea>
                </div>
                <button type="submit"
                        style="width:100%;padding:10px;background:var(--coral);color:var(--bg);border:none;
                               border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    ✕ Confirm Reject
                </button>
            </form>
        </div>

        @else

        {{-- Already reviewed state --}}
        <div class="card" style="padding:20px;text-align:center;">
            <div style="font-size:28px;margin-bottom:10px;">
                {{ $application->status === 'approved' ? '✅' : '✕' }}
            </div>
            <div style="font-size:13px;font-weight:600;color:var(--{{ $s['color'] }});">
                {{ $s['label'] }}
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:6px;line-height:1.5;">
                This application has already been reviewed and cannot be changed here.
            </div>
        </div>

        @endif

        {{-- Meta card --}}
        <div class="card" style="padding:16px;">
            <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">TIMELINE</div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:var(--muted);">Submitted</span>
                    <span style="color:var(--text);font-weight:500;">{{ $application->created_at->format('M d, Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:var(--muted);">Last updated</span>
                    <span style="color:var(--text);font-weight:500;">{{ $application->updated_at->format('M d, Y') }}</span>
                </div>
                @if($application->reviewed_at)
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:var(--muted);">Reviewed</span>
                    <span style="color:var(--{{ $s['color'] }});font-weight:600;">{{ $application->reviewed_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection