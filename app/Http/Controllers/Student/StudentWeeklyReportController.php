<?php
namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentWeeklyReportController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        
        $application = $user->activeApplication()->first();
        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need an approved application to submit reports.');
                // dd([
                //     'application'  => $application,
                //     'status'       => $application?->status,
                //     'isApproved'   => $application?->isApproved(),
                // ]);
        }
        $reports = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->orderBy('week_number', 'desc')->paginate(10);
        return view('student.reports.index', compact('application','reports'));

        $rejectedReports = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'rejected')
            ->count();
        return view('student.reports.index', compact('application','reports','rejectedReports'));

         
    }

    public function create()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.reports.index');
        }
        $nextWeek = WeeklyReport::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->max('week_number') + 1;
        return view('student.reports.create', compact('application','nextWeek'));
    }

    public function store(Request $request)
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        $request->validate([
            'week_number'  => ['required','integer','min:1'],
            'week_start'   => ['required','date'],
            'week_end'     => ['required','date','after:week_start'],
            'description'  => ['required','string','min:20'],
            'file'         => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:5120'],
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store('weekly-reports/' . $user->id, 'public');
        }
        WeeklyReport::create([
            'student_id'     => $user->id,
            'application_id' => $application->id,
            'week_number'    => $request->week_number,
            'week_start'     => $request->week_start,
            'week_end'       => $request->week_end,
            'description'    => $request->description,
            'file_path'      => $filePath,
            'status'         => 'pending',
        ]);
        return redirect()->route('student.reports.index')
            ->with('success', 'Weekly report submitted successfully.');
    }
}
