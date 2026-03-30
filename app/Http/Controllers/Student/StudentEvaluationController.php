<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StudentEvaluationController extends Controller
{
    public function show()
    {
        $user        = Auth::user();
        $application = $user->activeApplication()->with('evaluation')->first();
        $evaluation  = $application?->evaluation;
        return view('student.evaluations.show', compact('application','evaluation'));
    }
}
