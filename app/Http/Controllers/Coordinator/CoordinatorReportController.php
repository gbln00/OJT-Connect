<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class CoordinatorReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyReport::with(['student','application.company'])->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        $reports = $query->paginate(15)->withQueryString();
        $pending = WeeklyReport::where('status','pending')->count();
        return view('coordinator.reports.index', compact('reports','pending'));
    }

    public function approve(Request $request, WeeklyReport $report)
    {
        $request->validate(['feedback'=>['nullable','string','max:1000']]);
        $report->update(['status'=>'approved','feedback'=>$request->feedback,'reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        return back()->with('success', 'Report approved.');
    }

    public function return(Request $request, WeeklyReport $report)
    {
        $request->validate(['feedback'=>['required','string','max:1000']]);
        $report->update(['status'=>'returned','feedback'=>$request->feedback,'reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        return back()->with('success', 'Report returned for revision.');
    }
}
