@extends('layouts.auth')
@section('title', 'Forgot Password')


@section('content')

   {{-- Back button --}}
    <a href="/login" class="btn-ghost-nav" style="display:inline-flex;align-items:center;gap:8px;margin-bottom:28px;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to sign in
    </a>

    <h2 class="auth-card-title">Reset your password</h2>
    <p class="auth-card-sub">Enter your registered email and we'll send you a link to reset your password.</p>

    @if (session('status'))
        <div class="alert alert-success">
            <strong>Email sent!</strong> Check your inbox for a password reset link. If you don't see it, check your spam folder.
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/forgot-password" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@buksu.edu.ph"
                autocomplete="email"
                autofocus
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- reCAPTCHA v2 --}}
        @if (config('services.recaptcha.enabled'))
        <div class="form-group recaptcha-group">
            <div class="recaptcha-frame">
            <div id="recaptcha-container"></div>
        </div>
            @error('g-recaptcha-response')
                <div class="invalid-feedback recaptcha-error">{{ $message }}</div>
            @enderror
        </div>
        @endif

        


        <button type="submit" class="btn-primary">Send reset link</button>
    </form>

        @if (config('services.recaptcha.enabled'))
        <script>
            function onRecaptchaLoad() {
                const el = document.getElementById('recaptcha-container');
                if (el) {
                    grecaptcha.render(el, {
                        sitekey: '{{ config("services.recaptcha.site_key") }}',
                        theme: 'dark'
                    });
                }
            }
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=onRecaptchaLoad&render=explicit" async defer></script>
        @endif
    
@endsection