@extends('layouts.app')
@section('title', 'Application Details')
@section('page-title', 'Application Details')

@section('content')
@php
$pillMap = ['pending'=>'gold','approved'=>'green','rejected'=>'crimson'];
$pillCls = $pillMap[$application->status] ?? 'steel';
@endphp

<div style="max-width:1100px;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow + back --}}
    <div class="fade-up" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
                Applications / #{{ $application->id }}
            </span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="status-pill {{ $pillCls }}">{{ $application->status_label }}</span>
            <a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>
    </div>

    {{-- TWO COLUMN GRID --}}
    <div style="display:grid;grid-template-columns:1fr 320px;gap:14px;align-items:start;" class="fade-up fade-up-1">

        {{-- LEFT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Student card --}}
            <div class="card">
                <div class="card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                            {{ strtoupper(substr($application->student->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="card-title">{{ $application->student->name }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $application->student->email }}</div>
                        </div>
                    </div>
                    <span class="status-dot {{ $application->student->is_active ? 'active' : 'inactive' }}">
                        {{ $application->student->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div style="padding:20px;">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Program / Course</div>
                            <div class="detail-value">{{ $application->program }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Account status</div>
                            <div class="detail-value">{{ $application->student->is_active ? 'Active' : 'Inactive' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OJT details --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">OJT Details</div>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.08em;">
                        {{ $application->semester }} — {{ $application->school_year }}
                    </span>
                </div>
                <div style="padding:20px;">
                    <div class="detail-grid" style="grid-template-columns:repeat(3,1fr);">
                        <div class="detail-item">
                            <div class="detail-label">Company</div>
                            <div class="detail-value" style="font-weight:600;">{{ $application->company->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Industry</div>
                            <div class="detail-value">{{ $application->company->industry ?? '—' }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Required hours</div>
                            <div class="detail-value">
                                <span style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:var(--text);">{{ number_format($application->required_hours) }}</span>
                                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">School year</div>
                            <div class="detail-value">{{ $application->school_year }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Semester</div>
                            <div class="detail-value">{{ $application->semester }} Semester</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date submitted</div>
                            <div class="detail-value" style="font-family:'DM Mono',monospace;font-size:12px;">{{ $application->created_at->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Document --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Submitted Document</div>
                </div>
                <div style="padding:20px;">
                    @if($application->document_path)
                        <a href="{{ Storage::url($application->document_path) }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:10px;padding:12px 18px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);text-decoration:none;transition:border-color 0.15s;"
                           onmouseover="this.style.borderColor='var(--crimson)'" onmouseout="this.style.borderColor='var(--border2)'">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                            </svg>
                            <span style="font-size:13px;font-weight:500;">View document</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-left:auto;color:var(--muted);">
                                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                        </a>
                    @else
                        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);letter-spacing:0.05em;">// No document uploaded.</div>
                    @endif
                    
                </div>
            </div>
             
            {{-- Remarks --}}
            @if($application->remarks)
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Remarks</div>
                </div>
                <div style="padding:20px;font-size:13.5px;color:var(--text2);line-height:1.7;">
                    {{ $application->remarks }}
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Review status --}}
            <div class="card" style="position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--crimson);"></div>
                <div class="card-header">
                    <div class="card-title">Review Status</div>
                </div>
                <div style="padding:4px 0;">
                    @php
                    $reviewRows = [
                        ['Status',      '<span class="status-pill ' . $pillCls . '">' . $application->status_label . '</span>'],
                        ['Reviewed by', $application->reviewer?->name ?? '—'],
                        ['Reviewed at', $application->reviewed_at?->format('M d, Y') ?? '—'],
                    ];
                    @endphp
                    @foreach($reviewRows as [$lbl, $val])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 20px;border-bottom:1px solid var(--border);">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);">{{ $lbl }}</span>
                        <span style="font-size:13px;color:var(--text);">{!! $val !!}</span>
                    </div>
                    @endforeach
                </div>
               
            </div>

            {{-- Actions --}}
            @if($application->isPending())
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Actions</div>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                    <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label class="form-label-sm">Remarks <span style="color:var(--muted);text-transform:none;">(optional)</span></label>
                            <textarea name="remarks" rows="2" placeholder="Add a note for the student..."
                                class="form-input-sm" style="resize:none;font-family:inherit;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-approve" style="width:100%;justify-content:center;padding:10px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Approve application
                        </button>
                    </form>

                   

                    <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label class="form-label-sm">Reason for rejection <span style="color:var(--crimson);">✦</span></label>
                            <textarea name="remarks" rows="2" required placeholder="Explain the reason..."
                                class="form-input-sm" style="resize:none;font-family:inherit;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;padding:10px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Reject application
                        </button>
                    </form>

                    <div style="height:1px;background:var(--border);"></div>

                    @if($application->isPending())
                    <form method="POST"
                        action="{{ route('admin.applications.sendToReview', $application) }}"
                        style="display:inline;">
                        @csrf
                        <textarea name="review_notes" placeholder="Review notes (optional)"
                                style="width:100%;padding:8px;margin-bottom:8px;
                                        background:var(--surface2);border:1px solid var(--border2);
                                        color:var(--text);font-size:13px;" rows="2"></textarea>
                        <button type="submit" class="btn btn-ghost">
                            ⟳ Send for Document Review
                        </button>
                    </form>
                    @endif

                    @if($application->isUnderReview())
                    <div style="padding:12px 16px;border:1px solid rgba(96,165,250,0.3);
                        background:rgba(96,165,250,0.07);margin-bottom:16px;">
                        <strong style="color:#60a5fa;">Under Document Review</strong>
                        <p style="font-size:13px;color:var(--text2);margin-top:4px;">
                            Review notes: {{ $application->remarks ?? 'None' }}
                        </p>
                        {{-- Show approve/reject buttons to complete the review --}}
                    </div>
                    @endif
                    
                </div>
            </div>
            @endif

            {{-- Delete --}}
            <div class="card danger-zone">
                <div style="padding:16px;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:10px;letter-spacing:0.05em;">
                        // Permanently deletes this application and its document.
                    </div>
                    <form method="POST" action="{{ route('admin.applications.destroy', $application) }}"
                          onsubmit="return confirm('Delete this application? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
                            Delete application
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('styles')
<style>
.detail-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
}
.detail-item {}
.detail-label {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 5px;
}
.detail-value { font-size: 13.5px; color: var(--text); }
.form-label-sm {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 5px;
}
.form-input-sm {
    width: 100%; padding: 8px 10px;
    background: var(--surface2); border: 1px solid var(--border2);
    color: var(--text); font-size: 12px; outline: none;
    transition: border-color 0.15s; box-sizing: border-box; border-radius: 0;
}
.form-input-sm:focus { border-color: var(--crimson); }
</style>
@endpush
@endsection