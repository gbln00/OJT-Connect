<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantNotification;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index()
    {
        $notifications = TenantNotification::latest()->paginate(20);
        $unreadCount   = TenantNotification::unread()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markRead(TenantNotification $notification)
    {
        $notification->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        TenantNotification::unread()->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearRead()
    {
        TenantNotification::where('is_read', true)->delete();
        return back()->with('success', 'Read notifications cleared.');
    }

    public function destroy(TenantNotification $notification)
    {
        $notification->delete();
        return response()->json(['ok' => true]);
    }
}