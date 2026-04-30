<?php

// app/Http/Controllers/Auth/ForgotPasswordController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request, RecaptchaService $recaptcha)
    {
        $rules = [
            'email'                => ['required', 'email'],
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

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
