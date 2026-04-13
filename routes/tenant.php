<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// QR Code scanning for clock-ins
use App\Http\Controllers\QrScanController;

// Tenant Notifications
use App\Http\Controllers\TenantNotificationController;

// Auth
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\TwoFactorController;

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
use App\Http\Controllers\Admin\AdminPlanRequestController;
use App\Http\Controllers\Admin\TenantCustomizationController;
use App\Http\Controllers\Admin\AdminQrController;

// Coordinator
use App\Http\Controllers\Coordinator\CoordinatorController;
use App\Http\Controllers\Coordinator\CoordinatorStudentController;
use App\Http\Controllers\Coordinator\CoordinatorApplicationController;
use App\Http\Controllers\Coordinator\CoordinatorHourLogController;
use App\Http\Controllers\Coordinator\CoordinatorReportController;
use App\Http\Controllers\Coordinator\CoordinatorEvaluationController;
use App\Http\Controllers\Coordinator\CoordinatorPlanController;
use App\Http\Controllers\Coordinator\CoordinatorCompanyController;
use App\Http\Controllers\Coordinator\CoordinatorSettingsController;

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
use App\Http\Controllers\Supervisor\SupervisorQrController;

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
        Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    });

    Route::post('/login', [LoginController::class, 'login']);

    // ── Password reset & 2FA routes (some overlap with admin) ─────────────────
    Route::middleware('guest')->group(function () {
        Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
        Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');
        Route::get('/auth/google',            [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    });

    // ── Authenticated routes (all roles) ─────────────────────────────────
    Route::get('/auth/google/tenant-login', [GoogleAuthController::class, 'tenantLogin'])
        ->name('google.tenant.login');

    // 2FA routes (some overlap with admin)
    Route::middleware('auth')->group(function () {
        Route::get('/2fa/challenge',  [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
        Route::post('/2fa/verify',    [TwoFactorController::class, 'verify'])->name('2fa.verify');
        Route::post('/2fa/resend',    [TwoFactorController::class, 'resend'])->name('2fa.resend');
        Route::post('/2fa/disable',   [TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::post('/2fa/enable',    [TwoFactorController::class, 'enable'])->name('2fa.enable');
    });

    // ── Logout (all authenticated users) ─────────────────────────────────
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');

    // ── QR Code Scanning (for clock-in) ─────────────────────────────────
    Route::get('/qr/scan/{token}', [QrScanController::class, 'scan'])->name('qr.scan');

    // ══════════════════════════════════════════════════════════════════
    // ADMIN  (Basic plan = all core routes; Standard+ = reports/evals;
    //         Premium = exports)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:admin', '2fa'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        
        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // ── 2FA setup (admin) ─────────────────────────────────────────────
        Route::get('/2fa/setup',   [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/2fa/disable',[TwoFactorController::class, 'disable'])->name('2fa.disable');

        // ── Profile & password settings ─────────────────────────────────────
        Route::get('/settings',            [AdminController::class, 'settings'])->name('settings');
        Route::patch('/settings/profile',  [AdminController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.update.password');

        // ── User account management ─────────────────────────────────────────────
        Route::get('/users',                 [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create',          [UserController::class, 'create'])->name('users.create');
        Route::post('/users',                [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',          [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
        Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('users.destroy');

        // ── Company management ─────────────────────────────────────────────
        Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create',             [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies',                   [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit',     [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/toggle', [CompanyController::class, 'toggleActive'])->name('companies.toggle');
        Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');

        // ── OJT Application management ─────────────────────────────────────────────
        Route::get('/applications',                        [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',          [ApplicationController::class, 'show'])->name('applications.show');
        Route::delete('/applications/{application}',       [ApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',  [ApplicationController::class, 'reject'])->name('applications.reject');

        // ── Hour log management ─────────────────────────────────────────────
        Route::get('/hours',                       [HoursController::class, 'index'])->name('hours.index');
        Route::get('/hours/{student}',             [HoursController::class, 'show'])->name('hours.show');
        Route::post('/hours/{hourLog}/approve',    [HoursController::class, 'approve'])->name('hours.approve');
        Route::post('/hours/{student}/approve-all',[HoursController::class, 'approveAll'])->name('hours.approve-all');

        // ── Notifications ─────────────────────────────────────────────
        Route::get('/notifications',                              [TenantNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count',                 [TenantNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/mark-all-read',               [TenantNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/clear-read',                  [TenantNotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::post('/notifications/{notification}/read',         [TenantNotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::delete('/notifications/{notification}',            [TenantNotificationController::class, 'destroy'])->name('notifications.destroy');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            
            // ── Weekly reports management ─────────────────────────────────────────────
            Route::get('/reports',                   [WeeklyReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/{report}',          [WeeklyReportController::class, 'show'])->name('reports.show');
            Route::post('/reports/{report}/approve', [WeeklyReportController::class, 'approve'])->name('reports.approve');
            Route::post('/reports/{report}/return',  [WeeklyReportController::class, 'return'])->name('reports.return');

            // ── Evaluation management ─────────────────────────────────────────────
            Route::get('/evaluations',                  [EvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{evaluation}',     [EvaluationController::class, 'show'])->name('evaluations.show');
        });

        // ── Premium plan only ───────────────────────────────────────────

        Route::middleware('plan:premium')->group(function () {

            // ── Data exports ─────────────────────────────────────────────
            Route::get('/exports',                              [ExportController::class, 'index'])->name('export.index');
            Route::get('/exports/pdf/students',                 [ExportController::class, 'pdfStudents'])->name('export.pdf.students');
            Route::get('/exports/pdf/evaluations',              [ExportController::class, 'pdfEvaluations'])->name('export.pdf.evaluations');
            Route::get('/exports/excel',                        [ExportController::class, 'excelFull'])->name('export.excel');
            Route::get('/exports/certificate/{application}',    [ExportController::class, 'certificate'])->name('export.certificate');
            
            // ── Analytics Dashboard (Premium only) ──────────────────────────
            Route::get('/analytics',                    [AdminController::class, 'analytics'])->name('analytics.index');
            
            // ── Tenant Customization (Premium only) ───────────────────────────
            Route::get('/customization',                [TenantCustomizationController::class, 'index'])->name('customization.index');
            Route::post('/customization',               [TenantCustomizationController::class, 'update'])->name('customization.update');
            Route::delete('/customization/logo',        [TenantCustomizationController::class, 'deleteLogo'])->name('customization.logo.delete');
            Route::post('/customization/reset',         [TenantCustomizationController::class, 'reset'])->name('customization.reset');  
        });
        
        // ── Plans & Promotions ────────────────────────────────────────
        Route::get('/plan', [AdminPlanController::class, 'index'])->name('plan.index');
        Route::post('/plan/request', [AdminPlanRequestController::class, 'store'])->name('plan.request');

    });

    // ══════════════════════════════════════════════════════════════════
    // COORDINATOR  (Basic = applications, hours; Standard+ = reports,
    //               evaluations)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:ojt_coordinator', '2fa'])
        ->prefix('coordinator')
        ->name('coordinator.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard',                    [CoordinatorController::class, 'dashboard'])->name('dashboard');

        // ── Student active-interns list (existing, unchanged) ──────────────
        Route::get('/students',                     [CoordinatorStudentController::class, 'index'])->name('students.index');

        // ── User account management (students + supervisors) ───────────────
        Route::get('/accounts',                     [CoordinatorStudentController::class, 'accounts'])->name('accounts.index');
        Route::get('/accounts/create',              [CoordinatorStudentController::class, 'create'])->name('accounts.create');
        Route::post('/accounts',                    [CoordinatorStudentController::class, 'store'])->name('accounts.store');
        Route::get('/accounts/{user}/edit',         [CoordinatorStudentController::class, 'edit'])->name('accounts.edit');
        Route::put('/accounts/{user}',              [CoordinatorStudentController::class, 'update'])->name('accounts.update');
        Route::patch('/accounts/{user}/toggle',     [CoordinatorStudentController::class, 'toggleActive'])->name('accounts.toggle');
        Route::delete('/accounts/{user}',           [CoordinatorStudentController::class, 'destroy'])->name('accounts.destroy');

         // ── Company management ─────────────────────────────────────────────
        Route::get('/companies',                    [CoordinatorCompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/create',             [CoordinatorCompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies',                   [CoordinatorCompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit',     [CoordinatorCompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}',          [CoordinatorCompanyController::class, 'update'])->name('companies.update');
        Route::patch('/companies/{company}/toggle', [CoordinatorCompanyController::class, 'toggleActive'])->name('companies.toggle');
        Route::delete('/companies/{company}',       [CoordinatorCompanyController::class, 'destroy'])->name('companies.destroy');


        Route::post('/applications/bulk',                   [CoordinatorApplicationController::class, 'bulk'])->name('applications.bulk');

        Route::get('/applications',                         [CoordinatorApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}',           [CoordinatorApplicationController::class, 'show'])->name('applications.show');
        Route::post('/applications/{application}/approve',  [CoordinatorApplicationController::class, 'approve'])->name('applications.approve');
        Route::post('/applications/{application}/reject',   [CoordinatorApplicationController::class, 'reject'])->name('applications.reject');
        

        // Hour log view — read-only, Basic+
        Route::get('/hours',                                [CoordinatorHourLogController::class, 'index'])->name('hours.index');

        // ── Notifications ─────────────────────────────────────────────
        Route::get('/notifications',                        [TenantNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count',           [TenantNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/mark-all-read',         [TenantNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/clear-read',            [TenantNotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::post('/notifications/{notification}/read',   [TenantNotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::delete('/notifications/{notification}',      [TenantNotificationController::class, 'destroy'])->name('notifications.destroy');

        // ── Profile & password settings ─────────────────────────────────────
        Route::get('/settings',              [CoordinatorSettingsController::class, 'index'])->name('settings');
        Route::patch('/settings/profile',    [CoordinatorSettingsController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password',   [CoordinatorSettingsController::class, 'updatePassword'])->name('settings.update.password');
        Route::post('/settings/avatar',      [CoordinatorSettingsController::class, 'updateAvatar'])->name('settings.avatar');
        Route::delete('/settings/avatar',    [CoordinatorSettingsController::class, 'deleteAvatar'])->name('settings.avatar.delete');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/reports',                                  [CoordinatorReportController::class, 'index'])->name('reports.index');
            Route::post('/reports/{report}/approve',                [CoordinatorReportController::class, 'approve'])->name('reports.approve');
            Route::post('/reports/{report}/return',                 [CoordinatorReportController::class, 'return'])->name('reports.return');

            Route::get('/evaluations',                              [CoordinatorEvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{evaluation}',                 [CoordinatorEvaluationController::class, 'show'])->name('evaluations.show');
            Route::post('/evaluations/{evaluation}/complete',       [CoordinatorEvaluationController::class, 'complete'])->name('evaluations.complete');
        });

        // ── Plan overview ─────────────────────────────────────────────
        Route::get('/plan', [CoordinatorPlanController::class, 'index'])->name('plan.index');

    });

    // ══════════════════════════════════════════════════════════════════
    // SUPERVISOR  (Basic = hour logs; Standard+ = evaluations)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:company_supervisor', '2fa'])
        ->prefix('supervisor')
        ->name('supervisor.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard',                [SupervisorController::class, 'dashboard'])->name('dashboard');
        Route::get('/interns',                  [SupervisorController::class, 'interns'])->name('interns.index');

        // Hour log view — read-only, Basic+
        Route::get('/hours',                                [SupervisorHourLogController::class, 'index'])->name('hours.index');
        Route::get('/hours/{student}',                      [SupervisorHourLogController::class, 'show'])->name('hours.show');
        Route::post('/hours/{hourLog}/approve',             [SupervisorHourLogController::class, 'approve'])->name('hours.approve');
        Route::post('/hours/{hourLog}/reject',              [SupervisorHourLogController::class, 'reject'])->name('hours.reject');
        Route::post('/hours/{student}/approve-all',         [SupervisorHourLogController::class, 'approveAll'])->name('hours.approve-all');

        // ── QR Code management for clock-ins ─────────────────────────────
        Route::prefix('qr')->name('qr.')->group(function () {
            Route::get('/',             [SupervisorQrController::class, 'show'])       ->name('show');
            Route::post('/regenerate',  [SupervisorQrController::class, 'regenerate']) ->name('regenerate');
            Route::post('/toggle',      [SupervisorQrController::class, 'toggle'])     ->name('toggle');
        });

        // ── Profile & password settings ─────────────────────────────────────
        Route::get('/profile/settings',    [SupervisorSettingsController::class, 'edit'])->name('profile.settings');
        Route::patch('/settings/profile',  [SupervisorSettingsController::class, 'updateProfile'])->name('settings.update.profile');
        Route::patch('/settings/password', [SupervisorSettingsController::class, 'updatePassword'])->name('settings.update.password');

        // ── Notifications ─────────────────────────────────────────────
        Route::get('/notifications',                              [TenantNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count',                 [TenantNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/mark-all-read',               [TenantNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/clear-read',                  [TenantNotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::post('/notifications/{notification}/read',         [TenantNotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::delete('/notifications/{notification}',            [TenantNotificationController::class, 'destroy'])->name('notifications.destroy');

        // ── Standard plan and above ─────────────────────────────────────

        Route::middleware('plan:standard')->group(function () {
            Route::get('/evaluations',                      [SupervisorEvaluationController::class, 'index'])->name('evaluations.index');
            Route::get('/evaluations/{application}',        [SupervisorEvaluationController::class, 'create'])->name('evaluations.create');
            Route::post('/evaluations/{application}',       [SupervisorEvaluationController::class, 'store'])->name('evaluations.store');
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // STUDENT  (Basic = application, hours; Standard+ = reports, evals)
    // ══════════════════════════════════════════════════════════════════
    Route::middleware(['auth', 'role:student_intern', '2fa'])
        ->prefix('student')
        ->name('student.')
        ->group(function () {

        // ── Available on ALL plans (Basic+) ────────────────────────────

        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

        Route::get('/application/create',        [StudentApplicationController::class, 'create'])->name('application.create');
        Route::post('/application',              [StudentApplicationController::class, 'store'])->name('application.store');
        Route::get('/application/{application}', [StudentApplicationController::class, 'show'])->name('application.show');

        Route::get('/hours',                 [StudentHourLogController::class, 'index'])->name('hours.index');
        Route::get('/hours/create',          [StudentHourLogController::class, 'create'])->name('hours.create');
        Route::get('/hours/calendar-data',   [StudentHourLogController::class, 'calendarData'])->name('hours.calendar');
        Route::post('/hours',                [StudentHourLogController::class, 'store'])->name('hours.store');
        Route::get('/hours/{hourLog}/edit',  [StudentHourLogController::class, 'edit'])->name('hours.edit');
        Route::patch('/hours/{hourLog}',     [StudentHourLogController::class, 'update'])->name('hours.update');

        Route::get('/settings',                 [StudentSettingsController::class, 'index'])->name('settings');
        Route::patch('/settings/profile',       [StudentSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::patch('/settings/password',      [StudentSettingsController::class, 'updatePassword'])->name('settings.password');
        Route::post('/settings/avatar',   [StudentSettingsController::class, 'updateAvatar'])->name('settings.avatar');
        Route::delete('/settings/avatar', [StudentSettingsController::class, 'deleteAvatar'])->name('settings.avatar.delete');

        // ── Notifications ─────────────────────────────────────────────
        Route::get('/notifications',                              [TenantNotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread-count',                 [TenantNotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
        Route::post('/notifications/mark-all-read',               [TenantNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/clear-read',                  [TenantNotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::post('/notifications/{notification}/read',         [TenantNotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::delete('/notifications/{notification}',            [TenantNotificationController::class, 'destroy'])->name('notifications.destroy');

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