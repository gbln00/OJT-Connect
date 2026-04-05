<?php
namespace App\Http\Controllers\SuperAdmin;
 
use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\TenantMetricsService;
 
class SuperAdminTenantMonitoringController extends Controller
{
    public function __construct(
        private TenantMetricsService $metrics
    ) {}
 
    /**
     * Overview: all tenants with summary metrics.
     */
    public function index()
    {
        $tenants    = Tenant::with('domains')->latest()->get();
        $summaries  = $this->metrics->getAllSummaries();
 
        return view('super_admin.monitoring.index', compact(
            'tenants', 'summaries'
        ));
    }
 
    /**
     * Detail view: full metrics for a single tenant.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('domains');
        $metrics = $this->metrics->getMetrics($tenant);
 
        return view('super_admin.monitoring.show', compact(
            'tenant', 'metrics'
        ));
    }
}
