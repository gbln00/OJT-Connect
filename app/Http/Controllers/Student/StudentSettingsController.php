<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentSettingsController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->studentProfile;
        return view('student.profile.settings', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone'   => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:500'],
        ]);
        Auth::user()->studentProfile?->update($request->only('phone','address'));
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required','current_password'],
            'password'         => ['required','min:8','confirmed'],
        ]);
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated successfully.');
    }
}
