<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        if ($isTenant) {
            return view('auth.tenant-login');
        }

        return view('auth.login');
    }

    public function login(Request $request, RecaptchaService $recaptcha)
    {
        $rules = [
            'email'                => ['required', 'email'],
            'password'             => ['required'],
        ];

        if (config('services.recaptcha.enabled')) {
            $rules['g-recaptcha-response'] = ['required'];
        }

        $request->validate($rules, [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
        ]);

        if (config('services.recaptcha.enabled') && !$recaptcha->verify($request->input('g-recaptcha-response'))) {
            return back()->withErrors([
                'email' => 'CAPTCHA verification failed. Please try again.',
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $attempted   = Auth::attempt($credentials, $request->boolean('remember'));

        if ($attempted) {
            $user = Auth::user();
            $request->session()->regenerate();

            if ($user->role === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            }

            return redirect(match($user->role) {
                'admin'              => '/admin/dashboard',
                'ojt_coordinator'    => '/coordinator/dashboard',
                'company_supervisor' => '/supervisor/dashboard',
                'student_intern'     => '/student/dashboard',
                default              => '/',
            });
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

        return redirect('/login');
    }
}