@extends('layouts.auth')
@section('title', 'Sign in — OJTConnect')

@push('scripts')
@if (config('services.recaptcha.enabled'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endpush

@section('content')
    <h2 class="auth-card-title">Welcome back</h2>
    <p class="auth-card-sub">Sign in to your OJTConnect account to continue.</p>

    @if (session('status'))
        <div class="alert alert-success">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any() && !$errors->has('email') && !$errors->has('g-recaptcha-response'))
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">Email address</label>
            <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@buksu.edu.ph"
                    autocomplete="email"
                    autofocus
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                />
            </div>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrap password-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                />
                <button type="button" class="toggle-pass" onclick="togglePassword('password')" tabindex="-1" aria-label="Toggle password visibility">
                    <svg id="eye-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label class="remember-wrap">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span class="remember-box"></span>
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
            @endif
        </div>

         {{-- reCAPTCHA v2 --}}
        @if (config('services.recaptcha.enabled'))
        <div class="form-group" style="margin-top: 4px;">
            <div style="display: flex; justify-content: center;">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
            </div>
            @error('g-recaptcha-response')
                <div class="invalid-feedback" style="display:block; margin-top: 6px; text-align: center;">{{ $message }}</div>
            @enderror
        </div>
        @endif
        <button type="submit" class="btn-primary">
            Sign in
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>

        <div class="divider">
            <span>or</span>
        </div>

        <a href="{{ route('google.redirect') }}" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.93 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            </svg>
            Continue with Google
        </a>

        <div class="form-footer">
            Don't have an account?
            <a href="{{ route('tenant.register') }}" class="link">Register your institution</a>
        </div>
    </form>
    @stack('scripts')
@endsection

