<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google. 
     * We encode whether this is a tenant context in the `state` param
     * so the central callback knows where to redirect back.
     */
    public function redirect(Request $request)
    {
        // Store the current host so the callback can redirect correctly
        $state = base64_encode(json_encode([
            'host'      => $request->getHost(),
            'port'      => $request->getPort(),
            'is_tenant' => app(\Stancl\Tenancy\Tenancy::class)->initialized,
        ]));

        return Socialite::driver('google')
            ->with(['state' => $state])
            ->redirect();
    }

    /**
     * Handle Google's callback — always hits the central domain.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'email' => 'Google authentication failed. Please try again.',
            ]);
        }

        // Decode where this request came from
        $state = json_decode(base64_decode($request->get('state', '')), true);
        $isTenant = $state['is_tenant'] ?? false;
        $host = $state['host'] ?? null;
        $port = $state['port'] ?? null;

        // Find or create the user in the correct DB context
        if ($isTenant && $host) {
            return $this->handleTenantCallback($googleUser, $host, $port);
        }

        return $this->handleCentralCallback($googleUser);
    }

    /**
     * Central domain: only super_admin accounts allowed via Google.
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

        // Link Google account if not already linked
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
     * Tenant domain: redirect back to tenant domain to complete login there.
     * We use a signed short-lived token to pass identity securely.
     */
    private function handleTenantCallback($googleUser, string $host, ?int $port)
    {
        // Build a signed payload the tenant callback will verify
        $payload = encrypt([
            'google_id' => $googleUser->getId(),
            'email'     => $googleUser->getEmail(),
            'name'      => $googleUser->getName(),
            'avatar'    => $googleUser->getAvatar(),
            'expires'   => now()->addMinutes(2)->timestamp,
        ]);

        $tenantUrl = 'http://' . $host . ($port && $port != 80 ? ":$port" : '') . '/auth/google/tenant-login';

        return redirect($tenantUrl . '?' . http_build_query(['token' => $payload]));
    }

    /**
     * Called on the tenant domain — validates the encrypted token and logs in.
     */
    public function tenantLogin(Request $request)
    {
        try {
            $data = decrypt($request->get('token'));
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'email' => 'Google login failed. Please try again.',
            ]);
        }

        if (now()->timestamp > $data['expires']) {
            return redirect('/login')->withErrors([
                'email' => 'Google login link expired. Please try again.',
            ]);
        }

        $user = User::where('google_id', $data['google_id'])
                    ->orWhere('email', $data['email'])
                    ->first();

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

        // Link Google account if not already linked
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