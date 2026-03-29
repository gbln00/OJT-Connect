@extends('layouts.app')
@section('title', 'Add User')
@section('page-title', 'Add User')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Users / New Account
        </span>
    </div>

    {{-- Main card --}}
    <div class="card fade-up fade-up-1">

        <div class="card-header">
            <div>
                <div class="card-title">Create new user account</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Extra fields appear based on the selected role
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" style="padding:24px;">
            @csrf

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

            {{-- ── Account Information ── --}}
            <div class="form-section-divider"><span>Account Information</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Full name <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. Juan dela Cruz"
                           class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Email address <span style="color:var(--crimson);">✦</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="e.g. jdelacruz@institution.edu.ph"
                           class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Role <span style="color:var(--crimson);">✦</span></label>
                <select id="role-select" name="role"
                        class="form-input {{ $errors->has('role') ? 'is-invalid' : '' }}"
                        style="cursor:pointer;">
                    <option value="">Select a role</option>
                    <option value="ojt_coordinator"    {{ old('role') === 'ojt_coordinator'    ? 'selected' : '' }}>OJT Coordinator</option>
                    <option value="company_supervisor" {{ old('role') === 'company_supervisor' ? 'selected' : '' }}>Company Supervisor</option>
                    <option value="student_intern"     {{ old('role') === 'student_intern'     ? 'selected' : '' }}>Student Intern</option>
                </select>
                <div class="form-hint">// Admins can only be created via the database seeder.</div>
                @error('role')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- ── Supervisor Fields ── --}}
            <div id="supervisor-fields" style="display:none;">
                <div class="form-section-divider"><span>Supervisor Details</span></div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Assigned company <span style="color:var(--crimson);">✦</span></label>

                    <input type="text" id="company-search-sup"
                           placeholder="Search companies..."
                           autocomplete="off"
                           class="form-input {{ $errors->has('company_id') ? 'is-invalid' : '' }}"
                           style="border-bottom:none;">

                    <div id="company-list-sup"
                         style="border:1px solid var(--border2);border-top:none;max-height:220px;overflow-y:auto;background:var(--surface2);">
                        @forelse($companies ?? [] as $company)
                            <label id="sup-company-{{ $company->id }}"
                                   style="display:flex;align-items:center;gap:12px;padding:11px 14px;cursor:pointer;border-bottom:1px solid var(--border);transition:background 0.12s;"
                                   onmouseover="this.style.background='var(--surface3)'"
                                   onmouseout="this.style.background='transparent'">
                                <input type="radio" name="company_id" value="{{ $company->id }}"
                                       {{ old('company_id') == $company->id ? 'checked' : '' }}
                                       style="accent-color:var(--crimson);flex-shrink:0;">
                                <div>
                                    <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $company->name }}</div>
                                    @if($company->industry || $company->address)
                                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">
                                        {{ implode(' · ', array_filter([$company->industry, $company->address])) }}
                                    </div>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div style="padding:18px;text-align:center;color:var(--muted);font-size:13px;">
                                No active companies.
                                <a href="{{ route('admin.companies.create') }}" style="color:var(--crimson);text-decoration:none;font-weight:500;">Add one →</a>
                            </div>
                        @endforelse
                    </div>
                    @error('company_id')<div class="form-error" style="margin-top:5px;">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ── Student Profile ── --}}
            <div id="student-fields" style="display:none;">
                <div class="form-section-divider"><span>Student Profile</span></div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Student ID <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="student_id" value="{{ old('student_id') }}"
                               placeholder="e.g. 2021-00123"
                               class="form-input {{ $errors->has('student_id') ? 'is-invalid' : '' }}">
                        @error('student_id')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Course <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="course" value="{{ old('course') }}"
                               placeholder="e.g. BSIT"
                               class="form-input {{ $errors->has('course') ? 'is-invalid' : '' }}">
                        @error('course')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Year level <span style="color:var(--crimson);">✦</span></label>
                        <select name="year_level"
                                class="form-input {{ $errors->has('year_level') ? 'is-invalid' : '' }}"
                                style="cursor:pointer;">
                            <option value="">Select year</option>
                            @foreach(['1st Year','2nd Year','3rd Year','4th Year'] as $yr)
                            <option value="{{ $yr }}" {{ old('year_level') === $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                        @error('year_level')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Section <span style="color:var(--crimson);">✦</span></label>
                        <input type="text" name="section" value="{{ old('section') }}"
                               placeholder="e.g. 3F"
                               class="form-input {{ $errors->has('section') ? 'is-invalid' : '' }}">
                        @error('section')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                               placeholder="e.g. 09171234567"
                               class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Required hours <span style="color:var(--crimson);">✦</span></label>
                        <input type="number" name="required_hours" value="{{ old('required_hours', 486) }}"
                               class="form-input">
                        <div class="form-hint">// Default: 486 hours</div>
                    </div>
                </div>

                <div style="margin-bottom:28px;">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="2"
                              placeholder="e.g. Malaybalay City, Bukidnon"
                              class="form-input"
                              style="resize:vertical;font-family:inherit;">{{ old('address') }}</textarea>
                </div>
            </div>

            {{-- ── Password ── --}}
            <div class="form-section-divider"><span>Password</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">Password <span style="color:var(--crimson);">✦</span></label>
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
                    <label class="form-label">Confirm password <span style="color:var(--crimson);">✦</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="pw-confirm"
                               placeholder="Repeat password"
                               class="form-input"
                               style="padding-right:38px;"
                               oninput="checkMatch()">
                        <button type="button" onclick="togglePw('pw-confirm',this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--muted);cursor:pointer;padding:2px;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div id="pw-match-msg" style="font-family:'DM Mono',monospace;font-size:10px;margin-top:4px;min-height:16px;letter-spacing:0.04em;"></div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Create user
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>

@push('styles')
<style>
.form-section-divider {
    display: flex; align-items: center; gap: 14px;
    margin-bottom: 16px;
}
.form-section-divider::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
.form-section-divider::after  { content: ''; flex: 1; height: 1px; background: var(--border); }
.form-section-divider span {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 10px; font-weight: 600;
    letter-spacing: 0.22em; text-transform: uppercase;
    color: var(--muted);
}
.form-input {
    width: 100%;
    padding: 10px 14px;
    background: var(--surface2);
    border: 1px solid var(--border2);
    color: var(--text);
    font-size: 13px;
    font-family: 'Barlow', sans-serif;
    outline: none;
    transition: border-color 0.15s;
    box-sizing: border-box;
    border-radius: 0;
}
.form-input:focus { border-color: var(--crimson); }
.form-input.is-invalid { border-color: var(--crimson); }
.form-label {
    display: block;
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em;
    text-transform: uppercase; color: var(--muted);
    margin-bottom: 6px;
}
.form-hint  { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); margin-top: 4px; letter-spacing: 0.04em; }
.form-error { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--crimson); margin-top: 4px; letter-spacing: 0.04em; }
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
    isSupervisor ? animateIn(supervisorFields) : animateOut(supervisorFields);
    if (!isSupervisor) {
        document.querySelectorAll('#company-list-sup input[type="radio"]').forEach(r => r.checked = false);
    }
    isStudent ? animateIn(studentFields) : animateOut(studentFields);
    studentFields.querySelectorAll('input, select, textarea').forEach(el => {
        if (studentRequired.includes(el.name)) el.required = isStudent;
    });
}
roleSelect.addEventListener('change', toggleRoleFields);
toggleRoleFields();

document.getElementById('company-search-sup').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#company-list-sup label[id^="sup-company-"]').forEach(label => {
        label.style.display = label.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
    });
});
</script>

@endsection