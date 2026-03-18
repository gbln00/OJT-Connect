@extends('layouts.app')
@section('title', 'Student Verification')
@section('page-title', 'Student Verification')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending verification</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $counts['verified'] }}</div>
        <div class="stat-label">Verified students</div>
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

{{-- TABS --}}
<div style="display:flex;gap:4px;margin-bottom:16px;border-bottom:1px solid var(--border);padding-bottom:0;">
    @foreach(['pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $key => $label)
    <a href="{{ route('admin.verification.index', ['tab' => $key]) }}"
       style="padding:9px 18px;font-size:13px;font-weight:{{ $tab === $key ? '500' : '400' }};
              color:{{ $tab === $key ? 'var(--gold)' : 'var(--muted2)' }};
              border-bottom:2px solid {{ $tab === $key ? 'var(--gold)' : 'transparent' }};
              text-decoration:none;transition:color 0.15s;margin-bottom:-1px;">
        {{ $label }}
        @if($counts[$key] > 0)
        <span style="margin-left:6px;font-size:10px;padding:1px 6px;border-radius:10px;
            background:{{ $tab === $key ? 'var(--gold-dim)' : 'var(--surface2)' }};
            color:{{ $tab === $key ? 'var(--gold)' : 'var(--muted)' }};">
            {{ $counts[$key] }}
        </span>
        @endif
    </a>
    @endforeach
</div>

{{-- SEARCH --}}
<div style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.verification.index') }}" style="display:flex;gap:8px;">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name, email, or student ID..."
               style="flex:1;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
        <button type="submit" style="padding:8px 16px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">
            Search
        </button>
        @if(request('search'))
        <a href="{{ route('admin.verification.index', ['tab' => $tab]) }}"
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
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Year</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;border-radius:50%;background:var(--teal-dim);border:1px solid rgba(45,212,191,0.3);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:var(--teal);flex-shrink:0;">
                                {{ strtoupper(substr($student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;color:var(--text);font-weight:500;">{{ $student->name }}</div>
                                @if($student->studentProfile)
                                    <div style="font-size:11px;color:var(--muted);">{{ $student->studentProfile->full_name }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12.5px;color:var(--text);">
                            {{ $student->studentProfile?->student_id ?? '—' }}
                        </span>
                    </td>
                    <td style="font-size:12.5px;">{{ $student->studentProfile?->course ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $student->studentProfile?->year_level ?? '—' }}</td>
                    <td style="font-size:12.5px;color:var(--muted2);">{{ $student->email }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $student->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="{{ route('admin.verification.show', $student) }}"
                               style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;"
                               onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
                               View
                            </a>
                            @if(!$student->is_verified && !$student->rejection_reason)
                            <form method="POST" action="{{ route('admin.verification.verify', $student) }}">
                                @csrf
                                <button type="submit"
                                    style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--teal);font-family:inherit;">
                                    Verify
                                </button>
                            </form>
                            <button onclick="openReject({{ $student->id }}, '{{ addslashes($student->name) }}')"
                                style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--coral);font-family:inherit;">
                                Reject
                            </button>
                            @elseif($student->is_verified)
                            <span style="font-size:11px;color:var(--teal);">✓ Verified</span>
                            @else
                            <span style="font-size:11px;color:var(--coral);">✕ Rejected</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        No {{ $tab }} registrations found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">
            Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
        </span>
        <div style="display:flex;gap:4px;">
            @if($students->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $students->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- REJECT MODAL --}}
<div id="reject-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:300;align-items:center;justify-content:center;">
    <div style="background:var(--surface);border:1px solid var(--border2);border-radius:12px;padding:24px;width:100%;max-width:420px;margin:16px;">
        <div style="font-size:15px;font-weight:600;color:var(--text);margin-bottom:4px;">Reject registration</div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:16px;">Student: <span id="reject-name" style="color:var(--text);"></span></div>
        <form id="reject-form" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Reason for rejection <span style="color:var(--coral);">*</span></label>
                <textarea name="rejection_reason" rows="3" required
                    placeholder="e.g. Student ID not found in class records..."
                    style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;font-family:inherit;outline:none;"
                    onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModal()"
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
function openReject(id, name) {
    document.getElementById('reject-name').textContent = name;
    document.getElementById('reject-form').action = `/admin/verification/${id}/reject`;
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('reject-modal').style.display = 'none';
}
document.getElementById('reject-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

@endsection