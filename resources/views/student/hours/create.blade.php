{{-- resources/views/student/reports/create.blade.php --}}
@extends('layouts.student-app')
@section('title', 'Submit Weekly Report')
@section('page-title', 'Submit Weekly Report')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Reports / New Entry
        </span>
    </div>

    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div>
                <div class="card-title">New Weekly Report</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Fill in the details for this week's internship report
                </div>
            </div>
            <a href="{{ route('student.reports.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form action="{{ route('student.reports.store') }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
            @csrf

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);padding:13px 16px;margin-bottom:24px;font-size:13px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">Please fix the following:</strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div class="form-section-divider"><span>Week Information</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Week Number <span style="color:var(--crimson);">✦</span></label>
                    <input type="number" name="week_number" min="1"
                           value="{{ old('week_number', $nextWeekNumber ?? '') }}"
                           placeholder="e.g. 1"
                           class="form-input {{ $errors->has('week_number') ? 'is-invalid' : '' }}" required>
                    @error('week_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Date From <span style="color:var(--crimson);">✦</span></label>
                    <input type="date" name="week_start" value="{{ old('week_start') }}"
                           class="form-input {{ $errors->has('week_start') ? 'is-invalid' : '' }}" required>
                    @error('week_start')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Date To <span style="color:var(--crimson);">✦</span></label>
                    <input type="date" name="week_end" value="{{ old('week_end') }}"
                           class="form-input {{ $errors->has('week_end') ? 'is-invalid' : '' }}" required>
                    @error('week_end')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Hours Rendered This Week <span style="color:var(--crimson);">✦</span></label>
                <input type="number" name="hours_this_week" min="0" max="168" step="0.5"
                       value="{{ old('hours_this_week') }}"
                       placeholder="e.g. 40"
                       class="form-input {{ $errors->has('hours_this_week') ? 'is-invalid' : '' }}" style="max-width:200px;" required>
                @error('hours_this_week')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div class="form-section-divider"><span>Report Content</span></div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Activities Summary <span style="color:var(--crimson);">✦</span></label>
                <textarea name="description" rows="4"
                          placeholder="Briefly describe the tasks and activities you did this week…"
                          class="form-input {{ $errors->has('description') ? 'is-invalid' : '' }}"
                          style="resize:vertical;font-family:inherit;" required>{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Learnings & Reflections</label>
                <textarea name="learnings" rows="4"
                          placeholder="What did you learn this week? Any challenges or highlights?"
                          class="form-input"
                          style="resize:vertical;font-family:inherit;">{{ old('learnings') }}</textarea>
            </div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Challenges Encountered</label>
                <textarea name="challenges" rows="3"
                          placeholder="Any difficulties or problems you encountered…"
                          class="form-input"
                          style="resize:vertical;font-family:inherit;">{{ old('challenges') }}</textarea>
            </div>

            <div class="form-section-divider"><span>Attachment</span></div>

            <div style="margin-bottom:28px;">
                <label class="form-label">File <span style="color:var(--muted);">(optional — PDF, DOC, image)</span></label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                       class="form-input" style="cursor:pointer;">
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('student.reports.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Submit Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection