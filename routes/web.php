<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantManagementController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantApprovalController as TenantApprovalController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantManualController as TenantManualController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantRegisterController as TenantRegisterController;
use App\Http\Controllers\SuperAdmin\SuperAdminNotificationController;
use App\Http\Controllers\Auth\GoogleAuthController;


foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        // ── Root Redirect ─────────────────────────────────────────────────
        Route::get('/', function () {
            if (auth()->check()) {
                $role = auth()->user()->role;

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

            // Google redirect only — 
            Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
        });

        // ── Google callback  ──────────────────────
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

            // ── Approval queue ── (must come BEFORE {tenant} wildcard)
            Route::get('/approvals',                            [TenantApprovalController::class, 'pending'])->name('approvals.pending');
            Route::post('/approvals/{registration}/approve',    [TenantApprovalController::class, 'approve'])->name('approvals.approve');
            Route::post('/approvals/{registration}/reject',     [TenantApprovalController::class, 'reject'])->name('approvals.reject');

            // ── Tenant CRUD ──
            Route::get('/tenants',               [SuperAdminTenantManagementController::class, 'index'])->name('tenants.index');
            Route::get('/tenants/create',        [SuperAdminTenantManagementController::class, 'create'])->name('tenants.create');
            Route::post('/tenants',              [SuperAdminTenantManagementController::class, 'store'])->name('tenants.store');
            Route::get('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'show'])->name('tenants.show');
            Route::get('/tenants/{tenant}/edit', [SuperAdminTenantManagementController::class, 'edit'])->name('tenants.edit');
            Route::put('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'update'])->name('tenants.update');
            Route::delete('/tenants/{tenant}',   [SuperAdminTenantManagementController::class, 'destroy'])->name('tenants.destroy');

            // Notifications
            Route::get('notifications',                [SuperAdminNotificationController::class, 'index'])       ->name('notifications.index');
            Route::post('notifications/mark-all-read', [SuperAdminNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
            Route::post('notifications/clear-read',    [SuperAdminNotificationController::class, 'clearRead'])  ->name('notifications.clearRead');
            Route::post('notifications/{notification}/read', [SuperAdminNotificationController::class, 'markRead'])->name('notifications.markRead');
            Route::delete('notifications/{notification}',    [SuperAdminNotificationController::class, 'destroy'])  ->name('notifications.destroy');

            });

    });
}