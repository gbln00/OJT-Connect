<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLogin()
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        \Log::info('=== SHOW LOGIN ===', [
            'is_tenant'    => $isTenant,
            'tenant_id'    => $isTenant ? tenant('id') : null,
            'host'         => request()->getHttpHost(),
            'session_id'   => session()->getId(),
            'session_name' => config('session.cookie'),
            'session_driver' => config('session.driver'),
            'db_connection'  => DB::getDefaultConnection(),
            'is_authed'    => Auth::check(),
        ]);

        if ($isTenant) {
            return view('auth.tenant-login');
        }

        return view('auth.login');
    }

    public function login(Request $request, RecaptchaService $recaptcha)
    {
        \Log::info('=== LOGIN POST START ===', [
            'host'           => $request->getHttpHost(),
            'url'            => $request->fullUrl(),
            'is_tenant'      => app(\Stancl\Tenancy\Tenancy::class)->initialized,
            'tenant_id'      => app(\Stancl\Tenancy\Tenancy::class)->initialized ? tenant('id') : null,
            'session_id'     => session()->getId(),
            'session_cookie' => config('session.cookie'),
            'session_driver' => config('session.driver'),
            'session_conn'   => config('session.connection'),
            'db_default'     => DB::getDefaultConnection(),
            'email_input'    => $request->input('email'),
        ]);

        $request->validate([
            'email'                => ['required', 'email'],
            'password'             => ['required'],
            'g-recaptcha-response' => ['required'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
        ]);

        if (!$recaptcha->verify($request->input('g-recaptcha-response'))) {
            \Log::warning('=== RECAPTCHA FAILED ===');
            return back()->withErrors([
                'email' => 'CAPTCHA verification failed. Please try again.',
            ])->withInput($request->only('email'));
        }

        // Check if user exists in current DB before attempting
        $email = $request->input('email');
        $userInDb = DB::table('users')->where('email', $email)->first();
        \Log::info('=== USER LOOKUP ===', [
            'email'         => $email,
            'found_in_db'   => $userInDb ? true : false,
            'db_connection' => DB::getDefaultConnection(),
            'user_role'     => $userInDb->role ?? 'N/A',
            'user_active'   => $userInDb->is_active ?? 'N/A',
        ]);

        $credentials = $request->only('email', 'password');
        $attempted   = Auth::attempt($credentials, $request->boolean('remember'));

        \Log::info('=== AUTH ATTEMPT RESULT ===', [
            'success'      => $attempted,
            'auth_check'   => Auth::check(),
            'user_id'      => Auth::id(),
            'session_id'   => session()->getId(),
        ]);

        if ($attempted) {
            $user = Auth::user();
            $request->session()->regenerate();

            \Log::info('=== SESSION REGENERATED ===', [
                'new_session_id' => session()->getId(),
                'session_cookie' => config('session.cookie'),
                'user_role'      => $user->role,
                'user_id'        => $user->id,
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

            \Log::info('=== REDIRECTING TO ===', [
                'path'     => $path,
                'full_url' => $request->getSchemeAndHttpHost() . $path,
            ]);

            return redirect($path);
        }

        \Log::warning('=== LOGIN FAILED - credentials did not match ===', [
            'email'         => $email,
            'db_connection' => DB::getDefaultConnection(),
        ]);

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