<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Super-admin view of all support tickets across all tenants.
 * Runs on the central domain — uses tenancy()->initialize() to
 * query a specific tenant's DB when viewing individual tickets.
 */
class SuperAdminSupportController extends Controller
{
    // ── Index: all tickets across all tenants ─────────────────────

    public function index(Request $request)
    {
        $tenants        = Tenant::with('domains')->get();
        $allTickets     = collect();
        $ticketSummary  = [];

        foreach ($tenants as $tenant) {
            try {
                $rows = $tenant->run(function () use ($request) {
                    $q = SupportTicket::with('user')->latest();

                    if (request()->filled('status')) {
                        $q->where('status', request()->status);
                    }
                    if (request()->filled('priority')) {
                        $q->where('priority', request()->priority);
                    }

                    return $q->take(50)->get();
                });

                foreach ($rows as $ticket) {
                    $ticket->tenant_id   = $tenant->id;
                    $ticket->tenant_name = $tenant->name ?? $tenant->id;
                    $allTickets->push($ticket);
                }

                // Summary counts for sidebar
                $counts = $tenant->run(fn() => [
                    'open'     => SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_on_user'])->count(),
                    'resolved' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
                    'total'    => SupportTicket::count(),
                ]);

                $ticketSummary[$tenant->id] = $counts;

            } catch (\Throwable) {
                // Tenant DB may not have the table yet — skip gracefully
                $ticketSummary[$tenant->id] = ['open' => 0, 'resolved' => 0, 'total' => 0];
            }
        }

        // Sort: urgent + open first
        $priorityOrder = ['urgent' => 0, 'high' => 1, 'normal' => 2, 'low' => 3];
        $statusOrder   = ['open' => 0, 'in_progress' => 1, 'waiting_on_user' => 2, 'resolved' => 3, 'closed' => 4];

        $allTickets = $allTickets->sortBy([
            fn($a, $b) => ($statusOrder[$a->status] ?? 9) <=> ($statusOrder[$b->status] ?? 9),
            fn($a, $b) => ($priorityOrder[$a->priority] ?? 9) <=> ($priorityOrder[$b->priority] ?? 9),
        ])->values();

        $totalOpen = $allTickets->whereIn('status', ['open', 'in_progress', 'waiting_on_user'])->count();
        $totalUrgent = $allTickets->where('priority', 'urgent')
                                  ->whereIn('status', ['open', 'in_progress'])->count();

        return view('super_admin.support.index', compact(
            'allTickets', 'tenants', 'ticketSummary', 'totalOpen', 'totalUrgent'
        ));
    }

    // ── Show single ticket (from a specific tenant) ───────────────

    public function show(Request $request, string $tenantId, int $ticketId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        [$ticket, $replies] = $tenant->run(function () use ($ticketId) {
            $ticket  = SupportTicket::with('user')->findOrFail($ticketId);
            $replies = SupportTicketReply::where('ticket_id', $ticketId)
                            ->orderBy('created_at')->get();
            return [$ticket, $replies];
        });

        $ticket->tenant_id   = $tenant->id;
        $ticket->tenant_name = $tenant->name ?? $tenant->id;

        return view('super_admin.support.show', compact('ticket', 'replies', 'tenant'));
    }

    // ── Reply to a ticket (support team response) ─────────────────

    public function reply(Request $request, string $tenantId, int $ticketId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $validated = $request->validate([
            'message'     => ['required', 'string', 'min:5', 'max:3000'],
            'status'      => ['required', 'in:open,in_progress,waiting_on_user,resolved,closed'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $tenant->run(function () use ($validated, $ticketId) {
            $ticket = SupportTicket::findOrFail($ticketId);

            // Save support reply
            SupportTicketReply::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => null, // support team — no tenant user
                'sender_type' => 'support',
                'sender_name' => 'Support Team',
                'message'     => $validated['message'],
            ]);

            // Update ticket status and optional internal note
            $updates = ['status' => $validated['status']];

            if ($validated['status'] === 'resolved') {
                $updates['resolved_at'] = now();
            }

            if (!empty($validated['internal_note'])) {
                $updates['internal_note'] = $validated['internal_note'];
            }

            $ticket->update($updates);

            // Notify the tenant user (in-app notification)
            try {
                \App\Models\TenantNotification::notify(
                    title:      'Support Reply Received',
                    message:    "The support team has replied to your ticket: \"{$ticket->subject}\".",
                    type:       'info',
                    targetRole: $ticket->user?->role ?? 'admin',
                    userId:     $ticket->user_id
                );
            } catch (\Throwable) {
                // Non-critical
            }
        });

        return back()->with('success', 'Reply sent and ticket updated.');
    }

    // ── Update status only (quick action) ────────────────────────

    public function updateStatus(Request $request, string $tenantId, int $ticketId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $request->validate([
            'status' => ['required', 'in:open,in_progress,waiting_on_user,resolved,closed'],
        ]);

        $tenant->run(function () use ($request, $ticketId) {
            $ticket  = SupportTicket::findOrFail($ticketId);
            $updates = ['status' => $request->status];
            if ($request->status === 'resolved') {
                $updates['resolved_at'] = now();
            }
            $ticket->update($updates);
        });

        return back()->with('success', 'Ticket status updated.');
    }
}