<?php
namespace App\Http\Controllers\Coordinator;
use App\Http\Controllers\Controller;
use App\Models\{OjtApplication, Company};
use Illuminate\Http\Request;

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
        $request->validate(['remarks' => ['nullable','string','max:1000']]);
        $application->update(['status'=>'approved','reviewed_by'=>auth()->id(),'reviewed_at'=>now(),'remarks'=>$request->remarks]);
        // Send approval email
        Mail::to($application->student->email)->send(new ApplicationApproved($application));
        return back()->with('success', $application->student->name.' has been approved.');
    }

    public function reject(Request $request, OjtApplication $application)
    {
        $request->validate(['remarks' => ['required','string','max:1000']]);
        $application->update(['status'=>'rejected','reviewed_by'=>auth()->id(),'reviewed_at'=>now(),'remarks'=>$request->remarks]);
        // Send rejection email
        Mail::to($application->student->email)->send(new ApplicationRejected($application));
        return back()->with('success', $application->student->name.' has been rejected.');
    }

    public function show(OjtApplication $application)
    {
        $application->load(['student', 'company', 'reviewer']);
        return view('coordinator.applications.show', compact('application'));
    }
}
