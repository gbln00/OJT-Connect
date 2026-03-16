@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'OJT Applications')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('error') }}
    </div>
@endif

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:20px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total applications</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending</div>
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
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['rejected'] }}</div>
        <div class="stat-label">Rejected</div>
    </div>

</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.applications.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name or email..."
               style="flex:1;min-width:160px;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">

        <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All status</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <select name="company_id" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        <select name="semester" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All semesters</option>
            <option value="1st"    {{ request('semester') === '1st'    ? 'selected' : '' }}>1st Semester</option>
            <option value="2nd"    {{ request('semester') === '2nd'    ? 'selected' : '' }}>2nd Semester</option>
            <option value="Summer" {{ request('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
        </select>

        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">
            Filter
        </button>

        @if(request()->hasAny(['search','status','company_id','semester']))
            <a href="{{ route('admin.applications.index') }}"
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
                    <th>#</th>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Program</th>
                    <th>Semester</th>
                    <th>Req. Hours</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td style="color:var(--muted);font-size:11px;">{{ $app->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--gold);flex-shrink:0;">
                                {{ strtoupper(substr($app->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $app->student->name }}</div>
                                <div style="font-size:11px;color:var(--muted);">{{ $app->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--text);">{{ $app->company->name }}</td>
                    <td>{{ $app->program }}</td>
                    <td>{{ $app->semester }} — {{ $app->school_year }}</td>
                    <td>{{ number_format($app->required_hours) }} hrs</td>
                    <td>
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;
                            background:var(--{{ $app->status_class }}-dim);color:var(--{{ $app->status_class }});">
                            {{ $app->status_label }}
                        </span>
                    </td>
                    <td style="font-size:12px;color:var(--muted);">{{ $app->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="{{ route('admin.applications.show', $app) }}"
                               style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;"
                               onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
                               View
                            </a>

                            @if($app->isPending())
                            <button onclick="openApprove({{ $app->id }}, '{{ addslashes($app->student->name) }}')"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--teal);">
                                Approve
                            </button>
                            <button onclick="openReject({{ $app->id }}, '{{ addslashes($app->student->name) }}')"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--coral);">
                                Reject
                            </button>
                            @endif

                            <form method="POST" action="{{ route('admin.applications.destroy', $app) }}"
                                  onsubmit="return confirm('Delete this application? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--muted);">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:var(--muted);">
                        No applications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($applications->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }} applications
        </span>
        <div style="display:flex;gap:4px;">
            @if($applications->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
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
        <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;">Approve application</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;">Student: <span id="approve-name" style="color:var(--text);"></span></div>
        <form id="approve-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Remarks <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <textarea name="remarks" rows="3"
                    placeholder="Add a note for the student..."
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

{{-- REJECT MODAL --}}
<div id="reject-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:300;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border:1px solid var(--border2);border-radius:12px;padding:24px;width:100%;max-width:420px;margin:16px;">
        <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;">Reject application</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;">Student: <span id="reject-name" style="color:var(--text);"></span></div>
        <form id="reject-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Reason for rejection <span style="color:var(--coral);">*</span></label>
                <textarea name="remarks" rows="3" required
                    placeholder="Explain why this application is being rejected..."
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
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApprove(id, name) {
    document.getElementById('approve-name').textContent = name;
    document.getElementById('approve-form').action = `/admin/applications/${id}/approve`;
    document.getElementById('approve-modal').style.display = 'flex';
}
function openReject(id, name) {
    document.getElementById('reject-name').textContent = name;
    document.getElementById('reject-form').action = `/admin/applications/${id}/reject`;
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeModals() {
    document.getElementById('approve-modal').style.display = 'none';
    document.getElementById('reject-modal').style.display = 'none';
}
// Close on backdrop click
['approve-modal','reject-modal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModals();
    });
});
</script>

@endsection