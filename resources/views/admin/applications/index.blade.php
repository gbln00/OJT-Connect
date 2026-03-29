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

{{-- TABLE --}}
<div class="card fade-up fade-up-2">
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
                @php
                $pillMap = ['pending'=>'gold','approved'=>'green','rejected'=>'crimson'];
                $pillCls = $pillMap[$app->status] ?? 'steel';
                @endphp
                <tr>
                    <td style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($app->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $app->student->name }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:var(--text);font-size:13px;">{{ $app->company->name }}</td>
                    <td style="font-size:12px;color:var(--text2);">{{ $app->program }}</td>
                    <td style="font-size:12px;color:var(--text2);">{{ $app->semester }} — {{ $app->school_year }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;">{{ number_format($app->required_hours) }} hrs</td>
                    <td><span class="status-pill {{ $pillCls }}">{{ $app->status_label }}</span></td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $app->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-ghost btn-sm">View</a>

                            @if($app->isPending())
                            <button onclick="openApprove({{ $app->id }}, '{{ addslashes($app->student->name) }}')"
                                class="btn btn-approve btn-sm">Approve</button>
                            <button onclick="openReject({{ $app->id }}, '{{ addslashes($app->student->name) }}')"
                                class="btn btn-danger btn-sm">Reject</button>
                            @endif

                            <form method="POST" action="{{ route('admin.applications.destroy', $app) }}"
                                  onsubmit="return confirm('Delete this application?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--muted);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:var(--muted);">
                        No applications found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div class="pagination">
        <span class="pagination-info">
            Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }}
        </span>
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
</div>

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
</script>

@endsection