@extends('layouts.app')
@section('title', 'Companies')
@section('page-title', 'Partner Companies')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $total }}</div>
        <div class="stat-label">Total companies</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $active }}</div>
        <div class="stat-label">Active</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $total - $active }}</div>
        <div class="stat-label">Inactive</div>
    </div>

</div>

{{-- Toolbar --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
    <form method="GET" action="{{ route('admin.companies.index') }}" style="display:flex;gap:8px;flex:1;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search company name..."
               style="flex:1;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
        <select name="status" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
            <option value="">All status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit" style="padding:8px 14px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;color:var(--text);font-size:13px;cursor:pointer;">Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.companies.index') }}" style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);color:var(--muted);font-size:13px;text-decoration:none;">Clear</a>
        @endif
    </form>
    <a href="{{ route('admin.companies.create') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:var(--gold);color:var(--bg);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add company
    </a>
</div>

{{-- Table --}}
<div class="card">
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
                            <div style="font-size:11px;color:var(--muted);">{{ $company->address }}</div>
                        @endif
                    </td>
                    <td>{{ $company->industry ?? '—' }}</td>
                    <td>{{ $company->contact_person ?? '—' }}</td>
                    <td>{{ $company->contact_email ?? '—' }}</td>
                    <td>
                        <span class="status-dot {{ $company->is_active ? 'active' : 'inactive' }}">
                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <a href="{{ route('admin.companies.edit', $company) }}"
                               style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--muted2);font-size:11px;text-decoration:none;">Edit</a>
                            <form method="POST" action="{{ route('admin.companies.toggle', $company) }}">
                                @csrf @method('PATCH')
                                <button type="submit" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:{{ $company->is_active ? 'var(--coral)' : 'var(--teal)' }};">
                                    {{ $company->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.companies.destroy', $company) }}"
                                  onsubmit="return confirm('Delete {{ $company->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);background:none;cursor:pointer;font-size:11px;color:var(--muted);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                        No companies yet.
                        <a href="{{ route('admin.companies.create') }}" style="color:var(--gold);text-decoration:none;">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:12px;color:var(--muted);">Showing {{ $companies->firstItem() }}–{{ $companies->lastItem() }} of {{ $companies->total() }}</span>
        <div style="display:flex;gap:4px;">
            @if($companies->onFirstPage())
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">← Prev</span>
            @else
                <a href="{{ $companies->previousPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">← Prev</a>
            @endif
            @if($companies->hasMorePages())
                <a href="{{ $companies->nextPageUrl() }}" style="padding:5px 10px;border-radius:6px;border:1px solid var(--border2);color:var(--text);font-size:12px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:5px 10px;border-radius:6px;border:1px solid var(--border);color:var(--muted);font-size:12px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection