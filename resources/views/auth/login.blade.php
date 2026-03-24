@extends('layouts.auth')
@section('title', 'Sign in')

@section('content')
    <h2 class="auth-card-title">Welcome back</h2>
    <p class="auth-card-sub">Sign in to your OJTConnect account to continue.</p>

    {{-- Session status (e.g. after password reset) --}}
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    {{-- Global error --}}
    @if ($errors->any() && !$errors->has('email'))
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/login">
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

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                >
                <button type="button" class="toggle-pass" onclick="togglePassword('password')" tabindex="-1">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <label class="remember-wrap">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Sign in</button>
    </form>
@endsection