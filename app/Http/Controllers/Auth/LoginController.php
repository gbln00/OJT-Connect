<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();


            $request->session()->regenerate();

            return match($user->role) {
                'super_admin'        => redirect()->route('super_admin.dashboard'),
                'admin'              => redirect()->route('admin.dashboard'),
                'ojt_coordinator'    => redirect()->route('coordinator.dashboard'),
                'company_supervisor' => redirect()->route('supervisor.dashboard'),
                'student_intern'     => redirect()->route('student.dashboard'),
                default              => redirect('/'),
            };
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}