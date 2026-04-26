@extends('layouts.superadmin-app')
@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')

{{-- ── Page header ──────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;
            margin-bottom:28px;flex-wrap:wrap;gap:12px;">
    <div>
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:6px;">
            OJTConnect · Support
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text);">
            All Support Tickets
        </h2>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">
            Showing up to 50 recent tickets per tenant.
            <span style="color:var(--text2);">{{ $totalOpen }} open</span>
            @if($totalUrgent > 0)
                · <span style="color:var(--coral-color);font-weight:600;">
                    {{ $totalUrgent }} urgent
                  </span>
            @endif
        </p>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;">
        <select name="status" onchange="this.form.submit()"
                class="form-select" style="width:auto;padding:6px 10px;font-size:12px;">
            <option value="">All Statuses</option>
            <option value="open"            {{ request('status') === 'open'            ? 'selected' : '' }}>Open</option>
            <option value="in_progress"     {{ request('status') === 'in_progress'     ? 'selected' : '' }}>In Progress</option>
            <option value="waiting_on_user" {{ request('status') === 'waiting_on_user' ? 'selected' : '' }}>Waiting on User</option>
            <option value="resolved"        {{ request('status') === 'resolved'        ? 'selected' : '' }}>Resolved</option>
            <option value="closed"          {{ request('status') === 'closed'          ? 'selected' : '' }}>Closed</option>
        </select>
        <select name="priority" onchange="this.form.submit()"
                class="form-select" style="width:auto;padding:6px 10px;font-size:12px;">
            <option value="">All Priorities</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
            <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
            <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
        </select>
    </form>
</div>

{{-- ── Tenant summary sidebar + tickets grid ───────────────────── --}}
<div style="display:grid;grid-template-columns:220px 1fr;gap:16px;align-items:flex-start;">

    {{-- Tenant sidebar --}}
    <div class="card" style="position:sticky;top:90px;">
        <div class="card-header">
            <span class="card-title">By Tenant</span>
        </div>
        <div style="padding:8px 0;">
            @foreach($tenants as $tenant)
            @php $counts = $ticketSummary[$tenant->id] ?? ['open' => 0, 'resolved' => 0, 'total' => 0]; @endphp
            <div style="padding:10px 16px;border-bottom:1px solid var(--border);
                        display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:12px;font-weight:500;color:var(--text);">
                        {{ $tenant->name ?? $tenant->id }}
                    </div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;
                                color:var(--muted);margin-top:2px;">
                        {{ $counts['open'] }} open · {{ $counts['total'] }} total
                    </div>
                </div>
                @if($counts['open'] > 0)
                <span style="background:var(--crimson);color:#fff;
                             font-family:'DM Mono',monospace;font-size:10px;
                             padding:1px 6px;line-height:1.5;">
                    {{ $counts['open'] }}
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tickets table --}}
    <div class="card">
        @if($allTickets->isEmpty())
        <div style="padding:60px 20px;text-align:center;">
            <p style="font-family:'DM Mono',monospace;font-size:11px;
                      color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">
                No tickets found
            </p>
            <p style="font-size:13px;color:var(--muted);margin-top:6px;">
                Tenants haven't submitted any support tickets yet.
            </p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Tenant</th>
                        <th>Subject</th>
                        <th>From</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Replies</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allTickets as $ticket)
                    {{-- All properties are plain stdClass set inside $tenant->run() --}}
                    {{-- No Eloquent accessors are called during Blade rendering.    --}}
                    <tr>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:11px;
                                         color:var(--muted);letter-spacing:0.06em;">
                                {{ $ticket->ref }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:11px;
                                         color:var(--text2);padding:1px 6px;
                                         border:1px solid var(--border2);">
                                {{ $ticket->tenant_name }}
                            </span>
                        </td>
                        <td style="max-width:220px;">
                            <span style="font-weight:500;color:var(--text);font-size:13px;
                                         white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                         display:block;max-width:220px;"
                                  title="{{ $ticket->subject }}">
                                {{ Str::limit($ticket->subject, 45) }}
                            </span>
                        </td>
                        <td>
                            <span style="font-size:12px;color:var(--text2);">
                                {{ $ticket->user_name ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill {{ $ticket->priority_color }}">
                                {{ $ticket->priority_label }}
                            </span>
                        </td>
                        <td>
                            <span class="status-pill {{ $ticket->status_color }}">
                                {{ $ticket->status_label }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);">
                                {{ $ticket->reply_count }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $ticket->created_at->format('M d, Y') }}
                        </td>
                        <td>
                            <a href="{{ route('super_admin.support.show', [
                                    'tenantId' => $ticket->tenant_id,
                                    'ticketId' => $ticket->id,
                                ]) }}"
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

@endsection
