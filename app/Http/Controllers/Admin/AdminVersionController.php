<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemVersion;
use App\Models\VersionReadReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVersionController extends Controller
{
    public function index()
    {
        $versions = SystemVersion::published()->paginate(10);
        $tenantId = tenancy()->tenant?->id;
        $userEmail = Auth::user()?->email;

        $unreadCount = SystemVersion::published()
            ->whereDoesntHave('readReceipts', fn($q) =>
                $q->where('tenant_id', $tenantId)->where('read_by', $userEmail)
            )->count();

        return view('admin.whats-new.index', compact('versions', 'tenantId', 'userEmail', 'unreadCount'));
    }

    public function markRead(Request $request, SystemVersion $version)
    {
        $tenantId  = tenancy()->tenant?->id;
        $userEmail = Auth::user()?->email;

        VersionReadReceipt::firstOrCreate(
            ['version_id' => $version->id, 'tenant_id' => $tenantId, 'read_by' => $userEmail],
            ['read_at' => now()]
        );

        return response()->json(['ok' => true]);
    }
}

