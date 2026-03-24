<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupervisorHourLogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;

        $query = HourLog::with(['student','application.company'])
            ->whereHas('application', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(15)->withQueryString();

        $pending = HourLog::where('status', 'pending')
            ->whereHas('application', fn($q) => $q->where('company_id', $companyId))
            ->count();

        return view('supervisor.hours.index', compact('logs', 'pending'));
    }

    public function approve(HourLog $hourLog)
    {
        $this->authorizeLog($hourLog);

        $hourLog->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Hour log approved.');
    }

    public function reject(HourLog $hourLog)
    {
        $this->authorizeLog($hourLog);

        $hourLog->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Hour log rejected.');
    }

    private function authorizeLog($hourLog)
    {
        $companyId = Auth::user()->company_id;

        abort_if(
            $hourLog->application->company_id !== $companyId,
            403,
            'Unauthorized action.'
        );
    }
}