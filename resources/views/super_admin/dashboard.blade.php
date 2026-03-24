@extends('layouts.superadmin-app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}" class="btn btn-primary">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Tenant
    </a>
@endsection

@section('content')

    {{-- Stats --}}
    <div class="grid-3" style="margin-bottom: 28px;">
        <div class="stat-card">
            <div class="stat-label">Total Tenants</div>
            <div class="stat-value">{{ $totalTenants }}</div>
            <div class="stat-sub">Registered schools / institutions</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Active Domains</div>
            <div class="stat-value">{{ $recentTenants->count() }}</div>
            <div class="stat-sub">Across all tenants</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Platform</div>
            <div class="stat-value" style="font-size:22px; padding-top:4px;">OJT Hub</div>
            <div class="stat-sub">Multi-tenant internship system</div>
        </div>
    </div>

    {{-- Recent Tenants --}}
    <div class="card">
        <div class="section-header">
            <div class="section-title">Recent Tenants</div>
            <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>

        @if($recentTenants->isEmpty())
            <div class="empty">
                <div class="empty-icon">🏢</div>
                <div class="empty-title">No tenants yet</div>
                <div class="empty-text">Create your first tenant to get started.</div>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Tenant ID</th>
                            <th>Domain</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTenants as $tenant)
                        <tr>
                            <td>
                                <span style="font-family:'Syne',sans-serif; font-weight:700;">{{ $tenant->id }}</span>
                            </td>
                            <td>
                                @foreach($tenant->domains as $domain)
                                    <span class="badge badge-purple">{{ $domain->domain }}</span>
                                @endforeach
                            </td>
                            <td style="color:var(--muted);">{{ $tenant->created_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ route('super_admin.tenants.show', $tenant) }}" class="btn btn-ghost btn-sm">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection