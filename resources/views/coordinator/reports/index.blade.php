@extends('layouts.coordinator-app')
@section('title', 'Weekly Reports')
@section('page-title', 'Weekly Reports')
@section('content')

@if($pending > 0)
<div class="fade-up" style="background:var(--gold-dim);border:1px solid var(--gold-border);color:var(--gold);padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span><strong>{{ $pending }}</strong> weekly {{ Str::plural('report', $pending) }} waiting for your review.</span>
</div>
@endif

{{-- Filter --}}
<div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('coordinator.reports.index') }}" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="status" class="form-input" style="width:auto;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="returned" {{ request('status')==='returned' ? 'selected' : '' }}>Returned</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request('status'))
            <a href="{{ route('coordinator.reports.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
</div>

{{-- Report cards --}}
<div style="display:flex;flex-direction:column;gap:12px;" class="fade-up fade-up-2">
@forelse($reports as $report)
@php
    $rClass = $report->status === 'approved' ? 'teal' : ($report->status === 'returned' ? 'blue' : 'gold');
@endphp
<div class="card">
    <div style="padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">

            {{-- Left: week + info --}}
            <div style="display:flex;gap:16px;align-items:flex-start;">

                {{-- Week number badge --}}
                <div style="min-width:52px;text-align:center;padding:10px 8px;background:var(--surface2);border:1px solid var(--border);flex-shrink:0;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">WK</div>
                    <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--teal);line-height:1;">{{ $report->week_number }}</div>
                </div>

                <div>
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                        <div style="font-size:13px;font-weight:600;color:var(--text);">
                            {{ $report->student->name ?? '—' }}
                        </div>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $report->application->company->name ?? '—' }}</span>
                        <span class="status-dot {{ $report->status }}">{{ ucfirst($report->status) }}</span>
                    </div>

                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:8px;letter-spacing:0.04em;">
                        {{ \Carbon\Carbon::parse($report->week_start)->format('M d') }} –
                        {{ \Carbon\Carbon::parse($report->week_end)->format('M d, Y') }}
                        &nbsp;·&nbsp;
                        Submitted {{ $report->created_at->format('M d, Y') }}
                    </div>

                    <div style="font-size:12.5px;color:var(--muted2);line-height:1.6;max-width:560px;
                                display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $report->description }}
                    </div>

                    @if($report->feedback)
                    <div style="margin-top:10px;padding:10px 14px;background:var(--surface2);border-left:2px solid var(--crimson);font-size:12px;color:var(--muted2);">
                        <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:3px;">Your feedback</span>
                        {{ $report->feedback }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Right: actions --}}
            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-end;flex-shrink:0;">
                @if($report->file_path)
                <a href="{{ Storage::url($report->file_path) }}" target="_blank" class="btn btn-ghost btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    Attachment
                </a>
                @endif

                @if($report->status === 'pending')
                <button onclick="openPanel('approve-{{ $report->id }}')" class="btn btn-approve btn-sm">✓ Approve</button>
                <button onclick="openPanel('return-{{ $report->id }}')"  class="btn btn-return btn-sm">↩ Return</button>
                @endif
            </div>
        </div>

        {{-- Approve panel --}}
        @if($report->status === 'pending')
        <div id="approve-{{ $report->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--teal);margin-bottom:10px;">// Approve Report</div>
            <form method="POST" action="{{ route('coordinator.reports.approve', $report->id) }}"
                  style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                @csrf
                <div style="flex:1;min-width:260px;">
                    <label class="form-label">Feedback <span style="font-weight:300;text-transform:none;letter-spacing:0;">(optional)</span></label>
                    <input type="text" name="feedback" placeholder="Add a comment for the student…"
                           class="form-input" style="border-color:rgba(45,212,191,0.2);"
                           onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='rgba(45,212,191,0.2)'">
                </div>
                <button type="submit" class="btn btn-approve">Confirm Approve</button>
                <button type="button" onclick="closePanel('approve-{{ $report->id }}')" class="btn btn-ghost">Cancel</button>
            </form>
        </div>

        {{-- Return panel --}}
        <div id="return-{{ $report->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;text-transform:uppercase;color:var(--blue);margin-bottom:10px;">// Return Report</div>
            <form method="POST" action="{{ route('coordinator.reports.return', $report->id) }}"
                  style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                @csrf
                <div style="flex:1;min-width:260px;">
                    <label class="form-label">Reason for return <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" name="feedback" placeholder="Tell the student what needs to be revised…" required
                           class="form-input" style="border-color:rgba(96,165,250,0.2);"
                           onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='rgba(96,165,250,0.2)'">
                </div>
                <button type="submit" class="btn btn-return">Confirm Return</button>
                <button type="button" onclick="closePanel('return-{{ $report->id }}')" class="btn btn-ghost">Cancel</button>
            </form>
        </div>
        @endif

    </div>
</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-size:28px;margin-bottom:10px;">📋</div>
    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">No weekly reports found</div>
    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">Try adjusting your filters.</div>
</div>
@endforelse
</div>

{{-- Pagination --}}
<div style="margin-top:16px;">{{ $reports->links() }}</div>

<script>
function openPanel(id) {
    document.querySelectorAll('[id^="approve-"],[id^="return-"]').forEach(el => el.style.display = 'none');
    const el = document.getElementById(id);
    if (el) {
        el.style.display = 'block';
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        const inp = el.querySelector('input[name="feedback"]');
        if (inp) inp.focus();
    }
}
function closePanel(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}
</script>

@endsection