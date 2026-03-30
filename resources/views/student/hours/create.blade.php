{{-- resources/views/student/hours/create.blade.php --}}
@extends('layouts.student-app')
@section('title', 'Log Hours')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Hour Logs / New Entry
    </span>
</div>

<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Card header --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div>
                <div class="card-title">Log Daily Hours</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Record your time-in and time-out for each session
                </div>
            </div>
            <a href="{{ route('student.hours.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('student.hours.store') }}" style="padding:24px;">
            @csrf

            @if ($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);padding:13px 16px;margin-bottom:24px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">
                    Please fix the following:
                </strong>
                @foreach ($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- Date --}}
            <div class="form-section-divider"><span>Date</span></div>
            <div style="margin-bottom:28px;">
                <label class="form-label">Log date <span style="color:var(--crimson);">✦</span></label>
                <input type="date" name="date"
                       value="{{ old('date', today()->toDateString()) }}"
                       max="{{ today()->toDateString() }}"
                       required
                       class="form-input {{ $errors->has('date') ? 'is-invalid' : '' }}">
                @error('date')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Morning --}}
            <div class="form-section-divider">
                <span class="session-label morning-label">AM</span>
                <span>Morning Session</span>
            </div>

            <div id="morning-toggle-row" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Typical: 8:00 AM – 12:00 PM</div>
                @if (in_array('morning', $todayLogs))
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;letter-spacing:0.06em;">✓ Already logged today</span>
                @else
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                        <input type="checkbox" name="log_morning" id="toggle-morning" value="1"
                               class="toggle-check"
                               data-target="morning-fields"
                               {{ old('log_morning', 1) ? 'checked' : '' }}>
                        <span class="toggle-label" id="label-morning">Log this session</span>
                    </label>
                @endif
            </div>

            @if (!in_array('morning', $todayLogs))
            <div id="morning-fields" style="margin-bottom:28px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Time In</label>
                        <input type="time" name="am_time_in"
                               value="{{ old('am_time_in', '08:00') }}"
                               class="form-input {{ $errors->has('am_time_in') ? 'is-invalid' : '' }}">
                        @error('am_time_in')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Time Out</label>
                        <input type="time" name="am_time_out"
                               value="{{ old('am_time_out', '12:00') }}"
                               class="form-input {{ $errors->has('am_time_out') ? 'is-invalid' : '' }}">
                        @error('am_time_out')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Activities / Description <span style="color:var(--muted);">(optional)</span></label>
                    <textarea name="am_description" class="form-input" rows="2"
                              placeholder="What did you work on this morning?" style="resize:vertical;">{{ old('am_description') }}</textarea>
                </div>
            </div>
            @else
            <div style="padding:14px 16px;background:rgba(52,211,153,0.06);border:1px solid rgba(52,211,153,0.2);margin-bottom:28px;">
                <span style="font-family:'DM Mono',monospace;font-size:11px;color:#34d399;">✓ Morning session already submitted for today</span>
            </div>
            @endif

            {{-- Afternoon --}}
            <div class="form-section-divider">
                <span class="session-label afternoon-label">PM</span>
                <span>Afternoon Session</span>
            </div>

            <div id="afternoon-toggle-row" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">Typical: 1:00 PM – 5:00 PM</div>
                @if (in_array('afternoon', $todayLogs))
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:#34d399;letter-spacing:0.06em;">✓ Already logged today</span>
                @else
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                        <input type="checkbox" name="log_afternoon" id="toggle-afternoon" value="1"
                               class="toggle-check"
                               data-target="afternoon-fields"
                               {{ old('log_afternoon', 1) ? 'checked' : '' }}>
                        <span class="toggle-label" id="label-afternoon">Log this session</span>
                    </label>
                @endif
            </div>

            @if (!in_array('afternoon', $todayLogs))
            <div id="afternoon-fields" style="margin-bottom:28px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">Time In</label>
                        <input type="time" name="pm_time_in"
                               value="{{ old('pm_time_in', '13:00') }}"
                               class="form-input {{ $errors->has('pm_time_in') ? 'is-invalid' : '' }}">
                        @error('pm_time_in')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Time Out</label>
                        <input type="time" name="pm_time_out"
                               value="{{ old('pm_time_out', '17:00') }}"
                               class="form-input {{ $errors->has('pm_time_out') ? 'is-invalid' : '' }}">
                        @error('pm_time_out')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Activities / Description <span style="color:var(--muted);">(optional)</span></label>
                    <textarea name="pm_description" class="form-input" rows="2"
                              placeholder="What did you work on this afternoon?" style="resize:vertical;">{{ old('pm_description') }}</textarea>
                </div>
            </div>
            @else
            <div style="padding:14px 16px;background:rgba(52,211,153,0.06);border:1px solid rgba(52,211,153,0.2);margin-bottom:28px;">
                <span style="font-family:'DM Mono',monospace;font-size:11px;color:#34d399;">✓ Afternoon session already submitted for today</span>
            </div>
            @endif

            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('student.hours.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Submit Log
                    </button>
                </div>
            </div>

        </form>
    </div>

</div>

@push('styles')
<style>
.form-section-divider {
    display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
}
.form-section-divider::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
.form-section-divider::after  { content: ''; flex: 1; height: 1px; background: var(--border); }
.form-section-divider span {
    font-family: 'Barlow Condensed', sans-serif; font-size: 10px; font-weight: 600;
    letter-spacing: 0.22em; text-transform: uppercase; color: var(--muted);
}
.session-label {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 20px; font-size: 9px; font-weight: 700;
    letter-spacing: 0.06em; border: 1px solid; flex-shrink: 0;
}
.morning-label   { background: rgba(254,243,199,0.1); color: #D97706; border-color: rgba(217,119,6,0.4); }
.afternoon-label { background: rgba(219,234,254,0.1); color: #3B82F6; border-color: rgba(59,130,246,0.4); }
.form-input {
    width: 100%; padding: 10px 14px;
    background: var(--surface2); border: 1px solid var(--border2);
    color: var(--text); font-size: 13px; font-family: 'Barlow', sans-serif;
    outline: none; transition: border-color 0.15s;
    box-sizing: border-box; border-radius: 0;
}
.form-input:focus { border-color: var(--crimson); }
.form-input.is-invalid { border-color: var(--crimson); }
.form-label {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 6px;
}
.form-hint  { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.04em; }
.form-error { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--crimson); margin-top: 4px; letter-spacing: 0.04em; }
.toggle-check {
    appearance: none; width: 14px; height: 14px;
    border: 1px solid var(--border2); background: var(--surface2);
    cursor: pointer; position: relative; flex-shrink: 0;
    transition: background 0.15s, border-color 0.15s;
}
.toggle-check:checked {
    background: var(--crimson); border-color: var(--crimson);
}
.toggle-check:checked::after {
    content: ''; position: absolute; left: 3px; top: 0px;
    width: 5px; height: 9px; border: 2px solid white;
    border-left: none; border-top: none; transform: rotate(45deg);
}
</style>
@endpush

<script>
document.querySelectorAll('.toggle-check').forEach(function (chk) {
    var targetId = chk.dataset.target;
    var fields   = document.getElementById(targetId);
    function sync() {
        if (fields) fields.style.display = chk.checked ? '' : 'none';
    }
    sync();
    chk.addEventListener('change', sync);
});
</script>

@endsection