@extends('layouts.supervisor-app')
@section('title', 'Evaluate Intern')
@section('page-title', 'Evaluate Intern')
@section('content')

<div style="max-width:720px;">

    {{-- Back link --}}
    <a href="{{ route('supervisor.dashboard') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:20px;">
        ← Back to Dashboard
    </a>

    {{-- Intern info card --}}
    <div class="card" style="padding:20px;margin-bottom:16px;display:flex;align-items:center;gap:16px;">
        <div style="width:48px;height:48px;border-radius:50%;background:var(--gold-dim);border:2px solid rgba(240,180,41,0.3);
                    display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;
                    color:var(--gold);flex-shrink:0;">
            {{ strtoupper(substr($application->student->name ?? 'S', 0, 2)) }}
        </div>
        <div>
            <div style="font-size:15px;font-weight:700;color:var(--text);">{{ $application->student->name ?? '—' }}</div>
            <div style="font-size:12.5px;color:var(--muted);margin-top:2px;">{{ $application->student->email ?? '' }}</div>
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:11px;color:var(--muted);letter-spacing:.5px;">REQUIRED HOURS</div>
            <div style="font-size:15px;font-weight:700;color:var(--blue);margin-top:2px;">
                {{ number_format($application->required_hours) }} hrs
            </div>
        </div>
    </div>

    {{-- Evaluation form --}}
    <div class="card" style="padding:28px;">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:4px;">Intern Evaluation Form</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:24px;">
            Ratings are from 1 (Poor) to 5 (Excellent). This evaluation is final once submitted.
        </div>

        <form method="POST" action="{{ route('supervisor.evaluations.store', $application->id) }}">
            @csrf

            @if($errors->any())
            <div style="background:var(--coral-dim);border:1px solid rgba(248,113,113,0.3);border-radius:8px;
                        padding:12px 16px;margin-bottom:20px;">
                <div style="font-size:12px;font-weight:600;color:var(--coral);margin-bottom:6px;">Please fix the following:</div>
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li style="font-size:12px;color:var(--coral);">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Attendance Rating --}}
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                              letter-spacing:.5px;margin-bottom:10px;">
                    ATTENDANCE RATING <span style="color:var(--coral);">*</span>
                </label>
                <div style="display:flex;gap:10px;">
                    @for($i = 1; $i <= 5; $i++)
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="attendance_rating" value="{{ $i }}"
                               {{ old('attendance_rating') == $i ? 'checked' : '' }}
                               style="display:none;" class="star-radio" data-group="attendance">
                        <div class="star-btn" data-value="{{ $i }}" data-group="attendance"
                             style="text-align:center;padding:10px 6px;border-radius:8px;border:1px solid var(--border2);
                                    background:var(--surface2);cursor:pointer;transition:all .15s;">
                            <div style="font-size:18px;">{{ $i <= 1 ? '★' : '★' }}</div>
                            <div style="font-size:10.5px;color:var(--muted);margin-top:3px;">
                                {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}
                            </div>
                        </div>
                    </label>
                    @endfor
                </div>
                @error('attendance_rating')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Performance Rating --}}
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                              letter-spacing:.5px;margin-bottom:10px;">
                    PERFORMANCE RATING <span style="color:var(--coral);">*</span>
                </label>
                <div style="display:flex;gap:10px;">
                    @for($i = 1; $i <= 5; $i++)
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="performance_rating" value="{{ $i }}"
                               {{ old('performance_rating') == $i ? 'checked' : '' }}
                               style="display:none;" class="star-radio" data-group="performance">
                        <div class="star-btn" data-value="{{ $i }}" data-group="performance"
                             style="text-align:center;padding:10px 6px;border-radius:8px;border:1px solid var(--border2);
                                    background:var(--surface2);cursor:pointer;transition:all .15s;">
                            <div style="font-size:18px;">★</div>
                            <div style="font-size:10.5px;color:var(--muted);margin-top:3px;">
                                {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}
                            </div>
                        </div>
                    </label>
                    @endfor
                </div>
                @error('performance_rating')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:6px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Overall Grade + Recommendation (2 col) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

                {{-- Overall Grade --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                                  letter-spacing:.5px;margin-bottom:6px;">
                        OVERALL GRADE (0–100) <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="number" name="overall_grade" min="0" max="100" step="0.01"
                           value="{{ old('overall_grade') }}"
                           placeholder="e.g. 92.5"
                           style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);
                                  background:var(--surface2);color:var(--text);font-size:13px;outline:none;
                                  font-family:inherit;box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'"
                           required>
                    @error('overall_grade')
                        <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Recommendation --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                                  letter-spacing:.5px;margin-bottom:6px;">
                        RECOMMENDATION <span style="color:var(--coral);">*</span>
                    </label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="recommendation" value="pass"
                                   {{ old('recommendation') === 'pass' ? 'checked' : '' }}
                                   style="display:none;" class="rec-radio">
                            <div class="rec-btn" data-value="pass"
                                 style="text-align:center;padding:9px 8px;border-radius:8px;border:1px solid var(--border2);
                                        background:var(--surface2);cursor:pointer;font-size:13px;font-weight:600;
                                        color:var(--muted);transition:all .15s;">
                                ✓ Pass
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="recommendation" value="fail"
                                   {{ old('recommendation') === 'fail' ? 'checked' : '' }}
                                   style="display:none;" class="rec-radio">
                            <div class="rec-btn" data-value="fail"
                                 style="text-align:center;padding:9px 8px;border-radius:8px;border:1px solid var(--border2);
                                        background:var(--surface2);cursor:pointer;font-size:13px;font-weight:600;
                                        color:var(--muted);transition:all .15s;">
                                ✕ Fail
                            </div>
                        </label>
                    </div>
                    @error('recommendation')
                        <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Remarks --}}
            <div style="margin-bottom:28px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);
                              letter-spacing:.5px;margin-bottom:6px;">
                    REMARKS <span style="font-weight:400;">(optional)</span>
                </label>
                <textarea name="remarks" rows="4"
                          placeholder="Additional comments about the intern's performance, attitude, strengths, or areas for improvement…"
                          style="width:100%;padding:9px 12px;border-radius:8px;border:1px solid var(--border2);
                                 background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;
                                 outline:none;font-family:inherit;box-sizing:border-box;"
                          onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- Warning notice --}}
            <div style="padding:10px 14px;background:var(--surface2);border:1px solid var(--border2);
                        border-radius:8px;font-size:12px;color:var(--muted);margin-bottom:20px;line-height:1.5;">
                ⚠️ <strong style="color:var(--text);">This evaluation is final.</strong>
                Once submitted it cannot be edited. Please review your ratings carefully before submitting.
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:12px;">
                <button type="submit"
                        style="padding:10px 28px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;
                               font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:opacity .15s;"
                        onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                    Submit Evaluation
                </button>
                <a href="{{ route('supervisor.dashboard') }}"
                   style="padding:10px 20px;background:transparent;color:var(--muted);border:1px solid var(--border2);
                          border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

<script>
// Star rating buttons
document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const group = this.dataset.group;
        const value = parseInt(this.dataset.value);

        // Check the hidden radio
        document.querySelector(`input.star-radio[data-group="${group}"][value="${value}"]`).checked = true;

        // Update styles for all buttons in this group
        document.querySelectorAll(`.star-btn[data-group="${group}"]`).forEach(b => {
            const bVal = parseInt(b.dataset.value);
            if (bVal <= value) {
                b.style.background    = 'var(--gold-dim)';
                b.style.borderColor   = 'var(--gold)';
                b.style.color         = 'var(--gold)';
                b.querySelector('div:first-child').style.color = 'var(--gold)';
            } else {
                b.style.background    = 'var(--surface2)';
                b.style.borderColor   = 'var(--border2)';
                b.style.color         = '';
                b.querySelector('div:first-child').style.color = '';
            }
        });
    });
});

// Recommendation buttons
document.querySelectorAll('.rec-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const value = this.dataset.value;
        document.querySelector(`input.rec-radio[value="${value}"]`).checked = true;

        document.querySelectorAll('.rec-btn').forEach(b => {
            const isPass = b.dataset.value === 'pass';
            const isSelected = b.dataset.value === value;
            if (isSelected) {
                b.style.background  = isPass ? 'var(--teal-dim)'  : 'var(--coral-dim)';
                b.style.borderColor = isPass ? 'var(--teal)'      : 'var(--coral)';
                b.style.color       = isPass ? 'var(--teal)'      : 'var(--coral)';
            } else {
                b.style.background  = 'var(--surface2)';
                b.style.borderColor = 'var(--border2)';
                b.style.color       = 'var(--muted)';
            }
        });
    });
});

// Restore old() state on page load (after validation error)
@if(old('attendance_rating'))
    document.querySelector('.star-btn[data-group="attendance"][data-value="{{ old('attendance_rating') }}"]')?.click();
@endif
@if(old('performance_rating'))
    document.querySelector('.star-btn[data-group="performance"][data-value="{{ old('performance_rating') }}"]')?.click();
@endif
@if(old('recommendation'))
    document.querySelector('.rec-btn[data-value="{{ old('recommendation') }}"]')?.click();
@endif
</script>

@endsection