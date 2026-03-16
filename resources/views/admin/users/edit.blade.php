@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div style="max-width:560px;">

    <a href="{{ route('admin.users.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to users
    </a>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Edit — {{ $user->name }}</div>
            <span class="role-badge {{ ['admin'=>'admin','ojt_coordinator'=>'coordinator','company_supervisor'=>'supervisor','student_intern'=>'student'][$user->role] ?? 'student' }}">
                {{ $user->role_label }}
            </span>
        </div>
        <div style="padding:20px;">

            @if($errors->any())
                <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                    @foreach($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PUT')

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Email address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Role</label>
                    <select name="role" style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                        <option value="admin"              {{ old('role', $user->role) === 'admin'              ? 'selected' : '' }}>Admin</option>
                        <option value="ojt_coordinator"    {{ old('role', $user->role) === 'ojt_coordinator'    ? 'selected' : '' }}>OJT Coordinator</option>
                        <option value="company_supervisor" {{ old('role', $user->role) === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                        <option value="student_intern"     {{ old('role', $user->role) === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                    </select>
                </div>

                <div style="height:1px;background:var(--border);margin:20px 0;"></div>
                <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">Leave password fields blank to keep the current password.</div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">New password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Confirm new password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat new password"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit"
                            style="padding:10px 24px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                        Save changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       style="padding:10px 20px;border:1px solid var(--border2);border-radius:8px;color:var(--muted2);font-size:13px;text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection