@extends('layouts.app')
@section('title', 'Report Detail')
@section('page-title', 'Weekly Reports')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- BACK + STATUS --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <a href="{{ route('admin.reports.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;transition:color 0.15s;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
        Back to reports
    </a>
    <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:500;
        background:var(--{{ $report->status_class }}-dim);color:var(--{{ $report->status_class }});">
        {{ $report->status_label }}
    </span>
</div>

<div style="max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 320px;gap:16px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info --}}
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:rgba(140,14,3,0.08);display:flex;align-items:center;justify-content:center;color:var(--crimson);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    Student information
                </div>
            </div>
            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Full name</div>
                    <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $report->student->name }}</div>
                </div>
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Email</div>
                    <div style="font-size:12px;color:var(--muted2);font-family:'DM Mono',monospace;">{{ $report->student->email }}</div>
                </div>
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Company</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->application->company->name }}</div>
                </div>
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                    <div style="font-size:10px;color:var(--muted);margin-bottom:4px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Program</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->application->program }}</div>
                </div>
            </div>
        </div>

        {{-- Report content --}}
        <div class="card fade-up fade-up-1">
            <div class="card-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(96,165,250,0.1);border:1px solid rgba(96,165,250,0.2);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:13px;font-weight:700;color:var(--blue);">
                        W{{ $report->week_number }}
                    </div>
                    <div>
                        <div class="card-title">Week {{ $report->week_number }} report</div>
                        <div style="font-size:12px;color:var(--muted);margin-top:1px;">{{ $report->date_range }}</div>
                    </div>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="font-size:11px;color:var(--muted);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Description / work done</div>
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;font-size:13.5px;color:var(--text);line-height:1.7;white-space:pre-wrap;">{{ $report->description }}</div>
            </div>
        </div>

        {{-- Attached file --}}
        <div class="card fade-up fade-up-2">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:rgba(96,165,250,0.1);display:flex;align-items:center;justify-content:center;color:var(--blue);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    </div>
                    Attached file
                </div>
            </div>
            <div style="padding:20px;">
                @if($report->file_path)
                    <a href="{{ Storage::url($report->file_path) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;border:1px solid var(--border2);color:var(--blue);font-size:13px;text-decoration:none;background:rgba(96,165,250,0.06);transition:all 0.15s;"
                       onmouseover="this.style.background='rgba(96,165,250,0.12)';this.style.borderColor='var(--blue)'" onmouseout="this.style.background='rgba(96,165,250,0.06)';this.style.borderColor='var(--border2)'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        View attached file
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.5;"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                @else
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);background:var(--surface2);border:1px dashed var(--border2);border-radius:8px;padding:14px 16px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        No file attached.
                    </div>
                @endif
            </div>
        </div>

        {{-- Existing feedback --}}
        @if($report->feedback)
        <div class="card fade-up fade-up-3">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--surface2);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--muted2);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    Coordinator feedback
                </div>
            </div>
            <div style="padding:20px;">
                <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:16px;font-size:13px;color:var(--muted2);line-height:1.7;">
                    {{ $report->feedback }}
                </div>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Review status --}}
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title">Review status</div>
            </div>
            <div style="padding:4px 0;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Status</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--{{ $report->status_class }});">{{ $report->status_label }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed by</span>
                    <span style="font-size:12.5px;color:var(--text);">{{ $report->reviewer?->name ?? '—' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed at</span>
                    <span style="font-size:11px;color:var(--text);font-family:'DM Mono',monospace;">{{ $report->reviewed_at?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions (only if pending) --}}
        @if($report->isPending())
        <div class="card fade-up fade-up-1">
            <div class="card-header">
                <div class="card-title">Actions</div>
                <span style="font-size:10px;padding:2px 8px;border-radius:10px;background:rgba(240,180,41,0.1);color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">Pending</span>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:5px;font-weight:500;">Feedback <span style="font-weight:400;color:var(--muted);">(optional)</span></label>
                        <textarea name="feedback" rows="2" placeholder="Add a note..."
                            style="width:100%;padding:9px 11px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;transition:border 0.15s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--teal);color:var(--bg);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Approve report
                    </button>
                </form>

                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                    <span style="font-size:11px;color:var(--muted);">or</span>
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                </div>

                {{-- Return --}}
                <form method="POST" action="{{ route('admin.reports.return', $report) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:5px;font-weight:500;">Reason for returning <span style="color:var(--coral);">*</span></label>
                        <textarea name="feedback" rows="2" required placeholder="Explain what needs to be revised..."
                            style="width:100%;padding:9px 11px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;transition:border 0.15s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:1px solid var(--coral);background:none;color:var(--coral);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(248,113,113,0.08)'" onmouseout="this.style.background='none'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9,14 4,9 9,4"/><path d="M20 20v-7a4 4 0 00-4-4H4"/></svg>
                        Return report
                    </button>
                </form>

            </div>
        </div>
        @endif

    </div>
</div>

@endsection