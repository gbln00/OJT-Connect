@extends($layout.coordinator-app)

@section('title', 'Support & Feedback')
@section('page-title', 'Support & Feedback')

@section('content')

{{-- ── Page header ──────────────────────────────────────────────── --}}
<div class="greeting fade-up">
    <div class="greeting-sub">// Help Center</div>
    <h1 class="greeting-title">Support <span>&amp; Feedback</span></h1>
    <p style="font-size:13px;color:var(--muted);margin-top:6px;max-width:520px;">
        Submit a bug report, suggest a feature, or ask a question.
        Our team typically responds within 1–2 business days.
    </p>
</div>

{{-- ── Summary stat row ─────────────────────────────────────────── --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4m8-4v4"/>
                </svg>
            </div>
            <span class="stat-tag">TOTAL</span>
        </div>
        <div class="stat-num">{{ $counts['total'] }}</div>
        <div class="stat-label">All Tickets</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">ACTIVE</span>
        </div>
        <div class="stat-num">{{ $counts['open'] }}</div>
        <div class="stat-label">Open / In Progress</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="stat-tag">RESOLVED</span>
        </div>
        <div class="stat-num">{{ $counts['resolved'] }}</div>
        <div class="stat-label">Resolved / Closed</div>
    </div>
</div>

{{-- ── Toolbar ───────────────────────────────────────────────────── --}}
<div class="fade-up fade-up-2" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="status" onchange="this.form.submit()" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;">
            <option value="">All Statuses</option>
            <option value="open"            {{ request('status') === 'open'            ? 'selected' : '' }}>Open</option>
            <option value="in_progress"     {{ request('status') === 'in_progress'     ? 'selected' : '' }}>In Progress</option>
            <option value="waiting_on_user" {{ request('status') === 'waiting_on_user' ? 'selected' : '' }}>Waiting on You</option>
            <option value="resolved"        {{ request('status') === 'resolved'        ? 'selected' : '' }}>Resolved</option>
            <option value="closed"          {{ request('status') === 'closed'          ? 'selected' : '' }}>Closed</option>
        </select>
        <select name="type" onchange="this.form.submit()" class="form-select" style="width:auto;padding:6px 10px;font-size:12px;">
            <option value="">All Types</option>
            <option value="bug"             {{ request('type') === 'bug'             ? 'selected' : '' }}>Bug Report</option>
            <option value="feature_request" {{ request('type') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
            <option value="general_inquiry" {{ request('type') === 'general_inquiry' ? 'selected' : '' }}>General Inquiry</option>
            <option value="billing"         {{ request('type') === 'billing'         ? 'selected' : '' }}>Billing</option>
            <option value="account"         {{ request('type') === 'account'         ? 'selected' : '' }}>Account</option>
            <option value="other"           {{ request('type') === 'other'           ? 'selected' : '' }}>Other</option>
        </select>
    </form>

    <a href="{{ route(request()->route()->getName() === 'admin.support.index'     ? 'admin.support.create'
                : (request()->route()->getName() === 'coordinator.support.index' ? 'coordinator.support.create'
                : (request()->route()->getName() === 'supervisor.support.index'  ? 'supervisor.support.create'
                : 'student.support.create'))) }}" class="btn btn-primary">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Ticket
    </a>
</div>

{{-- ── Tickets list ─────────────────────────────────────────────── --}}
<div class="card fade-up fade-up-3">
    @if($tickets->isEmpty())
    <div style="padding:60px 20px;text-align:center;">
        <div style="width:48px;height:48px;border:1px solid var(--border2);background:var(--surface2);
                    display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--muted);">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
            </svg>
        </div>
        <p style="font-family:'DM Mono',monospace;font-size:11px;letter-spacing:0.1em;color:var(--muted);text-transform:uppercase;">
            No tickets found
        </p>
        <p style="font-size:13px;color:var(--muted);margin-top:6px;">
            Submit a ticket and our team will get back to you.
        </p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Ref</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Replies</th>
                    <th>Submitted</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                <tr>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;
                                     color:var(--muted);letter-spacing:0.06em;">
                            {{ $ticket->ref }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:500;color:var(--text);font-size:13px;">
                            {{ Str::limit($ticket->subject, 55) }}
                        </span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);letter-spacing:0.06em;">
                            {{ $ticket->type_label }}
                        </span>
                    </td>
                    <td>
                        @php
                            $pc = match($ticket->priority) {
                                'urgent' => 'coral', 'high' => 'gold',
                                'normal' => 'blue',  default => 'steel',
                            };
                        @endphp
                        <span class="status-pill {{ $pc }}">{{ $ticket->priority_label }}</span>
                    </td>
                    <td>
                        <span class="status-pill {{ $ticket->status_color }}">
                            {{ $ticket->status_label }}
                        </span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);">
                            {{ $ticket->replies_count ?? $ticket->replies->count() }}
                        </span>
                    </td>
                    <td>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </span>
                    </td>
                    <td>
                        @php
                            $showRoute = match(true) {
                                request()->routeIs('admin.*')       => 'admin.support.show',
                                request()->routeIs('coordinator.*') => 'coordinator.support.show',
                                request()->routeIs('supervisor.*')  => 'supervisor.support.show',
                                default                             => 'student.support.show',
                            };
                        @endphp
                        <a href="{{ route($showRoute, $ticket) }}" class="btn btn-ghost btn-sm">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
    <div class="pagination">
        <span class="pagination-info">
            Showing {{ $tickets->firstItem() }}–{{ $tickets->lastItem() }} of {{ $tickets->total() }}
        </span>
        <div style="display:flex;gap:4px;">
            @if($tickets->onFirstPage())
                <span class="page-link disabled">← Prev</span>
            @else
                <a href="{{ $tickets->previousPageUrl() }}" class="page-link">← Prev</a>
            @endif
            @if($tickets->hasMorePages())
                <a href="{{ $tickets->nextPageUrl() }}" class="page-link">Next →</a>
            @else
                <span class="page-link disabled">Next →</span>
            @endif
        </div>
    </div>
    @endif
    @endif
</div>

@endsection