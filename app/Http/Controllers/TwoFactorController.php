<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // ── Setup: show QR code to enable 2FA ─────────────────────────
    public function setup()
    {
        $user   = auth()->user();
        $google2fa = new Google2FA();

        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => $secret]);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        return view('2fa.setup', compact('qrCodeUrl'));
    }

    // ── Setup: confirm and enable 2FA ─────────────────────────────
    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user      = auth()->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update(['two_factor_enabled' => true]);
        session(['2fa_verified' => true]);

        return redirect()->back()->with('success', '2FA has been enabled on your account.');
    }

    // ── Disable 2FA ───────────────────────────────────────────────
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user      = auth()->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret'  => null,
        ]);
        session()->forget('2fa_verified');

        return redirect()->back()->with('success', '2FA has been disabled.');
    }

    // ── Challenge: show verification form ────────────────────────
    public function challenge()
    {
        // Already verified this session
        if (session('2fa_verified')) {
            return redirect()->intended('/');
        }

        return view('2fa.challenge');
    }

    // ── Challenge: verify the code ────────────────────────────────
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user      = auth()->user();
        $google2fa = new Google2FA();

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }

        session(['2fa_verified' => true]);

        return redirect()->intended(match($user->role) {
            'admin'           => '/admin/dashboard',
            'ojt_coordinator' => '/coordinator/dashboard',
            default           => '/',
        });
    }
}