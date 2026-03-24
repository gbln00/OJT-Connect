<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\SuperAdminTenantManagementController;

// ── Root Redirect ─────────────────────────────────────────────────
Route::get('/', function () {
    if (auth()->check()) {
        return match(auth()->user()->role) {
            'super_admin'        => redirect()->route('super_admin.dashboard'),
            'admin'              => redirect()->route('admin.dashboard'),
            'ojt_coordinator'    => redirect()->route('coordinator.dashboard'),
            'company_supervisor' => redirect()->route('supervisor.dashboard'),
            'student_intern'     => redirect()->route('student.dashboard'),
            default              => abort(403, 'Invalid role.'),
        };
    }

    return view('welcome');
});

// ── Guest-only ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',                  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login',                 [LoginController::class, 'login']);
    Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Super Admin ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super_admin.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // Tenant management (CRUD)
    Route::get('/tenants',               [SuperAdminTenantManagementController::class, 'index'])->name('tenants.index');
    Route::get('/tenants/create',        [SuperAdminTenantManagementController::class, 'create'])->name('tenants.create');
    Route::post('/tenants',              [SuperAdminTenantManagementController::class, 'store'])->name('tenants.store');
    Route::get('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'show'])->name('tenants.show');
    Route::get('/tenants/{tenant}/edit', [SuperAdminTenantManagementController::class, 'edit'])->name('tenants.edit');
    Route::put('/tenants/{tenant}',      [SuperAdminTenantManagementController::class, 'update'])->name('tenants.update');
    Route::delete('/tenants/{tenant}',   [SuperAdminTenantManagementController::class, 'destroy'])->name('tenants.destroy');
});