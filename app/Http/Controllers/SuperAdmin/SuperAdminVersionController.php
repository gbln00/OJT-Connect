<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemVersion;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuperAdminVersionController extends Controller
{
    public function index()
    {
        $versions     = SystemVersion::latest()->paginate(20);
        $currentVersion = SystemVersion::current();
        $totalTenants = Tenant::where('status', 'active')->orWhereNull('status')->count();

        return view('super_admin.versions.index', compact('versions', 'currentVersion', 'totalTenants'));
    }

    public function create()
    {
        return view('super_admin.versions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'version'          => ['required', 'string', 'max:20', 'unique:system_versions,version'],
            'label'            => ['nullable', 'string', 'max:255'],
            'type'             => ['required', 'in:major,minor,patch,hotfix'],
            'changelog'        => ['required', 'string'],
            'is_critical'      => ['boolean'],          // NEW
            'requires_version' => ['nullable', 'string'], // NEW
            'migration_folder' => ['nullable', 'string'], // NEW
        ]);
    
        SystemVersion::create($data + [
            'is_published' => false,
            'is_current'   => false,
            'created_by'   => Auth::id(),
        ]);
    
        return redirect()->route('super_admin.versions.index')
            ->with('success', "Version {$data['version']} created as draft.");
    }

    public function edit(SystemVersion $version)
    {
        return view('super_admin.versions.edit', compact('version'));
    }

    public function update(Request $request, SystemVersion $version)
    {
        $data = $request->validate([
            'version'   => ['required', 'string', 'max:20',
                \Illuminate\Validation\Rule::unique('system_versions', 'version')->ignore($version->id)],
            'label'     => ['nullable', 'string', 'max:255'],
            'type'      => ['required', 'in:major,minor,patch,hotfix'],
            'changelog' => ['required', 'string'],
        ]);

        $version->update($data);

        return back()->with('success', 'Version updated.');
    }

    public function publish(SystemVersion $version)
    {
        $version->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $version->markAsCurrent();

        $tenants  = Tenant::where('status', 'active')->orWhereNull('status')->get();
        $notified = 0;

        foreach ($tenants as $tenant) {
            // Create TenantUpdate record
            \App\Models\TenantUpdate::firstOrCreate(
                ['tenant_id' => $tenant->id, 'version_id' => $version->id],
                ['status' => 'pending']
            );

            try {
                tenancy()->initialize($tenant);
                \App\Models\TenantNotification::notify(
                    title:      "New Update: v{$version->version}",
                    message:    $version->label ?? "A new system update is available.",
                    type:       $version->is_critical ? 'warning' : 'info',
                    targetRole: 'admin'
                );
                tenancy()->end();
                $notified++;
            } catch (\Throwable $e) {
                tenancy()->end();
                Log::error("Version notify failed for {$tenant->id}: " . $e->getMessage());
            }
        }

        return back()->with('success',
            "v{$version->version} published and {$notified} tenant(s) notified.");
    }

    /** Manually set a published version as the "current" live version */
    public function setCurrent(SystemVersion $version)
    {
        if (! $version->is_published) {
            return back()->with('error', 'Only published versions can be set as current.');
        }

        $version->markAsCurrent();

        return back()->with('success', "v{$version->version} is now marked as the current live version.");
    }

    public function destroy(SystemVersion $version)
    {
        $wasCurrent = $version->is_current;
        $version->delete();

        // If we deleted the current version, promote the next published one
        if ($wasCurrent) {
            SystemVersion::published()->first()?->update(['is_current' => true]);
        }

        return redirect()->route('super_admin.versions.index')
            ->with('success', 'Version deleted.');
    }
    
}