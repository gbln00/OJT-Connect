@extends('layouts.coordinator-app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')

{{-- ══════════════════════════════════════════════════
     PROFILE HEADER CARD
══════════════════════════════════════════════════ --}}
<div style="background:var(--surface);border:1px solid var(--border);padding:24px 28px;margin-bottom:20px;
            display:flex;align-items:center;gap:24px;flex-wrap:wrap;position:relative;overflow:hidden;"
     class="fade-up">

    {{-- Crimson top accent bar --}}
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--crimson);"></div>

    {{-- AVATAR --}}
    <div style="position:relative;flex-shrink:0;" id="avatar-wrapper">
        @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar_url }}"
                alt="Avatar"
                id="avatar-preview"
                style="width:72px;height:72px;object-fit:cover;
                        border:2px solid rgba(140,14,3,0.45);display:block;"
                onerror="this.style.display='none';
                        document.getElementById('avatar-initials').style.display='flex';">
        @else
            <div id="avatar-initials"
                 style="width:72px;height:72px;flex-shrink:0;
                        border:2px solid rgba(140,14,3,0.45);
                        background:rgba(140,14,3,0.08);
                        display:flex;align-items:center;justify-content:center;
                        font-family:'Playfair Display',serif;
                        font-size:24px;font-weight:900;color:var(--crimson);">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <img id="avatar-preview"
                 style="width:72px;height:72px;object-fit:cover;
                        border:2px solid rgba(140,14,3,0.45);display:none;">
        @endif

        {{-- Camera overlay button --}}
        <label for="avatar-input"
               style="position:absolute;bottom:-6px;right:-6px;
                      width:24px;height:24px;
                      background:var(--crimson);
                      border:2px solid var(--bg);
                      display:flex;align-items:center;justify-content:center;
                      cursor:pointer;transition:background 0.15s;"
               title="Change avatar"
               onmouseover="this.style.background='#a81004'"
               onmouseout="this.style.background='var(--crimson)'">
            <svg width="11" height="11" fill="none" stroke="rgba(255,255,255,0.9)"
                 stroke-width="2" viewBox="0 0 24 24">
                <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                <circle cx="12" cy="13" r="4"/>
            </svg>
        </label>
        <input type="file" id="avatar-input" accept="image/*" style="display:none;"
               onchange="previewAvatar(this)">
    </div>

    {{-- NAME / EMAIL / ROLE --}}
    <div style="flex:1;min-width:0;">
        <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;
                    color:var(--text);line-height:1.2;">
            {{ auth()->user()->name }}
        </div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);
                    margin-top:4px;letter-spacing:0.05em;">
            {{ auth()->user()->email }}
        </div>
        <div style="margin-top:10px;">
            <span class="role-badge coordinator">OJT Coordinator</span>
        </div>
    </div>

    {{-- MEMBER SINCE --}}
    <div style="text-align:right;flex-shrink:0;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                    text-transform:uppercase;color:var(--muted);">Member since</div>
        <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;
                    color:var(--text);margin-top:4px;">
            {{ auth()->user()->created_at->format('M d, Y') }}
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     AVATAR UPLOAD PENDING STRIP  (shows after picking a file)
══════════════════════════════════════════════════ --}}
<div id="avatar-pending-bar"
     style="display:none;background:var(--gold-dim);border:1px solid var(--gold-border);
            padding:12px 16px;margin-bottom:16px;
            align-items:center;gap:12px;" class="fade-up">
    <svg width="14" height="14" fill="none" stroke="var(--gold)" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--gold);
                 letter-spacing:0.05em;flex:1;">
        New avatar selected — save below to apply.
    </span>
    <form id="avatar-upload-form" method="POST"
          action="{{ route('coordinator.settings.avatar') }}"
          enctype="multipart/form-data">
        @csrf
        <input type="file" id="avatar-upload-input" name="avatar" style="display:none;">
        <button type="submit" class="btn btn-sm"
                style="background:var(--gold);color:#000;border-color:var(--gold);font-size:10px;">
            Save avatar
        </button>
    </form>
    <button type="button" onclick="cancelAvatar()"
            class="btn btn-ghost btn-sm" style="font-size:10px;">
        Cancel
    </button>
</div>

{{-- ══════════════════════════════════════════════════
     TWO-COLUMN: PROFILE INFO + CHANGE PASSWORD
══════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;"
     class="fade-up fade-up-1">

    {{-- ── PROFILE INFO ──────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Profile information</div>
        </div>

        <form method="POST" action="{{ route('coordinator.settings.update.profile') }}"
              style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Full name</label>
                <input type="text" name="name"
                       value="{{ old('name', auth()->user()->name) }}"
                       required class="form-input">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Email address</label>
                <input type="email" name="email"
                       value="{{ old('email', auth()->user()->email) }}"
                       required class="form-input">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">
                    Role
                    <span style="text-transform:none;letter-spacing:0;font-size:10px;color:var(--muted);">
                        (read-only)
                    </span>
                </label>
                <input type="text" value="OJT Coordinator" disabled
                       class="form-input" style="opacity:0.45;cursor:not-allowed;">
            </div>

            <div style="padding-top:4px;">
                <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
            </div>
        </form>
    </div>

    {{-- ── CHANGE PASSWORD ───────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Change password</div>
        </div>

        <form method="POST" action="{{ route('coordinator.settings.update.password') }}"
              style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Current password</label>
                <div style="position:relative;">
                    <input type="password" name="current_password" id="pw-cur"
                           required class="form-input" style="padding-right:38px;">
                    <button type="button" onclick="togglePw('pw-cur',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                   background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">New password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="pw-new"
                           required class="form-input" style="padding-right:38px;"
                           oninput="checkStrength(this.value)">
                    <button type="button" onclick="togglePw('pw-new',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                   background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                <div style="margin-top:6px;">
                    <div style="height:2px;background:var(--border2);overflow:hidden;">
                        <div id="strength-bar"
                             style="height:100%;width:0%;transition:width 0.3s,background 0.3s;">
                        </div>
                    </div>
                    <div id="strength-label"
                         style="font-family:'DM Mono',monospace;font-size:10px;
                                color:var(--muted);margin-top:3px;letter-spacing:0.05em;">
                    </div>
                </div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Confirm new password</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="pw-con"
                           required class="form-input" style="padding-right:38px;">
                    <button type="button" onclick="togglePw('pw-con',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                   background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="padding-top:4px;">
                <button type="submit" class="btn btn-primary btn-sm">Update password</button>
            </div>
        </form>
    </div>

</div>

{{-- ══════════════════════════════════════════════════
     AVATAR MANAGEMENT CARD
══════════════════════════════════════════════════ --}}
<div class="card fade-up fade-up-2" style="margin-top:16px;">
    <div class="card-header">
        <div class="card-title">Profile avatar</div>
        <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;
                     text-transform:uppercase;color:var(--muted);
                     border:1px solid var(--border2);padding:2px 7px;">
            JPG · PNG · GIF · WEBP · max 2 MB
        </span>
    </div>

    <div style="padding:20px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">

        {{-- Current avatar preview --}}
        <div style="position:relative;flex-shrink:0;">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar_url }}"
                     alt="Current avatar"
                     style="width:80px;height:80px;object-fit:cover;
                            border:1px solid rgba(140,14,3,0.35);">
            @else
                <div style="width:80px;height:80px;
                            border:1px solid rgba(140,14,3,0.35);
                            background:rgba(140,14,3,0.08);
                            display:flex;align-items:center;justify-content:center;
                            font-family:'Playfair Display',serif;
                            font-size:26px;font-weight:900;color:var(--crimson);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
            @endif
        </div>

        {{-- Upload controls --}}
        <div style="flex:1;min-width:200px;">
            <div style="font-size:13px;color:var(--text);font-weight:500;margin-bottom:6px;">
                {{ auth()->user()->avatar ? 'Replace your avatar' : 'Upload a profile picture' }}
            </div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                        margin-bottom:14px;line-height:1.6;">
                Square images work best. Minimum 100×100 px recommended.
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <label for="avatar-input-card"
                       class="btn btn-primary btn-sm"
                       style="cursor:pointer;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="17,8 12,3 7,8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Upload image
                </label>

                @if(auth()->user()->avatar)
                <form method="POST" action="{{ route('coordinator.settings.avatar.delete') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Remove your avatar?')">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="3,6 5,6 21,6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6"/>
                        </svg>
                        Remove
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Hidden form for this card's upload --}}
        <form id="avatar-card-form" method="POST"
              action="{{ route('coordinator.settings.avatar') }}"
              enctype="multipart/form-data" style="display:none;">
            @csrf
            <input type="file" id="avatar-input-card" name="avatar"
                   accept="image/*" onchange="submitAvatarCard(this)">
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     DANGER ZONE
══════════════════════════════════════════════════ --}}
<div class="card danger-zone fade-up fade-up-3" style="margin-top:16px;">
    <div class="card-header">
        <div class="card-title" style="color:var(--crimson);display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Danger zone
        </div>
    </div>
    <div style="padding:20px;display:flex;align-items:center;justify-content:space-between;
                flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:13.5px;font-weight:500;color:var(--text);">
                Log out of all sessions
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">
                Sign out from all devices and browsers.
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Log out</button>
        </form>
    </div>
</div>

@push('styles')
<style>
/* Responsive two-column collapses on narrow screens */
@media (max-width: 640px) {
    .settings-grid { grid-template-columns: 1fr !important; }
}
/* Danger zone subtle border */
.danger-zone {
    border-color: rgba(140, 14, 3, 0.18);
}
</style>
@endpush

<script>
/* ── Password visibility toggle ── */
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.style.color = isText ? 'var(--muted)' : 'var(--crimson)';
}

/* ── Password strength meter ── */
function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');
    if (!val) { bar.style.width = '0%'; lbl.textContent = ''; return; }
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { w: '25%',  color: '#c0392b', text: 'WEAK'   },
        { w: '50%',  color: '#c9a84c', text: 'FAIR'   },
        { w: '75%',  color: '#60a5fa', text: 'GOOD'   },
        { w: '100%', color: '#34d399', text: 'STRONG' },
    ];
    const l = levels[Math.max(score - 1, 0)];
    bar.style.width      = l.w;
    bar.style.background = l.color;
    lbl.textContent      = l.text;
    lbl.style.color      = l.color;
}

/* ── Header avatar live preview ── */
let pendingFile = null;

function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    pendingFile = input.files[0];

    const reader = new FileReader();
    reader.onload = function (e) {
        // Update header preview image
        const preview   = document.getElementById('avatar-preview');
        const initials  = document.getElementById('avatar-initials');
        if (preview)  { preview.src = e.target.result; preview.style.display = 'block'; }
        if (initials) { initials.style.display = 'none'; }

        // Show pending bar
        const bar = document.getElementById('avatar-pending-bar');
        bar.style.display = 'flex';

        // Wire the file into the upload form
        const dt = new DataTransfer();
        dt.items.add(pendingFile);
        document.getElementById('avatar-upload-input').files = dt.files;
    };
    reader.readAsDataURL(pendingFile);
}

function cancelAvatar() {
    const bar = document.getElementById('avatar-pending-bar');
    bar.style.display = 'none';
    pendingFile = null;
    // Restore original avatar display
    location.reload();
}

/* ── Avatar card upload (auto-submit on file pick) ── */
function submitAvatarCard(input) {
    if (input.files && input.files[0]) {
        document.getElementById('avatar-card-form').submit();
    }
}
</script>
@endsection