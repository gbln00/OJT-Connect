<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\{OjtApplication, Company};
use Illuminate\Http\Request;
use App\Models\TenantNotification;
use App\Mail\ApplicationApproved;
use App\Mail\ApplicationRejected;
use Illuminate\Support\Facades\Mail;



class CoordinatorApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = OjtApplication::with(['student','company','reviewer'])->latest();
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) => $q->where('name','like','%'.$request->search.'%'));
        }
        $applications = $query->paginate(15)->withQueryString();
        $counts = ['total'=>OjtApplication::count(),'pending'=>OjtApplication::where('status','pending')->count(),
                   'approved'=>OjtApplication::where('status','approved')->count(),'rejected'=>OjtApplication::where('status','rejected')->count()];
        return view('coordinator.applications.index', compact('applications','counts'));
    }

    public function approve(Request $request, OjtApplication $application)
    {
        $request->validate([
            'remarks' => ['nullable','string','max:1000']
        ]);

        $application->update([
            'status'=>'approved',
            'reviewed_by'=>auth()->id(),
            'reviewed_at'=>now(),
            'remarks'=>$request->remarks
        ]);

         TenantNotification::notify(
            title:      'Application Approved',
            message:    "Your OJT application for {$application->company->name} has been approved.",
            type:       'success',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );
        
        // Send approval email
        Mail::to($application->student->email)->send(new ApplicationApproved($application));
            return back()->with('success', $application->student->name.' has been approved.');
        }

    public function reject(Request $request, OjtApplication $application)
    {
        $request->validate([
            'remarks' => ['required','string','max:1000']
        ]);
        $application->update([
            'status'=>'rejected',
            'reviewed_by'=>auth()->id(),
            'reviewed_at'=>now(),
            'remarks'=>$request->remarks
        ]);

        TenantNotification::notify(
            title:      'Application Rejected',
            message:    "Your OJT application for {$application->company->name} was rejected. Remarks: {$request->remarks}",
            type:       'error',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        // Send rejection email
        Mail::to($application->student->email)->send(new ApplicationRejected($application));
        return back()->with('success', $application->student->name.' has been rejected.');
    }

    public function show(OjtApplication $application)
    {
        $application->load(['student', 'company', 'reviewer']);
        return view('coordinator.applications.show', compact('application'));
    }

    public function bulk(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'action' => 'required|in:approve,reject']);

        OjtApplication::whereIn('id', $request->ids)
            ->each(fn($app) => $this->{$request->action}(new Request, $app));

        return back()->with('success', 'Applications updated.');
    }
}
