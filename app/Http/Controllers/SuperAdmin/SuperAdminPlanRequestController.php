<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlanRequest;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\TenantPlanHistory;
use App\Models\SuperAdminNotification;
use Illuminate\Http\Request;

class SuperAdminPlanRequestController extends Controller
{
    public function approve(Request $request, PlanRequest $planRequest)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = Tenant::find($planRequest->tenant_id);
        if (!$tenant) {
            return back()->with('error', 'Tenant not found.');
        }

        $newPlan = Plan::where('name', $planRequest->requested_plan)->first();
        if (!$newPlan) {
            return back()->with('error', 'Requested plan not found.');
        }

        // Update tenant plan
        $oldPlan = $tenant->plan;
        $tenant->update(['plan' => $planRequest->requested_plan]);

        // Record plan history
        TenantPlanHistory::create([
            'tenant_id'    => $tenant->id,
            'plan_id'      => $newPlan->id,
            'promotion_id' => null,
            'price_paid'   => $newPlan->base_price,
            'starts_at'    => now(),
            'ends_at'      => null,
            'changed_by'   => auth()->id(),
            'notes'        => 'Approved via plan request. ' . ($data['admin_notes'] ?? ''),
        ]);

        // Mark request as approved
        $planRequest->update([
            'status'       => 'approved',
            'admin_notes'  => $data['admin_notes'] ?? null,
            'actioned_at'  => now(),
        ]);

        // Notify tenant (runs inside tenant context)
        $requestType = $planRequest->request_type;
        $planLabel   = $newPlan->label;
        tenancy()->initialize($tenant);
        \App\Models\TenantNotification::notify(
            title:      'Plan ' . ucfirst($requestType) . ' Approved',
            message:    "Your {$requestType} request to the \"{$planLabel}\" plan has been approved.",
            type:       'success',
            targetRole: 'admin'
        );
        tenancy()->end();

        // Super admin notification
        SuperAdminNotification::notify(
            type:    'tenant',
            title:   'Plan Request Approved',
            message: "Tenant \"{$tenant->id}\" plan changed from " . ucfirst($oldPlan) . " to " . ucfirst($planRequest->requested_plan) . ".",
            icon:    'check',
            link:    route('super_admin.tenants.show', $tenant->id),
        );

        return redirect()
            ->route('super_admin.tenants.show', $tenant->id)
            ->with('success', "Plan {$requestType} approved. Tenant is now on the \"{$planLabel}\" plan.");
    }

    public function reject(Request $request, PlanRequest $planRequest)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $tenant = Tenant::find($planRequest->tenant_id);

        // Mark request as rejected
        $planRequest->update([
            'status'      => 'rejected',
            'admin_notes' => $data['admin_notes'] ?? null,
            'actioned_at' => now(),
        ]);

        // Notify tenant
        if ($tenant) {
            tenancy()->initialize($tenant);
            \App\Models\TenantNotification::notify(
                title:      'Plan Request Rejected',
                message:    "Your " . $planRequest->request_type . " request to \"" . $planRequest->requested_plan . "\" was not approved." .
                            ($data['admin_notes'] ? " Note: " . $data['admin_notes'] : " Please contact support."),
                type:       'warning',
                targetRole: 'admin'
            );
            tenancy()->end();
        }

        return redirect()
            ->route('super_admin.tenants.show', $planRequest->tenant_id)
            ->with('success', 'Plan request rejected.');
    }
}