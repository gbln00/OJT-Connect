@extends('layouts.app')
@section('title', 'Companies')
@section('page-title', 'Partner Companies')

@section('content')

{{-- Stats --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon night">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
        </div><span class="stat-tag">all</span></div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Total companies</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon night">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        </div><span class="stat-tag">live</span></div>
        <div class="stat-num">{{ $active }}</div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon crimson">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div><span class="stat-tag">off</span></div>
        <div class="stat-num">{{ $total - $active }}</div>
        <div class="stat-label">Inactive</div>
    </div>
</div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('admin.companies.index') }}" style="display:flex;gap:8px;flex:1;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search company name..." class="form-input" style="flex:1;min-width:160px;">
        <select name="status" class="form-select" style="width:auto;">
            <option value="">All status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>
    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add company
    </a>
</div>

{{-- Table --}}
<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Company name</th>
                    <th>Industry</th>
                    <th>Contact person</th>
                    <th>Contact email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    <td>
                        <div style="font-weight:500;color:var(--text);">{{ $company->name }}</div>
                        @if($company->address)
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:1px;">{{ $company->address }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text2);">{{ $company->industry ?? '—' }}</td>
                    <td style="font-size:13px;">{{ $company->contact_person ?? '—' }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ $company->contact_email ?? '—' }}</td>
                    <td>
                        <span class="status-dot {{ $company->is_active ? 'active' : 'inactive' }}">
                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.companies.toggle', $company) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $company->is_active ? 'btn-danger' : 'btn-approve' }}">
                                    {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                                  onsubmit="return confirm('Delete {{ $company->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--muted);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;color:var(--muted);">
                        No companies yet.
                        <a href="{{ route('admin.companies.create') }}" style="color:var(--crimson);text-decoration:none;font-weight:500;">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="pagination">
        <span class="pagination-info">Showing {{ $companies->firstItem() }}–{{ $companies->lastItem() }} of {{ $companies->total() }}</span>
        <div style="display:flex;gap:4px;">
            @if($companies->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $companies->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($companies->hasMorePages())
                <a href="{{ $companies->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection