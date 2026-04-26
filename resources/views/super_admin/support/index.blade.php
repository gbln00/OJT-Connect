@extends('layouts.superadmin-app')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')

{{-- ── Page header ──────────────────────────────────────────────── --}}
<div class="greeting fade-up">
    <div class="greeting-sub">// Customer Support</div>
    <h1 class="greeting-title">Support <span style="color:var(--crimson);font-style:italic;">Inbox</span></h1>
    <p style="font-size:13px;color:var(--muted);margin-top:6px;">
        All support tickets submitted across every tenant institution.
    </p>
</div>

{{-- ── Summary stat row ─────────────────────────────────────────── --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4"/>
                </svg>
            </div>
            <span class="stat-tag">TOTAL</span>
        </div>
        <div class="stat-num">{{ $allTickets->count() }}</div>
        <div class="stat-label">All Tickets</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">OPEN</span>
        </div>
        <div class="stat-num">{{ $totalOpen }}</div>
        <div class="stat-label">Needs Response</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <span class="stat-tag">URGENT</span>
        </div>
        <div class="stat-num">{{ $totalUrgent }}</div>
        <div class="stat-label">Urgent / Open</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3"/>
                </svg>
            </div>
            <span class="stat-tag">TENANTS</span>
        </div>
        <div class="stat-num">{{ $tenants->count() }}</div>
        <div class="stat-label">Active Tenants</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;">

    {{-- ── Main tickets table ───────────────────────────────────── --}}
    <div>

        {{-- Filters --}}
        <form method="GET" class="fade-up fade-up-2"
              style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            <select name="status" onchange="this.form.submit()" class="form-select"
                    style="width:auto;padding:6px 10px;font-size:12px;">
                <option value="">All Statuses</option>
                @foreach(['open'=>'Open','in_progress'=>'In Progress','waiting_on_user'=>'Waiting on User','resolved'=>'Resolved','closed'=>'Closed'] as $v => $l)
                    <option value="{{ $v }}" {{ request('status') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>

            <select name="priority" onchange="this.form.submit()" class="form-select"
                    style="width:auto;padding:6px 10px;font-size:12px;">
                <option value="">All Priorities</option>
                @foreach(['urgent'=>'Urgent','high'=>'High','normal'=>'Normal','low'=>'Low'] as $v => $l)
                    <option value="{{ $v }}" {{ request('priority') === $v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['status','priority']))
                <a href="{{ route('super_admin.support.index') }}"
                   style="display:inline-flex;align-items:center;padding:6px 12px;border:1px solid var(--border2);
                          font-size:12px;color:var(--muted);text-decoration:none;background:var(--surface);
                          transition:all 0.15s;"
                   onmouseover="this.style.color='var(--text)'"
                   onmouseout="this.style.color='var(--muted)'">
                    Clear filters
                </a>
            @endif
        </form>

        <div class="card fade-up fade-up-3">
            @if($allTickets->isEmpty())
            <div style="padding:60px 20px;text-align:center;">
                <div style="font-family:'DM Mono',monospace;font-size:11px;
                            letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">
                    // No tickets found
                </div>
                <p style="font-size:13px;color:var(--muted);margin-top:8px;">
                    No support tickets have been submitted yet across any tenant.
                </p>
            </div>
            @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>Tenant</th>
                            <th>Submitted By</th>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allTickets as $ticket)
                        <tr style="{{ in_array($ticket->status, ['open','in_progress']) && $ticket->priority === 'urgent' ? 'background:rgba(248,113,113,0.04);' : '' }}">

                            {{-- Ref --}}
                            <td>
                                <span style="font-family:'DM Mono',monospace;font-size:11px;
                                             color:var(--muted);letter-spacing:0.06em;">
                                    {{ $ticket->ref }}
                                </span>
                            </td>

                            {{-- Tenant --}}
                            <td>
                                <span style="font-size:12px;font-weight:600;color:var(--text);">
                                    {{ $ticket->tenant_name }}
                                </span>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;
                                            color:var(--muted);margin-top:1px;">
                                    {{ $ticket->tenant_id }}
                                </div>
                            </td>

                            {{-- Submitted by --}}
                            <td>
                                <div style="font-size:12px;color:var(--text2);">
                                    {{ $ticket->user?->name ?? '—' }}
                                </div>
                                @if($ticket->user)
                                <div style="font-family:'DM Mono',monospace;font-size:10px;
                                            color:var(--muted);margin-top:1px;">
                                    {{ str_replace('_', ' ', ucfirst($ticket->user->role ?? '')) }}
                                </div>
                                @endif
                            </td>

                            {{-- Subject --}}
                            <td style="max-width:220px;">
                                <span style="font-size:13px;color:var(--text);font-weight:500;
                                             display:block;overflow:hidden;text-overflow:ellipsis;
                                             white-space:nowrap;">
                                    {{ $ticket->subject }}
                                </span>
                            </td>

                            {{-- Type --}}
                            <td>
                                <span style="font-family:'DM Mono',monospace;font-size:10px;
                                             color:var(--muted);letter-spacing:0.04em;">
                                    {{ $ticket->type_label }}
                                </span>
                            </td>

                            {{-- Priority --}}
                            <td>
                                @php $pc = match($ticket->priority) {'urgent'=>'coral','high'=>'gold','normal'=>'blue',default=>'steel'}; @endphp
                                <span class="status-pill {{ $pc }}">{{ $ticket->priority_label }}</span>
                            </td>

                            {{-- Status --}}
                            <td>
                                <span class="status-pill {{ $ticket->status_color }}">
                                    {{ $ticket->status_label }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td>
                                <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td>
                                <a href="{{ route('super_admin.support.show', ['tenantId' => $ticket->tenant_id, 'ticketId' => $ticket->id]) }}"
                                   class="btn btn-ghost btn-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Sidebar: per-tenant summary ─────────────────────────── --}}
    <div class="fade-up fade-up-4">
        <div class="card">
            <div class="card-header">
                <span class="card-title">By Tenant</span>
            </div>
            <div style="padding:8px 0;">
                @foreach($tenants as $tenant)
                @php
                    $summary = $ticketSummary[$tenant->id] ?? ['open' => 0, 'total' => 0];
                    $hasOpen  = ($summary['open'] ?? 0) > 0;
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;
                            padding:9px 16px;border-bottom:1px solid var(--border);
                            transition:background 0.12s;"
                     onmouseover="this.style.background='var(--surface2)'"
                     onmouseout="this.style.background='transparent'">
                    <div style="min-width:0;">
                        <div style="font-size:12px;font-weight:500;color:var(--text);
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">
                            {{ $tenant->name ?? $tenant->id }}
                        </div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:1px;">
                            {{ $summary['total'] ?? 0 }} total
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                        @if($hasOpen)
                        <span style="min-width:20px;height:20px;padding:0 5px;
                                     background:var(--crimson);color:#fff;
                                     font-family:'DM Mono',monospace;font-size:9px;
                                     display:flex;align-items:center;justify-content:center;">
                            {{ $summary['open'] }}
                        </span>
                        @else
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);border:1px solid var(--border2);
                                     padding:1px 6px;">0</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick legend --}}
        <div class="card" style="margin-top:12px;">
            <div class="card-header">
                <span class="card-title">Status Legend</span>
            </div>
            <div style="padding:12px 16px;display:flex;flex-direction:column;gap:8px;">
                @foreach([
                    ['open',            'blue',  'Newly submitted, no reply yet'],
                    ['in_progress',     'gold',  'Support team is working on it'],
                    ['waiting_on_user', 'coral', 'Awaiting user response'],
                    ['resolved',        'teal',  'Issue resolved'],
                    ['closed',          'steel', 'Ticket closed by user'],
                ] as [$s, $c, $desc])
                <div style="display:flex;align-items:flex-start;gap:8px;">
                    <span class="status-pill {{ $c }}" style="flex-shrink:0;font-size:10px;">
                        {{ str_replace('_', ' ', ucfirst($s)) }}
                    </span>
                    <span style="font-size:11px;color:var(--muted);line-height:1.4;">{{ $desc }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@endsection