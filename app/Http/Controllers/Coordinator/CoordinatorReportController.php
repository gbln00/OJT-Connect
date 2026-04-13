<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Mail\ReportReturned;
use App\Models\TenantNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;

class CoordinatorReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyReport::with([
            'student',
            'application.company'
            ])->latest();
        
        if ($request->filled('status')) 
            $query->where
            ('status', $request->status);

        $reports = $query->paginate(15)
            ->withQueryString();

        $pending = WeeklyReport::where(
            'status',
            'pending'
            )->count();

        return view('coordinator.reports.index', compact('reports','pending'));
    }

    public function approve(Request $request, WeeklyReport $report)
    {
        $request->validate([
            'feedback'=>['nullable','string','max:1000']
        ]);
        
        $report->update([
            'status'=>'approved',
            'feedback'=>$request->feedback,
            'reviewed_by'=>auth()->id(),
            'reviewed_at'=>now()
        ]);

        TenantNotification::notify(
            title:      'Weekly Report Approved',
            message:    "Your Week {$report->week_number} report has been approved." .
                        ($request->feedback ? " Feedback: {$request->feedback}" : ''),
            type:       'success',
            targetRole: 'student_intern',
            userId:     $report->student_id
        );

        return back()->with('success', 'Report approved.');
    }

    public function return(Request $request, WeeklyReport $report)
    {
        $request->validate([
            'feedback'=>['required','string','max:1000']
        ]);

        $report->update([
            'status'=>'returned',
            'feedback'=>$request->feedback,
            'reviewed_by'=>auth()->id(),
            'reviewed_at'=>now()
        ]);

        TenantNotification::notify(
            title:      'Weekly Report Returned',
            message:    "Your Week {$report->week_number} report was returned for revision. Feedback: {$request->feedback}",
            type:       'warning',
            targetRole: 'student_intern',
            userId:     $report->student_id
        );
        
        $report->load('student');
          try {
            Mail::to($report->student->email)
                ->send(new ReportReturned($report));

            \Log::info(
                'ReportReturned email sent to: ' 
                    . $report->student->email);

        } catch (\Exception $e) {
            \Log::error('ReportReturned email failed: ' 
                . $e->getMessage());
        }
        
        return back()->with('success', 'Report returned for revision.');
    }
}
