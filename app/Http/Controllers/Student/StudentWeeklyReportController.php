<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use App\Models\OjtApplication;
use App\Models\TenantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentWeeklyReportController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();

        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need an approved application to submit reports.');
        }

        $reports = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->orderBy('week_number', 'desc')
            ->paginate(10);

        // ✅ These must all be BEFORE the return
        $totalReports    = $reports->total();
        $approvedReports = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'approved')->count();
        $pendingReports  = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'pending')->count();
        $rejectedReports = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'rejected')->count();

        return view('student.reports.index', compact(
            'application',
            'reports',
            'totalReports',
            'approvedReports',
            'pendingReports',
            'rejectedReports'
        ));
    }

    public function create()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.reports.index');
        }
        $nextWeek = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->max('week_number') + 1;
        return view('student.reports.create', compact('application','nextWeek'));
    }

    public function store(Request $request)
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        $request->validate([
            'week_number'  => ['required','integer','min:1'],
            'week_start'   => ['required','date'],
            'week_end'     => ['required','date','after:week_start'],
            'description'  => ['required','string','min:20'],
            'file'         => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store('weekly-reports/' . $user->id, 'public');
        }
        WeeklyReport::create([
            'student_id'     => $user->id,
            'application_id' => $application->id,
            'week_number'    => $request->week_number,
            'week_start'     => $request->week_start,
            'week_end'       => $request->week_end,
            'description'    => $request->description,
            'file_path'      => $filePath,
            'status'         => 'pending',
        ]);

        TenantNotification::notify(
            title:      'New Weekly Report Submitted',
            message:    auth()->user()->name . " submitted Week {$request->week_number} report.",
            type:       'info',
            targetRole: 'ojt_coordinator'
        );

        TenantNotification::notify(
            title:      'New Weekly Report Submitted',
            message:    auth()->user()->name . " submitted Week {$request->week_number} report.",
            type:       'info',
            targetRole: 'admin'
        );

        return redirect()->route('student.reports.index')
            ->with('success', 'Weekly report submitted successfully.');
    }

    public function edit(WeeklyReport $report)
    {
        $user = Auth::user();

        // امنیت: ensure ownership
        if ($report->student_id !== $user->id) {
            abort(403);
        }

        // Only allow editing if rejected
        if ($report->status !== 'rejected') {
            return redirect()->route('student.reports.index')
                ->with('error', 'You can only edit rejected reports.');
        }

        return view('student.reports.edit', compact('report'));
    }

    public function update(Request $request, WeeklyReport $report)
    {
        $user = Auth::user();

        if ($report->student_id !== $user->id) {
            abort(403);
        }

        if ($report->status !== 'rejected') {
            return redirect()->route('student.reports.index')
                ->with('error', 'You can only edit rejected reports.');
        }

        $request->validate([
            'week_number'  => ['required','integer','min:1'],
            'week_start'   => ['required','date'],
            'week_end'     => ['required','date','after:week_start'],
            'description'  => ['required','string','min:20'],
            'file'         => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);

        // Handle file update
        if ($request->hasFile('file')) {
            // delete old file
            if ($report->file_path) {
                Storage::disk('public')->delete($report->file_path);
            }

            $filePath = $request->file('file')
                ->store('weekly-reports/' . $user->id, 'public');

            $report->file_path = $filePath;
        }

        $report->update([
            'week_number' => $request->week_number,
            'week_start'  => $request->week_start,
            'week_end'    => $request->week_end,
            'description' => $request->description,
            'status'      => 'pending', // 🔥 resubmitted
        ]);

        return redirect()->route('student.reports.index')
            ->with('success', 'Report updated and resubmitted.');
    }

    public function show(WeeklyReport $report)
    {
        $user = Auth::user();

        if ($report->student_id !== $user->id) {
            abort(403);
        }

        return view('student.reports.show', compact('report'));
    }
}
