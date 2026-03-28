@extends('layouts.auth')
@section('title', 'Sign in — ' . (tenant('name') ?? 'OJTConnect'))

@section('content')
    {{-- Institution badge --}}
    @if(tenant('name'))
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;padding:8px 12px;
                border:1px solid rgba(140,14,3,0.2);background:rgba(140,14,3,0.05);">
        <span style="width:6px;height:6px;background:#8C0E03;border-radius:50%;flex-shrink:0;
                     animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.15em;
                     text-transform:uppercase;color:rgba(140,14,3,0.7);">
            {{ tenant('name') }}
        </span>
    </div>
    @endif

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

    @if ($errors->any() && !$errors->has('email'))
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
                    placeholder="you@institution.edu.ph"
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

        <button type="submit" class="btn-primary">
            Sign in
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/>
                <polyline points="12 5 19 12 12 19"/>
            </svg>
        </button>

        {{-- Footer: no tenant.register link — that's central only --}}
        <div class="form-footer">
            Contact your OJT coordinator if you don't have an account yet.
        </div>

        {{-- Domain indicator --}}
        <div style="margin-top:16px;text-align:center;">
            <span style="font-family:'DM Mono',monospace;font-size:10px;
                         color:rgba(171,171,171,0.2);letter-spacing:0.12em;">
                {{ tenant('id') }}.{{ config('app.base_domain', 'ojtconnect.com') }}
            </span>
        </div>
    </form>
@endsection