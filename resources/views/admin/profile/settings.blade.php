@extends('layouts.app')
@section('title', 'Account Settings')
@section('page-title', 'Account Settings')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('error') }}
    </div>
@endif

{{-- PROFILE HEADER --}}
<div style="background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:24px;margin-bottom:20px;display:flex;align-items:center;gap:20px;">
    <div style="width:64px;height:64px;border-radius:50%;background:var(--gold-dim);border:2px solid rgba(240,180,41,0.3);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:600;color:var(--gold);flex-shrink:0;">
        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
    </div>
    <div>
        <div style="font-size:17px;font-weight:600;color:var(--text);letter-spacing:-0.3px;">{{ auth()->user()->name }}</div>
        <div style="font-size:12.5px;color:var(--muted);margin-top:3px;">{{ auth()->user()->email }}</div>
        <div style="margin-top:6px;">
            @php
                $roleMap = [
                    'admin'              => ['label' => 'Admin',       'class' => 'admin'],
                    'ojt_coordinator'    => ['label' => 'Coordinator', 'class' => 'coordinator'],
                    'company_supervisor' => ['label' => 'Supervisor',  'class' => 'supervisor'],
                    'student_intern'     => ['label' => 'Student',     'class' => 'student'],
                ];
                $r = $roleMap[auth()->user()->role] ?? ['label' => auth()->user()->role, 'class' => 'student'];
            @endphp
            <span class="role-badge {{ $r['class'] }}">{{ $r['label'] }}</span>
        </div>
    </div>
    <div style="margin-left:auto;text-align:right;">
        <div style="font-size:11px;color:var(--muted);">Member since</div>
        <div style="font-size:13px;color:var(--muted2);margin-top:2px;">{{ auth()->user()->created_at->format('M d, Y') }}</div>
    </div>
</div>

{{-- TWO COLUMN LAYOUT --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

    {{-- LEFT: UPDATE PROFILE INFO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--gold-dim);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    Profile information
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update.profile') }}" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf
            @method('PATCH')

            {{-- Name --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    Full name
                </label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       required
                       style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
                       onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                @error('name')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    Email address
                </label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                       required
                       style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
                       onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                @error('email')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Role (read-only) --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    Role <span style="color:var(--muted);font-weight:400;">(cannot be changed here)</span>
                </label>
                <input type="text" value="{{ $r['label'] }}" disabled
                       style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border);background:var(--bg);color:var(--muted);font-size:13px;cursor:not-allowed;font-family:inherit;">
            </div>

            <div style="padding-top:4px;">
                <button type="submit"
                        style="padding:9px 22px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    Save changes
                </button>
            </div>
        </form>
    </div>

    {{-- RIGHT: CHANGE PASSWORD --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--teal-dim);display:flex;align-items:center;justify-content:center;color:var(--teal);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                    </div>
                    Change password
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.settings.update.password') }}" style="padding:20px;display:flex;flex-direction:column;gap:16px;">
            @csrf
            @method('PATCH')

            {{-- Current password --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    Current password
                </label>
                <div style="position:relative;">
                    <input type="password" name="current_password" id="current_password"
                           required
                           style="width:100%;padding:9px 38px 9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                    <button type="button" onclick="togglePw('current_password', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- New password --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    New password
                </label>
                <div style="position:relative;">
                    <input type="password" name="password" id="new_password"
                           required
                           style="width:100%;padding:9px 38px 9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'"
                           oninput="checkStrength(this.value)">
                    <button type="button" onclick="togglePw('new_password', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                {{-- Strength bar --}}
                <div style="margin-top:8px;">
                    <div style="height:3px;border-radius:4px;background:var(--border);overflow:hidden;">
                        <div id="strength-bar" style="height:100%;width:0%;border-radius:4px;transition:width 0.3s,background 0.3s;"></div>
                    </div>
                    <div id="strength-label" style="font-size:11px;color:var(--muted);margin-top:4px;"></div>
                </div>
                @error('password')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                    Confirm new password
                </label>
                <div style="position:relative;">
                    <input type="password" name="password_confirmation" id="confirm_password"
                           required
                           style="width:100%;padding:9px 38px 9px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                    <button type="button" onclick="togglePw('confirm_password', this)"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="padding-top:4px;">
                <button type="submit"
                        style="padding:9px 22px;background:var(--teal);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                    Update password
                </button>
            </div>
        </form>
    </div>

</div>

{{-- DANGER ZONE --}}
<div class="card" style="margin-top:16px;border-color:rgba(248,113,113,0.2);">
    <div class="card-header" style="border-bottom-color:rgba(248,113,113,0.15);">
        <div class="card-title">
            <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:7px;background:var(--coral-dim);display:flex;align-items:center;justify-content:center;color:var(--coral);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <span style="color:var(--coral);">Danger zone</span>
            </div>
        </div>
    </div>
    <div style="padding:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <div style="font-size:13.5px;font-weight:500;color:var(--text);">Log out of all sessions</div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Sign out from all devices and browsers.</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    style="padding:9px 18px;background:none;border:1px solid var(--coral);color:var(--coral);border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:background 0.15s;"
                    onmouseover="this.style.background='var(--coral-dim)'" onmouseout="this.style.background='none'">
                Log out
            </button>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.style.color = isText ? 'var(--muted)' : 'var(--gold)';
    }

    // Password strength checker
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
            { w: '25%', color: 'var(--coral)',  text: 'Weak' },
            { w: '50%', color: 'var(--gold)',   text: 'Fair' },
            { w: '75%', color: 'var(--blue)',   text: 'Good' },
            { w: '100%',color: 'var(--teal)',   text: 'Strong' },
        ];
        const l = levels[score - 1] || levels[0];
        bar.style.width      = l.w;
        bar.style.background = l.color;
        label.textContent    = l.text;
        label.style.color    = l.color;
    }
</script>

@endsection