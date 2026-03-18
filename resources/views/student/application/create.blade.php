@extends('layouts.student-app')
@section('title', 'Apply for OJT')
@section('page-title', 'OJT Application')

@section('content')
<div style="max-width:620px;">

    <a href="{{ route('student.dashboard') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Back to dashboard
    </a>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Submit OJT Application</div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                Complete all required fields. Your coordinator will review your submission.
            </div>
        </div>

        <div style="padding:24px;">

            @if($errors->any())
                <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">
                    <div style="font-weight:600;margin-bottom:4px;display:flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please fix the following:
                    </div>
                    @foreach($errors->all() as $error)
                        <div>· {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('student.application.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- SECTION: OJT Details --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    OJT Details
                </div>

                {{-- Program + Required Hours --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Program <span style="color:var(--coral);">*</span>
                        </label>
                        <input type="text" name="program" value="{{ old('program') }}"
                               placeholder="e.g. BSIT"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('program') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Required Hours <span style="color:var(--coral);">*</span>
                        </label>
                        <input type="number" name="required_hours" value="{{ old('required_hours', 486) }}"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                    </div>
                </div>

                {{-- School Year + Semester --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            School Year <span style="color:var(--coral);">*</span>
                        </label>
                        <select name="school_year"
                                style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('school_year') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;transition:border 0.15s;"
                                onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                            <option value="">Select school year</option>
                            <option value="2023-2024" {{ old('school_year') === '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                            <option value="2024-2025" {{ old('school_year') === '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                            <option value="2025-2026" {{ old('school_year') === '2025-2026' ? 'selected' : '' }}>2025-2026</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                            Semester <span style="color:var(--coral);">*</span>
                        </label>
                        <select name="semester"
                                style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('semester') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;transition:border 0.15s;"
                                onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                            <option value="">Select semester</option>
                            <option value="1st Semester" {{ old('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd Semester" {{ old('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="Summer"       {{ old('semester') === 'Summer'       ? 'selected' : '' }}>Summer</option>
                        </select>
                    </div>
                </div>

                {{-- SECTION: Company Selection --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Company Selection
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Select Company <span style="color:var(--coral);">*</span>
                    </label>

                    <input type="text" id="company-search"
                           placeholder="Search companies..."
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;margin-bottom:8px;transition:border 0.15s;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">

                    <div id="company-list"
                         style="border:1px solid {{ $errors->has('company_id') ? 'var(--coral)' : 'var(--border2)' }};border-radius:8px;max-height:240px;overflow-y:auto;">
                        @forelse($companies as $company)
                            <label id="company-item-{{ $company->id }}"
                                   style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;cursor:pointer;border-bottom:1px solid var(--border2);transition:background 0.12s;"
                                   onmouseover="this.style.background='var(--surface2)'"
                                   onmouseout="this.style.background='transparent'">
                                <input type="radio" name="company_id" value="{{ $company->id }}"
                                       {{ old('company_id') == $company->id ? 'checked' : '' }}
                                       style="margin-top:3px;accent-color:var(--gold);flex-shrink:0;">
                                <div>
                                    <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $company->name }}</div>
                                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                                        {{ $company->industry }} · {{ $company->address }}
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px;">
                                No active companies available. Contact your coordinator.
                            </div>
                        @endforelse
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:6px;">
                        Only active partner companies are shown.
                    </div>
                </div>

                {{-- SECTION: Document Upload --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Supporting Document
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Endorsement Letter / Document
                        <span style="color:var(--muted);font-weight:400;">(optional)</span>
                    </label>

                    {{-- Custom file drop zone --}}
                    <div id="drop-zone"
                         style="border:1.5px dashed var(--border2);border-radius:8px;padding:24px;text-align:center;cursor:pointer;transition:border-color 0.15s,background 0.15s;"
                         onclick="document.getElementById('document-input').click()"
                         ondragover="event.preventDefault();this.style.borderColor='var(--gold)';this.style.background='var(--surface2)'"
                         ondragleave="this.style.borderColor='var(--border2)';this.style.background='transparent'"
                         ondrop="handleDrop(event)">
                        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:var(--muted);margin-bottom:8px;">
                            <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                        </svg>
                        <div style="font-size:13px;color:var(--muted2);font-weight:500;" id="drop-label">
                            Click to upload or drag & drop
                        </div>
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            PDF, JPG, PNG — max 5MB
                        </div>
                    </div>
                    <input type="file" id="document-input" name="document"
                           accept=".pdf,.jpg,.jpeg,.png"
                           style="display:none;"
                           onchange="updateFileName(this)">
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:10px;border-top:1px solid var(--border2);padding-top:20px;">
                    <button type="submit"
                            style="padding:10px 28px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:opacity 0.15s;"
                            onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        Submit Application
                    </button>
                    <a href="{{ route('student.dashboard') }}"
                       style="padding:10px 20px;border:1px solid var(--border2);border-radius:8px;color:var(--muted2);font-size:13px;text-decoration:none;"
                       onmouseover="this.style.borderColor='var(--muted)'" onmouseout="this.style.borderColor='var(--border2)'">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    // Company live search
    document.getElementById('company-search').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#company-list label').forEach(label => {
            label.style.display = label.textContent.toLowerCase().includes(query) ? 'flex' : 'none';
        });
    });

    // File upload display
    function updateFileName(input) {
        const label = document.getElementById('drop-label');
        if (input.files && input.files[0]) {
            label.textContent = '📎 ' + input.files[0].name;
            document.getElementById('drop-zone').style.borderColor = 'var(--gold)';
        }
    }

    // Drag and drop
    function handleDrop(event) {
        event.preventDefault();
        document.getElementById('drop-zone').style.borderColor = 'var(--border2)';
        document.getElementById('drop-zone').style.background = 'transparent';
        const input = document.getElementById('document-input');
        input.files = event.dataTransfer.files;
        updateFileName(input);
    }
</script>
@endsection