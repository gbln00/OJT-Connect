<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Auth controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;


// Admin controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\HoursController;
use App\Http\Controllers\Admin\WeeklyReportController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ExportController;

// Coordinator controllers
use App\Http\Controllers\Coordinator\CoordinatorController;
use App\Http\Controllers\Coordinator\CoordinatorStudentController;
use App\Http\Controllers\Coordinator\CoordinatorApplicationController;
use App\Http\Controllers\Coordinator\CoordinatorHourLogController;
use App\Http\Controllers\Coordinator\CoordinatorReportController;
use App\Http\Controllers\Coordinator\CoordinatorEvaluationController;

// Student controllers
use App\Http\Controllers\Student\StudentApplicationController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\HourLogController;
use App\Http\Controllers\Student\StudentWeeklyReportController;
use App\Http\Controllers\Student\StudentEvaluationController;
use App\Http\Controllers\Student\StudentSettingsController;

// Supervisor controllers
use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Supervisor\SupervisorHourLogController;
use App\Http\Controllers\Supervisor\SupervisorEvaluationController;
use App\Http\Controllers\Supervisor\SupervisorSettingsController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,   // 1. Boots tenancy — tenant() is available after this
    PreventAccessFromCentralDomains::class, // 2. Rejects central domain hits
    'tenant.active',                    // 3. Aborts with 503 if tenant status === 'inactive'
])->group(function () {

    // ── Root redirect ─────────────────────────────────────────────────
    Route::get('/', function () {
        if (auth()->check()) {
            $path = match(auth()->user()->role) {
                'admin'              => '/admin/dashboard',
                'ojt_coordinator'    => '/coordinator/dashboard',
                'company_supervisor' => '/supervisor/dashboard',
                'student_intern'     => '/student/dashboard',
                default              => abort(403),
            };

            return redirect($path);
        }

        return view('welcome');
    });

    // ── Guest-only routes ─────────────────────────────────────────────
        Route::middleware('guest')->group(function () {
            Route::get('/login',                  [LoginController::class, 'showLogin'])->name('login');
            Route::post('/login',                 [LoginController::class, 'login']);
            Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
            Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
            Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
            Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

            // Google redirect only —
            Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
        });

        // ── Google tenant callback —
        Route::get('/auth/google/tenant-login', [GoogleAuthController::class, 'tenantLogin'])->name('google.tenant.login');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

    // ── Admin ─────────────────────────────────────────────────────────
    Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Settings
        Route::get('/settings',            [AdminController::class, 'settings'])->name('settings');
        Route::patch('/settings/profile',  [AdminController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.update.password');

        // User management
        Route::get('/users',                 [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',          [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',          [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('users.destroy');

        // Company management
        Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create',             [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies',                   [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit',     [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/toggle', [CompanyController::class, 'toggleActive'])->name('companies.toggle');
        Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');

        // Applications
        Route::get('/applications',                        [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',          [ApplicationController::class, 'show'])->name('applications.show');
        Route::delete('/applications/{application}',       [ApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [ApplicationController::class, 'reject'])->name('applications.reject');

        // Hours monitoring
        Route::get('/hours',           [HoursController::class, 'index'])->name('hours.index');
        Route::get('/hours/{student}', [HoursController::class, 'show'])->name('hours.show');

        // Weekly reports
        Route::get('/reports',          [WeeklyReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [WeeklyReportController::class, 'show'])->name('reports.show');

        // Evaluations
        Route::get('/evaluations',                [EvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}',   [EvaluationController::class, 'show'])->name('evaluations.show');

        // Exports
        Route::get('/exports',                 [ExportController::class, 'index'])->name('export.index');
        Route::get('/exports/pdf/students',    [ExportController::class, 'pdfStudents'])->name('export.pdf.students');
        Route::get('/exports/pdf/evaluations', [ExportController::class, 'pdfEvaluations'])->name('export.pdf.evaluations');
        Route::get('/exports/excel',           [ExportController::class, 'excelFull'])->name('export.excel');
    });

    // ── Coordinator ───────────────────────────────────────────────────
    Route::middleware(['auth', 'role:ojt_coordinator'])
        ->prefix('coordinator')
        ->name('coordinator.')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [CoordinatorController::class, 'dashboard'])->name('dashboard');

        // Students
        Route::get('/students', [CoordinatorStudentController::class, 'index'])->name('students.index');

        // Applications
        Route::get('/applications',                        [CoordinatorApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',          [CoordinatorApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/approve', [CoordinatorApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [CoordinatorApplicationController::class, 'reject'])->name('applications.reject');

        // Weekly reports
        Route::get('/reports',                   [CoordinatorReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/approve', [CoordinatorReportController::class, 'approve'])->name('reports.approve');
        Route::post('/reports/{report}/return',  [CoordinatorReportController::class, 'return'])->name('reports.return');

        // Evaluations
        Route::get('/evaluations',                        [CoordinatorEvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}',           [CoordinatorEvaluationController::class, 'show'])->name('evaluations.show');
        Route::post('/evaluations/{evaluation}/complete', [CoordinatorEvaluationController::class, 'complete'])->name('evaluations.complete');
    });

    // ── Supervisor ────────────────────────────────────────────────────
    Route::middleware(['auth', 'role:company_supervisor'])
        ->prefix('supervisor')
        ->name('supervisor.')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

        // Interns
        Route::get('/interns', [SupervisorController::class, 'interns'])->name('interns.index');

        // Hour logs
        Route::get('/hours',                    [SupervisorHourLogController::class, 'index'])->name('hours.index');
        Route::post('/hours/{hourLog}/approve', [SupervisorHourLogController::class, 'approve'])->name('hours.approve');
        Route::post('/hours/{hourLog}/reject',  [SupervisorHourLogController::class, 'reject'])->name('hours.reject');

        // Evaluations
        Route::get('/evaluations',               [SupervisorEvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/{application}', [SupervisorEvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/evaluations/{application}',[SupervisorEvaluationController::class, 'store'])->name('evaluations.store');

        // Settings
        Route::get('/profile/settings',    [SupervisorSettingsController::class, 'edit'])->name('profile.settings');
        Route::patch('/settings/profile',  [SupervisorSettingsController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [SupervisorSettingsController::class, 'updatePassword'])->name('settings.update.password');
    });

    // ── Student ───────────────────────────────────────────────────────
    Route::middleware(['auth', 'role:student_intern'])
        ->prefix('student')
        ->name('student.')
        ->group(function () {

        // Dashboard
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

        // OJT Application
        Route::get('/application/create',        [StudentApplicationController::class, 'create'])->name('application.create');
        Route::post('/application',              [StudentApplicationController::class, 'store'])->name('application.store');
        Route::get('/application/{application}', [StudentApplicationController::class, 'show'])->name('application.show');

        // Hour logs
        Route::get('/hours',        [HourLogController::class, 'index'])->name('hours.index');
        Route::get('/hours/create', [HourLogController::class, 'create'])->name('hours.create');
        Route::post('/hours',       [HourLogController::class, 'store'])->name('hours.store');

        // Reports
        Route::get('/reports',               [StudentWeeklyReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/create',        [StudentWeeklyReportController::class, 'create'])->name('reports.create');
        Route::post('/reports',              [StudentWeeklyReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}/edit', [StudentWeeklyReportController::class, 'edit'])->name('reports.edit');
        Route::patch('/reports/{report}',    [StudentWeeklyReportController::class, 'update'])->name('reports.update');
        Route::get('/reports/{report}',      [StudentWeeklyReportController::class, 'show'])->name('reports.show');

        // Evaluations
        Route::get('/evaluation', [StudentEvaluationController::class, 'show'])->name('evaluation.show');

        // Settings
        Route::get('/settings',            [StudentSettingsController::class, 'index'])->name('settings');
        Route::patch('/settings/profile',  [StudentSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password', [StudentSettingsController::class, 'updatePassword'])->name('settings.password');
    });

});