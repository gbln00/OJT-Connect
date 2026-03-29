@extends('layouts.app')
@section('title', 'Report Detail')
@section('page-title', 'Weekly Reports')

@section('content')
@php
$pillMap = ['pending'=>'gold','approved'=>'green','returned'=>'crimson'];
$pillCls = $pillMap[$report->status] ?? 'steel';
@endphp

<div style="max-width:1100px;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow + back --}}
    <div class="fade-up" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
                Weekly Reports / Week {{ $report->week_number }}
            </span>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="status-pill {{ $pillCls }}">{{ $report->status_label }}</span>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 320px;gap:14px;align-items:start;" class="fade-up fade-up-1">

        {{-- LEFT --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Student info --}}
            <div class="card">
                <div class="card-header">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                            {{ strtoupper(substr($report->student->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="card-title">{{ $report->student->name }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $report->student->email }}</div>
                        </div>
                    </div>
                </div>
                <div style="padding:20px;">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">Company</div>
                            <div class="detail-value" style="font-weight:600;">{{ $report->application->company->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Program</div>
                            <div class="detail-value">{{ $report->application->program }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Report content --}}
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Week {{ $report->week_number }} — Report</div>
                    </div>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.08em;">
                        {{ $report->date_range }}
                    </span>
                </div>
                <div style="padding:20px;">
                    <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);margin-bottom:10px;">Description / work done</div>
                    <div style="font-size:14px;color:var(--text);line-height:1.75;white-space:pre-wrap;">{{ $report->description }}</div>
                </div>
            </div>

            {{-- Attached file --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Attached File</div>
                </div>
                <div style="padding:20px;">
                    @if($report->file_path)
                        <a href="{{ Storage::url($report->file_path) }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:10px;padding:12px 18px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);text-decoration:none;transition:border-color 0.15s;"
                           onmouseover="this.style.borderColor='var(--crimson)'" onmouseout="this.style.borderColor='var(--border2)'">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14,2 14,8 20,8"/>
                            </svg>
                            <span style="font-size:13px;font-weight:500;">View attached file</span>
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-left:auto;color:var(--muted);">
                                <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                            </svg>
                        </a>
                    @else
                        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);letter-spacing:0.05em;">// No file attached.</div>
                    @endif
                </div>
            </div>

            {{-- Existing feedback --}}
            @if($report->feedback)
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Feedback</div>
                </div>
                <div style="padding:20px;font-size:13.5px;color:var(--text2);line-height:1.7;">
                    {{ $report->feedback }}
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT --}}
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
                        ['Status',      '<span class="status-pill ' . $pillCls . '">' . $report->status_label . '</span>'],
                        ['Reviewed by', $report->reviewer?->name ?? '—'],
                        ['Reviewed at', $report->reviewed_at?->format('M d, Y') ?? '—'],
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
            @if($report->isPending())
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Actions</div>
                </div>
                <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                    <form method="POST" action="{{ route('admin.reports.approve', $report) }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label class="form-label-sm">Feedback <span style="color:var(--muted);text-transform:none;">(optional)</span></label>
                            <textarea name="feedback" rows="2" placeholder="Add a note..."
                                class="form-input-sm" style="resize:none;font-family:inherit;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-approve" style="width:100%;justify-content:center;padding:10px;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                            Approve report
                        </button>
                    </form>

                    <div style="height:1px;background:var(--border);"></div>

                    <form method="POST" action="{{ route('admin.reports.return', $report) }}">
                        @csrf
                        <div style="margin-bottom:8px;">
                            <label class="form-label-sm">Reason for returning <span style="color:var(--crimson);">✦</span></label>
                            <textarea name="feedback" rows="2" required placeholder="Explain what needs to be revised..."
                                class="form-input-sm" style="resize:none;font-family:inherit;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;padding:10px;">
                            ↩ Return report
                        </button>
                    </form>

                </div>
            </div>
            @endif

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