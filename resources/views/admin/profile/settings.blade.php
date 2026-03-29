@extends('layouts.app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')

{{-- PROFILE HEADER --}}
<div style="background:var(--surface);border:1px solid var(--border);padding:24px;margin-bottom:20px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;position:relative;overflow:hidden;" class="fade-up">
    <div style="position:absolute;top:0;left:0;right:0;height:2px;background:var(--crimson);"></div>
    <div style="width:56px;height:56px;flex-shrink:0;border:1px solid rgba(140,14,3,0.45);background:rgba(140,14,3,0.08);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:var(--crimson);">
        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
    </div>
    <div>
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">{{ auth()->user()->name }}</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:3px;letter-spacing:0.05em;">{{ auth()->user()->email }}</div>
        <div style="margin-top:8px;">
            @php
            $roleMap = ['admin'=>'admin','ojt_coordinator'=>'coordinator','company_supervisor'=>'supervisor','student_intern'=>'student'];
            $roleLabel = ['admin'=>'Admin','ojt_coordinator'=>'Coordinator','company_supervisor'=>'Supervisor','student_intern'=>'Student'];
            $r = $roleMap[auth()->user()->role] ?? 'student';
            @endphp
            <span class="role-badge {{ $r }}">{{ $roleLabel[auth()->user()->role] ?? auth()->user()->role }}</span>
        </div>
    </div>
    <div style="margin-left:auto;text-align:right;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">Member since</div>
        <div style="font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:var(--text);margin-top:4px;">{{ auth()->user()->created_at->format('M d, Y') }}</div>
    </div>
</div>

{{-- TWO COLUMN --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;" class="fade-up fade-up-1">

    {{-- PROFILE INFO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Profile information</div>
        </div>
        <form method="POST" action="{{ route('admin.settings.update.profile') }}" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Full name</label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="form-input">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Email address</label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="form-input">
                @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Role <span style="text-transform:none;letter-spacing:0;font-size:10px;color:var(--muted);">(read-only)</span></label>
                <input type="text" value="{{ $roleLabel[auth()->user()->role] ?? auth()->user()->role }}" disabled
                       class="form-input" style="opacity:0.45;cursor:not-allowed;">
            </div>

            <div style="padding-top:4px;">
                <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
            </div>
        </form>
    </div>

    {{-- CHANGE PASSWORD --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Change password</div>
        </div>
        <form method="POST" action="{{ route('admin.settings.update.password') }}" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Current password</label>
                <div style="position:relative;">
                    <input type="password" name="current_password" id="pw-cur" required class="form-input" style="padding-right:38px;">
                    <button type="button" onclick="togglePw('pw-cur',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('current_password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">New password</label>
                <div style="position:relative;">
                    <input type="password" name="password" id="pw-new" required class="form-input" style="padding-right:38px;" oninput="checkStrength(this.value)">
                    <button type="button" onclick="togglePw('pw-new',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div style="margin-top:6px;">
                    <div style="height:2px;background:var(--border2);overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0%;transition:width 0.3s,background 0.3s;"></div>
                    </div>
                    <div id="strength-label" style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;letter-spacing:0.05em;"></div>
                </div>
                @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="form-label">Confirm new password</label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="pw-con" required class="form-input" style="padding-right:38px;">
                    <button type="button" onclick="togglePw('pw-con',this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <div style="padding-top:4px;">
                <button type="submit" class="btn btn-primary btn-sm">Update password</button>
            </div>
        </form>
    </div>

</div>

{{-- DANGER ZONE --}}
<div class="card danger-zone fade-up fade-up-2" style="margin-top:16px;">
    <div class="card-header">
        <div class="card-title" style="color:var(--crimson);display:flex;align-items:center;gap:8px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Danger zone
        </div>
    </div>
    <div style="padding:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:13.5px;font-weight:500;color:var(--text);">Log out of all sessions</div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Sign out from all devices and browsers.</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm">Log out</button>
        </form>
    </div>
</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.style.color = isText ? 'var(--muted)' : 'var(--crimson)';
}
function checkStrength(val) {
    const bar = document.getElementById('strength-bar');
    const lbl = document.getElementById('strength-label');
    if (!val) { bar.style.width = '0%'; lbl.textContent = ''; return; }
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { w: '25%', color: '#c0392b',  text: 'WEAK' },
        { w: '50%', color: '#c9a84c',  text: 'FAIR' },
        { w: '75%', color: '#60a5fa',  text: 'GOOD' },
        { w: '100%',color: '#34d399',  text: 'STRONG' },
    ];
    const l = levels[score - 1] || levels[0];
    bar.style.width = l.w; bar.style.background = l.color;
    lbl.textContent = l.text; lbl.style.color = l.color;
}
</script>

@endsection