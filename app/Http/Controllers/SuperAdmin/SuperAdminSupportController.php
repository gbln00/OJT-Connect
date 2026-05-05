<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperAdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $tenants       = Tenant::with('domains')->get();
        $allTickets    = collect();
        $ticketSummary = [];

        foreach ($tenants as $tenant) {
            try {
                // ── Everything happens INSIDE run() ──────────────────────
                $result = $tenant->run(function () use ($request) {

                    $q = SupportTicket::with('user')->latest();

                    if ($request->filled('status')) {
                        $q->where('status', $request->status);
                    }
                    if ($request->filled('priority')) {
                        $q->where('priority', $request->priority);
                    }

                    $rows = $q->take(50)->get();

                    // Map to plain objects HERE while still inside tenant context
                    $mapped = $rows->map(fn ($ticket) => (object) [
                        'id'             => $ticket->id,
                        'ref'            => $ticket->ref,
                        'subject'        => $ticket->subject,
                        'type'           => $ticket->type,
                        'type_label'     => $ticket->type_label,
                        'priority'       => $ticket->priority,
                        'priority_label' => $ticket->priority_label,
                        'priority_color' => $ticket->priority_color,
                        'status'         => $ticket->status,
                        'status_label'   => $ticket->status_label,
                        'status_color'   => $ticket->status_color,
                        'module'         => $ticket->module,
                        'created_at'     => $ticket->created_at,
                        'resolved_at'    => $ticket->resolved_at,
                        'reply_count'    => $ticket->replies()->count(),
                        'user_name'      => $ticket->user?->name,
                        'user_role'      => $ticket->user?->role,
                    ]);

                    $counts = [
                        'open'     => SupportTicket::whereIn('status', ['open', 'in_progress', 'waiting_on_user'])->count(),
                        'resolved' => SupportTicket::whereIn('status', ['resolved', 'closed'])->count(),
                        'total'    => SupportTicket::count(),
                    ];

                    return compact('mapped', 'counts');
                });

                // Now safely add to collection — all plain stdClass, no Eloquent
                foreach ($result['mapped'] as $ticket) {
                    $ticket->tenant_id   = $tenant->id;
                    $ticket->tenant_name = $tenant->name ?? $tenant->id;
                    $allTickets->push($ticket);
                }

                $ticketSummary[$tenant->id] = $result['counts'];

            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning(
                    "SuperAdminSupportController: skipped tenant {$tenant->id}: " . $e->getMessage()
                );
                $ticketSummary[$tenant->id] = ['open' => 0, 'resolved' => 0, 'total' => 0];
            }
        }

        // Sort: open first, then by priority
        $priorityOrder = ['urgent' => 0, 'high' => 1, 'normal' => 2, 'low' => 3];
        $statusOrder   = ['open' => 0, 'in_progress' => 1, 'waiting_on_user' => 2, 'resolved' => 3, 'closed' => 4];

        $allTickets = $allTickets->sortBy([
            fn ($a, $b) => ($statusOrder[$a->status]    ?? 9) <=> ($statusOrder[$b->status]    ?? 9),
            fn ($a, $b) => ($priorityOrder[$a->priority] ?? 9) <=> ($priorityOrder[$b->priority] ?? 9),
        ])->values();

        $totalOpen   = $allTickets->whereIn('status', ['open', 'in_progress', 'waiting_on_user'])->count();
        $totalUrgent = $allTickets
            ->where('priority', 'urgent')
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return view('super_admin.support.index', compact(
            'allTickets', 'tenants', 'ticketSummary', 'totalOpen', 'totalUrgent'
        ));
    }

    public function show(Request $request, string $tenantId, int $ticketId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        [$ticket, $replies] = $tenant->run(function () use ($ticketId) {
            $ticketModel = SupportTicket::with('user')->findOrFail($ticketId);

            $ticketDto = (object) [
                'id'             => $ticketModel->id,
                'ref'            => $ticketModel->ref,
                'subject'        => $ticketModel->subject,
                'message'        => $ticketModel->message,
                'type'           => $ticketModel->type,
                'type_label'     => $ticketModel->type_label,
                'priority'       => $ticketModel->priority,
                'priority_label' => $ticketModel->priority_label,
                'priority_color' => $ticketModel->priority_color,
                'status'         => $ticketModel->status,
                'status_label'   => $ticketModel->status_label,
                'status_color'   => $ticketModel->status_color,
                'module'         => $ticketModel->module,
                'internal_note'  => $ticketModel->internal_note,
                'created_at'     => $ticketModel->created_at,
                'resolved_at'    => $ticketModel->resolved_at,
                'user_name'      => $ticketModel->user?->name,
                'user_email'     => $ticketModel->user?->email,
                'user_id'        => $ticketModel->user_id,
                'is_active'      => $ticketModel->isActive(),
                'is_closed'      => $ticketModel->isClosed(),
            ];

            $replyDtos = SupportTicketReply::where('ticket_id', $ticketId)
                ->orderBy('created_at')
                ->get()
                ->map(fn ($r) => (object) [
                    'id'              => $r->id,
                    'sender_type'     => $r->sender_type,
                    'sender_name'     => $r->sender_name,
                    'display_name'    => $r->display_name,
                    'message'         => $r->message,
                    'attachment_path' => $r->attachment_path,
                    'attachment_name' => $r->attachment_name,
                    'created_at'      => $r->created_at,
                    'is_from_support' => $r->isFromSupport(),
                ]);

            return [$ticketDto, $replyDtos];
        });

        $ticket->tenant_id   = $tenant->id;
        $ticket->tenant_name = $tenant->name ?? $tenant->id;

        return view('super_admin.support.show', compact('ticket', 'replies', 'tenant'));
    }

    public function reply(Request $request, string $tenantId, int $ticketId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        $validated = $request->validate([
            'message'       => ['required', 'string', 'min:5', 'max:3000'],
            'status'        => ['required', 'in:open,in_progress,waiting_on_user,resolved,closed'],
            'internal_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $tenant->run(function () use ($validated, $ticketId) {
            $ticket  = SupportTicket::findOrFail($ticketId);

            SupportTicketReply::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => null,
                'sender_type' => 'support',
                'sender_name' => 'Support Team',
                'message'     => $validated['message'],
            ]);

            $updates = ['status' => $validated['status']];

            if ($validated['status'] === 'resolved') {
                $updates['resolved_at'] = now();
            }

            if (!empty($validated['internal_note'])) {
                $updates['internal_note'] = $validated['internal_note'];
            }

            $ticket->update($updates);

            try {
                \App\Models\TenantNotification::notify(
                    title:      'Support Reply Received',
                    message:    "The support team has replied to your ticket: \"{$ticket->subject}\".",
                    type:       'info',
                    targetRole: $ticket->user?->role ?? 'admin',
                    userId:     $ticket->user_id
                );
            } catch (\Throwable) {}
        });

        return back()->with('success', 'Reply sent and ticket updated.');
    }

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