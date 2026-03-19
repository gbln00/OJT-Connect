<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Admin controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\HoursController;
use App\Http\Controllers\Admin\WeeklyReportController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ExportController;

//student controllers
use App\Http\Controllers\Student\StudentApplicationController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\HourLogController;
use App\Http\Controllers\Student\StudentWeeklyReportController;
use App\Http\Controllers\Student\StudentEvaluationController;
use App\Http\Controllers\Student\StudentSettingsController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();

        return match($user->role) {
            'admin'              => redirect()->route('admin.dashboard'),
            'ojt_coordinator'    => redirect()->route('coordinator.dashboard'),
            'company_supervisor' => redirect()->route('supervisor.dashboard'),
            'student_intern'     => redirect()->route('student.dashboard'),
            default              => abort(403, 'Invalid role: ' . $user->role),
        };
    }

    return view('welcome');
});

// ── Guest-only routes ─────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',                  [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
    Route::post('/login',                 [LoginController::class, 'login']);
    Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Settings
    Route::get('/settings',            [AdminController::class, 'settings'])->name('settings');
    Route::patch('/settings/profile',  [AdminController::class, 'updateProfile'])->name('settings.update.profile');
    Route::patch('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.update.password');

    // User management (admin creates ALL user types including students)
    Route::get('/users',                 [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create',          [UserController::class, 'create'])->name('users.create');
    Route::post('/users',                [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',          [UserController::class, 'update'])->name('users.update');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('users.destroy');

    // Company management (admin manages all companies)
    Route::get('/companies',                    [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create',             [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies',                   [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{company}/edit',     [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{company}',          [CompanyController::class, 'update'])->name('companies.update');
    Route::patch('/companies/{company}/toggle', [CompanyController::class, 'toggleActive'])->name('companies.toggle');
    Route::delete('/companies/{company}',       [CompanyController::class, 'destroy'])->name('companies.destroy');

    // Applications — admin view only, no approve/reject
    Route::get('/applications',               [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::delete('/applications/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::post('/applications/{application}/approve', [ApplicationController::class, 'approve'])->name('applications.approve');
    Route::post('/applications/{application}/reject',  [ApplicationController::class, 'reject'])->name('applications.reject');
    
    // Hours monitoring — admin view only
    Route::get('/hours',           [HoursController::class, 'index'])->name('hours.index');
    Route::get('/hours/{student}', [HoursController::class, 'show'])->name('hours.show');

    // Weekly reports — admin view only
    Route::get('/reports',          [WeeklyReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [WeeklyReportController::class, 'show'])->name('reports.show');

    // Evaluations — admin view only
    Route::get('/evaluations',              [EvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/{evaluation}', [EvaluationController::class, 'show'])->name('evaluations.show');

    // Exports
    Route::get('/exports',                 [ExportController::class, 'index'])->name('export.index');
    Route::get('/exports/pdf/students',    [ExportController::class, 'pdfStudents'])->name('export.pdf.students');
    Route::get('/exports/pdf/evaluations', [ExportController::class, 'pdfEvaluations'])->name('export.pdf.evaluations');
    Route::get('/exports/excel',           [ExportController::class, 'excelFull'])->name('export.excel');
});

// ── COORDINATOR ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:ojt_coordinator'])
    ->prefix('coordinator')
    ->name('coordinator.')
    ->group(function () {
    
    Route::get('/dashboard', fn() => view('coordinator.dashboard'))->name('dashboard');

    // Uncomment when CoordinatorApplicationController is created:
    // Route::get('/applications', [CoordinatorApplicationController::class, 'index'])->name('applications.index');
    // Route::post('/applications/{application}/approve', [CoordinatorApplicationController::class, 'approve'])->name('applications.approve');
    // Route::post('/applications/{application}/reject',  [CoordinatorApplicationController::class, 'reject'])->name('applications.reject');
});

// ── SUPERVISOR ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:company_supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        
    Route::get('/dashboard', fn() => view('supervisor.dashboard'))->name('dashboard');
});

// ── STUDENT ───────────────────────────────────────────────────────

Route::middleware(['auth', 'role:student_intern'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

    // Dashboard
    Route::get('/dashboard',    [StudentController::class, 'dashboard'])->name('dashboard');

    // OJT Application
    Route::get('/application/create',           [StudentApplicationController::class, 'create'])->name('application.create');
    Route::post('/application',                 [StudentApplicationController::class, 'store'])->name('application.store');
    Route::get('/application/{application}',    [StudentApplicationController::class, 'show'])->name('application.show');

    // Hour logs
    Route::get('/hours',                [HourLogController::class, 'index'])->name('hours.index');
    Route::get('/hours/create',         [HourLogController::class, 'create'])->name('hours.create');
    Route::post('/hours',               [HourLogController::class, 'store'])->name('hours.store');

    // Reports
    Route::get('/reports',            [StudentWeeklyReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create',     [StudentWeeklyReportController::class, 'create'])->name('reports.create');
    Route::post('/reports',           [StudentWeeklyReportController::class, 'store'])->name('reports.store');

    // Evaluations
    Route::get('/evaluation', [StudentEvaluationController::class, 'show'])->name('evaluation.show');
    
    
    // Others
    Route::get('/settings',          [StudentSettingsController::class, 'index'])->name('settings');
    Route::patch('/settings/profile',  [StudentSettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [StudentSettingsController::class, 'updatePassword'])->name('settings.password');

});