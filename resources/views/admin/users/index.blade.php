@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')

{{-- STAT STRIP --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(5,1fr);">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon crimson">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        </div><span class="stat-tag">all</span></div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">Total users</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon steel">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/></svg>
        </div><span class="stat-tag">admins</span></div>
        <div class="stat-num">{{ $counts['admin'] }}</div>
        <div class="stat-label">Admins</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon night">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        </div><span class="stat-tag">coord</span></div>
        <div class="stat-num">{{ $counts['coordinator'] }}</div>
        <div class="stat-label">Coordinators</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon gold">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
        </div><span class="stat-tag">sup</span></div>
        <div class="stat-num">{{ $counts['supervisor'] }}</div>
        <div class="stat-label">Supervisors</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon crimson">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
        </div><span class="stat-tag">intern</span></div>
        <div class="stat-num">{{ $counts['student'] }}</div>
        <div class="stat-label">Students</div>
    </div>
</div>

{{-- TOOLBAR --}}
<div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px;flex-wrap:wrap;" class="fade-up fade-up-1">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:8px;flex:1;min-width:0;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search name or email..."
               class="form-input" style="flex:1;min-width:160px;">

        <select name="role" class="form-select" style="width:auto;">
            <option value="">All roles</option>
            <option value="admin"              {{ request('role') === 'admin'              ? 'selected' : '' }}>Admin</option>
            <option value="ojt_coordinator"    {{ request('role') === 'ojt_coordinator'    ? 'selected' : '' }}>Coordinator</option>
            <option value="company_supervisor" {{ request('role') === 'company_supervisor' ? 'selected' : '' }}>Supervisor</option>
            <option value="student_intern"     {{ request('role') === 'student_intern'     ? 'selected' : '' }}>Student</option>
        </select>

        <select name="status" class="form-select" style="width:auto;">
            <option value="">All status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>

        @if(request()->hasAny(['search','role','status']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Clear</a>
        @endif
    </form>

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add user
    </a>
</div>

{{-- TABLE --}}
<div class="card fade-up fade-up-2">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                @php
                $roleClasses = ['admin'=>'admin','ojt_coordinator'=>'coordinator','company_supervisor'=>'supervisor','student_intern'=>'student'];
                @endphp
                <tr>
                    <td style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $user->id }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span style="color:var(--text);font-weight:500;">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span style="font-family:'DM Mono',monospace;font-size:9px;padding:1px 5px;background:rgba(140,14,3,0.1);color:var(--crimson);border:1px solid rgba(140,14,3,0.2);letter-spacing:0.05em;">YOU</span>
                            @endif
                        </div>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;">{{ $user->email }}</td>
                    <td>
                        <span class="role-badge {{ $roleClasses[$user->role] ?? 'student' }}">{{ $user->role_label }}</span>
                    </td>
                    <td>
                        <span class="status-dot {{ $user->is_active ? 'active' : 'inactive' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:4px;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>

                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-approve' }}">
                                    {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  onsubmit="return confirm('Delete {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--muted);">Delete</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">
                        No users found.
                        <a href="{{ route('admin.users.create') }}" style="color:var(--crimson);text-decoration:none;font-weight:500;">Create one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="pagination">
        <span class="pagination-info">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users</span>
        <div style="display:flex;gap:4px;">
            @if($users->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@endsection