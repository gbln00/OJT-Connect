<?php

namespace App\Http\Controllers;

use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\QrClockIn;
use App\Models\TenantNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QrScanController extends Controller
{
    /**
     * Handle a QR scan.
     *
     * REQUIRES student to be logged in — they are identified by their
     * auth session, NOT by anything embedded in the QR token.
     *
     * The QR token only identifies the COMPANY.
     * The logged-in student identifies THEMSELVES.
     *
     * Route: GET /qr/scan/{token}
     * Middleware: auth (student must be logged in)
     */
    public function scan(string $token)
    {
        $student = Auth::user();

        // 1. Only students can clock in via QR
        if ($student->role !== 'student_intern') {
            return view('qr.result', [
                'status'  => 'error',
                'title'   => 'Not a student account',
                'message' => 'Only student interns can clock in via QR.',
            ]);
        }

        // 2. Find and validate the QR (identifies the company)
        $qr = QrClockIn::with('company')->where('token', $token)->first();

        if (! $qr || ! $qr->isUsable()) {
            return view('qr.result', [
                'status'  => 'error',
                'title'   => 'Invalid or inactive QR',
                'message' => 'This QR code is not valid. Please ask your supervisor for the current one.',
            ]);
        }

        // 3. Student must have an approved application at THIS company
        $application = OjtApplication::where('student_id', $student->id)
            ->where('company_id', $qr->company_id)
            ->where('status', 'approved')
            ->with('company')
            ->latest()
            ->first();

        if (! $application) {
            return view('qr.result', [
                'status'  => 'error',
                'title'   => 'No approved application',
                'message' => "You don't have an approved OJT application at {$qr->company->name}.",
            ]);
        }

        // 4. Determine session from server time
        $session = QrClockIn::currentSession();
        $today   = now()->toDateString();

        // 5. Check for duplicate log for today's session
        $exists = HourLog::where('student_id', $student->id)
            ->where('application_id', $application->id)
            ->whereDate('date', $today)
            ->where('session', $session)
            ->exists();

        if ($exists) {
            return view('qr.result', [
                'status'      => 'info',
                'title'       => 'Already clocked in',
                'message'     => "Your " . ($session === 'morning' ? 'morning (AM)' : 'afternoon (PM)') . " session for today is already recorded.",
                'student'     => $student,
                'session'     => $session,
                'application' => $application,
            ]);
        }

        // 6. Create the HourLog
        [$timeIn, $timeOut] = $session === 'morning'
            ? ['08:00', '12:00']
            : ['13:00', '17:00'];

        $hours = round(
            Carbon::createFromFormat('H:i', $timeIn)
                  ->diffInMinutes(Carbon::createFromFormat('H:i', $timeOut)) / 60,
            2
        );

        $log = HourLog::create([
            'student_id'     => $student->id,
            'application_id' => $application->id,
            'date'           => $today,
            'session'        => $session,
            'time_in'        => $timeIn,
            'time_out'       => $timeOut,
            'total_hours'    => $hours,
            'description'    => 'Logged via QR scan.',
            'status'         => 'pending',
        ]);

        // 7. Notify supervisor and coordinator
        TenantNotification::notify(
            title:      'QR Clock-In Recorded',
            message:    "{$student->name} clocked in (" . ($session === 'morning' ? 'AM' : 'PM') . ") via QR on {$today}.",
            type:       'info',
            targetRole: 'company_supervisor'
        );

        TenantNotification::notify(
            title:      'QR Clock-In Recorded',
            message:    "{$student->name} clocked in (" . ($session === 'morning' ? 'AM' : 'PM') . ") via QR on {$today}.",
            type:       'info',
            targetRole: 'ojt_coordinator'
        );

        return view('qr.result', [
            'status'      => 'success',
            'title'       => 'Clocked in!',
            'message'     => "Your " . ($session === 'morning' ? 'morning (AM)' : 'afternoon (PM)') . " session has been recorded for {$today}. Pending supervisor approval.",
            'student'     => $student,
            'session'     => $session,
            'application' => $application,
            'log'         => $log,
        ]);
    }
}