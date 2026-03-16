@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<div style="max-width:560px;">

    <a href="{{ route('admin.users.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to users
    </a>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Create new user account</div>
        </div>
        <div style="padding:20px;">

            @if($errors->any())
                <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                    <div style="font-weight:500;margin-bottom:4px;">Please fix the following errors:</div>
                    @foreach($errors->all() as $error)
                        <div>· {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                {{-- Name --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Full name <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. Juan dela Cruz"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                {{-- Email --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Email address <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="e.g. jdelacruz@buksu.edu.ph"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('email') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                {{-- Role --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Role <span style="color:var(--coral);">*</span>
                    </label>
                    <select name="role"
                            style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('role') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;">
                        <option value="">Select a role</option>
                        <option value="ojt_coordinator"    {{ old('role') === 'ojt_coordinator'    ? 'selected' : '' }}>OJT Coordinator</option>
                        <option value="company_supervisor" {{ old('role') === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                        <option value="student_intern"     {{ old('role') === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                    </select>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">Admins can only be created via database seeder.</div>
                </div>

                {{-- Password --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Password <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="password" name="password"
                           placeholder="Min. 8 characters"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('password') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                {{-- Confirm Password --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Confirm password <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="password" name="password_confirmation"
                           placeholder="Repeat password"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit"
                            style="padding:10px 24px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                        Create user
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