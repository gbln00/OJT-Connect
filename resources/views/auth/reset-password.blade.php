@extends('layouts.auth')
@section('title', 'Set New Password')

@section('content')
    <h2 class="auth-card-title">Set new password</h2>
    <p class="auth-card-sub">Choose a strong password for your account. Must be at least 8 characters.</p>

    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="/reset-password" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $email ?? '') }}"
                placeholder="you@buksu.edu.ph"
                autocomplete="email"
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">New password</label>
            <div class="password-wrap">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Min. 8 characters"
                    autocomplete="new-password"
                    autofocus
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    oninput="checkStrength(this.value)"
                >
                <button type="button" class="toggle-pass" onclick="togglePassword('password')" tabindex="-1">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <!-- Strength bar -->
            <div id="strength-bar" style="display:none;margin-top:8px;">
                <div style="height:4px;border-radius:4px;background:#E2E8F0;overflow:hidden;">
                    <div id="strength-fill" style="height:100%;width:0;border-radius:4px;transition:width 0.3s,background 0.3s;"></div>
                </div>
                <div id="strength-label" style="font-size:11px;color:#6B7A8D;margin-top:4px;"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm new password</label>
            <div class="password-wrap">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                >
                <button type="button" class="toggle-pass" onclick="togglePassword('password_confirmation')" tabindex="-1">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary">Update password</button>
    </form>
@endsection

@push('scripts')
<script>
function checkStrength(val) {
    const bar   = document.getElementById('strength-bar');
    const fill  = document.getElementById('strength-fill');
    const label = document.getElementById('strength-label');

    if (!val) { bar.style.display = 'none'; return; }
    bar.style.display = 'block';

    let score = 0;
    if (val.length >= 8)              score++;
    if (val.length >= 12)             score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const levels = [
        { pct: '20%', color: '#E05A5A', text: 'Very weak' },
        { pct: '40%', color: '#E88A38', text: 'Weak' },
        { pct: '60%', color: '#E8C438', text: 'Fair' },
        { pct: '80%', color: '#6ABF69', text: 'Good' },
        { pct: '100%',color: '#2D9E5F', text: 'Strong' },
    ];

    const lvl = levels[Math.max(0, score - 1)];
    fill.style.width      = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color;
}
</script>
@endpush