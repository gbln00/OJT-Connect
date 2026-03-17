<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class WeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyReport::with(['student', 'application.company', 'reviewer'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('week_number')) {
            $query->where('week_number', $request->week_number);
        }

        $reports = $query->paginate(15)->withQueryString();

        $counts = [
            'total'    => WeeklyReport::count(),
            'pending'  => WeeklyReport::where('status', 'pending')->count(),
            'approved' => WeeklyReport::where('status', 'approved')->count(),
            'returned' => WeeklyReport::where('status', 'returned')->count(),
        ];

        $maxWeek = WeeklyReport::max('week_number') ?? 0;

        return view('admin.reports.index', compact('reports', 'counts', 'maxWeek'));
    }

    public function show(WeeklyReport $report)
    {
        $report->load(['student', 'application.company', 'reviewer']);
        return view('admin.reports.show', compact('report'));
    }

    public function approve(Request $request, WeeklyReport $report)
    {
        $request->validate([
            'feedback' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status'      => 'approved',
            'feedback'    => $request->feedback,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Week {$report->week_number} report for {$report->student->name} approved.");
    }

    public function return(Request $request, WeeklyReport $report)
    {
        $request->validate([
            'feedback' => ['required', 'string', 'max:1000'],
        ]);

        $report->update([
            'status'      => 'returned',
            'feedback'    => $request->feedback,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "Week {$report->week_number} report for {$report->student->name} returned.");
    }
}