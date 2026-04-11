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
        \Log::info('SHOW LOGIN CALLED', [
            'is_authed'  => Auth::check(),
            'session_id' => session()->getId(),
            'host'       => request()->getHttpHost(),
        ]);

        if (app(\Stancl\Tenancy\Tenancy::class)->initialized) {
            return view('auth.tenant-login');
        }

        return view('auth.login');
    }
        public function login(Request $request, RecaptchaService $recaptcha)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
        ]);

        if (!$recaptcha->verify($request->input('g-recaptcha-response'))) {
            return back()->withErrors([
                'email' => 'CAPTCHA verification failed. Please try again.',
            ])->withInput($request->only('email'));
        }

      
        $host = $request->getSchemeAndHttpHost();

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
    $user = Auth::user();
    $request->session()->regenerate();

    \Log::info('LOGIN SUCCESS', [
        'user_id'    => $user->id,
        'role'       => $user->role,
        'session_id' => session()->getId(),
        'is_authed'  => Auth::check(),
    ]);

    if ($user->role === 'super_admin') {
            return redirect()->route('super_admin.dashboard');
        }

        $path = match($user->role) {
            'admin'              => '/admin/dashboard',
            'ojt_coordinator'    => '/coordinator/dashboard',
            'company_supervisor' => '/supervisor/dashboard',
            'student_intern'     => '/student/dashboard',
            default              => '/',
        };

        $url = 'http://' . tenant('id') . '.' . config('app.base_domain', 'localhost') . ':8000' . $path;

        \Log::info('REDIRECTING TO', ['url' => $url]);

        return redirect()->away($url);
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