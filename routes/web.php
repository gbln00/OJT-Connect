<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\HoursController;
use App\Http\Controllers\Admin\WeeklyReportController;
use App\Http\Controllers\Admin\EvaluationController;
use App\Http\Controllers\Admin\ExportController;

use App\Http\Controllers\Student\StudentApplicationController;
use App\Http\Controllers\Student\StudentController;

Route::get('/', function () {
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
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Settings
    Route::get('/settings',           [AdminController::class, 'settings'])->name('settings');
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
Route::middleware(['auth', 'role:student_intern'])->prefix('student')->name('student.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');

    // OJT Application
    Route::get('/application/create',        [StudentApplicationController::class, 'create'])->name('application.create');
    Route::post('/application',              [StudentApplicationController::class, 'store'])->name('application.store');
    Route::get('/application/{application}', [StudentApplicationController::class, 'show'])->name('application.show');

    // Placeholder routes — prevents sidebar from crashing before modules are built
    Route::get('/hours',      fn() => abort(404))->name('hours.index');
    Route::get('/reports',    fn() => abort(404))->name('reports.index');
    Route::get('/evaluation', fn() => abort(404))->name('evaluation.show');
    Route::get('/settings',   fn() => abort(404))->name('settings');

});