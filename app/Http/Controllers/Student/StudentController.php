<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user    = Auth::user();
        $profile = $user->studentProfile;

        return view('student.dashboard', compact('user', 'profile'));
    }
}