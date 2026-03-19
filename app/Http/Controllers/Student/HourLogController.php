<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HourLogController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->with('company')->first();
        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need an approved application to log hours.');
        }
        $logs = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->orderBy('date', 'desc')->paginate(15);
        $totalApproved = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'approved')->sum('total_hours');
        return view('student.hours.index', compact('application','logs','totalApproved'));
    }

    public function create()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.hours.index');
        }
        return view('student.hours.create', compact('application'));
    }

    public function store(Request $request)
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();
        $request->validate([
            'date'        => ['required','date','before_or_equal:today'],
            'time_in'     => ['required','date_format:H:i'],
            'time_out'    => ['required','date_format:H:i','after:time_in'],
            'description' => ['nullable','string','max:500'],
        ]);
        // Calculate total hours
        $in    = \Carbon\Carbon::createFromFormat('H:i', $request->time_in);
        $out   = \Carbon\Carbon::createFromFormat('H:i', $request->time_out);
        $hours = round($in->diffInMinutes($out) / 60, 2);
        HourLog::create([
            'student_id'     => $user->id,
            'application_id' => $application->id,
            'date'           => $request->date,
            'time_in'        => $request->time_in,
            'time_out'       => $request->time_out,
            'total_hours'    => $hours,
            'description'    => $request->description,
            'status'         => 'pending',
        ]);
        return redirect()->route('student.hours.index')
            ->with('success', 'Hour log submitted successfully.');
    }
}