<?php

namespace App\Http\Controllers;

use App\Models\TenantNotification;
use Illuminate\Http\Request;

class TenantNotificationController extends Controller
{
    // ── Base query — always scoped to current user ────────────────

    private function myNotifications(): \Illuminate\Database\Eloquent\Builder
    {
        return TenantNotification::forRole(auth()->user()->role)
                                 ->forUser(auth()->id());
    }

    // ── Pages ─────────────────────────────────────────────────────

    public function index()
    {
        $notifications = $this->myNotifications()->latest()->paginate(20);
        $unreadCount   = $this->myNotifications()->unread()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    // ── Actions ───────────────────────────────────────────────────

    public function markRead(TenantNotification $notification)
    {
        $this->authorizeNotification($notification);

        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $this->myNotifications()->unread()->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearRead()
    {
        $this->myNotifications()->where('is_read', true)->delete();

        return back()->with('success', 'Read notifications cleared.');
    }

    public function destroy(TenantNotification $notification)
    {
        $this->authorizeNotification($notification);

        $notification->delete();

        return response()->json(['ok' => true]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => $this->myNotifications()->unread()->count(),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────

    private function authorizeNotification(TenantNotification $notification): void
    {
        abort_if(
            $notification->user_id     !== auth()->id() ||
            $notification->target_role !== auth()->user()->role,
            403
        );
    }
}