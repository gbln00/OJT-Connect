<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Services\AnalyticsService;
use App\Models\SystemVersion;
use App\Models\TenantSetting;
use App\Models\TenantUpdate;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private const MOCK_UPDATE_VERSION = '1.4.4';

    public function dashboard()
    {
        $totalUsers          = User::count();
        $totalStudents       = User::where('role', 'student_intern')->count();
        $pendingApplications = 0;
        $totalCompanies      = Company::count();
        $activeCompanies     = Company::where('is_active', true)->count();
        $activeUsers         = User::where('is_active', true)->count();
        $inactiveUsers       = User::where('is_active', false)->count();
        $pendingCount        = 0;

        $roleBreakdown = [
            'admin'       => User::where('role', 'admin')->count(),
            'coordinator' => User::where('role', 'ojt_coordinator')->count(),
            'supervisor'  => User::where('role', 'company_supervisor')->count(),
            'student'     => User::where('role', 'student_intern')->count(),
        ];

        $recentUsers    = User::latest()->take(6)->get();
        $recentActivity = [];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'pendingApplications',
            'totalCompanies',
            'activeCompanies',
            'activeUsers',
            'inactiveUsers',
            'pendingCount',
            'roleBreakdown',
            'recentUsers',
            'recentActivity'
        ));
    }

    // ── ANALYTICS ───────────────────────────────────────────────────────────
    public function analytics(AnalyticsService $analytics)
    {
        // All data is JSON-encoded and passed to Chart.js via Blade
        $kpis         = $analytics->summaryKpis();
        $appsPerMonth = $analytics->applicationsPerMonth();
        $logsPerWeek  = $analytics->hourLogsPerWeek();
        $passFailRate = $analytics->passFailRate();
        $perCompany   = $analytics->progressPerCompany();
        $recentEvals  = $analytics->recentEvaluations(6);
        $topStudents  = $analytics->topStudentsByHours(5);
    
        return view('admin.analytics.index', compact(
            'kpis',
            'appsPerMonth',
            'logsPerWeek',
            'passFailRate',
            'perCompany',
            'recentEvals',
            'topStudents'
        ));
    }

    public function mockLab()
    {
        $tenantId = tenancy()->tenant?->id;

        $requiredVersion = SystemVersion::where('version', self::MOCK_UPDATE_VERSION)->first();

        if (! $requiredVersion) {
            return redirect()
                ->route('admin.whats-new.index')
                ->with('error', "Mock feature requires update v" . self::MOCK_UPDATE_VERSION . ", but it is not published yet.");
        }

        $installed = TenantUpdate::where('tenant_id', $tenantId)
            ->where('version_id', $requiredVersion->id)
            ->where('status', 'completed')
            ->exists();

        if (! $installed) {
            return redirect()
                ->route('admin.whats-new.index')
                ->with('error', "Install update v{$requiredVersion->version} to unlock the Mock Feature Lab.");
        }

        $activatedAt = TenantSetting::get('mock_feature.activated_at');

        return view('admin.mock-lab.index', [
            'requiredVersion' => $requiredVersion->version,
            'activatedAt' => $activatedAt,
        ]);
    }


    // ── SETTINGS ─────────────────────────────────────────────────────────────

    public function settings()
    {
        return view('admin.profile.settings');
    }
    
    public function updateProfile(UpdateProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        auth()->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}