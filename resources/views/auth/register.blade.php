<!DOCTYPE html>
<html lang="en" data-appearance="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration — OJTConnect</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:       #0f1117;
            --surface:  #161b27;
            --surface2: #1c2333;
            --border:   rgba(255,255,255,0.07);
            --border2:  rgba(255,255,255,0.11);
            --text:     #e8eaf0;
            --muted:    #6b7280;
            --muted2:   #9ca3af;
            --gold:     #f0b429;
            --gold-dim: rgba(240,180,41,0.12);
            --teal:     #2dd4bf;
            --teal-dim: rgba(45,212,191,0.12);
            --coral:    #f87171;
            --coral-dim:rgba(248,113,113,0.12);
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 40px 16px;
        }
        .register-wrap {
            width: 100%;
            max-width: 560px;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
            justify-content: center;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: var(--gold-dim);
            border: 1px solid rgba(240,180,41,0.3);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: var(--gold); font-size: 16px; font-weight: 700;
        }
        .logo-text { font-size: 18px; font-weight: 600; color: var(--text); }
        .logo-text span { color: var(--gold); }

        /* Card */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }
        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
        }
        .card-title { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 3px; }
        .card-sub { font-size: 12.5px; color: var(--muted); }

        .section-label {
            font-size: 10px; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--muted); margin-bottom: 14px;
        }

        label { display: block; font-size: 12px; font-weight: 500; color: var(--muted2); margin-bottom: 6px; }
        input, select {
            width: 100%; padding: 10px 14px;
            border-radius: 8px; border: 1px solid var(--border2);
            background: var(--surface2); color: var(--text);
            font-size: 13px; outline: none; font-family: inherit;
            transition: border-color 0.15s;
        }
        input:focus, select:focus { border-color: var(--teal); }
        .field-error { font-size: 11.5px; color: var(--coral); margin-top: 4px; }

        .student-id-hint {
            font-size: 11px; color: var(--muted); margin-top: 5px;
            display: flex; align-items: center; gap: 4px;
        }
        #student-id-status { font-size: 11px; margin-top: 5px; }

        .btn-primary {
            width: 100%; padding: 11px;
            background: var(--teal); color: var(--bg);
            border: none; border-radius: 8px;
            font-size: 13px; font-weight: 500;
            cursor: pointer; font-family: inherit;
            transition: opacity 0.15s;
        }
        .btn-primary:hover { opacity: 0.85; }

        .divider { height: 1px; background: var(--border); margin: 20px 0; }

        .login-link {
            text-align: center; margin-top: 16px;
            font-size: 13px; color: var(--muted);
        }
        .login-link a { color: var(--teal); text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }

        .alert-success {
            background: var(--teal-dim); border: 1px solid rgba(45,212,191,0.25);
            color: var(--teal); padding: 12px 16px; border-radius: 8px;
            margin-bottom: 20px; font-size: 13px;
        }
        .alert-error {
            background: var(--coral-dim); border: 1px solid var(--coral);
            color: var(--coral); padding: 12px 16px; border-radius: 8px;
            margin-bottom: 20px; font-size: 13px;
        }
    </style>
</head>
<body>
<div class="register-wrap">

    <div class="logo">
        <div class="logo-icon">O</div>
        <div class="logo-text">OJT<span>Connect</span></div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            @foreach($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">Student Registration</div>
            <div class="card-sub">Register to access the OJT Management System. Your account will be verified by the admin before you can log in.</div>
        </div>

        <form method="POST" action="{{ route('register') }}" style="padding:24px;">
            @csrf

            {{-- Student Information --}}
            <div class="section-label">Student information</div>

            <div style="margin-bottom:16px;">
                <label>Student ID <span style="color:var(--coral);">*</span></label>
                <input type="text" name="student_id" value="{{ old('student_id') }}"
                       id="student-id-input" maxlength="10"
                       placeholder="e.g. 2201101234"
                       oninput="validateStudentId(this.value)">
                <div class="student-id-hint">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Must be exactly 10 digits (numbers only)
                </div>
                <div id="student-id-status"></div>
                @error('student_id')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                <div>
                    <label>First name <span style="color:var(--coral);">*</span></label>
                    <input type="text" name="firstname" value="{{ old('firstname') }}">
                    @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Last name <span style="color:var(--coral);">*</span></label>
                    <input type="text" name="lastname" value="{{ old('lastname') }}">
                    @error('lastname')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div style="grid-column:span 2;">
                    <label>Middle name <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                    <input type="text" name="middlename" value="{{ old('middlename') }}">
                    @error('middlename')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Course <span style="color:var(--coral);">*</span></label>
                    <select name="course">
                        <option value="">Select course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course }}" {{ old('course') === $course ? 'selected' : '' }}>{{ $course }}</option>
                        @endforeach
                    </select>
                    @error('course')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Year level <span style="color:var(--coral);">*</span></label>
                    <select name="year_level">
                        <option value="">Select year</option>
                        @foreach($yearLevels as $year)
                            <option value="{{ $year }}" {{ old('year_level') === $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                    @error('year_level')<div class="field-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="divider"></div>

            {{-- Account credentials --}}
            <div class="section-label">Account credentials</div>

            <div style="margin-bottom:14px;">
                <label>Email address <span style="color:var(--coral);">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com">
                @error('email')<div class="field-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                <div>
                    <label>Password <span style="color:var(--coral);">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 characters">
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label>Confirm password <span style="color:var(--coral);">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password">
                </div>
            </div>

            {{-- Notice --}}
            <div style="background:var(--gold-dim);border:1px solid rgba(240,180,41,0.2);border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:12.5px;color:var(--muted2);line-height:1.6;">
                <strong style="color:var(--gold);">Note:</strong> After registration, your account will be reviewed by the admin. You will only be able to log in once your account has been verified.
            </div>

            <button type="submit" class="btn-primary">Submit registration</button>
        </form>
    </div>

    <div class="login-link">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </div>

</div>

<script>
function validateStudentId(value) {
    const status = document.getElementById('student-id-status');
    const input  = document.getElementById('student-id-input');
    const digits = value.replace(/\D/g, '');

    // Only allow digits
    if (value !== digits) {
        input.value = digits;
        value = digits;
    }

    if (value.length === 0) {
        status.textContent = '';
        input.style.borderColor = 'var(--border2)';
    } else if (value.length < 10) {
        status.textContent = `${value.length}/10 digits`;
        status.style.color = 'var(--gold)';
        input.style.borderColor = 'var(--gold)';
    } else if (value.length === 10) {
        status.textContent = '✓ Valid format';
        status.style.color = 'var(--teal)';
        input.style.borderColor = 'var(--teal)';
    }
}
</script>
</body>
</html>