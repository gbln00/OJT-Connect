<?php
namespace App\Http\Controllers\Supervisor;
use App\Http\Controllers\Controller;
use App\Models\{Evaluation, OjtApplication};

use App\Mail\EvaluationSubmitted;
use Illuminate\Support\Facades\Mail;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class SupervisorEvaluationController extends Controller
{
    public function create(OjtApplication $application)
    {
        // Make sure this intern belongs to this supervisor's company
        abort_if($application->company_id !== Auth::user()->company_id, 403);
        $existing = Evaluation::where('application_id', $application->id)->first();
        if ($existing) return redirect()->route('supervisor.dashboard')
            ->with('info', 'Evaluation already submitted for this intern.');
        return view('supervisor.evaluations.create', compact('application'));
    }

    public function store(Request $request, OjtApplication $application)
    {
        abort_if($application->company_id !== Auth::user()->company_id, 403);
        $request->validate([
            'attendance_rating'  => ['required','integer','min:1','max:5'],
            'performance_rating' => ['required','integer','min:1','max:5'],
            'overall_grade'      => ['required','numeric','min:0','max:100'],
            'recommendation'     => ['required','in:pass,fail'],
            'remarks'            => ['nullable','string','max:2000'],
        ]);
        
        $evaluation = Evaluation::create([
            'student_id'         => $application->student_id,
            'application_id'     => $application->id,
            'supervisor_id'      => Auth::id(),
            'attendance_rating'  => $request->attendance_rating,
            'performance_rating' => $request->performance_rating,
            'overall_grade'      => $request->overall_grade,
            'recommendation'     => $request->recommendation,
            'remarks'            => $request->remarks,
            'submitted_at'       => now(),
        ]);

        // Send email to student
        $evaluation->load(['student', 'application.company']);
        Mail::to($evaluation->student->email)->send(new EvaluationSubmitted($evaluation));

        // Notify the coordinator
        $coordinator = \App\Models\User::where('role', 'ojt_coordinator')->first();
        if ($coordinator) {
            Mail::to($coordinator->email)->send(new EvaluationSubmitted($evaluation));
        }


        return redirect()->route('supervisor.dashboard')
            ->with('success', 'Evaluation submitted successfully.');
    }

    
    public function index()
    {
        $companyId   = Auth::user()->company_id;
        $applications = OjtApplication::with(['student', 'evaluation'])
            ->where('company_id', $companyId)
            ->where('status', 'approved')
            ->latest()
            ->get();
        return view('supervisor.evaluations.index', compact('applications'));
    }
}
