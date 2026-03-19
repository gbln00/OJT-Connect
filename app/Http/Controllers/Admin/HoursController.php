<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\User;
use Illuminate\Http\Request;

class HoursController extends Controller
{
    // Overview — all students with progress bars
    public function index(Request $request)
    {
        $query = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved')
            ->withCount('hourLogs')
            ->withSum('hourLogs', 'total_hours');

        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total_students'  => OjtApplication::where('status', 'approved')->count(),
            'total_logs'      => HourLog::count(),
            'total_hours'     => HourLog::sum('total_hours'),
            'pending_logs'    => HourLog::where('status', 'pending')->count(),
        ];

        $companies = \App\Models\Company::where('is_active', true)->orderBy('name')->get();

        return view('admin.hours.index', compact('applications', 'stats', 'companies'));
    }

    // Detail — all logs for one student
    public function show(Request $request, User $student)
    {
        $application = OjtApplication::where('student_id', $student->id)
            ->where('status', 'approved')
            ->with('company')
            ->latest()
            ->firstOrFail();

        $query = HourLog::where('student_id', $student->id)
            ->where('application_id', $application->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();

        $totalLogged   = HourLog::where('student_id', $student->id)
                                ->where('application_id', $application->id)
                                ->sum('total_hours');
        $totalApproved = HourLog::where('student_id', $student->id)
                                ->where('application_id', $application->id)
                                ->where('status', 'approved')
                                ->sum('total_hours');

        return view('admin.hours.show', compact(
            'student', 'application', 'logs', 'totalLogged', 'totalApproved'
        ));
    }

    // Approve a single log entry
    public function approve(HourLog $hourLog)
    {
        $hourLog->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(), 
            'approved_at' => now(),          
        ]);
        return back()->with('success', 'Hour log approved.');
    }

    // Approve all pending logs for a student
    public function approveAll(Request $request, User $student)
    {
        HourLog::where('student_id', $student->id)
               ->where('status', 'pending')
               ->update(['status' => 'approved']);

        return back()->with('success', 'All pending logs approved for ' . $student->name . '.');
    }
}