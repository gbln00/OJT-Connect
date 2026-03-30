@extends('layouts.student-app')
@section('title', 'Apply for OJT')
@section('page-title', 'OJT Application')

@section('content')
<div style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Application / New
        </span>
    </div>

    {{-- Main card --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div>
                <div class="card-title">Submit OJT Application</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // Complete all required fields. Your coordinator will review your submission.
                </div>
            </div>
            <a href="{{ route('student.dashboard') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('student.application.store') }}" enctype="multipart/form-data" style="padding:24px;">
            @csrf

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);padding:13px 16px;margin-bottom:24px;font-size:13px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">Please fix the following:</strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- ── OJT Details ── --}}
            <div class="form-section-divider"><span>OJT Details</span></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Program <span style="color:var(--crimson);">✦</span></label>
                    <input type="text" name="program" value="{{ old('program') }}"
                           placeholder="e.g. BSIT"
                           class="form-input {{ $errors->has('program') ? 'is-invalid' : '' }}">
                    @error('program')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Required Hours <span style="color:var(--crimson);">✦</span></label>
                    <input type="number" name="required_hours" value="{{ old('required_hours', 486) }}"
                           class="form-input {{ $errors->has('required_hours') ? 'is-invalid' : '' }}">
                    <div class="form-hint">// Default: 486 hours</div>
                    @error('required_hours')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">School Year <span style="color:var(--crimson);">✦</span></label>
                    <select name="school_year" class="form-input {{ $errors->has('school_year') ? 'is-invalid' : '' }}" style="cursor:pointer;">
                        <option value="">Select school year</option>
                        <option value="2023-2024" {{ old('school_year') === '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                        <option value="2024-2025" {{ old('school_year') === '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                        <option value="2025-2026" {{ old('school_year') === '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                    </select>
                    @error('school_year')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Semester <span style="color:var(--crimson);">✦</span></label>
                    <select name="semester" class="form-input {{ $errors->has('semester') ? 'is-invalid' : '' }}" style="cursor:pointer;">
                        <option value="">Select semester</option>
                        <option value="1st Semester" {{ old('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ old('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="Summer"       {{ old('semester') === 'Summer'       ? 'selected' : '' }}>Summer</option>
                    </select>
                    @error('semester')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ── Company Selection ── --}}
            <div class="form-section-divider"><span>Company Selection</span></div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Select Company <span style="color:var(--crimson);">✦</span></label>

                <input type="text" id="company-search"
                       placeholder="Search companies..."
                       autocomplete="off"
                       class="form-input"
                       style="border-bottom:none;">

                <div id="company-list"
                     style="border:1px solid {{ $errors->has('company_id') ? 'var(--crimson)' : 'var(--border2)' }};border-top:none;max-height:220px;overflow-y:auto;background:var(--surface2);">
                    @forelse($companies as $company)
                        <label id="company-item-{{ $company->id }}"
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
                            No active companies available. Contact your coordinator.
                        </div>
                    @endforelse
                </div>
                <div class="form-hint">// Only active partner companies are shown.</div>
                @error('company_id')<div class="form-error" style="margin-top:5px;">{{ $message }}</div>@enderror
            </div>

            {{-- ── Supporting Document ── --}}
            <div class="form-section-divider"><span>Supporting Document</span></div>

            <div style="margin-bottom:28px;">
                <label class="form-label">Endorsement Letter <span style="color:var(--muted);">(optional)</span></label>

                <div id="drop-zone"
                     style="border:1px dashed var(--border2);padding:28px;text-align:center;cursor:pointer;transition:border-color 0.15s,background 0.15s;"
                     onclick="document.getElementById('document-input').click()"
                     ondragover="event.preventDefault();this.style.borderColor='var(--crimson)';this.style.background='var(--surface2)'"
                     ondragleave="this.style.borderColor='var(--border2)';this.style.background='transparent'"
                     ondrop="handleDrop(event)">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--muted);margin-bottom:8px;">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                    </svg>
                    <div style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:var(--muted2);" id="drop-label">
                        Click to upload or drag & drop
                    </div>
                    <div class="form-hint" style="margin-top:4px;">PDF, JPG, PNG — max 5MB</div>
                </div>
                <input type="file" id="document-input" name="document"
                       accept=".pdf,.jpg,.jpeg,.png"
                       style="display:none;"
                       onchange="updateFileName(this)">
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('student.dashboard') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
                        </svg>
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('company-search').addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#company-list label').forEach(label => {
            label.style.display = label.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    });

    function updateFileName(input) {
        const label = document.getElementById('drop-label');
        if (input.files && input.files[0]) {
            label.textContent = '📎 ' + input.files[0].name;
            document.getElementById('drop-zone').style.borderColor = 'var(--crimson)';
        }
    }

    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone').style.borderColor = 'var(--border2)';
        document.getElementById('drop-zone').style.background  = 'transparent';
        const input = document.getElementById('document-input');
        input.files = event.dataTransfer.files;
        updateFileName(input);
    }
</script>
@endsection