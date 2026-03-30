@extends('layouts.student-app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            System / Settings
        </span>
    </div>

    {{-- PROFILE HEADER --}}
    <div class="card fade-up fade-up-1">
        <div style="padding:20px 24px;display:flex;align-items:center;gap:16px;">
            <div style="width:52px;height:52px;flex-shrink:0;border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--crimson);">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);">{{ auth()->user()->name }}</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:2px;">{{ auth()->user()->email }}</div>
                <div style="margin-top:6px;"><span class="role-badge student">Student Intern</span></div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <div class="form-hint">Member since</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);margin-top:2px;">{{ auth()->user()->created_at->format('M d, Y') }}</div>
            </div>
        </div>
    </div>

    {{-- TWO COLUMN --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;" class="fade-up fade-up-2">

        {{-- PROFILE INFO --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Profile Information</div>
            </div>
            <form method="POST" action="{{ route('student.settings.profile') }}" style="padding:24px;">
                @csrf @method('PATCH')

                <div style="margin-bottom:16px;">
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           required class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           required class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Role <span style="color:var(--muted);">(read-only)</span></label>
                    <input type="text" value="Student Intern" disabled
                           class="form-input" style="cursor:not-allowed;color:var(--muted);background:var(--bg);">
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                </div>
            </form>
        </div>

        {{-- CHANGE PASSWORD --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Change Password</div>
            </div>
            <form method="POST" action="{{ route('student.settings.password') }}" style="padding:24px;">
                @csrf @method('PATCH')

                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:16px;letter-spacing:0.04em;">
                    // Leave blank to keep current password unchanged
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Current password</label>
                    <div style="position:relative;">
                        <input type="password" name="current_password" id="pw-current"
                               class="form-input {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                               style="padding-right:38px;">
                        <button type="button" onclick="togglePw('pw-current',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">New password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pw-main"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               style="padding-right:38px;" oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePw('pw-main',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div style="margin-top:6px;">
                        <div class="progress-track" style="height:3px;"><div id="strength-bar" style="height:100%;width:0%;transition:width 0.3s,background 0.3s;"></div></div>
                        <div id="strength-label" class="form-hint" style="margin-top:3px;"></div>
                    </div>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Confirm new password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="pw-confirm"
                               class="form-input" style="padding-right:38px;" oninput="checkMatch()">
                        <button type="button" onclick="togglePw('pw-confirm',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div id="pw-match-msg" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:4px;min-height:16px;letter-spacing:0.04em;"></div>
                </div>

                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
                </div>
            </form>
        </div>

    </div>

    {{-- DANGER ZONE --}}
    <div class="card fade-up fade-up-3" style="border-color:rgba(140,14,3,0.2);">
        <div class="card-header" style="border-bottom-color:rgba(140,14,3,0.12);">
            <div class="card-title" style="color:var(--crimson);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;margin-right:6px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Danger Zone
            </div>
        </div>
        <div style="padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <div style="font-size:13.5px;font-weight:500;color:var(--text);">Log out of all sessions</div>
                <div class="form-hint" style="margin-top:2px;">Sign out from all devices and browsers.</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Log Out</button>
            </form>
        </div>
    </div>

</div>

<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.style.color = isText ? 'var(--muted)' : 'var(--crimson)';
    }

    const pwMain    = document.getElementById('pw-main');
    const pwConfirm = document.getElementById('pw-confirm');
    const pwMsg     = document.getElementById('pw-match-msg');

    function checkMatch() {
        if (!pwConfirm.value) { pwMsg.textContent = ''; return; }
        if (pwMain.value === pwConfirm.value) {
            pwMsg.textContent = '✓ passwords match';
            pwMsg.style.color = '#34d399';
            pwConfirm.style.borderColor = '#34d399';
        } else {
            pwMsg.textContent = '✕ passwords do not match';
            pwMsg.style.color = 'var(--crimson)';
            pwConfirm.style.borderColor = 'var(--crimson)';
        }
    }
    if (pwMain) pwMain.addEventListener('input', checkMatch);

    function checkStrength(val) {
        const bar   = document.getElementById('strength-bar');
        const label = document.getElementById('strength-label');
        if (!val) { bar.style.width = '0%'; label.textContent = ''; return; }
        let score = 0;
        if (val.length >= 8)          score++;
        if (/[A-Z]/.test(val))        score++;
        if (/[0-9]/.test(val))        score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        const levels = [
            { w: '25%', color: 'var(--crimson)',    text: 'Weak' },
            { w: '50%', color: 'var(--gold-color)', text: 'Fair' },
            { w: '75%', color: 'var(--blue-color)', text: 'Good' },
            { w: '100%',color: 'var(--teal-color)', text: 'Strong' },
        ];
        const l = levels[Math.min(score - 1, 3)] || levels[0];
        bar.style.width      = l.w;
        bar.style.background = l.color;
        label.textContent    = l.text;
        label.style.color    = l.color;
    }
</script>
@endsection