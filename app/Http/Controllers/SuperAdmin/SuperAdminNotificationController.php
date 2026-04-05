<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminNotification;
use Stancl\Tenancy\Database\Concerns\CentralConnection;
use Illuminate\Http\Request;

class SuperAdminNotificationController extends Controller
{

    use CentralConnection;
    
    public function index()
    {
        $notifications = SuperAdminNotification::latest()->paginate(20);
        $unreadCount   = SuperAdminNotification::unread()->count();

        return view('super_admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    // Mark a single notification as read
    public function markRead(SuperAdminNotification $notification)
    {
        $notification->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    // Mark all as read
    public function markAllRead()
    {
        SuperAdminNotification::unread()->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    // Delete a single notification
    public function destroy(SuperAdminNotification $notification)
    {
        $notification->delete();

        return response()->json(['ok' => true]);
    }

    // Clear all read notifications
    public function clearRead()
    {
        SuperAdminNotification::where('is_read', true)->delete();

        return back()->with('success', 'Read notifications cleared.');
    }
}