@extends('layouts.student-app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@push('styles')
<style>
/* ── Avatar upload zone ───────────────────────────────────────── */
.avatar-zone {
    position: relative;
    width: 72px;
    height: 72px;
    flex-shrink: 0;
    cursor: pointer;
}
.avatar-zone input[type=file] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 2;
}
.avatar-preview {
    width: 72px; height: 72px;
    border: 1px solid rgba(140,14,3,0.4);
    background: rgba(140,14,3,0.07);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 900; color: var(--crimson);
    overflow: hidden; position: relative;
}
.avatar-preview img {
    width: 100%; height: 100%; object-fit: cover;
    position: absolute; inset: 0;
}
.avatar-overlay {
    position: absolute; inset: 0; z-index: 1;
    background: rgba(0,0,0,0.55);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.18s;
    flex-direction: column; gap: 2px;
}
.avatar-zone:hover .avatar-overlay { opacity: 1; }
.avatar-overlay svg { color: #fff; }
.avatar-overlay span {
    font-family: 'DM Mono', monospace;
    font-size: 8px; letter-spacing: 0.12em;
    text-transform: uppercase; color: #fff; font-weight: 600;
}

/* Upload progress bar */
.avatar-progress {
    height: 2px; background: var(--surface2);
    border: none; overflow: hidden; margin-top: 6px;
    display: none;
}
.avatar-progress-bar {
    height: 100%; width: 0%; background: var(--crimson);
    transition: width 0.25s ease;
}

/* ── Password strength track ─────────────────────────────────── */
.progress-track {
    height: 3px;
    background: var(--surface2);
    border: 1px solid var(--border);
    overflow: hidden;
}

/* ── Danger zone row ─────────────────────────────────────────── */
.danger-row {
    padding: 16px 20px;
    display: flex; align-items: center;
    justify-content: space-between; gap: 16px;
    flex-wrap: wrap;
}
.danger-row + .danger-row {
    border-top: 1px solid rgba(140,14,3,0.1);
}
</style>
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div style="max-width:740px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            System / Settings
        </span>
    </div>

    {{-- ── PROFILE HEADER with avatar ───────────────────────────── --}}
    <div class="card fade-up fade-up-1">
        <div style="padding:22px 24px;display:flex;align-items:center;gap:18px;">

            {{-- Avatar upload zone --}}
            <form id="avatar-form" method="POST"
                  action="{{ route('student.settings.avatar') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="avatar-zone" title="Click to change avatar">
                    <input type="file" name="avatar" id="avatar-input"
                           accept="image/jpeg,image/png,image/webp"
                           onchange="previewAvatar(this)">
                    <div class="avatar-preview" id="avatar-preview">
                        @if(auth()->user()->avatar)
                            <img id="avatar-img"
                                 src="{{ Storage::url(auth()->user()->avatar) }}"
                                 alt="Avatar">
                        @else
                            <span id="avatar-initials">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="avatar-overlay">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <span>Change</span>
                    </div>
                </div>
                {{-- Progress bar under avatar --}}
                <div class="avatar-progress" id="avatar-progress">
                    <div class="avatar-progress-bar" id="avatar-progress-bar"></div>
                </div>
            </form>

            <div style="flex:1;min-width:0;">
                <div style="font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--text);">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:2px;">
                    {{ auth()->user()->email }}
                </div>
                <div style="margin-top:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span class="role-badge student">Student Intern</span>
                    {{-- Avatar status feedback --}}
                    <span id="avatar-status" style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.08em;display:none;"></span>
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:6px;letter-spacing:0.04em;">
                    // Click the photo to upload a new avatar · JPG, PNG, WEBP · max 2 MB
                </div>
            </div>

            <div style="text-align:right;flex-shrink:0;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);">Member since</div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);margin-top:3px;">
                    {{ auth()->user()->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Avatar validation error --}}
        @error('avatar')
        <div style="padding:0 24px 14px;font-family:'DM Mono',monospace;font-size:11px;color:var(--crimson);">
            ✕ {{ $message }}
        </div>
        @enderror
    </div>

    {{-- Flash messages --}}
    @if(session('avatar_success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:11px 16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('avatar_success') }}
    </div>
    @endif
    @if(session('success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:11px 16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('success') }}
    </div>
    @endif
    @if(session('password_success'))
    <div style="background:rgba(52,211,153,0.08);border:1px solid rgba(52,211,153,0.3);color:#34d399;padding:11px 16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('password_success') }}
    </div>
    @endif

    {{-- ── TWO COLUMN: Profile + Password ───────────────────────── --}}
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
                    <input type="text" name="name"
                           value="{{ old('name', auth()->user()->name) }}"
                           required
                           class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email"
                           value="{{ old('email', auth()->user()->email) }}"
                           required
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Role <span style="color:var(--muted);font-weight:400;text-transform:none;letter-spacing:0;">(read-only)</span></label>
                    <input type="text" value="Student Intern" disabled
                           class="form-input"
                           style="cursor:not-allowed;color:var(--muted);background:var(--bg);">
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
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">New password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="pw-main"
                               class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               style="padding-right:38px;"
                               oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePw('pw-main',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <div style="margin-top:6px;">
                        <div class="progress-track"><div id="strength-bar" style="height:100%;width:0%;transition:width 0.3s,background 0.3s;"></div></div>
                        <div id="strength-label" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:3px;min-height:14px;letter-spacing:0.04em;"></div>
                    </div>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Confirm new password</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="pw-confirm"
                               class="form-input" style="padding-right:38px;"
                               oninput="checkMatch()">
                        <button type="button" onclick="togglePw('pw-confirm',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
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

    {{-- ── DANGER ZONE ────────────────────────────────────────────── --}}
    <div class="card fade-up fade-up-3" style="border-color:rgba(140,14,3,0.2);">
        <div class="card-header" style="border-bottom-color:rgba(140,14,3,0.12);">
            <div class="card-title" style="color:var(--crimson);display:flex;align-items:center;gap:7px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Danger Zone
            </div>
        </div>

        <div class="danger-row">
            <div>
                <div style="font-size:13.5px;font-weight:500;color:var(--text);">Remove avatar</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;letter-spacing:0.04em;">
                    Revert to the initials placeholder.
                </div>
            </div>
            @if(auth()->user()->avatar)
            <form method="POST" action="{{ route('student.settings.avatar') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Remove Photo</button>
            </form>
            @else
            <button disabled class="btn btn-ghost btn-sm" style="opacity:0.4;cursor:not-allowed;">No photo set</button>
            @endif
        </div>

        <div class="danger-row">
            <div>
                <div style="font-size:13.5px;font-weight:500;color:var(--text);">Log out of all sessions</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;letter-spacing:0.04em;">
                    Sign out from all devices and browsers.
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Log Out</button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
// ── Avatar preview + auto-submit ────────────────────────────────
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];

    // Client-side size guard (2 MB)
    if (file.size > 2 * 1024 * 1024) {
        showAvatarStatus('✕ File too large — max 2 MB', 'var(--crimson)');
        input.value = '';
        return;
    }

    // Show local preview immediately
    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('avatar-preview');
        const initials = document.getElementById('avatar-initials');
        if (initials) initials.remove();

        let img = document.getElementById('avatar-img');
        if (!img) {
            img = document.createElement('img');
            img.id = 'avatar-img';
            preview.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);

    // Upload via fetch with progress
    const form     = document.getElementById('avatar-form');
    const bar      = document.getElementById('avatar-progress-bar');
    const track    = document.getElementById('avatar-progress');
    const formData = new FormData(form);

    // ── Get CSRF token from the meta tag or the hidden input ──
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                   ?? document.querySelector('input[name="_token"]')?.value;

    track.style.display = 'block';
    bar.style.width = '10%';
    showAvatarStatus('Uploading…', 'var(--muted)');

    const xhr = new XMLHttpRequest();
    xhr.open('POST', form.action);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);      // ← was missing
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.addEventListener('progress', function (e) {
        if (e.lengthComputable) {
            const pct = Math.round((e.loaded / e.total) * 90) + 10;
            bar.style.width = pct + '%';
        }
    });

    xhr.onload = function () {
        bar.style.width = '100%';
        if (xhr.status === 200) {                          // ← removed 302
            setTimeout(() => {
                track.style.display = 'none';
                bar.style.width = '0%';
            }, 600);
            showAvatarStatus('✓ Avatar updated', '#34d399');
            setTimeout(() => hideAvatarStatus(), 3000);
        } else {
            track.style.display = 'none';
            // Show the actual error for easier debugging
            console.error('Avatar upload failed:', xhr.status, xhr.responseText);
            showAvatarStatus('✕ Upload failed — try again', 'var(--crimson)');
        }
    };

    xhr.onerror = function () {
        track.style.display = 'none';
        showAvatarStatus('✕ Network error', 'var(--crimson)');
    };

    xhr.send(formData);
}

function showAvatarStatus(msg, color) {
    const el = document.getElementById('avatar-status');
    el.textContent = msg;
    el.style.color = color;
    el.style.display = 'inline';
}
function hideAvatarStatus() {
    document.getElementById('avatar-status').style.display = 'none';
}

// ── Password utilities ───────────────────────────────────────────
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
        { w: '25%', color: 'var(--crimson)',   text: 'Weak' },
        { w: '50%', color: '#D97706',           text: 'Fair' },
        { w: '75%', color: '#60a5fa',           text: 'Good' },
        { w: '100%',color: '#34d399',           text: 'Strong' },
    ];
    const l = levels[Math.min(score - 1, 3)] || levels[0];
    bar.style.width      = l.w;
    bar.style.background = l.color;
    label.textContent    = l.text;
    label.style.color    = l.color;
}
</script>
@endpush

@endsection