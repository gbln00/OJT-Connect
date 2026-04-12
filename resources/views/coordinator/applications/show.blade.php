@extends('layouts.coordinator-app')
@section('title', 'Application — ' . $application->student->name)
@section('page-title', 'Application Detail')
@section('content')

@php
    $statusMap = [
        'pending'  => ['class' => 'gold',  'label' => 'Pending Review'],
        'approved' => ['class' => 'teal',  'label' => 'Approved'],
        'rejected' => ['class' => 'coral', 'label' => 'Rejected'],
    ];
    $s = $statusMap[$application->status] ?? $statusMap['pending'];
@endphp

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Applications / Detail
    </span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--border2);">·</span>
    <a href="{{ route('coordinator.companies.index') }}" class="btn btn-ghost btn-sm">
        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back
    </a>x`
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start;" class="fade-up fade-up-1">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:16px;">
        
        {{-- Student info card --}}
        <div class="card">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                        {{ strtoupper(substr($application->student->name ?? 'S', 0, 2)) }}
                    </div>
                    <div>
                        <div class="card-title">{{ $application->student->name ?? '—' }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">{{ $application->student->email ?? '' }}</div>
                    </div>
                </div>
                <span class="status-dot {{ $application->status }}" style="font-size:13px;">{{ $s['label'] }}</span>
                
            </div>

            {{-- Application fields --}}
            <div style="padding:20px;">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--muted);margin-bottom:16px;">Application Details</div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Company</div>
                        <div style="font-size:13.5px;font-weight:600;color:var(--text);">{{ $application->company->name ?? '—' }}</div>
                        @if($application->company?->address)
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $application->company->address }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Program</div>
                        <div style="font-size:13.5px;color:var(--text);">{{ $application->program }}</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">School Year</div>
                        <div style="font-size:13.5px;color:var(--text);">{{ $application->school_year }}</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Semester</div>
                        <div style="font-size:13.5px;color:var(--text);">{{ $application->semester }}</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Required Hours</div>
                        <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:var(--blue);">{{ number_format($application->required_hours) }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">hours</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Submitted</div>
                        <div style="font-size:13.5px;color:var(--text);">{{ $application->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                {{-- Document --}}
                <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:18px;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Supporting Document</div>
                    @if($application->document_path)
                    <a href="{{ Storage::url($application->document_path) }}" target="_blank" class="btn btn-ghost btn-sm">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        View Document
                    </a>
                    @else
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No document uploaded.</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Review record (if reviewed) --}}
        @if($application->reviewed_at)
        <div class="card">
            <div class="card-header">
                <div class="card-title">Review record</div>
            </div>
            <div style="padding:20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Reviewed by</div>
                        <div style="font-size:13px;color:var(--text);font-weight:600;">{{ $application->reviewer->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Reviewed on</div>
                        <div style="font-size:13px;color:var(--text);">{{ $application->reviewed_at->format('M d, Y \a\t h:i A') }}</div>
                    </div>
                </div>
                @if($application->remarks)
                <div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Remarks</div>
                    <div style="padding:12px 14px;background:var(--surface2);border-left:2px solid var(--crimson);font-size:13px;color:var(--text);line-height:1.6;">
                        {{ $application->remarks }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        @if($application->status === 'pending')

        {{-- Approve --}}
        <div class="card" style="border-color:rgba(45,212,191,0.2);">
            <div class="card-header" style="border-bottom-color:rgba(45,212,191,0.15);">
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--teal);display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    Approve Application
                </div>
            </div>
            <form method="POST" action="{{ route('coordinator.applications.approve', $application->id) }}" style="padding:16px;">
                @csrf
                <label class="form-label">Remarks <span style="font-weight:300;text-transform:none;letter-spacing:0;">(optional)</span></label>
                <textarea name="remarks" rows="3" placeholder="Add a note for the student…"
                          class="form-input" style="resize:vertical;margin-bottom:12px;border-color:rgba(45,212,191,0.2);"
                          onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='rgba(45,212,191,0.2)'"></textarea>
                <button type="submit" class="btn btn-approve" style="width:100%;justify-content:center;">
                    ✓ Confirm Approve
                </button>
            </form>
        </div>

        {{-- Reject --}}
        <div class="card" style="border-color:rgba(248,113,113,0.2);">
            <div class="card-header" style="border-bottom-color:rgba(248,113,113,0.15);">
                <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--coral);display:flex;align-items:center;gap:6px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reject Application
                </div>
            </div>
            <form method="POST" action="{{ route('coordinator.applications.reject', $application->id) }}" style="padding:16px;">
                @csrf
                <label class="form-label">Reason <span style="color:var(--crimson);">✦</span></label>
                <textarea name="remarks" rows="3" placeholder="State the reason for rejection…" required
                          class="form-input" style="resize:vertical;margin-bottom:12px;border-color:rgba(248,113,113,0.2);"
                          onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='rgba(248,113,113,0.2)'"></textarea>
                <button type="submit" class="btn btn-reject" style="width:100%;justify-content:center;">
                    ✕ Confirm Reject
                </button>
            </form>
        </div>

        @else

        <div class="card" style="padding:24px;text-align:center;">
            <div style="font-size:28px;margin-bottom:10px;">{{ $application->status === 'approved' ? '✅' : '✕' }}</div>
            <span class="status-dot {{ $application->status }}" style="font-size:14px;font-weight:600;">{{ $s['label'] }}</span>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:10px;line-height:1.6;letter-spacing:0.04em;">
                // This application has already been<br>reviewed and cannot be changed.
            </div>
        </div>

        @endif

        {{-- Timeline --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Timeline</div>
            </div>
            <div style="padding:4px 0;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--text2);">Submitted</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $application->created_at->format('M d, Y') }}</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--text2);">Last updated</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $application->updated_at->format('M d, Y') }}</span>
                </div>
                @if($application->reviewed_at)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 20px;">
                    <span style="font-size:12px;color:var(--text2);">Reviewed</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--{{ $s['class'] }});">{{ $application->reviewed_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection