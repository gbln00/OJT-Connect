@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'OJT Applications')

@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(5,1fr);">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            </div>
            <span class="stat-tag">all</span>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
            <span class="stat-tag">review</span>
        </div>
        <div class="stat-num">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:rgba(96,165,250,0.12);color:#60a5fa;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <span class="stat-tag">docs</span>
        </div>
        <div class="stat-num">{{ $counts['under_review'] }}</div>
        <div class="stat-label">Under Review</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
            <span class="stat-tag">ok</span>
        </div>
        <div class="stat-num">{{ $counts['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <span class="stat-tag">denied</span>
        </div>
        <div class="stat-num">{{ $counts['rejected'] }}</div>
        <div class="stat-label">Rejected</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('admin.applications.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name or email..."
               class="form-input" style="flex:1;min-width:160px;">

        <select name="status" class="form-select" style="width:auto;">
            <option value="">All status</option>
            <option value="pending"      {{ request('status') === 'pending'       ? 'selected' : '' }}>Pending</option>
            <option value="under_review" {{ request('status') === 'under_review'  ? 'selected' : '' }}>Under Review</option>
            <option value="approved"     {{ request('status') === 'approved'      ? 'selected' : '' }}>Approved</option>
            <option value="rejected"     {{ request('status') === 'rejected'      ? 'selected' : '' }}>Rejected</option>
        </select>

        <select name="company_id" class="form-select" style="width:auto;">
            <option value="">All companies</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                    {{ $company->name }}
                </option>
            @endforeach
        </select>

        <select name="semester" class="form-select" style="width:auto;">
            <option value="">All semesters</option>
            <option value="1st"    {{ request('semester') === '1st'    ? 'selected' : '' }}>1st Semester</option>
            <option value="2nd"    {{ request('semester') === '2nd'    ? 'selected' : '' }}>2nd Semester</option>
            <option value="Summer" {{ request('semester') === 'Summer' ? 'selected' : '' }}>Summer</option>
        </select>

        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>

        @if(request()->hasAny(['search','status','company_id','semester']))
            <a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
</div>

{{-- Bulk Action Bar --}}
<form method="POST" action="{{ route('admin.applications.bulk') }}" id="bulk-form">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input" value="">

    <div id="bulk-bar" style="display:none;background:rgba(140,14,3,0.08);
         border:1px solid rgba(140,14,3,0.2);padding:12px 16px;margin-bottom:12px;
         align-items:center;gap:12px;flex-wrap:wrap;">
        <span id="selected-count" style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">0 selected</span>
        <div style="margin-left:auto;display:flex;gap:8px;">
            <input type="text" name="remarks" placeholder="Remarks (optional for approve / required for reject)"
                   style="padding:5px 10px;border:1px solid var(--border2);background:var(--surface2);
                          color:var(--text);font-size:12px;width:280px;" />
            <button type="button" onclick="bulkSubmit('approve')" class="btn btn-approve btn-sm">✓ Approve Selected</button>
            <button type="button" onclick="bulkSubmit('reject')"  class="btn btn-danger btn-sm">✕ Reject Selected</button>
        </div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="select-all" onchange="toggleAll(this)">
                    </th>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                @php
                    $statusCls = match($app->status) {
                        'approved'     => 'teal',
                        'rejected'     => 'coral',
                        'under_review' => 'blue',
                        default        => 'gold',
                    };
                    $actionable = in_array($app->status, ['pending', 'under_review']);
                @endphp
                <tr>
                    <td>
                        @if($actionable)
                        <input type="checkbox" name="ids[]" value="{{ $app->id }}"
                               class="app-checkbox" onchange="updateBulkBar()">
                        @endif
                    </td>
                    <td>
                        <div style="font-weight:500;color:var(--text);">{{ $app->student->name }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->student->email }}</div>
                    </td>
                    <td>{{ $app->company->name }}</td>
                    <td>{{ $app->program }}</td>
                    <td>
                        <span class="status-pill {{ $statusCls }}">{{ $app->status_label }}</span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $app->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-ghost btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">
                        No applications found.
                        <span style="font-family:'DM Mono',monospace;font-size:11px;"> Try adjusting your filters.</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div class="pagination" style="padding:12px 0;">
        <span class="pagination-info">Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }}</span>
        <div style="display:flex;gap:4px;">
            @if($applications->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $applications->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($applications->hasMorePages())
                <a href="{{ $applications->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
</form>

@push('scripts')
<script>
function toggleAll(cb) {
    document.querySelectorAll('.app-checkbox').forEach(c => c.checked = cb.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.app-checkbox:checked');
    const bar = document.getElementById('bulk-bar');
    bar.style.display = checked.length > 0 ? 'flex' : 'none';
    document.getElementById('selected-count').textContent = `${checked.length} selected`;
}

function bulkSubmit(action) {
    const checked = document.querySelectorAll('.app-checkbox:checked');
    if (checked.length === 0) { alert('Please select at least one application.'); return; }
    const label = action === 'approve' ? 'Approve' : 'Reject';
    if (!confirm(`${label} ${checked.length} application(s)?`)) return;
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}
</script>
@endpush

@endsection