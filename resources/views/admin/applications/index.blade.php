@extends('layouts.app')
@section('title', 'Applications')
@section('page-title', 'OJT Applications')

@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(4,1fr);">
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
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
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

{{-- Bulk Action Bar (shown when items are selected) --}}
<form method="POST" action="{{ route('admin.applications.bulk') }}" id="bulk-form">
    @csrf
    <input type="hidden" name="action" id="bulk-action-input" value="">

    <div id="bulk-bar" style="display:none;background:rgba(140,14,3,0.08);
         border:1px solid rgba(140,14,3,0.2);padding:12px 16px;margin-bottom:12px;
         display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <span id="selected-count" style="font-family:'DM Mono',monospace;font-size:11px;
              color:var(--text2);">0 selected</span>
        <div style="margin-left:auto;display:flex;gap:8px;">
            <input type="text" name="remarks" placeholder="Remarks (optional)"
                   style="padding:5px 10px;border:1px solid var(--border2);
                          background:var(--surface2);color:var(--text);font-size:12px;
                          width:220px;" />
            <button type="button" onclick="bulkSubmit('approve')"
                    class="btn btn-approve btn-sm">✓ Approve Selected</button>
            <button type="button" onclick="bulkSubmit('reject')"
                    class="btn btn-danger btn-sm">✕ Reject Selected</button>
        </div>
    </div>

    {{-- Table with checkboxes --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">
                        <input type="checkbox" id="select-all"
                               onchange="toggleAll(this)">
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
                @foreach($applications as $app)
                <tr>
                    <td>
                        @if($app->status === 'pending')
                        <input type="checkbox" name="ids[]"
                               value="{{ $app->id }}"
                               class="app-checkbox"
                               onchange="updateBulkBar()">
                        @endif
                    </td>
                    <td>{{ $app->student->name }}</td>
                    <td>{{ $app->company->name }}</td>
                    <td>{{ $app->program }}</td>
                    <td>
                        <span class="status-pill
                            {{ $app->status === 'approved' ? 'teal' : ($app->status === 'rejected' ? 'coral' : 'gold') }}">
                            {{ ucfirst($app->status) }}
                        </span>
                    </td>
                    <td>{{ $app->created_at->format('M d, Y') }}</td>
                    <td>
                        {{-- existing individual action buttons here --}}
                        <a href="{{ route('admin.applications.show', $app) }}"
                           class="btn btn-ghost btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>

{{-- APPROVE MODAL --}}
<div class="modal-backdrop" id="approve-modal">
    <div class="modal">
        <div class="modal-title">Approve application</div>
        <div class="modal-sub">Student: <span id="approve-name" style="color:var(--text);font-weight:500;"></span></div>
        <form id="approve-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Remarks <span style="color:var(--muted);text-transform:none;letter-spacing:0;">(optional)</span></label>
                <textarea name="remarks" rows="3" placeholder="Add a note for the student..."
                    class="form-textarea"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModals()" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-approve btn-sm">Approve</button>
            </div>
        </form>
    </div>
</div>

{{-- REJECT MODAL --}}
<div class="modal-backdrop" id="reject-modal">
    <div class="modal">
        <div class="modal-title">Reject application</div>
        <div class="modal-sub">Student: <span id="reject-name" style="color:var(--text);font-weight:500;"></span></div>
        <form id="reject-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Reason for rejection <span style="color:var(--crimson);">*</span></label>
                <textarea name="remarks" rows="3" required placeholder="Explain why this application is being rejected..."
                    class="form-textarea"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModals()" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openApprove(id, name) {
    document.getElementById('approve-name').textContent = name;
    document.getElementById('approve-form').action = `/admin/applications/${id}/approve`;
    document.getElementById('approve-modal').classList.add('open');
}
function openReject(id, name) {
    document.getElementById('reject-name').textContent = name;
    document.getElementById('reject-form').action = `/admin/applications/${id}/reject`;
    document.getElementById('reject-modal').classList.add('open');
}
function closeModals() {
    document.querySelectorAll('.modal-backdrop').forEach(m => m.classList.remove('open'));
}
document.querySelectorAll('.modal-backdrop').forEach(b => {
    b.addEventListener('click', function(e) { if (e.target === this) closeModals(); });
});


function toggleAll(cb) {
    document.querySelectorAll('.app-checkbox')
            .forEach(c => c.checked = cb.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.app-checkbox:checked');
    const bar = document.getElementById('bulk-bar');
    const count = document.getElementById('selected-count');
    bar.style.display  = checked.length > 0 ? 'flex' : 'none';
    count.textContent  = `${checked.length} selected`;
}

function bulkSubmit(action) {
    const checked = document.querySelectorAll('.app-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one application.');
        return;
    }
    const label = action === 'approve' ? 'approve' : 'reject';
    if (!confirm(`${label.charAt(0).toUpperCase() + label.slice(1)} ${checked.length} application(s)?`)) return;
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}

</script>
@endpush



@endsection