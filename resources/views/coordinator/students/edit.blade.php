@extends('layouts.coordinator-app')
@section('title', 'Edit Account')
@section('page-title', 'Edit Account')

@section('content')
<div style="max-width:620px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Accounts / Edit
        </span>
    </div>

    <div class="card fade-up fade-up-1">

        <div class="card-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="card-title">{{ $user->name }}</div>
                    <div style="margin-top:4px;">
                        <span class="role-badge {{ $user->role === 'student_intern' ? 'student' : 'supervisor' }}">
                            {{ $user->role_label }}
                        </span>
                    </div>
                </div>
            </div>
            <a href="{{ route('coordinator.accounts.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('coordinator.accounts.update', $user) }}" style="padding:24px;">
            @csrf @method('PUT')

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);padding:13px 16px;margin-bottom:24px;font-size:13px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">
                    Please fix the following:
                </strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div class="form-section-divider"><span>Account Information</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Role</label>
                <select name="role" class="form-input" style="cursor:pointer;">
                    <option value="student_intern"     {{ old('role', $user->role) === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                    <option value="company_supervisor" {{ old('role', $user->role) === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                </select>
                @error('role')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Company for supervisors --}}
            @if($user->role === 'company_supervisor')
            <div class="form-section-divider"><span>Supervisor Details</span></div>
            <div style="margin-bottom:28px;">
                <label class="form-label">Assigned company</label>
                <select name="company_id" class="form-input {{ $errors->has('company_id') ? 'is-invalid' : '' }}" style="cursor:pointer;">
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                    @endforeach
                </select>
                @error('company_id')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            @endif

            <div class="form-section-divider"><span>Change Password</span></div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-bottom:14px;letter-spacing:0.04em;">
                // Leave blank to keep the current password unchanged
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">New password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pw-main"
                               placeholder="Min. 8 characters"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               style="padding-right:38px;">
                        <button type="button" onclick="togglePw('pw-main',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
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
                               style="padding-right:38px;">
                        <button type="button" onclick="togglePw('pw-confirm',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div id="pw-match-msg" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:4px;min-height:16px;letter-spacing:0.04em;"></div>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">// Changes are applied immediately on save.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('coordinator.accounts.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
.form-section-divider {
    display:flex;align-items:center;gap:14px;margin-bottom:16px;
}
.form-section-divider::before { content:'';width:20px;height:2px;background:var(--crimson);flex-shrink:0; }
.form-section-divider::after  { content:'';flex:1;height:1px;background:var(--border); }
.form-section-divider span {
    font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;
    letter-spacing:0.22em;text-transform:uppercase;color:var(--muted);
}
.form-input.is-invalid { border-color:var(--crimson); }
</style>
@endpush

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
pwMain.addEventListener('input', checkMatch);
pwConfirm.addEventListener('input', checkMatch);
</script>
@endsection