@extends('layouts.app')
@section('title', 'Weekly Reports')
@section('page-title', 'Weekly Reports')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total reports</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending review</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 1 0 .49-3.51"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['returned'] }}</div>
        <div class="stat-label">Returned</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.reports.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name or email..."
               style="flex:1;min-width:160px;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">

        <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All status</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
        </select>

        @if($maxWeek > 0)
        <select name="week_number" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All weeks</option>
            @for($w = 1; $w <= $maxWeek; $w++)
                <option value="{{ $w }}" {{ request('week_number') == $w ? 'selected' : '' }}>Week {{ $w }}</option>
            @endfor
        </select>
        @endif

        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">
            Filter
        </button>

        @if(request()->hasAny(['search', 'status', 'week_number']))
            <a href="{{ route('admin.reports.index') }}"
               style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;">
                Clear
            </a>
        @endif
    </form>
</div>

{{-- TABLE --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Week</th>
                    <th>Date range</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($report->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $report->student->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $report->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $report->application->company->name }}</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:var(--blue-dim);color:var(--blue);">
                            Week {{ $report->week_number }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--muted2);">{{ $report->date_range }}</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                            background:var(--{{ $report->status_class }}-dim);color:var(--{{ $report->status_class }});">
                            {{ $report->status_label }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $report->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="{{ route('admin.reports.show', $report) }}"
                               style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;"
                               onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
                               View
                            </a>
                            @if($report->isPending())
                            <button onclick="openApprove({{ $report->id }}, '{{ addslashes($report->student->name) }}', {{ $report->week_number }})"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--teal);">
                                Approve
                            </button>
                            <button onclick="openReturn({{ $report->id }}, '{{ addslashes($report->student->name) }}', {{ $report->week_number }})"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--coral);">
                                Return
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        No weekly reports found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $reports->firstItem() }}–{{ $reports->lastItem() }} of {{ $reports->total() }} reports
        </span>
        <div style="display:flex;gap:4px;">
            @if($reports->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $reports->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($reports->hasMorePages())
                <a href="{{ $reports->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- APPROVE MODAL --}}
<div id="approve-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:300;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border:1px solid var(--border2);border-radius:12px;padding:24px;width:100%;max-width:420px;margin:16px;">
        <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;">Approve report</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;"><span id="approve-label"></span></div>
        <form id="approve-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Feedback <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <textarea name="feedback" rows="3" placeholder="Add a note for the student..."
                    style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModals()"
                    style="padding:8px 16px;border-radius:8px;border:1px solid var(--border2);background:none;color:var(--muted2);font-size:13px;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:8px 20px;border-radius:8px;border:none;background:var(--teal);color:var(--bg);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

{{-- RETURN MODAL --}}
<div id="return-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:300;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border:1px solid var(--border2);border-radius:12px;padding:24px;width:100%;max-width:420px;margin:16px;">
        <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;">Return report</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;"><span id="return-label"></span></div>
        <form id="return-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Reason for returning <span style="color:var(--coral);">*</span></label>
                <textarea name="feedback" rows="3" required placeholder="Explain what needs to be revised..."
                    style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModals()"
                    style="padding:8px 16px;border-radius:8px;border:1px solid var(--border2);background:none;color:var(--muted2);font-size:13px;cursor:pointer;font-family:inherit;">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:8px 20px;border-radius:8px;border:none;background:var(--coral);color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                    Return
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApprove(id, name, week) {
    document.getElementById('approve-label').textContent = name + ' — Week ' + week;
    document.getElementById('approve-form').action = `/admin/reports/${id}/approve`;
    document.getElementById('approve-modal').style.display = 'flex';
}
function openReturn(id, name, week) {
    document.getElementById('return-label').textContent = name + ' — Week ' + week;
    document.getElementById('return-form').action = `/admin/reports/${id}/return`;
    document.getElementById('return-modal').style.display = 'flex';
}
function closeModals() {
    document.getElementById('approve-modal').style.display = 'none';
    document.getElementById('return-modal').style.display = 'none';
}
['approve-modal','return-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModals();
    });
});
</script>

@endsection