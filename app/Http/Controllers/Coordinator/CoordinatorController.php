<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\{OjtApplication, HourLog, WeeklyReport, User};

class CoordinatorController extends Controller
{
    public function dashboard()
    {
        $pendingApplications = OjtApplication::where('status','pending')->count();
        $activeInterns       = OjtApplication::where('status','approved')->count();
        $pendingLogs         = HourLog::where('status','pending')->count();
        $pendingReports      = WeeklyReport::where('status','pending')->count();
        $recentApplications  = OjtApplication::with(['student','company'])
            ->where('status','pending')->latest()->take(5)->get();
        return view('coordinator.dashboard', compact(
            'pendingApplications','activeInterns',
            'pendingLogs','pendingReports','recentApplications'
        ));
    }
}
