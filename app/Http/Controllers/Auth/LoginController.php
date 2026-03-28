<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
       // If we're in a tenant context, use tenant login view
        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            return view('auth.tenant-login');
        }

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

            // Super admin stays on central domain via named route
            if ($user->role === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }

            // Tenant roles use relative paths to stay on current domain
            $path = match($user->role) {
                'admin'              => '/admin/dashboard',
                'ojt_coordinator'    => '/coordinator/dashboard',
                'company_supervisor' => '/supervisor/dashboard',
                'student_intern'     => '/student/dashboard',
                default              => '/',
            };
            

            return redirect($path);
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

        return redirect()->to('/login');
        }
}