@extends('layouts.coordinator-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

{{-- GREETING --}}
<div class="greeting fade-up">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <span class="stat-tag">pending</span>
        </div>
        <div class="stat-num">{{ $pendingApplications }}</div>
        <div class="stat-label">Pending Applications</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <span class="stat-tag">active</span>
        </div>
        <div class="stat-num">{{ $activeInterns }}</div>
        <div class="stat-label">Active Interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                </svg>
            </div>
            <span class="stat-tag">review</span>
        </div>
        <div class="stat-num">{{ $pendingReports }}</div>
        <div class="stat-label">Pending Reports</div>
    </div>

</div>

{{-- BOTTOM GRID --}}
<div style="display:grid;grid-template-columns:1fr 300px;gap:16px;" class="fade-up fade-up-2">

    {{-- RECENT PENDING APPLICATIONS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent pending applications</div>
            <a href="{{ route('coordinator.applications.index') }}" class="card-action">View all →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Company</th>
                        <th>Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentApplications as $app)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:28px;height:28px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($app->student->name ?? 'S', 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-weight:500;color:var(--text);font-size:13px;">{{ $app->student->name ?? '—' }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $app->student->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;">{{ $app->company->name ?? $app->company_name ?? '—' }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);white-space:nowrap;">
                        {{ $app->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('coordinator.applications.show', $app->id) }}" class="btn btn-approve btn-sm">Review</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:48px;color:var(--muted);">
                        🎉 No pending applications right now.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Quick actions</div>
        </div>
        <div class="quick-actions">
            <a href="{{ route('coordinator.applications.index') }}" class="qa-btn">
                <div class="qa-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                </div>
                <div>
                    <div class="qa-label">Applications</div>
                    <div class="qa-sublabel">Review & approve</div>
                </div>
                @if($pendingApplications > 0)
                <span style="margin-left:auto;padding:1px 6px;background:var(--gold-dim);color:var(--gold);border:1px solid var(--gold-border);font-family:'DM Mono',monospace;font-size:10px;">{{ $pendingApplications }}</span>
                @endif
            </a>

            <a href="{{ route('coordinator.reports.index') }}" class="qa-btn">
                <div class="qa-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="qa-label">Weekly Reports</div>
                    <div class="qa-sublabel">Read & give feedback</div>
                </div>
                @if($pendingReports > 0)
                <span style="margin-left:auto;padding:1px 6px;background:var(--coral-dim);color:var(--coral);border:1px solid var(--coral-border);font-family:'DM Mono',monospace;font-size:10px;">{{ $pendingReports }}</span>
                @endif
            </a>

            <a href="{{ route('coordinator.hours.index') }}" class="qa-btn">
                <div class="qa-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12,6 12,12 16,14"/>
                    </svg>
                </div>
                <div>
                    <div class="qa-label">Hour Logs</div>
                    <div class="qa-sublabel">Approve hours</div>
                </div>
            </a>

            <a href="{{ route('coordinator.students.index') }}" class="qa-btn">
                <div class="qa-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="qa-label">Students</div>
                    <div class="qa-sublabel">View intern list</div>
                </div>
            </a>

            <a href="{{ route('coordinator.evaluations.index') }}" class="qa-btn">
                <div class="qa-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <div>
                    <div class="qa-label">Evaluations</div>
                    <div class="qa-sublabel">View grades</div>
                </div>
            </a>
        </div>
    </div>

</div>

@push('styles')
<style>
.greeting { margin-bottom: 28px; }
.greeting-sub {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.15em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 6px;
}
.greeting-title {
    font-family: 'Playfair Display', serif;
    font-size: clamp(22px, 3vw, 30px); font-weight: 900;
    color: var(--text); line-height: 1.1;
}
.greeting-title span { color: var(--crimson); font-style: italic; }
</style>
@endpush

@endsection