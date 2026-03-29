<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google.
     * Store tenant context in session before leaving.
     */
    public function redirect(Request $request)
    {
        $isTenant = app(\Stancl\Tenancy\Tenancy::class)->initialized;

        $state = base64_encode(json_encode([
            'is_tenant' => $isTenant,
            'host'      => $request->getHost(),
            'port'      => $request->getPort(),
            'csrf'      => \Str::random(40), // our own CSRF token
        ]));

        \Log::info('Google redirect', [
            'is_tenant' => $isTenant,
            'host'      => $request->getHost(),
        ]);

        return Socialite::driver('google')
            ->stateless()           // ← disables Socialite's own state/session check
            ->with(['state' => $state])
            ->redirect();
    }

    /**
     * Handle Google's callback — always hits the central domain.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            \Log::error('Google OAuth callback error: ' . $e->getMessage());

            return redirect('/login')->withErrors([
                'email' => 'Google authentication failed: ' . $e->getMessage(),
            ]);
        }

        // Read our custom state
        $state    = json_decode(base64_decode($request->get('state', '')), true);
        $isTenant = $state['is_tenant'] ?? false;
        $host     = $state['host'] ?? null;
        $port     = $state['port'] ?? null;

        \Log::info('Google callback', [
            'is_tenant' => $isTenant,
            'host'      => $host,
            'email'     => $googleUser->getEmail(),
        ]);

        if ($isTenant && $host) {
            return $this->handleTenantCallback($googleUser, $host, $port);
        }

        return $this->handleCentralCallback($googleUser);
    }
    /**
     * Central domain: only super_admin allowed via Google.
     */
    private function handleCentralCallback($googleUser)
    {
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'No account found for this Google account. Please contact your administrator.',
            ]);
        }

        if ($user->role !== 'super_admin') {
            return redirect('/login')->withErrors([
                'email' => 'Please log in from your institution\'s domain.',
            ]);
        }

        if (!$user->google_id) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);
        return redirect()->route('super_admin.dashboard');
    }

    /**
     * Tenant domain: forward identity securely to tenant domain.
     */
    private function handleTenantCallback($googleUser, string $host, ?int $port)
    {
        $payload = encrypt([
            'google_id' => $googleUser->getId(),
            'email'     => $googleUser->getEmail(),
            'name'      => $googleUser->getName(),
            'avatar'    => $googleUser->getAvatar(),
            'expires'   => now()->addMinutes(2)->timestamp,
        ]);

        $tenantUrl = 'http://' . $host
            . ($port && $port != 80 ? ":$port" : '')
            . '/auth/google/tenant-login';

        \Log::info('Forwarding to tenant', ['url' => $tenantUrl]);

        return redirect($tenantUrl . '?' . http_build_query(['token' => $payload]));
    }

    /**
     * Called on the tenant domain — validates token and logs in.
     */
    public function tenantLogin(Request $request)
    {
        try {
            $data = decrypt($request->get('token'));
        } catch (\Exception $e) {
            \Log::error('Tenant login decrypt error: ' . $e->getMessage());
            return redirect('/login')->withErrors([
                'email' => 'Google login failed. Please try again.',
            ]);
        }

        if (now()->timestamp > $data['expires']) {
            return redirect('/login')->withErrors([
                'email' => 'Google login link expired. Please try again.',
            ]);
        }

        // ↓ Replace the old lookup with this debug version
        $user = User::where('google_id', $data['google_id'])
                    ->orWhere('email', $data['email'])
                    ->first();

        \Log::info('Tenant login lookup', [
            'google_id'  => $data['google_id'],
            'email'      => $data['email'],
            'user_found' => $user ? $user->email : 'NOT FOUND',
        ]);

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'No account found for this Google account. Please contact your administrator.',
            ]);
        }

        if (!$user->is_active) {
            return redirect('/login')->withErrors([
                'email' => 'Your account has been deactivated.',
            ]);
        }

        if (!$user->google_id) {
            $user->update([
                'google_id' => $data['google_id'],
                'avatar'    => $data['avatar'],
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $path = match($user->role) {
            'admin'              => '/admin/dashboard',
            'ojt_coordinator'    => '/coordinator/dashboard',
            'company_supervisor' => '/supervisor/dashboard',
            'student_intern'     => '/student/dashboard',
            default              => '/',
        };

        return redirect($path);
    }
}