<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\HourLog;
use Illuminate\Http\Request;

class CoordinatorHourLogController extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = HourLog::with(['student','application.company'])->latest();
    //     if ($request->filled('status')) $query->where('status', $request->status);
    //     $logs = $query->paginate(20)->withQueryString();
    //     $pending = HourLog::where('status','pending')->count();
    //     return view('coordinator.hours.index', compact('logs','pending'));
    // }

    // public function approve(HourLog $hourLog)
    // {
    //     $hourLog->update(['status'=>'approved','approved_by'=>auth()->id(),'approved_at'=>now()]);
    //     return back()->with('success', 'Hour log approved.');
    // }

    // public function reject(HourLog $hourLog)
    // {
    //     $hourLog->update(['status'=>'rejected']);
    //     return back()->with('success', 'Hour log rejected.');
    // }
}
