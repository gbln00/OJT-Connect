@extends('layouts.supervisor-app')
@section('title', 'Evaluate Intern')
@section('page-title', 'Evaluate Intern')

@section('content')

<div style="max-width:720px;">

    {{-- EYEBROW --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Evaluations / New
        </span>
        <a href="{{ route('supervisor.evaluations.index') }}"
           style="margin-left:auto;display:inline-flex;align-items:center;gap:4px;
                  font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;
                  letter-spacing:0.1em;text-transform:uppercase;color:var(--muted);text-decoration:none;">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Back
        </a>
    </div>

    {{-- INTERN INFO CARD --}}
    <div class="card fade-up fade-up-1" style="margin-bottom:12px;">
        <div style="padding:20px;display:flex;align-items:center;gap:16px;">
            <div style="width:48px;height:48px;flex-shrink:0;border:1px solid var(--gold-border);
                        background:var(--gold-dim);display:flex;align-items:center;justify-content:center;
                        font-family:'Playfair Display',serif;font-size:18px;font-weight:900;color:var(--gold);">
                {{ strtoupper(substr($application->student->name ?? 'S', 0, 2)) }}
            </div>
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);">
                    {{ $application->student->name ?? '—' }}
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:3px;">
                    {{ $application->student->email ?? '' }}
                </div>
            </div>
            <div style="margin-left:auto;text-align:right;">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);">
                    Required hours
                </div>
                <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:900;color:var(--blue);margin-top:3px;">
                    {{ number_format($application->required_hours) }}
                    <span style="font-family:'DM Mono',monospace;font-size:12px;font-weight:400;color:var(--muted);">hrs</span>
                </div>
            </div>
        </div>
    </div>

    {{-- EVALUATION FORM --}}
    <div class="card fade-up fade-up-2">

        <div class="card-header">
            <div>
                <div class="card-title">Intern Evaluation Form</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Ratings 1–5 · This evaluation is final once submitted
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('supervisor.evaluations.store', $application->id) }}" style="padding:24px;">
            @csrf

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);
                        padding:13px 16px;margin-bottom:24px;font-size:13px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;
                               letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">
                    Please fix the following:
                </strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- ── Attendance Rating ── --}}
            <div class="form-section-divider"><span>Attendance Rating</span></div>

            <div style="margin-bottom:28px;">
                <div style="display:flex;gap:10px;">
                    @for($i = 1; $i <= 5; $i++)
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="attendance_rating" value="{{ $i }}"
                               {{ old('attendance_rating') == $i ? 'checked' : '' }}
                               style="display:none;" class="star-radio" data-group="attendance">
                        <div class="star-btn" data-value="{{ $i }}" data-group="attendance"
                             style="text-align:center;padding:12px 6px;border:1px solid var(--border2);
                                    background:var(--surface2);cursor:pointer;transition:all .15s;">
                            <div style="font-size:18px;color:var(--muted);">★</div>
                            <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:4px;letter-spacing:0.05em;">
                                {{ ['','Poor','Fair','Good','V.Good','Excellent'][$i] }}
                            </div>
                        </div>
                    </label>
                    @endfor
                </div>
                @error('attendance_rating')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Performance Rating ── --}}
            <div class="form-section-divider"><span>Performance Rating</span></div>

            <div style="margin-bottom:28px;">
                <div style="display:flex;gap:10px;">
                    @for($i = 1; $i <= 5; $i++)
                    <label style="flex:1;cursor:pointer;">
                        <input type="radio" name="performance_rating" value="{{ $i }}"
                               {{ old('performance_rating') == $i ? 'checked' : '' }}
                               style="display:none;" class="star-radio" data-group="performance">
                        <div class="star-btn" data-value="{{ $i }}" data-group="performance"
                             style="text-align:center;padding:12px 6px;border:1px solid var(--border2);
                                    background:var(--surface2);cursor:pointer;transition:all .15s;">
                            <div style="font-size:18px;color:var(--muted);">★</div>
                            <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);margin-top:4px;letter-spacing:0.05em;">
                                {{ ['','Poor','Fair','Good','V.Good','Excellent'][$i] }}
                            </div>
                        </div>
                    </label>
                    @endfor
                </div>
                @error('performance_rating')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── Grade & Recommendation ── --}}
            <div class="form-section-divider"><span>Grade & Recommendation</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">

                <div>
                    <label class="form-label">Overall Grade (0–100) <span style="color:var(--crimson);">✦</span></label>
                    <input type="number" name="overall_grade" min="0" max="100" step="0.01"
                           value="{{ old('overall_grade') }}"
                           placeholder="e.g. 92.5"
                           class="form-input {{ $errors->has('overall_grade') ? 'is-invalid' : '' }}"
                           style="border-radius:0;"
                           required>
                    @error('overall_grade')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Recommendation <span style="color:var(--crimson);">✦</span></label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="recommendation" value="pass"
                                   {{ old('recommendation') === 'pass' ? 'checked' : '' }}
                                   style="display:none;" class="rec-radio">
                            <div class="rec-btn" data-value="pass"
                                 style="text-align:center;padding:10px 8px;border:1px solid var(--border2);
                                        background:var(--surface2);cursor:pointer;font-size:13px;font-weight:600;
                                        color:var(--muted);transition:all .15s;font-family:'Barlow Condensed',sans-serif;
                                        letter-spacing:0.08em;text-transform:uppercase;">
                                ✓ Pass
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="recommendation" value="fail"
                                   {{ old('recommendation') === 'fail' ? 'checked' : '' }}
                                   style="display:none;" class="rec-radio">
                            <div class="rec-btn" data-value="fail"
                                 style="text-align:center;padding:10px 8px;border:1px solid var(--border2);
                                        background:var(--surface2);cursor:pointer;font-size:13px;font-weight:600;
                                        color:var(--muted);transition:all .15s;font-family:'Barlow Condensed',sans-serif;
                                        letter-spacing:0.08em;text-transform:uppercase;">
                                ✕ Fail
                            </div>
                        </label>
                    </div>
                    @error('recommendation')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- ── Remarks ── --}}
            <div class="form-section-divider"><span>Remarks <span style="font-weight:400;letter-spacing:0;">(optional)</span></span></div>

            <div style="margin-bottom:24px;">
                <textarea name="remarks" rows="4"
                          placeholder="Additional comments about the intern's performance, attitude, strengths, or areas for improvement…"
                          class="form-input"
                          style="resize:vertical;font-family:inherit;">{{ old('remarks') }}</textarea>
                @error('remarks')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Warning notice --}}
            <div style="padding:12px 16px;background:var(--surface2);border:1px solid var(--border2);
                        font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);
                        margin-bottom:24px;letter-spacing:0.03em;line-height:1.6;">
                ⚠ &nbsp;<strong style="color:var(--text);font-family:'Barlow',sans-serif;">Evaluation is final.</strong>
                &nbsp;Once submitted it cannot be edited. Review your ratings carefully.
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <p style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    Fields marked <span style="color:var(--crimson);">✦</span> are required.
                </p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('supervisor.evaluations.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                        Submit Evaluation
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
.form-input { border-radius: 0 !important; }
.form-input:focus { border-color: var(--crimson); }
</style>
@endpush

@push('scripts')
<script>
// Star rating buttons
document.querySelectorAll('.star-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const group = this.dataset.group;
        const value = parseInt(this.dataset.value);
        document.querySelector(`input.star-radio[data-group="${group}"][value="${value}"]`).checked = true;
        document.querySelectorAll(`.star-btn[data-group="${group}"]`).forEach(b => {
            const bVal = parseInt(b.dataset.value);
            if (bVal <= value) {
                b.style.background  = 'var(--gold-dim)';
                b.style.borderColor = 'var(--gold)';
                b.querySelector('div:first-child').style.color = 'var(--gold)';
                b.querySelector('div:last-child').style.color  = 'var(--gold)';
            } else {
                b.style.background  = 'var(--surface2)';
                b.style.borderColor = 'var(--border2)';
                b.querySelector('div:first-child').style.color = 'var(--muted)';
                b.querySelector('div:last-child').style.color  = 'var(--muted)';
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
            const isPass     = b.dataset.value === 'pass';
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

// Restore old() state after validation error
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
@endpush

@endsection