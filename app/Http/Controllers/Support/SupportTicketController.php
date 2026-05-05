<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\SuperAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Handles all tenant-side support ticket interactions.
 * Shared by all roles (admin, coordinator, supervisor, student).
 * The route prefix and layout view are the only things that differ per role.
 */
class SupportTicketController extends Controller
{
    // ── Index: list all tickets for the current user ──────────────

    public function index(Request $request)
    {
        try {
            $user  = Auth::user();
            $query = SupportTicket::forUser($user->id)->latest();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $tickets = $query->paginate(12)->withQueryString();

            $counts = [
                'total'    => SupportTicket::forUser($user->id)->count(),
                'open'     => SupportTicket::forUser($user->id)->whereIn('status', ['open', 'in_progress', 'waiting_on_user'])->count(),
                'resolved' => SupportTicket::forUser($user->id)->whereIn('status', ['resolved', 'closed'])->count(),
            ];

            $layout = $this->layoutForRole($user->role);
            $view   = $this->viewPrefixForRole($user->role) . '.index';

            return view($view, compact('tickets', 'counts', 'layout'));

        } catch (\Throwable $e) {
            $tickets = collect();
            $counts  = ['total' => 0, 'open' => 0, 'resolved' => 0];
            $layout  = $this->layoutForRole(Auth::user()->role);
            $view    = $this->viewPrefixForRole(Auth::user()->role) . '.index';
            return view($view, compact('tickets', 'counts', 'layout'));
        }
    }
    // ── Create form ────────────────────────────────────────────────

    public function create()
    {
        $user   = Auth::user();
        $layout = $this->layoutForRole($user->role);
        $view   = $this->viewPrefixForRole($user->role) . '.create';
    
        return view($view, compact('layout'));
    }

    // ── Store a new ticket ─────────────────────────────────────────

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'type'     => ['required', 'in:bug,feature_request,general_inquiry,billing,account,other'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'module'   => ['nullable', 'string', 'max:100'],
            'message'  => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id'  => $user->id,
            'subject'  => $validated['subject'],
            'type'     => $validated['type'],
            'priority' => $validated['priority'],
            'module'   => $validated['module'] ?? null,
            'message'  => $validated['message'],
            'status'   => 'open',
        ]);

        $this->notifySuperAdmin($ticket, $user);

        return redirect()
            ->route($this->routePrefix($user->role) . 'support.show', $ticket)
            ->with('success', "Ticket {$ticket->ref} submitted successfully. Our team will respond shortly.");
    }

    // ── Show single ticket with reply thread ──────────────────────

    public function show(SupportTicket $ticket)
    {
        $user = Auth::user();
    
        if ($ticket->user_id !== $user->id) {
            abort(403);
        }
    
        $ticket->load('replies');
        $layout = $this->layoutForRole($user->role);
        $view   = $this->viewPrefixForRole($user->role) . '.show';
    
        return view($view, compact('ticket', 'layout'));
    }
    // ── Post a reply to a ticket ───────────────────────────────────

    public function reply(Request $request, SupportTicket $ticket)
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        if ($ticket->isClosed()) {
            return back()->with('error', 'This ticket is closed. Please open a new ticket if you need further assistance.');
        }

        $validated = $request->validate([
            'message'    => ['required', 'string', 'min:5', 'max:3000'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,txt,doc,docx', 'max:5120'],
        ]);

        $attachPath = null;
        $attachName = null;

        if ($request->hasFile('attachment')) {
            $file       = $request->file('attachment');
            $attachPath = $file->store('support-attachments/' . $ticket->id, 'public');
            $attachName = $file->getClientOriginalName();
        }

        SupportTicketReply::create([
            'ticket_id'       => $ticket->id,
            'user_id'         => $user->id,
            'sender_type'     => 'user',
            'sender_name'     => $user->name,
            'message'         => $validated['message'],
            'attachment_path' => $attachPath,
            'attachment_name' => $attachName,
        ]);

        if ($ticket->isWaiting()) {
            $ticket->update(['status' => 'in_progress']);
        }

        $this->notifySuperAdminReply($ticket, $user);

        return back()->with('success', 'Reply sent.');
    }

    // ── Close a ticket (user can close their own) ─────────────────

    public function close(SupportTicket $ticket)
    {
        $user = Auth::user();

        if ($ticket->user_id !== $user->id) {
            abort(403);
        }

        if (! $ticket->isActive()) {
            return back()->with('info', 'Ticket is already closed or resolved.');
        }

        $ticket->update([
            'status'      => 'closed',
            'resolved_at' => now(),
        ]);

        return back()->with('success', "Ticket {$ticket->ref} has been closed.");
    }

    // ── Private helpers ───────────────────────────────────────────

    private function layoutForRole(string $role): string
    {
        return match($role) {
            'admin'              => 'layouts.app',
            'ojt_coordinator'    => 'layouts.coordinator-app',  // fix this
            'company_supervisor' => 'layouts.supervisor-app',   // fix this
            'student_intern'     => 'layouts.student-app',      // fix this
            default              => 'layouts.app',
        };
    }
    
    private function routePrefix(string $role): string
    {
        return match($role) {
            'admin'              => 'admin.',
            'ojt_coordinator'    => 'coordinator.',
            'company_supervisor' => 'supervisor.',
            'student_intern'     => 'student.',
            default              => 'admin.',
        };
    }

    private function notifySuperAdmin(SupportTicket $ticket, $user): void
    {
        try {
            $tenantId = tenancy()->tenant?->id ?? 'unknown';
            SuperAdminNotification::notify(
                type:    'support',
                title:   "New Support Ticket [{$ticket->ref}]",
                message: "From {$user->name} ({$tenantId}) — {$ticket->subject}. Priority: {$ticket->priority_label}.",
                icon:    'bell',
                link:    route('super_admin.support.show', ['tenantId' => $tenantId, 'ticketId' => $ticket->id]),
            );
        } catch (\Throwable) {
            // Silently fail — notification is non-critical
        }
    }

    private function notifySuperAdminReply(SupportTicket $ticket, $user): void
    {
        try {
            $tenantId = tenancy()->tenant?->id ?? 'unknown';
            SuperAdminNotification::notify(
                type:    'support',
                title:   "Ticket Reply [{$ticket->ref}]",
                message: "{$user->name} ({$tenantId}) replied to: {$ticket->subject}.",
                icon:    'bell',
                link:    route('super_admin.support.show', ['tenantId' => $tenantId, 'ticketId' => $ticket->id]),
            );
        } catch (\Throwable) {
            // Silently fail
        }
    }
    
    private function viewPrefixForRole(string $role): string
    {
        return match($role) {
            'admin'              => 'admin.support',
            'ojt_coordinator'    => 'coordinator.support',
            'company_supervisor' => 'supervisor.support',
            'student_intern'     => 'student.support',
            default              => 'admin.support',
        };
    }
}