@extends('layouts.student-app')
@section('title', 'Submit Weekly Report')
@section('page-title', 'Submit Weekly Report')
@section('content')

<div style="max-width:720px;">

    {{-- Back link --}}
    <a href="{{ route('student.reports.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);text-decoration:none;margin-bottom:20px;">
        ← Back to Weekly Reports
    </a>

    <div class="card" style="padding:28px;">
        <div style="font-size:15px;font-weight:700;margin-bottom:4px;">New Weekly Report</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:24px;">
            Fill in the details for this week's internship report.
        </div>

        @if($errors->any())
        <div style="background:var(--red-dim);border:1px solid var(--red);border-radius:8px;padding:12px 16px;margin-bottom:20px;">
            <div style="font-size:12px;font-weight:600;color:var(--red);margin-bottom:6px;">Please fix the following errors:</div>
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li style="font-size:12px;color:var(--red);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Week number + date range --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:18px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                        WEEK NUMBER
                    </label>
                    <input type="number" name="week_number" min="1"
                           value="{{ old('week_number', $nextWeekNumber ?? '') }}"
                           style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                  color:var(--text);font-size:13px;box-sizing:border-box;"
                           placeholder="e.g. 1" required>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                        DATE FROM
                    </label>
                    <input type="date" name="week_start" value="{{ old('date_from') }}"
                           style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                  color:var(--text);font-size:13px;box-sizing:border-box;" required>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                        DATE TO
                    </label>
                    <input type="date" name="week_end" value="{{ old('date_to') }}"
                           style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                  color:var(--text);font-size:13px;box-sizing:border-box;" required>
                </div>
            </div>

            {{-- Hours this week --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                    HOURS RENDERED THIS WEEK
                </label>
                <input type="number" name="hours_this_week" min="0" max="168" step="0.5"
                       value="{{ old('hours_this_week') }}"
                       style="width:200px;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                              color:var(--text);font-size:13px;"
                       placeholder="e.g. 40" required>
            </div>

            {{-- Activities summary --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                    ACTIVITIES SUMMARY
                </label>
                <textarea name="description" rows="4"
                          style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                 color:var(--text);font-size:13px;resize:vertical;box-sizing:border-box;"
                          placeholder="Briefly describe the tasks and activities you did this week…" required>{{ old('activities_summary') }}</textarea>
            </div>

            {{-- Learnings / Reflections --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                    LEARNINGS & REFLECTIONS
                </label>
                <textarea name="learnings" rows="4"
                          style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                 color:var(--text);font-size:13px;resize:vertical;box-sizing:border-box;"
                          placeholder="What did you learn this week? Any challenges or highlights?">{{ old('learnings') }}</textarea>
            </div>

            {{-- Challenges --}}
            <div style="margin-bottom:18px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                    CHALLENGES ENCOUNTERED
                </label>
                <textarea name="challenges" rows="3"
                          style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                                 color:var(--text);font-size:13px;resize:vertical;box-sizing:border-box;"
                          placeholder="Any difficulties or problems you encountered…">{{ old('challenges') }}</textarea>
            </div>

            {{-- File attachment --}}
            <div style="margin-bottom:28px;">
                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:6px;letter-spacing:.5px;">
                    ATTACHMENT <span style="font-weight:400;">(optional — PDF, DOC, image)</span>
                </label>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                       style="width:100%;padding:9px 12px;background:var(--surface2);border:1px solid var(--border2);border-radius:8px;
                              color:var(--text);font-size:13px;box-sizing:border-box;">
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:12px;">
                <button type="submit"
                        style="padding:10px 28px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;
                               font-size:13px;font-weight:600;cursor:pointer;">
                    Submit Report
                </button>
                <a href="{{ route('student.reports.index') }}"
                   style="padding:10px 20px;background:transparent;color:var(--muted);border:1px solid var(--border2);
                          border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection