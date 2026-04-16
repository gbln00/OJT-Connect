<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\User;
use Illuminate\Http\Request;

class CoordinatorHourLogController extends Controller
{
    /**
     * Show all APPROVED hour logs from all interns in this tenant.
     * Coordinators can VIEW approved logs only — approval/rejection
     * belongs to supervisors (and admins as fallback).
     *
     * The $pending variable is kept for the sidebar badge on the old
     * view but is passed as 0 here since coordinator no longer approves.
     */
    public function index(Request $request)
    {
        $query = HourLog::with(['student', 'application.company'])
            ->where('status', 'approved')   
            ->orderBy('date', 'desc')
            ->orderByRaw("FIELD(session, 'morning', 'afternoon')");

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('company_id')) {
            $query->whereHas('application', fn($q) =>
                $q->where('company_id', $request->company_id)
            );
        }

        $logs = $query->paginate(20)->withQueryString();

        // Sidebar info: total approved hours across all interns
        $totalApprovedHours = HourLog::where('status', 'approved')->sum('total_hours');

        // Intern list for filter dropdown
        $interns = OjtApplication::with('student')
            ->where('status', 'approved')
            ->get()
            ->map(fn($app) => $app->student)
            ->unique('id');

        // Company list for filter dropdown
        $companies = \App\Models\Company::where('is_active', true)->orderBy('name')->get();

        // Pass 0 — coordinator no longer has approve action
        $pending = 0;

        return view('coordinator.hours.index', compact(
            'logs', 'pending', 'interns', 'companies', 'totalApprovedHours'
        ));
    }
}