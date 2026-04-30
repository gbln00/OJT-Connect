<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RecaptchaService
{
    public function verify(string $token): bool
    {
        if (app()->environment(['local', 'testing']) || ! config('services.recaptcha.enabled')) {
            return true;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $token,
        ]);

        return $response->json('success', false);
    }
}