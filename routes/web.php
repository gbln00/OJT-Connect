<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Super Admin Controllers
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantManagementController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantApprovalController as TenantApprovalController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantRegisterController as TenantRegisterController;
use App\Http\Controllers\SuperAdmin\SuperAdminNotificationController;
use App\Http\Controllers\SuperAdmin\SuperAdminPlanController as PlanController;
use App\Http\Controllers\SuperAdmin\SuperAdminPlanController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantMonitoringController;
use App\Http\Controllers\SuperAdmin\SuperAdminPlanRequestController;
use App\Http\Controllers\SuperAdmin\SuperAdminVersionController;

use App\Http\Controllers\Auth\GoogleAuthController;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        // ── Root Redirect ─────────────────────────────────────────────────
        Route::get('/', function () {
            if (Auth::check()) {
                $role = Auth::user()->role;

                if ($role === 'super_admin') {
                    return redirect()->route('super_admin.dashboard');
                }

                // Non-super-admin users don't belong on the central domain
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Please log in from your institution\'s domain.',
                ]);
            }

            return view('central');
        });

        // ── Guest-only ────────────────────────────────────────────────────
        Route::middleware('guest')->group(function () {
            Route::get('/login',                  [LoginController::class, 'showLogin'])->name('login');
            Route::post('/login',                 [LoginController::class, 'login']);
            Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
            Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
            Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
            Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

            // ── PUBLIC: Self-service registration form ──
            Route::get('/register/company',  [TenantRegisterController::class, 'showForm'])->name('tenant.register');
            Route::post('/register/company', [TenantRegisterController::class, 'submit'])->name('tenant.register.submit');

            // Google redirect only
            Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
        });

        // ── Google callback ───────────────────────────────────────────────
        Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

        // ── Logout ────────────────────────────────────────────────────────
        Route::post('/logout', [LoginController::class, 'logout'])->name('central.logout');

        // ── Super Admin ───────────────────────────────────────────────────
        Route::middleware(['auth', 'role:super_admin'])
            ->prefix('super-admin')
            ->name('super_admin.')
            ->group(function () {

            // Dashboard
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

            // ── Approval queue for new tenant registrations ─────────────────────────────────
            Route::get('/approvals',                            [TenantApprovalController::class, 'pending'])->name('approvals.pending');
            Route::post('/approvals/{registration}/approve',    [TenantApprovalController::class, 'approve'])->name('approvals.approve');
            Route::post('/approvals/{registration}/reject',     [TenantApprovalController::class, 'reject'])->name('approvals.reject');

            // ── Tenant CRUD ─────────────────────────────────────────────────
            Route::get('/tenants',               [SuperAdminTenantManagementController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/create',        [SuperAdminTenantManagementController::class, 'create'])->name('tenants.create');
            Route::post('/tenants',              [SuperAdminTenantManagementController::class, 'store'])->name('tenants.store');
            Route::get('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'show'])->name('tenants.show');
            Route::get('/tenants/{tenant}/edit', [SuperAdminTenantManagementController::class, 'edit'])->name('tenants.edit');
            Route::put('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'update'])->name('tenants.update');
            Route::delete('/tenants/{tenant}',   [SuperAdminTenantManagementController::class, 'destroy'])->name('tenants.destroy');

            // Plan Requests
            Route::post('/plan-requests/{planRequest}/approve', [SuperAdminPlanRequestController::class, 'approve'])->name('plan_requests.approve');
            Route::post('/plan-requests/{planRequest}/reject',  [SuperAdminPlanRequestController::class, 'reject'])->name('plan_requests.reject');
            
            // ── Tenant Monitoring ─────────────────────────────────────────────────
            Route::get('/monitoring',              [SuperAdminTenantMonitoringController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring/{tenant}',     [SuperAdminTenantMonitoringController::class, 'show'])->name('monitoring.show');


            // ── Plans ─────────────────────────────────────────────────
            Route::get   ('/plans',                [SuperAdminPlanController::class, 'index'])->name('plans.index');
            Route::get   ('/plans/create',         [SuperAdminPlanController::class, 'create'])->name('plans.create');
            Route::post  ('/plans',                [SuperAdminPlanController::class, 'store'])->name('plans.store');
            Route::get   ('/plans/{plan}/edit',    [SuperAdminPlanController::class, 'edit'])->name('plans.edit');
            Route::put   ('/plans/{plan}',         [SuperAdminPlanController::class, 'update'])->name('plans.update');
            Route::delete('/plans/{plan}',         [SuperAdminPlanController::class, 'destroy'])->name('plans.destroy');
            Route::patch ('/plans/{plan}/toggle',  [SuperAdminPlanController::class, 'toggle'])->name('plans.toggle');
  
            // ── Promotions ─────────────────────────────────────────────
            Route::post  ('/plans/{plan}/promotions',         [SuperAdminPlanController::class, 'storePromotion'])->name('plans.promotions.store');
            Route::put   ('/plans/promotions/{promo}',        [SuperAdminPlanController::class, 'updatePromotion'])->name('plans.promotions.update');
            Route::patch ('/plans/promotions/{promo}/toggle', [SuperAdminPlanController::class, 'togglePromotion'])->name('plans.promotions.toggle');
            Route::delete('/plans/promotions/{promo}',        [SuperAdminPlanController::class, 'destroyPromotion'])->name('plans.promotions.destroy');

            //
            Route::resource('super-admin/versions', SuperAdminVersionController::class)->names('super_admin.versions');
            Route::post('super-admin/versions/{version}/publish',   [SuperAdminVersionController::class, 'publish'])->name('super_admin.versions.publish');
            
            
            // ── Notifications ─────────────────────────────────────────────
            Route::get('notifications',                      [SuperAdminNotificationController::class, 'index'])->name('notifications.index');
            Route::post('notifications/mark-all-read',       [SuperAdminNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::post('notifications/clear-read',          [SuperAdminNotificationController::class, 'clearRead'])->name('notifications.clearRead');
            Route::post('notifications/{notification}/read', [SuperAdminNotificationController::class, 'markRead'])->name('notifications.markRead');
            Route::delete('notifications/{notification}',    [SuperAdminNotificationController::class, 'destroy'])->name('notifications.destroy');

        });

    });
}