@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- GREETING --}}
<div class="greeting">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <span class="stat-trend up">all roles</span>
        </div>
        <div class="stat-num">{{ $totalUsers }}</div>
        <div class="stat-label">Total users</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <span class="stat-trend neutral">interns</span>
        </div>
        <div class="stat-num">{{ $totalStudents }}</div>
        <div class="stat-label">Student interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <span class="stat-trend neutral">pending</span>
        </div>
        <div class="stat-num">{{ $pendingApplications }}</div>
        <div class="stat-label">Pending applications</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <span class="stat-trend up">partners</span>
        </div>
        <div class="stat-num">{{ $totalCompanies }}</div>
        <div class="stat-label">Partner companies</div>
    </div>

</div>

{{-- ROLE BREAKDOWN STRIP --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:24px;">

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:12px;color:var(--muted);">Admins</div>
        <span class="role-badge admin">{{ $roleBreakdown['admin'] }}</span>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:12px;color:var(--muted);">Coordinators</div>
        <span class="role-badge coordinator">{{ $roleBreakdown['coordinator'] }}</span>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:12px;color:var(--muted);">Supervisors</div>
        <span class="role-badge supervisor">{{ $roleBreakdown['supervisor'] }}</span>
    </div>

    <div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:12px;color:var(--muted);">Students</div>
        <span class="role-badge student">{{ $roleBreakdown['student'] }}</span>
    </div>

</div>

{{-- BOTTOM GRID --}}
<div class="bottom-grid">

    {{-- RECENT USERS TABLE --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent user accounts</div>
            <a href="{{ route('admin.users.index') }}" class="card-action">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:50%;background:var(--gold-dim);border:1px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:var(--gold);flex-shrink:0;">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td>
                            @php
                                $roleMap = [
                                    'admin'              => ['label' => 'Admin',       'class' => 'admin'],
                                    'ojt_coordinator'    => ['label' => 'Coordinator', 'class' => 'coordinator'],
                                    'company_supervisor' => ['label' => 'Supervisor',  'class' => 'supervisor'],
                                    'student_intern'     => ['label' => 'Student',     'class' => 'student'],
                                ];
                                $r = $roleMap[$user->role] ?? ['label' => $user->role, 'class' => 'student'];
                            @endphp
                            <span class="role-badge {{ $r['class'] }}">{{ $r['label'] }}</span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-dot {{ $user->is_active ? 'active' : 'inactive' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:32px;color:var(--muted);">
                            No users yet.
                            <a href="{{ route('admin.users.create') }}" style="color:var(--gold);text-decoration:none;">Create one →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- QUICK ACTIONS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Quick actions</div>
            </div>
            <div class="quick-actions">

                <a href="{{ route('admin.users.create') }}" class="qa-btn">
                    <div class="qa-icon" style="background:var(--gold-dim);color:var(--gold);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <line x1="19" y1="8" x2="19" y2="14"/>
                            <line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                    </div>
                    <span class="qa-label">Add user</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="qa-btn">
                    <div class="qa-icon" style="background:var(--teal-dim);color:var(--teal);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                        </svg>
                    </div>
                    <span class="qa-label">All users</span>
                </a>

                <a href="{{ route('admin.companies.create') }}" class="qa-btn">
                    <div class="qa-icon" style="background:var(--blue-dim);color:var(--blue);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            <polyline points="9,22 9,12 15,12 15,22"/>
                        </svg>
                    </div>
                    <span class="qa-label">Add company</span>
                </a>

                <a href="{{ route('admin.companies.index') }}" class="qa-btn">
                    <div class="qa-icon" style="background:var(--coral-dim);color:var(--coral);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="16"/>
                            <line x1="10" y1="14" x2="14" y2="14"/>
                        </svg>
                    </div>
                    <span class="qa-label">All companies</span>
                </a>

            </div>
        </div>

        {{-- SYSTEM STATUS --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">System status</div>
            </div>
            <div style="padding:4px 0;">

                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Active users</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--teal);">{{ $activeUsers }}</span>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Inactive users</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--coral);">{{ $inactiveUsers }}</span>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Active companies</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--blue);">{{ $activeCompanies }}</span>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 18px;">
                    <span style="font-size:12.5px;color:var(--muted2);">Pending OJT applications</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--gold);">{{ $pendingApplications }}</span>
                </div>

            </div>
        </div>

        {{-- ACTIVITY FEED --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent activity</div>
            </div>
            <div class="activity-list">
                @forelse($recentActivity as $activity)
                <div class="activity-item">
                    <div class="activity-dot-wrap">
                        <div class="activity-dot {{ $activity['color'] ?? 'gold' }}"></div>
                    </div>
                    <div class="activity-body">
                        <div class="activity-text">{!! $activity['text'] !!}</div>
                        <div class="activity-time">{{ $activity['time'] }}</div>
                    </div>
                </div>
                @empty
                <div class="activity-item">
                    <div class="activity-dot-wrap"><div class="activity-dot gold"></div></div>
                    <div class="activity-body">
                        <div class="activity-text">System initialized successfully.</div>
                        <div class="activity-time">Just now</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-dot-wrap"><div class="activity-dot teal"></div></div>
                    <div class="activity-body">
                        <div class="activity-text"><strong>Admin</strong> account created.</div>
                        <div class="activity-time">Today</div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>{{-- end right column --}}

</div>{{-- end bottom grid --}}

@endsection