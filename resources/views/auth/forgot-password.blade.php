@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('content')
    <a href="/login" class="link" style="display:inline-flex;align-items:center;gap:6px;margin-bottom:28px;font-size:13px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
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

        <button type="submit" class="btn-primary">Send reset link</button>
    </form>
@endsection