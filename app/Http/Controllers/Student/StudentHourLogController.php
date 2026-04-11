<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\TenantNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentHourLogController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->with('company')->first();

        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.dashboard')
                ->with('error', 'You need an approved application to log hours.');
        }

        // Group logs by date so the view can show morning + afternoon together
        $logs = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->orderBy('date', 'desc')
            ->orderByRaw("FIELD(session, 'morning', 'afternoon')")
            ->paginate(30);

        $totalApproved = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->where('status', 'approved')
            ->sum('total_hours');

        // Group for display: date => ['morning' => log|null, 'afternoon' => log|null]
        $groupedByDate = [];
        foreach ($logs as $log) {
            $key = $log->date->format('Y-m-d');
            $groupedByDate[$key][$log->session] = $log;
        }

        return view('student.hours.index', compact(
            'application', 'logs', 'groupedByDate', 'totalApproved'
        ));
    }

    public function create()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();

        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.hours.index');
        }

        // Warn if today's logs already exist
        $todayLogs = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->whereDate('date', today())
            ->pluck('session')
            ->toArray();

        return view('student.hours.create', compact('application', 'todayLogs'));
    }

    /**
     * Store one daily log entry (up to 2 sessions: morning and/or afternoon).
     * The form sends:
     *   date
     *   am_time_in, am_time_out, am_description   (optional if not logging AM)
     *   pm_time_in, pm_time_out, pm_description   (optional if not logging PM)
     *   log_morning (checkbox), log_afternoon (checkbox)
     */
    public function store(Request $request)
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();

        if (!$application || !$application->isApproved()) {
            return redirect()->route('student.hours.index')
                ->with('error', 'You need an approved application to log hours.');
        }

        $request->validate([
            'date'            => ['required', 'date', 'before_or_equal:today'],
            'log_morning'     => ['nullable', 'boolean'],
            'log_afternoon'   => ['nullable', 'boolean'],
            // Morning fields — required only when logging morning
            'am_time_in'      => ['required_if:log_morning,1', 'nullable', 'date_format:H:i'],
            'am_time_out'     => ['required_if:log_morning,1', 'nullable', 'date_format:H:i', 'after:am_time_in'],
            'am_description'  => ['nullable', 'string', 'max:500'],
            // Afternoon fields — required only when logging afternoon
            'pm_time_in'      => ['required_if:log_afternoon,1', 'nullable', 'date_format:H:i'],
            'pm_time_out'     => ['required_if:log_afternoon,1', 'nullable', 'date_format:H:i', 'after:pm_time_in'],
            'pm_description'  => ['nullable', 'string', 'max:500'],
        ], [
            'am_time_out.after'  => 'Morning time-out must be after time-in.',
            'pm_time_out.after'  => 'Afternoon time-out must be after time-in.',
        ]);

        $loggedMorning   = $request->boolean('log_morning');
        $loggedAfternoon = $request->boolean('log_afternoon');

        if (!$loggedMorning && !$loggedAfternoon) {
            return back()->withErrors(['log_morning' => 'Please select at least one session (morning or afternoon).'])
                         ->withInput();
        }

        $created = 0;

        DB::transaction(function () use ($request, $user, $application, $loggedMorning, $loggedAfternoon, &$created) {

            if ($loggedMorning) {
                // Prevent duplicate morning log for the same date
                $exists = HourLog::where('student_id', $user->id)
                    ->where('application_id', $application->id)
                    ->whereDate('date', $request->date)
                    ->where('session', 'morning')
                    ->exists();

                if (!$exists) {
                    $in    = Carbon::createFromFormat('H:i', $request->am_time_in);
                    $out   = Carbon::createFromFormat('H:i', $request->am_time_out);
                    $hours = round($in->diffInMinutes($out) / 60, 2);

                    HourLog::create([
                        'student_id'     => $user->id,
                        'application_id' => $application->id,
                        'date'           => $request->date,
                        'session'        => 'morning',
                        'time_in'        => $request->am_time_in,
                        'time_out'       => $request->am_time_out,
                        'total_hours'    => $hours,
                        'description'    => $request->am_description,
                        'status'         => 'pending',
                    ]);
                    $created++;
                }
            }

            if ($loggedAfternoon) {
                $exists = HourLog::where('student_id', $user->id)
                    ->where('application_id', $application->id)
                    ->whereDate('date', $request->date)
                    ->where('session', 'afternoon')
                    ->exists();

                if (!$exists) {
                    $in    = Carbon::createFromFormat('H:i', $request->pm_time_in);
                    $out   = Carbon::createFromFormat('H:i', $request->pm_time_out);
                    $hours = round($in->diffInMinutes($out) / 60, 2);

                    HourLog::create([
                        'student_id'     => $user->id,
                        'application_id' => $application->id,
                        'date'           => $request->date,
                        'session'        => 'afternoon',
                        'time_in'        => $request->pm_time_in,
                        'time_out'       => $request->pm_time_out,
                        'total_hours'    => $hours,
                        'description'    => $request->pm_description,
                        'status'         => 'pending',
                    ]);
                    $created++;
                }
            }
        });

        if ($created > 0) {
            TenantNotification::notify(
                title:      'New Hour Log Submitted',
                message:    auth()->user()->name . " submitted {$created} hour log(s) for " . $request->date . '.',
                type:       'info',
                targetRole: 'company_supervisor'
            );

            // Also notify admin and coordinator
            TenantNotification::notify(
                title:      'New Hour Log Submitted',
                message:    auth()->user()->name . " submitted {$created} hour log(s) for " . $request->date . '.',
                type:       'info',
                targetRole: 'ojt_coordinator'
            );
        }

        if ($created === 0) {
            return back()->with('info', 'Hour logs for the selected session(s) already exist for this date.');
        }

        return redirect()->route('student.hours.index')
            ->with('success', $created === 2
                ? 'Morning and afternoon hour logs submitted successfully.'
                : 'Hour log submitted successfully.');
    }

    // Edit and update are only allowed for rejected logs, to let students fix issues and resubmit.
    public function edit(HourLog $hourLog)
    {
        $user = Auth::user();

        if ($hourLog->student_id !== $user->id) {
            abort(403);
        }

        if (!$hourLog->isRejected()) {
            return redirect()->route('student.hours.index')
                ->with('error', 'You can only edit rejected hour logs.');
        }

        $application = $user->activeApplication()->first();

        return view('student.hours.edit', compact('hourLog', 'application'));
    }
    
    // Update the log after editing. Only allowed if the log is currently rejected.
    public function update(Request $request, HourLog $hourLog)
    {
        $user = Auth::user();

        if ($hourLog->student_id !== $user->id) {
            abort(403);
        }

        if (!$hourLog->isRejected()) {
            return redirect()->route('student.hours.index')
                ->with('error', 'You can only edit rejected hour logs.');
        }

        $request->validate([
            'time_in'     => ['required', 'date_format:H:i'],
            'time_out'    => ['required', 'date_format:H:i', 'after:time_in'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $in    = \Carbon\Carbon::createFromFormat('H:i', $request->time_in);
        $out   = \Carbon\Carbon::createFromFormat('H:i', $request->time_out);
        $hours = round($in->diffInMinutes($out) / 60, 2);

        $hourLog->update([
            'time_in'          => $request->time_in,
            'time_out'         => $request->time_out,
            'total_hours'      => $hours,
            'description'      => $request->description,
            'status'           => 'pending',
            'rejection_reason' => null,
            'approved_by'      => null,
            'approved_at'      => null,
        ]);

        return redirect()->route('student.hours.index')
            ->with('success', 'Hour log resubmitted successfully.');
    }

    public function calendarData(Request $request)
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();

        if (!$application) {
            return response()->json([]);
        }

        $logs = HourLog::where('student_id', $user->id)
            ->where('application_id', $application->id)
            ->get(['id', 'date', 'session', 'status', 'total_hours']);

        $events = $logs->map(function ($log) {
            return [
                'id'    => $log->id,
                'title' => ($log->session === 'morning' ? 'AM' : 'PM') . ' · ' . number_format($log->total_hours, 1) . 'h',
                'start' => $log->date->format('Y-m-d'),
                'allDay' => true,
                'extendedProps' => [
                    'session' => $log->session,
                    'status'  => $log->status,
                    'hours'   => number_format($log->total_hours, 1),
                ],
            ];
        });

        return response()->json($events);
    }
}