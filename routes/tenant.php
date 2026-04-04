<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;

// Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\HoursController;
use App\Http\Controllers\Admin\WeeklyReportController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\AdminPlanController;

// Coordinator
use App\Http\Controllers\Coordinator\CoordinatorController;
use App\Http\Controllers\Coordinator\CoordinatorStudentController;
use App\Http\Controllers\Coordinator\CoordinatorApplicationController;
use App\Http\Controllers\Coordinator\CoordinatorHourLogController;
use App\Http\Controllers\Coordinator\CoordinatorReportController;
use App\Http\Controllers\Coordinator\CoordinatorEvaluationController;
use App\Http\Controllers\Coordinator\CoordinatorPlanController;

// Student
use App\Http\Controllers\Student\StudentApplicationController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentWeeklyReportController;
use App\Http\Controllers\Student\StudentEvaluationController;
use App\Http\Controllers\Student\StudentHourLogController;
use App\Http\Controllers\Student\StudentSettingsController;

// Supervisor
use App\Http\Controllers\Supervisor\SupervisorController;
use App\Http\Controllers\Supervisor\SupervisorHourLogController;
use App\Http\Controllers\Supervisor\SupervisorEvaluationController;
use App\Http\Controllers\Supervisor\SupervisorSettingsController;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'tenant.active',
])->group(function () {

    // ── Root redirect ──────────────────────────────────────────────────
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

    // ── Guest-only ─────────────────────────────────────────────────────
    Route::middleware('guest')->group(function () {
        Route::get('/login',                  [LoginController::class, 'showLogin'])->name('login');
        Route::post('/login',                 [LoginController::class, 'login']);
        Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');
        Route::get('/auth/google',            [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    });

    Route::get('/auth/google/tenant-login', [GoogleAuthController::class, 'tenantLogin'])
        ->name('google.tenant.login');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');

    // ══════════════════════════════════════════════════════════════════
    // ADMIN  (Basic plan = all core routes; Standard+ = reports/evals;
    //         Premium = exports)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/settings',            [AdminController::class, 'settings'])->name('settings');
        Route::patch('/settings/profile',  [AdminController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.update.password');

        Route::get('/users',                 [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',          [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',          [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create',             [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies',                   [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit',     [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/toggle', [CompanyController::class, 'toggleActive'])->name('companies.toggle');
        Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');

        Route::get('/applications',                        [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',          [ApplicationController::class, 'show'])->name('applications.show');
        Route::delete('/applications/{application}',       [ApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [ApplicationController::class, 'reject'])->name('applications.reject');

        Route::get('/hours',                       [HoursController::class, 'index'])->name('hours.index');
        Route::get('/hours/{student}',             [HoursController::class, 'show'])->name('hours.show');
        Route::post('/hours/{hourLog}/approve',    [HoursController::class, 'approve'])->name('hours.approve');
        Route::post('/hours/{student}/approve-all',[HoursController::class, 'approveAll'])->name('hours.approve-all');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/reports',              [WeeklyReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}',     [WeeklyReportController::class, 'show'])->name('reports.show');
            Route::post('/reports/{report}/approve', [WeeklyReportController::class, 'approve'])->name('reports.approve');
            Route::post('/reports/{report}/return',  [WeeklyReportController::class, 'return'])->name('reports.return');

            Route::get('/evaluations',                  [EvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{evaluation}',     [EvaluationController::class, 'show'])->name('evaluations.show');
        });

        // ── Premium plan only ───────────────────────────────────────────

        Route::middleware('plan:premium')->group(function () {
            Route::get('/exports',                 [ExportController::class, 'index'])->name('export.index');
            Route::get('/exports/pdf/students',    [ExportController::class, 'pdfStudents'])->name('export.pdf.students');
            Route::get('/exports/pdf/evaluations', [ExportController::class, 'pdfEvaluations'])->name('export.pdf.evaluations');
            Route::get('/exports/excel',           [ExportController::class, 'excelFull'])->name('export.excel');
        });

        // ── Plans & Promotions ────────────────────────────────────────
        Route::get('/plan', [AdminPlanController::class, 'index'])->name('plan.index');

    });

    // ══════════════════════════════════════════════════════════════════
    // COORDINATOR  (Basic = applications, hours; Standard+ = reports,
    //               evaluations)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:ojt_coordinator'])
        ->prefix('coordinator')
        ->name('coordinator.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard',  [CoordinatorController::class, 'dashboard'])->name('dashboard');
        Route::get('/students',   [CoordinatorStudentController::class, 'index'])->name('students.index');

        Route::get('/applications',                        [CoordinatorApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',          [CoordinatorApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/approve', [CoordinatorApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [CoordinatorApplicationController::class, 'reject'])->name('applications.reject');

        // Hour log view — read-only, Basic+
        Route::get('/hours', [CoordinatorHourLogController::class, 'index'])->name('hours.index');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/reports',                   [CoordinatorReportController::class, 'index'])->name('reports.index');
            Route::post('/reports/{report}/approve', [CoordinatorReportController::class, 'approve'])->name('reports.approve');
            Route::post('/reports/{report}/return',  [CoordinatorReportController::class, 'return'])->name('reports.return');

            Route::get('/evaluations',                        [CoordinatorEvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{evaluation}',           [CoordinatorEvaluationController::class, 'show'])->name('evaluations.show');
            Route::post('/evaluations/{evaluation}/complete', [CoordinatorEvaluationController::class, 'complete'])->name('evaluations.complete');
        });

        // ── Plan overview ─────────────────────────────────────────────
        Route::get('/plan', [CoordinatorPlanController::class, 'index'])->name('plan.index');
        
    });

    // ══════════════════════════════════════════════════════════════════
    // SUPERVISOR  (Basic = hour logs; Standard+ = evaluations)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:company_supervisor'])
        ->prefix('supervisor')
        ->name('supervisor.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
        Route::get('/interns',   [SupervisorController::class, 'interns'])->name('interns.index');

        Route::get('/hours',                     [SupervisorHourLogController::class, 'index'])->name('hours.index');
        Route::get('/hours/{student}',            [SupervisorHourLogController::class, 'show'])->name('hours.show');
        Route::post('/hours/{hourLog}/approve',   [SupervisorHourLogController::class, 'approve'])->name('hours.approve');
        Route::post('/hours/{hourLog}/reject',    [SupervisorHourLogController::class, 'reject'])->name('hours.reject');
        Route::post('/hours/{student}/approve-all', [SupervisorHourLogController::class, 'approveAll'])->name('hours.approve-all');

        Route::get('/profile/settings',    [SupervisorSettingsController::class, 'edit'])->name('profile.settings');
        Route::patch('/settings/profile',  [SupervisorSettingsController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [SupervisorSettingsController::class, 'updatePassword'])->name('settings.update.password');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/evaluations',                [SupervisorEvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{application}',  [SupervisorEvaluationController::class, 'create'])->name('evaluations.create');
            Route::post('/evaluations/{application}', [SupervisorEvaluationController::class, 'store'])->name('evaluations.store');
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // STUDENT  (Basic = application, hours; Standard+ = reports, evals)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:student_intern'])
        ->prefix('student')
        ->name('student.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

        Route::get('/application/create',        [StudentApplicationController::class, 'create'])->name('application.create');
        Route::post('/application',              [StudentApplicationController::class, 'store'])->name('application.store');
        Route::get('/application/{application}', [StudentApplicationController::class, 'show'])->name('application.show');

        Route::get('/hours',        [StudentHourLogController::class, 'index'])->name('hours.index');
        Route::get('/hours/create', [StudentHourLogController::class, 'create'])->name('hours.create');
        Route::post('/hours',       [StudentHourLogController::class, 'store'])->name('hours.store');

        Route::get('/settings',            [StudentSettingsController::class, 'index'])->name('settings');
        Route::patch('/settings/profile',  [StudentSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password', [StudentSettingsController::class, 'updatePassword'])->name('settings.password');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/reports',               [StudentWeeklyReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/create',        [StudentWeeklyReportController::class, 'create'])->name('reports.create');
            Route::post('/reports',              [StudentWeeklyReportController::class, 'store'])->name('reports.store');
            Route::get('/reports/{report}/edit', [StudentWeeklyReportController::class, 'edit'])->name('reports.edit');
            Route::patch('/reports/{report}',    [StudentWeeklyReportController::class, 'update'])->name('reports.update');
            Route::get('/reports/{report}',      [StudentWeeklyReportController::class, 'show'])->name('reports.show');

            Route::get('/evaluation', [StudentEvaluationController::class, 'show'])->name('evaluation.show');
        });
    });

});