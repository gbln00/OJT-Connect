<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ExportController as AdminExportController;
use App\Models\{User, Company, OjtApplication, HourLog, WeeklyReport, Evaluation};
use Illuminate\Http\Request;

class CoordinatorExportController extends Controller
{
    // ── Index: show the coordinator-branded export page ──────────────────
    public function index()
    {
        $stats = [
            'total_students'     => User::where('role', 'student_intern')->count(),
            'total_companies'    => Company::count(),
            'total_applications' => OjtApplication::count(),
            'total_hours'        => HourLog::where('status', 'approved')->sum('total_hours'),
            'total_reports'      => WeeklyReport::count(),
            'total_evaluations'  => Evaluation::count(),
        ];

        return view('coordinator.export.index', compact('stats'));
        // ↑ was calling the parent which returned view('admin.exports.index')
    }

    // ── Delegate all download actions to the shared Admin ExportController ──

    public function pdfStudents()
    {
        return app(AdminExportController::class)->pdfStudents();
    }

    public function pdfEvaluations()
    {
        return app(AdminExportController::class)->pdfEvaluations();
    }

    public function excelFull()
    {
        return app(AdminExportController::class)->excelFull();
    }

    public function certificate(\App\Models\OjtApplication $application)
    {
        return app(AdminExportController::class)->certificate($application);
    }
}