@extends('layouts.coordinator-app')
@section('title', 'Applications')
@section('page-title', 'Applications')
@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(4,1fr);">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <span class="stat-tag">all</span>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <span class="stat-tag">pending</span>
        </div>
        <div class="stat-num">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
            </div>
            <span class="stat-tag">approved</span>
        </div>
        <div class="stat-num">{{ $counts['approved'] }}</div>
        <div class="stat-label">Approved</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </div>
            <span class="stat-tag">rejected</span>
        </div>
        <div class="stat-num">{{ $counts['rejected'] }}</div>
        <div class="stat-label">Rejected</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('coordinator.applications.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name…"
               class="form-input" style="flex:1;min-width:160px;">

        <select name="status" class="form-input" style="width:auto;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>

        @if(request()->hasAny(['search','status']))
            <a href="{{ route('coordinator.applications.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
</div>
<form id="bulk-form" method="POST" action="{{ route('coordinator.applications.bulk') }}">
  @csrf
  <input type="hidden" name="action" id="bulk-action">
  <button onclick="setBulkAction('approve')">Bulk Approve</button>
  <button onclick="setBulkAction('reject')">Bulk Reject</button>

  @foreach($applications as $app)
    <input type="checkbox" name="ids[]" value="{{ $app->id }}">
    <!-- existing row content -->
  @endforeach
</form>

{{-- TABLE --}}
<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Required Hours</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
            @php
                $statusClass = $app->status === 'approved' ? 'teal' : ($app->status === 'rejected' ? 'coral' : 'gold');
            @endphp
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                            {{ strtoupper(substr($app->student->name ?? 'S', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-weight:500;color:var(--text);">{{ $app->student->name ?? '—' }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->student->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="font-size:13px;color:var(--text);">{{ $app->company->name ?? $app->company_name ?? '—' }}</div>
                    @if($app->company?->address)
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->company->address }}</div>
                    @endif
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:12px;font-weight:600;color:var(--blue);">
                    {{ $app->required_hours ?? '—' }} hrs
                </td>
                <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                    {{ $app->created_at->format('M d, Y') }}
                </td>
                <td>
                    <span class="status-dot {{ $app->status }}">{{ ucfirst($app->status) }}</span>
                </td>
                <td>
                    <a href="{{ route('coordinator.applications.show', $app->id) }}" class="btn btn-ghost btn-sm">Review</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                    No applications found.
                    <span style="font-family:'DM Mono',monospace;font-size:11px;">Try adjusting your filters.</span>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div class="pagination">
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
</div>

@endsection