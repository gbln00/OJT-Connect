@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div style="max-width:580px;margin:0 auto;">

    <a href="{{ route('admin.users.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted2);font-size:13px;text-decoration:none;margin-bottom:24px;transition:color 0.15s;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to users
    </a>

    <div class="card fade-up">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:rgba(140,14,3,0.08);border:2px solid rgba(140,14,3,0.15);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--crimson);flex-shrink:0;">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <div class="card-title">{{ $user->name }}</div>
                    <div style="margin-top:3px;">
                        <span class="role-badge {{ ['admin'=>'admin','ojt_coordinator'=>'coordinator','company_supervisor'=>'supervisor','student_intern'=>'student'][$user->role] ?? 'student' }}">
                            {{ $user->role_label }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding:24px;">

            @if($errors->any())
                <div style="background:rgba(248,113,113,0.06);border:1px solid rgba(248,113,113,0.3);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">
                    <div style="display:flex;align-items:center;gap:6px;font-weight:600;margin-bottom:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please fix the following errors:
                    </div>
                    @foreach($errors->all() as $error)<div style="margin-top:2px;">· {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')

                {{-- Account section --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Account information
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='{{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }}'">
                    @error('name')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:14px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('email') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='{{ $errors->has('email') ? 'var(--coral)' : 'var(--border2)' }}'">
                    @error('email')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Role</label>
                    <select name="role" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;transition:border 0.15s;"
                            onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        <option value="admin"              {{ old('role', $user->role) === 'admin'              ? 'selected' : '' }}>Admin</option>
                        <option value="ojt_coordinator"    {{ old('role', $user->role) === 'ojt_coordinator'    ? 'selected' : '' }}>OJT Coordinator</option>
                        <option value="company_supervisor" {{ old('role', $user->role) === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                        <option value="student_intern"     {{ old('role', $user->role) === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                    </select>
                    @error('role')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                {{-- Password section --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Change password
                </div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:14px;display:flex;align-items:center;gap:5px;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Leave blank to keep the current password.
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">New password</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="pw-main"
                                   placeholder="Min. 8 characters"
                                   style="width:100%;padding:10px 38px 10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                            <button type="button" onclick="togglePw('pw-main',this)"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Confirm new password</label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="pw-confirm"
                                   placeholder="Repeat new password"
                                   style="width:100%;padding:10px 38px 10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="checkMatch()">
                            <button type="button" onclick="togglePw('pw-confirm',this)"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div id="pw-match-msg" style="font-size:11.5px;margin-top:4px;min-height:16px;"></div>
                    </div>
                </div>

                {{-- Actions --}}
                 <div style="display:flex;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary btn-sm">Add company</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.style.color = isText ? 'var(--muted)' : 'var(--gold)';
    }

    const pwMain    = document.getElementById('pw-main');
    const pwConfirm = document.getElementById('pw-confirm');
    const pwMsg     = document.getElementById('pw-match-msg');

    function checkMatch() {
        if (!pwConfirm.value) { pwMsg.textContent = ''; pwConfirm.style.borderColor = 'var(--border2)'; return; }
        if (pwMain.value === pwConfirm.value) {
            pwMsg.innerHTML             = '<span style="display:flex;align-items:center;gap:4px;"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg> Passwords match</span>';
            pwMsg.style.color           = 'var(--teal)';
            pwConfirm.style.borderColor = 'var(--teal)';
        } else {
            pwMsg.innerHTML             = '<span style="display:flex;align-items:center;gap:4px;"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Passwords do not match</span>';
            pwMsg.style.color           = 'var(--coral)';
            pwConfirm.style.borderColor = 'var(--coral)';
        }
    }

    pwMain.addEventListener('input', checkMatch);
    pwConfirm.addEventListener('input', checkMatch);
</script>

@endsection