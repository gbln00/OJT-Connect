<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\User;
use App\Mail\HourLogsApproved;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorHourLogController extends Controller
{
    // ── All interns — overview list ───────────────────────────────

    /**
     * Show all hour logs from interns assigned to this supervisor's company.
     * Filterable by student and/or status. Each row links to the per-student view.
     */
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = HourLog::with(['student', 'application'])
            ->whereHas('application', fn($q) =>
                $q->where('company_id', $companyId)->where('status', 'approved')
            )
            ->orderBy('date', 'desc')
            ->orderByRaw("FIELD(session, 'morning', 'afternoon')");

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Sidebar stats
        $pending  = HourLog::whereHas('application', fn($q) =>
                        $q->where('company_id', $companyId)->where('status', 'approved')
                    )->where('status', 'pending')->count();

        $approved = HourLog::whereHas('application', fn($q) =>
                        $q->where('company_id', $companyId)->where('status', 'approved')
                    )->where('status', 'approved')->count();

        // Intern list for the filter dropdown
        $interns = OjtApplication::with('student')
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->get()
            ->map(fn($app) => $app->student)
            ->unique('id');

        return view('supervisor.hours.index', compact(
            'logs', 'pending', 'approved', 'interns'
        ));
    }

    // ── Per-student detail view ───────────────────────────────────

    /**
     * Show all hour logs for one specific intern, grouped by date.
     * Supervisor can approve/reject individual sessions from this view.
     */
    public function show(Request $request, User $student)
    {
        $companyId = Auth::user()->company_id;

        // Verify this intern belongs to this supervisor's company
        $application = OjtApplication::where('student_id', $student->id)
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->with('company')
            ->latest()
            ->firstOrFail();

        $query = HourLog::where('student_id', $student->id)
            ->where('application_id', $application->id)
            ->orderBy('date', 'desc')
            ->orderByRaw("FIELD(session, 'morning', 'afternoon')");

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Group by date for the paired morning/afternoon display
        $groupedByDate = [];
        foreach ($logs as $log) {
            $key = $log->date->format('Y-m-d');
            $groupedByDate[$key][$log->session] = $log;
        }

        $stats = [
            'total_logged'   => HourLog::where('student_id', $student->id)
                                    ->where('application_id', $application->id)
                                    ->sum('total_hours'),
            'total_approved' => HourLog::where('student_id', $student->id)
                                    ->where('application_id', $application->id)
                                    ->where('status', 'approved')
                                    ->sum('total_hours'),
            'pending_count'  => HourLog::where('student_id', $student->id)
                                    ->where('application_id', $application->id)
                                    ->where('status', 'pending')
                                    ->count(),
        ];

        return view('supervisor.hours.show', compact(
            'student', 'application', 'logs', 'groupedByDate', 'stats'
        ));
    }

    // ── Actions ───────────────────────────────────────────────────

    public function approve(HourLog $hourLog)
    {
        $this->authorizeLog($hourLog);

        $hourLog->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', ucfirst($hourLog->session) . ' log approved.');
    }

    public function reject(Request $request, HourLog $hourLog)
    {
        $this->authorizeLog($hourLog);

        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $hourLog->update([
            'status'           => 'rejected',
            'approved_by'      => null,
            'approved_at'      => null,
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', ucfirst($hourLog->session) . ' log rejected.');
    }

    /**
     * Approve all pending logs for one intern in bulk.
     */
    public function approveAll(User $student)
    {
        $companyId = Auth::user()->company_id;

        // Security: intern must belong to this company
        $application = OjtApplication::where('student_id', $student->id)
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->firstOrFail();

        $count = HourLog::where('student_id', $student->id)
            ->where('application_id', $application->id)
            ->where('status', 'pending')
            ->count();

         $pendingLogs = HourLog::where('student_id', $student->id)
                ->where('application_id', $application->id)
                ->where('status', 'pending');

            $count = $pendingLogs->count();


            $pendingLogs->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);


            // Calculate total approved hours for the email
            $totalApproved = HourLog::where('student_id', $student->id)
                ->where('application_id', $application->id)
                ->where('status', 'approved')
                ->sum('total_hours');


            if ($count > 0) {
                Mail::to($student->email)
                    ->send(new HourLogsApproved($student, $count, $totalApproved));
            }


        return back()->with('success', "Approved {$count} pending log(s) for {$student->name}.");
    }

    // ── Private helpers ───────────────────────────────────────────

    private function authorizeLog(HourLog $hourLog): void
    {
        abort_if(
            $hourLog->application->company_id !== Auth::user()->company_id,
            403,
            'You are not authorized to manage this hour log.'
        );
    }
}