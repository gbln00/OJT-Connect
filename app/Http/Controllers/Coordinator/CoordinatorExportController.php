<?php

namespace App\Http\Controllers\coordinator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ExportController as BaseExportController;

use Illuminate\Http\Request;

class CoordinatorExportController extends Controller
{
   public function index()
    {

        $stats = [
            'total_students'     => \App\Models\User::where('role', 'student_intern')->count(),
            'total_companies'    => \App\Models\Company::count(),
            'total_applications' => \App\Models\OjtApplication::count(),
            'total_hours'        => \App\Models\HourLog::where('status', 'approved')->sum('total_hours'),
            'total_reports'      => \App\Models\WeeklyReport::count(),
            'total_evaluations'  => \App\Models\Evaluation::count(),
        ];

        return view('coordinator.export.index', compact('stats'));
    }

}
