@extends('layouts.app')
@section('title', 'Report Detail')
@section('page-title', 'Weekly Reports')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- BACK + STATUS --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <a href="{{ route('admin.reports.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
        Back to reports
    </a>
    <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:500;
        background:var(--{{ $report->status_class }}-dim);color:var(--{{ $report->status_class }});">
        {{ $report->status_label }}
    </span>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Student information</div>
            </div>
            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Full name</div>
                    <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $report->student->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Email</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $report->student->email }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Company</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->application->company->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Program</div>
                    <div style="font-size:13px;color:var(--text);">{{ $report->application->program }}</div>
                </div>
            </div>
        </div>

        {{-- Report details --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    Week {{ $report->week_number }} report
                    <span style="font-size:12px;font-weight:400;color:var(--muted);margin-left:8px;">{{ $report->date_range }}</span>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">Description / work done</div>
                <div style="font-size:13.5px;color:var(--text);line-height:1.7;white-space:pre-wrap;">{{ $report->description }}</div>
            </div>
        </div>

        {{-- Uploaded file --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Attached file</div>
            </div>
            <div style="padding:20px;">
                @if($report->file_path)
                    <a href="{{ Storage::url($report->file_path) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;border:1px solid var(--border2);color:var(--blue);font-size:13px;text-decoration:none;background:var(--blue-dim);"
                       onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        View attached file
                    </a>
                @else
                    <div style="font-size:13px;color:var(--muted);">No file attached.</div>
                @endif
            </div>
        </div>

        {{-- Existing feedback --}}
        @if($report->feedback)
        <div class="card">
            <div class="card-header">
                <div class="card-title">Feedback</div>
            </div>
            <div style="padding:20px;font-size:13px;color:var(--muted2);line-height:1.6;">
                {{ $report->feedback }}
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Review status --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Review status</div>
            </div>
            <div style="padding:4px 0;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Status</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--{{ $report->status_class }});">{{ $report->status_label }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed by</span>
                    <span style="font-size:12.5px;color:var(--text);">{{ $report->reviewer?->name ?? '—' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 18px;">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed at</span>
                    <span style="font-size:12.5px;color:var(--text);">{{ $report->reviewed_at?->format('M d, Y') ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Actions (only if pending) --}}
        @if($report->isPending())
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actions</div>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:4px;">Feedback (optional)</label>
                        <textarea name="feedback" rows="2" placeholder="Add a note..."
                            style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;"
                            onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--teal);color:var(--bg);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                        ✓ Approve report
                    </button>
                </form>

                <div style="height:1px;background:var(--border);"></div>

                {{-- Return --}}
                <form method="POST" action="{{ route('admin.reports.return', $report) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:4px;">Reason for returning <span style="color:var(--coral);">*</span></label>
                        <textarea name="feedback" rows="2" required placeholder="Explain what needs to be revised..."
                            style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;"
                            onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--coral);color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                        ↩ Return report
                    </button>
                </form>

            </div>
        </div>
        @endif

    </div>
</div>

@endsection