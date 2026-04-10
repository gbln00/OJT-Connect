<?php

namespace App\Http\Controllers;

use App\Models\TenantNotification;
use Illuminate\Http\Request;

class TenantNotificationController extends Controller
{
    /**
     * Get the current user's role for scoping queries.
     */
    private function role(): string
    {
        return auth()->user()->role;
    }

    public function index()
    {
        $notifications = TenantNotification::forRole($this->role())
            ->latest()
            ->paginate(20);

        $unreadCount = TenantNotification::forRole($this->role())
            ->unread()
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(TenantNotification $notification)
    {
        // Ensure the notification belongs to this role
        abort_if($notification->target_role !== $this->role(), 403);

        $notification->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        TenantNotification::forRole($this->role())
            ->unread()
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearRead()
    {
        TenantNotification::forRole($this->role())
            ->where('is_read', true)
            ->delete();

        return back()->with('success', 'Read notifications cleared.');
    }

    public function destroy(TenantNotification $notification)
    {
        abort_if($notification->target_role !== $this->role(), 403);

        $notification->delete();
        return response()->json(['ok' => true]);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => TenantNotification::forRole($this->role())
                ->unread()
                ->count(),
        ]);
    }
    
}