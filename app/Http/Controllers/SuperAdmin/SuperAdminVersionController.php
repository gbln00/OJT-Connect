<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemVersion;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperAdminVersionController extends Controller
{
    public function index()
    {
        $versions = SystemVersion::latest()->paginate(20);
        return view('super_admin.versions.index', compact('versions'));
    }

    public function create()
    {
        return view('super_admin.versions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'version'   => ['required', 'string', 'max:20', 'unique:system_versions,version'],
            'label'     => ['nullable', 'string', 'max:255'],
            'type'      => ['required', 'in:major,minor,patch,hotfix'],
            'changelog' => ['required', 'string'],
        ]);

        SystemVersion::create($data + [
            'is_published' => false,
            'created_by'  => auth()->id(),
        ]);

        return redirect()->route('super_admin.versions.index')
                         ->with('success', "Version {$data['version']} created. Publish it when ready.");
    }

    
    public function edit(SystemVersion $version)
    {
        return view('super_admin.versions.edit', compact('version'));
    }

    public function update(Request $request, SystemVersion $version)
    {
        $data = $request->validate([
            'version'   => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('system_versions','version')->ignore($version->id)],
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

        // Notify all active tenant admins
        $tenants = Tenant::where('status', 'active')
                          ->orWhereNull('status')
                          ->get();

        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                \App\Models\TenantNotification::notify(
                    title:      "New Update: v{$version->version}",
                    message:    $version->label ?? "A new system update is available. Check What's New.",
                    type:       'info',
                    targetRole: 'admin'
                );
                tenancy()->end();
            } catch (\Throwable $e) {
                tenancy()->end();
                \Log::error("Version notify failed for {$tenant->id}: " . $e->getMessage());
            }
        }

        return back()->with('success', "v{$version->version} published and {$tenants->count()} tenant(s) notified.");
    }

    public function destroy(SystemVersion $version)
    {
        $version->delete();
        return redirect()->route('super_admin.versions.index')
                         ->with('success', 'Version deleted.');
    }
}

