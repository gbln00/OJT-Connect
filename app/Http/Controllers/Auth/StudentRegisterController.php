<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentRegisterController extends Controller
{
    public function showRegister()
    {
        $courses    = StudentProfile::courses();
        $yearLevels = StudentProfile::yearLevels();
        return view('auth.register', compact('courses', 'yearLevels'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'student_id' => [
                'required',
                'string',
                'regex:/^\d{10}$/',
                'unique:student_profiles,student_id',
            ],
            'firstname'  => ['required', 'string', 'max:100'],
            'lastname'   => ['required', 'string', 'max:100'],
            'middlename' => ['nullable', 'string', 'max:100'],
            'course'     => ['required', 'in:' . implode(',', StudentProfile::courses())],
            'year_level' => ['required', 'in:' . implode(',', StudentProfile::yearLevels())],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ], [
            'student_id.regex'  => 'Student ID must be exactly 10 digits (numbers only).',
            'student_id.unique' => 'This Student ID is already registered.',
            'email.unique'      => 'This email is already registered.',
        ]);

        // Create user account (unverified by default)
        $user = User::create([
            'name'        => $request->firstname . ' ' . $request->lastname,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'student_intern',
            'is_active'   => true,
            'is_verified' => false,
        ]);

        // Create student profile
        StudentProfile::create([
            'user_id'    => $user->id,
            'student_id' => $request->student_id,
            'firstname'  => $request->firstname,
            'lastname'   => $request->lastname,
            'middlename' => $request->middlename,
            'course'     => $request->course,
            'year_level' => $request->year_level,
        ]);

        return redirect()->route('login')
            ->with('success', 'Registration submitted! Please wait for admin verification before logging in.');
    }
}