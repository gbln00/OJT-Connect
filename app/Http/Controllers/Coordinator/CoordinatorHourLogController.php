<?php
namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use Illuminate\Http\Request;

class CoordinatorHourLogController extends Controller
{
    /**
     * Show all hour logs from all interns in this tenant.
     * Coordinators can view but approval belongs to supervisors.
     * Admin-level approval is also available here as a fallback.
     */
    public function index(Request $request)
    {
        $query = HourLog::with(['student', 'application.company'])
            ->orderBy('date', 'desc')
            ->orderByRaw("FIELD(session, 'morning', 'afternoon')");

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $logs    = $query->paginate(20)->withQueryString();
        $pending = HourLog::where('status', 'pending')->count();

        // Intern list for the filter dropdown (approved applications only)
        $interns = OjtApplication::with('student')
            ->where('status', 'approved')
            ->get()
            ->map(fn($app) => $app->student)
            ->unique('id');

        return view('coordinator.hours.index', compact('logs', 'pending', 'interns'));
    }

    /**
     * Approve a single hour log (coordinator-level override).
     */
    public function approve(HourLog $hourLog)
    {
        $hourLog->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Hour log approved.');
    }

    /**
     * Reject a single hour log.
     */
    public function reject(HourLog $hourLog)
    {
        $hourLog->update([
            'status'      => 'rejected',
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return back()->with('success', 'Hour log rejected.');
    }
}