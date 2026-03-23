@extends('layouts.coordinator-app')
@section('title', 'Weekly Reports')
@section('page-title', 'Weekly Reports')
@section('content')

{{-- Pending banner --}}
@if($pending > 0)
<div style="background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);border-radius:8px;padding:12px 16px;
            margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:var(--gold);">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span><strong>{{ $pending }}</strong> weekly {{ Str::plural('report', $pending) }} waiting for your review.</span>
</div>
@endif

{{-- Filter bar --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <form method="GET" action="{{ route('coordinator.reports.index') }}"
          style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

        <select name="status"
                style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);
                       color:var(--text);font-size:13px;outline:none;font-family:inherit;cursor:pointer;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="returned" {{ request('status')==='returned' ? 'selected' : '' }}>Returned</option>
        </select>

        <button type="submit"
                style="padding:8px 18px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;
                       font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
            Filter
        </button>

        @if(request('status'))
        <a href="{{ route('coordinator.reports.index') }}"
           style="padding:8px 14px;border:1px solid var(--border2);border-radius:8px;font-size:13px;color:var(--muted);text-decoration:none;">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Report cards --}}
@forelse($reports as $report)
<div class="card" style="padding:20px;margin-bottom:12px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;">

        {{-- Left: week badge + info --}}
        <div style="display:flex;gap:14px;align-items:flex-start;">
            <div style="min-width:52px;text-align:center;padding:10px 8px;background:var(--surface2);
                        border-radius:10px;border:1px solid var(--border2);flex-shrink:0;">
                <div style="font-size:10px;color:var(--muted);letter-spacing:.5px;">WK</div>
                <div style="font-size:22px;font-weight:800;color:var(--teal);line-height:1;">{{ $report->week_number }}</div>
            </div>
            <div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                    <div style="font-size:13px;font-weight:700;color:var(--text);">
                        {{ $report->student->name ?? '—' }}
                    </div>
                    <div style="font-size:11.5px;color:var(--muted);">
                        {{ $report->application->company->name ?? '—' }}
                    </div>
                    <span style="padding:2px 9px;border-radius:20px;font-size:11px;font-weight:600;
                        background:var(--{{ $report->status === 'approved' ? 'teal' : ($report->status === 'returned' ? 'blue' : 'gold') }}-dim);
                        color:var(--{{ $report->status === 'approved' ? 'teal' : ($report->status === 'returned' ? 'blue' : 'gold') }});">
                        {{ ucfirst($report->status) }}
                    </span>
                </div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">
                    {{ \Carbon\Carbon::parse($report->week_start)->format('M d') }} –
                    {{ \Carbon\Carbon::parse($report->week_end)->format('M d, Y') }}
                    &nbsp;·&nbsp;
                    Submitted {{ $report->created_at->format('M d, Y') }}
                </div>
                <div style="font-size:12.5px;color:var(--muted2);line-height:1.6;max-width:560px;
                            display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $report->description }}
                </div>

                {{-- Existing feedback --}}
                @if($report->feedback)
                <div style="margin-top:10px;padding:8px 12px;background:var(--surface2);
                            border-left:3px solid var(--blue);border-radius:0 6px 6px 0;font-size:12px;color:var(--muted);">
                    <span style="font-weight:600;color:var(--blue);">Your feedback: </span>{{ $report->feedback }}
                </div>
                @endif
            </div>
        </div>

        {{-- Right: action buttons --}}
        <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end;flex-shrink:0;">
            @if($report->file_path)
            <a href="{{ Storage::url($report->file_path) }}" target="_blank"
               style="font-size:12px;color:var(--muted);text-decoration:none;border:1px solid var(--border2);
                      padding:5px 12px;border-radius:6px;display:flex;align-items:center;gap:5px;">
                📎 Attachment
            </a>
            @endif

            @if($report->status === 'pending')
            <button onclick="openPanel('approve-{{ $report->id }}')"
                    style="padding:5px 14px;background:var(--teal-dim);color:var(--teal);border:1px solid var(--teal);
                           border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">
                ✓ Approve
            </button>
            <button onclick="openPanel('return-{{ $report->id }}')"
                    style="padding:5px 14px;background:var(--blue-dim);color:var(--blue);border:1px solid var(--blue);
                           border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;">
                ↩ Return
            </button>
            @endif
        </div>

    </div>

    {{-- Approve panel --}}
    @if($report->status === 'pending')
    <div id="approve-{{ $report->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--border2);">
        <form method="POST" action="{{ route('coordinator.reports.approve', $report->id) }}"
              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div style="flex:1;min-width:260px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.5px;">
                    FEEDBACK <span style="font-weight:400;">(optional)</span>
                </label>
                <input type="text" name="feedback" placeholder="Add a comment for the student…"
                       style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);
                              background:var(--surface2);color:var(--text);font-size:13px;outline:none;
                              font-family:inherit;box-sizing:border-box;"
                       onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'">
            </div>
            <button type="submit"
                    style="padding:8px 20px;background:var(--teal);color:var(--bg);border:none;
                           border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                Confirm Approve
            </button>
            <button type="button" onclick="closePanel('approve-{{ $report->id }}')"
                    style="padding:8px 14px;background:none;border:1px solid var(--border2);color:var(--muted);
                           border-radius:8px;font-size:13px;cursor:pointer;font-family:inherit;">
                Cancel
            </button>
        </form>
    </div>

    {{-- Return panel --}}
    <div id="return-{{ $report->id }}" style="display:none;margin-top:16px;padding-top:16px;border-top:1px solid var(--border2);">
        <form method="POST" action="{{ route('coordinator.reports.return', $report->id) }}"
              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <div style="flex:1;min-width:260px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.5px;">
                    REASON FOR RETURN <span style="color:var(--coral);">*</span>
                </label>
                <input type="text" name="feedback" placeholder="Tell the student what needs to be revised…" required
                       style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid rgba(96,165,250,0.4);
                              background:var(--surface2);color:var(--text);font-size:13px;outline:none;
                              font-family:inherit;box-sizing:border-box;"
                       onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='rgba(96,165,250,0.4)'">
            </div>
            <button type="submit"
                    style="padding:8px 20px;background:var(--blue);color:var(--bg);border:none;
                           border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                Confirm Return
            </button>
            <button type="button" onclick="closePanel('return-{{ $report->id }}')"
                    style="padding:8px 14px;background:none;border:1px solid var(--border2);color:var(--muted);
                           border-radius:8px;font-size:13px;cursor:pointer;font-family:inherit;">
                Cancel
            </button>
        </form>
    </div>
    @endif

</div>
@empty
<div class="card" style="padding:60px;text-align:center;">
    <div style="font-size:28px;margin-bottom:10px;">📋</div>
    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">No weekly reports found</div>
    <div style="font-size:12px;color:var(--muted);">Try adjusting your filters.</div>
</div>
@endforelse

{{-- Pagination --}}
<div style="margin-top:16px;">
    {{ $reports->links() }}
</div>

<script>
    function openPanel(id) {
        document.querySelectorAll('[id^="approve-"], [id^="return-"]').forEach(el => {
            el.style.display = 'none';
        });
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'block';
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            const input = el.querySelector('input[name="feedback"]');
            if (input) input.focus();
        }
    }
    function closePanel(id) {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    }
</script>

@endsection