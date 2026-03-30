@extends('layouts.supervisor-app')
@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

<div style="max-width:680px;">

    {{-- EYEBROW --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            System / Settings
        </span>
    </div>

    {{-- PROFILE CARD --}}
    <div class="card fade-up fade-up-1" style="margin-bottom:12px;">

        <div class="card-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);
                            background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;
                            font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                    {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 2)) }}
                </div>
                <div>
                    <div class="card-title">{{ auth()->user()->name }}</div>
                    <div style="margin-top:3px;">
                        <span class="role-badge supervisor">Supervisor</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('supervisor.settings.update.profile') }}" style="padding:24px;">
            @csrf @method('PATCH')

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);
                        padding:13px 16px;margin-bottom:24px;font-size:13px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;
                               letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">
                    Please fix the following:
                </strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div class="form-section-divider"><span>Account Information</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                           class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           style="border-radius:0;">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           style="border-radius:0;">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary btn-sm">Save profile</button>
            </div>

        </form>
    </div>

    {{-- PASSWORD CARD --}}
    <div class="card fade-up fade-up-2">

        <div class="card-header">
            <div>
                <div class="card-title">Change password</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Leave blank to keep current password unchanged
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('supervisor.settings.update.password') }}" style="padding:24px;">
            @csrf @method('PATCH')

            <div class="form-section-divider"><span>New Password</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">New password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pw-main"
                               placeholder="Min. 8 characters"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               style="padding-right:38px;border-radius:0;">
                        <button type="button" onclick="togglePw('pw-main', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                       background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Confirm new password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="pw-confirm"
                               placeholder="Repeat new password"
                               class="form-input"
                               style="padding-right:38px;border-radius:0;"
                               oninput="checkMatch()">
                        <button type="button" onclick="togglePw('pw-confirm', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                       background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div id="pw-match-msg" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:4px;min-height:16px;letter-spacing:0.04em;"></div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary btn-sm">Update password</button>
            </div>

        </form>
    </div>

</div>

@push('styles')
<style>
.form-input { border-radius: 0 !important; }
.form-input:focus { border-color: var(--crimson); }
.form-input.is-invalid { border-color: var(--crimson); }
</style>
@endpush

@push('scripts')
<script>
function togglePw(id, btn) {
    const input  = document.getElementById(id);
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
pwMain.addEventListener('input', checkMatch);
</script>
@endpush

@endsection