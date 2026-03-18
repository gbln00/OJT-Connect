<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->first();

        return view('student.dashboard', compact('user', 'application'));
    }
}