<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantRegistration;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        // ── Tenant counts ──────────────────────────────────────────────
        $totalTenants    = Tenant::count();
        $activeTenants   = Tenant::where('status', 'active')->orWhereNull('status')->count();
        $inactiveTenants = Tenant::where('status', 'inactive')->count();
        $domainCount     = \Stancl\Tenancy\Database\Models\Domain::count();

        // ── Plan breakdown (from live tenants) ─────────────────────────
        $basicCount    = Tenant::where('plan', 'basic')->count();
        $standardCount = Tenant::where('plan', 'standard')->count();
        $premiumCount  = Tenant::where('plan', 'premium')->count();

        // ── Registration stats ─────────────────────────────────────────
        $pendingCount  = TenantRegistration::where('status', 'pending')->count();
        $approvedCount = TenantRegistration::where('status', 'approved')->count();
        $rejectedCount = TenantRegistration::where('status', 'rejected')->count();
        $totalRegs     = TenantRegistration::count();

        // ── Recent tenants (with domains + status) ─────────────────────
        $recentTenants = Tenant::with('domains')->latest()->take(5)->get();

        // ── Recent registrations feed ──────────────────────────────────
        $recentRegistrations = TenantRegistration::latest()->take(5)->get();

        return view('super_admin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'inactiveTenants',
            'domainCount',
            'basicCount',
            'standardCount',
            'premiumCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'totalRegs',
            'recentTenants',
            'recentRegistrations',
        ));
    }
}