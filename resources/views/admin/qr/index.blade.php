@extends('layouts.app')
@section('title', 'QR Clock-In Manager')
@section('page-title', 'QR Clock-In')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Management / QR Clock-In
    </span>
</div>

{{-- Stats --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <span class="stat-tag">approved</span>
        </div>
        <div class="stat-num">{{ $stats['total'] }}</div>
        <div class="stat-label">Active interns</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M15 9h.01M9 15h.01M15 15h.01"/>
                </svg>
            </div>
            <span class="stat-tag">active</span>
        </div>
        <div class="stat-num">{{ $stats['with_qr'] }}</div>
        <div class="stat-label">QR codes active</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">inactive</span>
        </div>
        <div class="stat-num">{{ $stats['inactive'] }}</div>
        <div class="stat-label">QR codes paused</div>
    </div>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.2);color:#34d399;padding:12px 16px;margin-bottom:16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
    ✓ {{ session('success') }}
</div>
@endif

{{-- Filters --}}
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <form method="GET" action="{{ route('admin.qr.index') }}" style="padding:16px 20px;display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div style="flex:1;min-width:180px;">
            <label class="form-label">Search student</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…" class="form-input">
        </div>
        <div style="min-width:180px;">
            <label class="form-label">Company</label>
            <select name="company_id" class="form-select">
                <option value="">All companies</option>
                @foreach($companies as $c)
                    <option value="{{ $c->id }}" {{ request('company_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:flex;gap:8px;">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('admin.qr.index') }}" class="btn btn-ghost btn-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card fade-up fade-up-3">
    <div class="card-header">
        <div class="card-title">Intern QR Codes</div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $applications->total() }} interns</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>QR Status</th>
                    <th>Last Scan</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                @php $qr = $app->qrClockIn; @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($app->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $app->student->name }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $app->company->name }}</td>
                    <td>
                        @if($qr)
                            @if($qr->is_active)
                                <span class="status-pill green">Active</span>
                            @else
                                <span class="status-pill steel">Inactive</span>
                            @endif
                        @else
                            <span class="status-pill gold">No QR Yet</span>
                        @endif
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $qr?->last_scanned_at?->diffForHumans() ?? '—' }}
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                            <a href="{{ route('admin.qr.show', $app) }}" class="btn btn-ghost btn-sm">
                                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 9h.01M15 9h.01M9 15h.01M15 15h.01"/>
                                </svg>
                                View QR
                            </a>
                            @if($qr)
                            <form method="POST" action="{{ route('admin.qr.toggle', $app) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $qr->is_active ? 'btn-danger' : 'btn-approve' }}">
                                    {{ $qr->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);font-family:'DM Mono',monospace;font-size:11px;">
                        No approved interns found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
    <div class="pagination">
        <span class="pagination-info">{{ $applications->firstItem() }}–{{ $applications->lastItem() }} of {{ $applications->total() }}</span>
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

@endsection