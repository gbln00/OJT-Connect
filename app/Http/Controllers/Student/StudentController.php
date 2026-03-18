<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user        = Auth::user();
        $profile     = $user->studentProfile;
        $application = $user->activeApplication()->with('company')->first();

        // Hours progress — only if approved application exists
        $totalLogged   = $application ? $application->total_logged_hours : 0;
        $requiredHours = $application ? $application->required_hours : ($profile->required_hours ?? 486);
        $progressPct   = $requiredHours > 0 ? min(100, round(($totalLogged / $requiredHours) * 100)) : 0;

        // Recent hour logs — latest 5
        $recentLogs = $application
            ? $application->hourLogs()->latest()->take(5)->get()
            : collect();

        // Weekly reports — latest 3
        $recentReports = $application
            ? $application->weeklyReports()->latest()->take(3)->get()
            : collect();

        return view('student.dashboard', compact(
            'user',
            'profile',
            'application',
            'totalLogged',
            'requiredHours',
            'progressPct',
            'recentLogs',
            'recentReports'
        ));
    }
}