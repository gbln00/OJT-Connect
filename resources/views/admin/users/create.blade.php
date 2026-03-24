@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<div style="max-width:640px;">

    <a href="{{ route('admin.users.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;transition:color 0.15s;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to users
    </a>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Create new user account</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                Fill in the details below. Extra fields appear based on the selected role.
            </div>
        </div>

        <div style="padding:24px;">

            {{-- Error block --}}
            @if($errors->any())
                <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">
                    <div style="font-weight:600;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please fix the following errors:
                    </div>
                    @foreach($errors->all() as $error)
                        <div style="margin-top:2px;">· {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                {{-- SECTION: Account Info --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Account Information
                </div>

                {{-- Name + Email --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Full name <span style="color:var(--coral);">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="e.g. Juan dela Cruz"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='{{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }}'">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Email address <span style="color:var(--coral);">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="e.g. jdelacruz@company.com"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('email') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='{{ $errors->has('email') ? 'var(--coral)' : 'var(--border2)' }}'">
                    </div>
                </div>

                {{-- Role --}}
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Role <span style="color:var(--coral);">*</span>
                    </label>
                    <select id="role-select" name="role"
                            style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('role') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;cursor:pointer;"
                            onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        <option value="">Select a role</option>
                        <option value="ojt_coordinator"    {{ old('role') === 'ojt_coordinator'    ? 'selected' : '' }}>OJT Coordinator</option>
                        <option value="company_supervisor" {{ old('role') === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                        <option value="student_intern"     {{ old('role') === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                    </select>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                        Admins can only be created via the database seeder.
                    </div>
                </div>

                {{-- ══════════════════════════════════════════════════════════
                     SECTION: Supervisor Fields (company_supervisor only)
                     ══════════════════════════════════════════════════════════ --}}
                <div id="supervisor-fields" style="display:none;">

                    <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                        Supervisor Details
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Assigned Company <span style="color:var(--coral);">*</span>
                        </label>

                        {{-- Live search filter --}}
                        <input type="text" id="company-search-sup"
                               placeholder="Search companies..."
                               autocomplete="off"
                               style="width:100%;padding:10px 14px;border-radius:8px 8px 0 0;border:1px solid {{ $errors->has('company_id') ? 'var(--coral)' : 'var(--border2)' }};border-bottom:none;background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">

                        <div id="company-list-sup"
                             style="border:1px solid {{ $errors->has('company_id') ? 'var(--coral)' : 'var(--border2)' }};border-radius:0 0 8px 8px;max-height:220px;overflow-y:auto;background:var(--surface2);">
                            @forelse($companies ?? [] as $company)
                                <label id="sup-company-{{ $company->id }}"
                                       style="display:flex;align-items:center;gap:12px;padding:11px 14px;cursor:pointer;border-bottom:1px solid var(--border);transition:background 0.12s;"
                                       onmouseover="this.style.background='var(--surface)'"
                                       onmouseout="this.style.background='transparent'">
                                    <input type="radio" name="company_id" value="{{ $company->id }}"
                                           {{ old('company_id') == $company->id ? 'checked' : '' }}
                                           style="accent-color:var(--gold);flex-shrink:0;">
                                    <div>
                                        <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $company->name }}</div>
                                        @if($company->industry || $company->address)
                                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">
                                            {{ implode(' · ', array_filter([$company->industry, $company->address])) }}
                                        </div>
                                        @endif
                                    </div>
                                </label>
                            @empty
                                <div style="padding:18px;text-align:center;color:var(--muted);font-size:13px;">
                                    No active companies available.
                                    <a href="{{ route('admin.companies.create') }}" style="color:var(--gold);text-decoration:none;">Add one →</a>
                                </div>
                            @endforelse
                        </div>

                        @error('company_id')
                            <div style="font-size:11.5px;color:var(--coral);margin-top:5px;">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                {{-- END Supervisor Fields --}}

                {{-- ══════════════════════════════════════════════════════════
                     SECTION: Student Profile (student_intern only)
                     ══════════════════════════════════════════════════════════ --}}
                <div id="student-fields" style="display:none;">

                    <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                        Student Profile
                    </div>

                    {{-- Student ID + Course --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                                Student ID <span style="color:var(--coral);">*</span>
                            </label>
                            <input type="text" name="student_id" value="{{ old('student_id') }}"
                                   placeholder="e.g. 2021-00123"
                                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('student_id') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                                Course <span style="color:var(--coral);">*</span>
                            </label>
                            <input type="text" name="course" value="{{ old('course') }}"
                                   placeholder="e.g. BSIT"
                                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('course') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        </div>
                    </div>

                    {{-- Year Level + Section --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                                Year Level <span style="color:var(--coral);">*</span>
                            </label>
                            <select name="year_level"
                                    style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('year_level') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;transition:border 0.15s;"
                                    onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                                <option value="">Select year</option>
                                <option value="1st Year" {{ old('year_level') === '1st Year' ? 'selected' : '' }}>1st Year</option>
                                <option value="2nd Year" {{ old('year_level') === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                                <option value="3rd Year" {{ old('year_level') === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                                <option value="4th Year" {{ old('year_level') === '4th Year' ? 'selected' : '' }}>4th Year</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                                Section <span style="color:var(--coral);">*</span>
                            </label>
                            <input type="text" name="section" value="{{ old('section') }}"
                                   placeholder="e.g. 3F"
                                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('section') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        </div>
                    </div>

                    {{-- Phone + Required Hours --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   placeholder="e.g. 09171234567"
                                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                                Required Hours <span style="color:var(--coral);">*</span>
                            </label>
                            <input type="number" name="required_hours" value="{{ old('required_hours', 486) }}"
                                   style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">Default is 486 hours.</div>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div style="margin-bottom:24px;">
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Address</label>
                        <textarea name="address" rows="2"
                                  placeholder="e.g. Malaybalay City, Bukidnon"
                                  style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;resize:vertical;"
                                  onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">{{ old('address') }}</textarea>
                    </div>

                </div>
                {{-- END Student Fields --}}

                {{-- SECTION: Password --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Password
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Password <span style="color:var(--coral);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="pw-main"
                                   placeholder="Min. 8 characters"
                                   style="width:100%;padding:10px 38px 10px 14px;border-radius:8px;border:1px solid {{ $errors->has('password') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                            <button type="button" onclick="togglePw('pw-main',this)"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Confirm password <span style="color:var(--coral);">*</span>
                        </label>
                        <div style="position:relative;">
                            {{-- IMPORTANT: name must be exactly "password_confirmation" for Laravel's 'confirmed' rule --}}
                            <input type="password" name="password_confirmation" id="pw-confirm"
                                   placeholder="Repeat password"
                                   style="width:100%;padding:10px 38px 10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                                   onfocus="this.style.borderColor='var(--gold)'" onblur="checkMatch()">
                            <button type="button" onclick="togglePw('pw-confirm',this)"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div id="pw-match-msg" style="font-size:11.5px;margin-top:4px;min-height:16px;"></div>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:10px;border-top:1px solid var(--border2);padding-top:20px;">
                    <button type="submit"
                            style="padding:10px 28px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                        Create user
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       style="padding:10px 20px;border:1px solid var(--border2);border-radius:8px;color:var(--muted2);font-size:13px;text-decoration:none;transition:border-color 0.15s;"
                       onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // ── Password visibility toggle ────────────────────────────────
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.style.color = isText ? 'var(--muted)' : 'var(--gold)';
    }

    // ── Live password match indicator ─────────────────────────────
    const pwMain    = document.getElementById('pw-main');
    const pwConfirm = document.getElementById('pw-confirm');
    const pwMsg     = document.getElementById('pw-match-msg');

    function checkMatch() {
        if (!pwConfirm.value) { pwMsg.textContent = ''; pwConfirm.style.borderColor = 'var(--border2)'; return; }
        if (pwMain.value === pwConfirm.value) {
            pwMsg.textContent            = '✓ Passwords match';
            pwMsg.style.color            = 'var(--teal)';
            pwConfirm.style.borderColor  = 'var(--teal)';
        } else {
            pwMsg.textContent            = '✕ Passwords do not match';
            pwMsg.style.color            = 'var(--coral)';
            pwConfirm.style.borderColor  = 'var(--coral)';
        }
    }

    pwMain.addEventListener('input',    checkMatch);
    pwConfirm.addEventListener('input', checkMatch);

    // ── Role-based field toggling ─────────────────────────────────
    const roleSelect       = document.getElementById('role-select');
    const supervisorFields = document.getElementById('supervisor-fields');
    const studentFields    = document.getElementById('student-fields');
    const studentRequired  = ['student_id', 'course', 'year_level', 'section', 'required_hours'];

    function animateIn(el) {
        el.style.display   = 'block';
        el.style.opacity   = '0';
        el.style.transform = 'translateY(-6px)';
        requestAnimationFrame(() => {
            el.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            el.style.opacity    = '1';
            el.style.transform  = 'translateY(0)';
        });
    }
    function animateOut(el) {
        el.style.opacity = '0';
        setTimeout(() => { el.style.display = 'none'; }, 200);
    }

    function toggleRoleFields() {
        const role         = roleSelect.value;
        const isSupervisor = role === 'company_supervisor';
        const isStudent    = role === 'student_intern';

        // Supervisor section
        isSupervisor ? animateIn(supervisorFields) : animateOut(supervisorFields);
        if (!isSupervisor) {
            document.querySelectorAll('#company-list-sup input[type="radio"]').forEach(r => r.checked = false);
        }

        // Student section
        isStudent ? animateIn(studentFields) : animateOut(studentFields);

        // Toggle required on student fields
        studentFields.querySelectorAll('input, select, textarea').forEach(el => {
            if (studentRequired.includes(el.name)) el.required = isStudent;
        });
    }

    roleSelect.addEventListener('change', toggleRoleFields);
    // Restore on validation failure
    toggleRoleFields();

    // ── Company live search (supervisor) ──────────────────────────
    document.getElementById('company-search-sup').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#company-list-sup label[id^="sup-company-"]').forEach(label => {
            label.style.display = label.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    });
</script>

@endsection