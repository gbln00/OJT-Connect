@extends('layouts.coordinator-app')
@section('title', 'Export Reports')
@section('page-title', 'Export Reports')
@section('content')



{{-- ── PAGE HEADER ─────────────────────────────────────────────────────────── --}}
<div class="exp-page-header fade-up">
    <div class="exp-page-header__left">
        <div class="greeting-sub">Data Export Center</div>
        <h1 class="greeting-title">
            Export <span>Reports</span>
        </h1>
        <p class="exp-page-header__desc">
            Download student data, evaluations, and hour logs as styled PDF or Excel reports.
            All exports reflect <strong>live data</strong> at the time of download.
        </p>
    </div>
    <div class="exp-page-header__right">
        <div class="exp-timestamp-block">
            <div class="exp-timestamp-label">Generated</div>
            <div class="exp-timestamp-date">{{ now()->format('M d, Y') }}</div>
            <div class="exp-timestamp-time">{{ now()->format('H:i') }} local</div>
        </div>
    </div>
</div>

{{-- ── STAT STRIP ──────────────────────────────────────────────────────────── --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(3,1fr);margin-bottom:28px;">

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="border-color:rgba(52,211,153,0.3);background:rgba(52,211,153,0.07);color:#34d399;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <span class="stat-tag">interns</span>
        </div>
        <div class="stat-num">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Student interns</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="border-color:rgba(96,165,250,0.3);background:rgba(96,165,250,0.07);color:#60a5fa;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <span class="stat-tag">approved</span>
        </div>
        <div class="stat-num">{{ number_format($stats['total_hours'], 1) }}</div>
        <div class="stat-label">Hours logged</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <span class="stat-tag">submitted</span>
        </div>
        <div class="stat-num">{{ $stats['total_evaluations'] }}</div>
        <div class="stat-label">Evaluations</div>
    </div>

</div>

{{-- ── TENANT BADGE (shows active customization) ──────────────────────────── --}}
<div class="exp-tenant-badge fade-up fade-up-1">
    <div class="exp-tenant-badge__inner">
        @if($tenantLogoUrl)
            <img src="{{ $tenantLogoUrl }}" alt="{{ $brandName }}" class="exp-tenant-badge__logo">
        @else
            <div class="exp-tenant-badge__logo-placeholder">
                {{ strtoupper(substr($brandName, 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="exp-tenant-badge__name">{{ $brandName }}</div>
            <div class="exp-tenant-badge__sub">All exports will be branded with your institution's identity</div>
        </div>
    </div>
    <div class="exp-tenant-badge__features">
        <span class="exp-tenant-feature">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            Custom colors
        </span>
        <span class="exp-tenant-feature">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            Institution name
        </span>
        @if($tenantLogoUrl)
        <span class="exp-tenant-feature">
            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            Logo included
        </span>
        @endif
    </div>
</div>

{{-- ── FILTER FORM ─────────────────────────────────────────────────────────── --}}
<div class="fade-up fade-up-2" style="margin-bottom:24px;">
    <form method="GET" action="{{ route('coordinator.export.index') }}">
        <div class="exp-filter-bar">
            <div class="exp-filter-bar__label">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"/>
                </svg>
                <span>Filter exports</span>
                @if(request('semester') || request('school_year'))
                    <div class="exp-filter-bar__active-pills">
                        @if(request('semester'))
                            <span class="exp-active-pill">{{ request('semester') }}</span>
                        @endif
                        @if(request('school_year'))
                            <span class="exp-active-pill">{{ request('school_year') }}</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="exp-filter-bar__fields">
                <div class="exp-filter-field">
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-select">
                        <option value="">All Semesters</option>
                        <option value="1st Semester" {{ request('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ request('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                        <option value="Summer"       {{ request('semester') === 'Summer'       ? 'selected' : '' }}>Summer</option>
                    </select>
                </div>
                <div class="exp-filter-field">
                    <label class="form-label">School Year</label>
                    <input type="text" name="school_year" class="form-input"
                           placeholder="e.g. 2024-2025" value="{{ request('school_year') }}">
                </div>
                <div class="exp-filter-bar__actions">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Apply
                    </button>
                    @if(request('semester') || request('school_year'))
                        <a href="{{ route('coordinator.export.index') }}" class="btn btn-ghost btn-sm">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ── SECTION LABEL ───────────────────────────────────────────────────────── --}}
<div class="exp-section-label fade-up fade-up-2">
    <span>Available exports</span>
    <div class="exp-section-label__line"></div>
    <span class="exp-count-badge">3 formats</span>
</div>

{{-- ── EXPORT CARDS ────────────────────────────────────────────────────────── --}}
<div class="exp-grid fade-up fade-up-2">

    {{-- ── PDF: Student Summary ─────────────────────────────────────────────── --}}
    <div class="exp-card" data-accent="coral">
        <div class="exp-card__stripe exp-card__stripe--coral"></div>
        <div class="exp-card__body">

            <div class="exp-card__top">
                <div class="exp-format-pill exp-format-pill--coral">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    PDF
                </div>
                <div class="exp-card__dots"><span></span><span></span><span></span></div>
            </div>

            <div class="exp-card__title-block">
                <div class="exp-card__title">Student OJT Summary</div>
                <div class="exp-card__subtitle">Approved interns · company placement · hour progress</div>
            </div>

            <div class="exp-pills">
                <span class="exp-pill">{{ $stats['total_applications'] }} applications</span>
                <span class="exp-pill">{{ $stats['total_students'] }} students</span>
                <span class="exp-pill">Landscape A4</span>
                <span class="exp-pill">Progress bars</span>
                @if(request('semester') || request('school_year'))
                    <span class="exp-pill exp-pill--active">Filtered</span>
                @endif
            </div>

            <div class="exp-card__divider"></div>

            <div class="exp-card__preview">
                <div class="exp-card__preview-label">// What's included</div>
                <ul class="exp-card__feature-list">
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Student name, email & program
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Company placement & semester
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Required vs approved hours with visual progress bar
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Branded with {{ $brandName }} identity
                    </li>
                </ul>
            </div>

            <div class="exp-card__footer">
                <div class="exp-field-list">
                    <span>Name</span><span class="sep">·</span>
                    <span>Company</span><span class="sep">·</span>
                    <span>Program</span><span class="sep">·</span>
                    <span>Hours</span>
                </div>
                <a href="{{ route('coordinator.export.pdf.students', array_filter(request()->only(['semester', 'school_year']))) }}"
                   class="exp-btn exp-btn--coral">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ── PDF: Evaluations ─────────────────────────────────────────────────── --}}
    <div class="exp-card" data-accent="crimson">
        <div class="exp-card__stripe exp-card__stripe--crimson"></div>
        <div class="exp-card__body">

            <div class="exp-card__top">
                <div class="exp-format-pill exp-format-pill--crimson">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    PDF
                </div>
                <div class="exp-card__dots"><span></span><span></span><span></span></div>
            </div>

            <div class="exp-card__title-block">
                <div class="exp-card__title">Evaluations Summary</div>
                <div class="exp-card__subtitle">Attendance · performance · grade · recommendation</div>
            </div>

            <div class="exp-pills">
                <span class="exp-pill">{{ $stats['total_evaluations'] }} evaluations</span>
                <span class="exp-pill">Ratings + grades</span>
                <span class="exp-pill">Landscape A4</span>
                <span class="exp-pill">Pass / Fail</span>
                @if(request('semester') || request('school_year'))
                    <span class="exp-pill exp-pill--active">Filtered</span>
                @endif
            </div>

            <div class="exp-card__divider"></div>

            <div class="exp-card__preview">
                <div class="exp-card__preview-label">// What's included</div>
                <ul class="exp-card__feature-list">
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Attendance & performance ratings (1–5 scale)
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Color-coded overall grade
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Pass / Fail recommendation & descriptive rating
                    </li>
                    <li>
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Supervisor name + summary statistics
                    </li>
                </ul>
            </div>

            <div class="exp-card__footer">
                <div class="exp-field-list">
                    <span>Student</span><span class="sep">·</span>
                    <span>Attendance</span><span class="sep">·</span>
                    <span>Performance</span><span class="sep">·</span>
                    <span>Grade</span>
                </div>
                <a href="{{ route('coordinator.export.pdf.evaluations', array_filter(request()->only(['semester', 'school_year']))) }}"
                   class="exp-btn exp-btn--crimson">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </div>

    {{-- ── Excel: Full Report (full width) ─────────────────────────────────── --}}
    <div class="exp-card exp-card--wide" data-accent="teal">
        <div class="exp-card__stripe exp-card__stripe--teal"></div>
        <div class="exp-card__body exp-card__body--wide">

            {{-- Left --}}
            <div class="exp-wide-left">
                <div class="exp-card__top" style="margin-bottom:16px;">
                    <div class="exp-format-pill exp-format-pill--teal">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="1"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="3" y1="15" x2="21" y2="15"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                            <line x1="15" y1="3" x2="15" y2="21"/>
                        </svg>
                        XLSX
                    </div>
                    <div class="exp-card__dots"><span></span><span></span><span></span></div>
                </div>

                <div class="exp-card__title" style="font-size:18px;">Full OJT Report</div>
                <div class="exp-card__subtitle" style="margin-bottom:18px;">Complete workbook · 3 sheets · styled headers · auto-sized columns</div>

                <p class="exp-desc" style="max-width:520px;margin-bottom:20px;">
                    A full Excel workbook with three sheets — Students, Evaluations, and Hour Logs. Headers are styled with your brand color, columns auto-sized, and alternating row shading applied. Ideal for record-keeping, data analysis, and institutional archiving.
                </p>

                {{-- Sheet breakdown --}}
                <div class="exp-sheet-grid">
                    <div class="exp-sheet-item">
                        <div class="exp-sheet-num">01</div>
                        <div>
                            <div class="exp-sheet-name">Students</div>
                            <div class="exp-sheet-detail">{{ $stats['total_applications'] }} rows · placement + hour data</div>
                        </div>
                    </div>
                    <div class="exp-sheet-div"></div>
                    <div class="exp-sheet-item">
                        <div class="exp-sheet-num">02</div>
                        <div>
                            <div class="exp-sheet-name">Evaluations</div>
                            <div class="exp-sheet-detail">{{ $stats['total_evaluations'] }} rows · grades + ratings</div>
                        </div>
                    </div>
                    <div class="exp-sheet-div"></div>
                    <div class="exp-sheet-item">
                        <div class="exp-sheet-num">03</div>
                        <div>
                            <div class="exp-sheet-name">Hour Logs</div>
                            <div class="exp-sheet-detail">All entries · timestamps</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right CTA --}}
            <div class="exp-wide-cta">
                <div class="exp-wide-cta__icon">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                        <line x1="15" y1="3" x2="15" y2="21"/>
                    </svg>
                </div>
                <div class="exp-wide-cta__format-label">Format</div>
                <div class="exp-wide-cta__format">.xlsx</div>
                <div class="exp-wide-cta__compat">Excel 2007+</div>

                @if(request('semester') || request('school_year'))
                <div class="exp-wide-cta__filter-badge">
                    <div class="exp-wide-cta__filter-title">Filtered</div>
                    @if(request('semester'))
                        <div class="exp-wide-cta__filter-val">{{ request('semester') }}</div>
                    @endif
                    @if(request('school_year'))
                        <div class="exp-wide-cta__filter-val">{{ request('school_year') }}</div>
                    @endif
                </div>
                @endif

                <a href="{{ route('coordinator.export.excel', array_filter(request()->only(['semester', 'school_year']))) }}"
                   class="exp-btn exp-btn--teal" style="width:100%;justify-content:center;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download Excel
                </a>
                <div class="exp-wide-cta__note">3 sheets · brand-styled headers</div>
            </div>

        </div>
    </div>

</div>

{{-- ── EXPORT NOTE ─────────────────────────────────────────────────────────── --}}
<div class="exp-note fade-up fade-up-3">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p>
        All exports reflect <strong>live data</strong> at the time of download.
        @if(request('semester') || request('school_year'))
            Currently filtered by
            @if(request('semester'))<strong>{{ request('semester') }}</strong>@endif
            @if(request('semester') && request('school_year')) · @endif
            @if(request('school_year'))<strong>{{ request('school_year') }}</strong>@endif.
        @else
            Re-download periodically to keep records current.
        @endif
        Data includes only approved applications unless otherwise noted.
        PDFs and Excel files are branded with <strong>{{ $brandName }}</strong>.
    </p>
</div>

@push('styles')
<style>
/* ─────────────────────────────────────────────────────────────
   Export page — scoped component styles
   ───────────────────────────────────────────────────────────── */
:root {
    --exp-teal:          #2dd4bf;
    --exp-teal-dim:      rgba(45,212,191,0.08);
    --exp-teal-border:   rgba(45,212,191,0.2);
    --exp-coral:         #f87171;
    --exp-coral-dim:     rgba(248,113,113,0.08);
    --exp-coral-border:  rgba(248,113,113,0.2);
}
[data-theme="light"] {
    --exp-teal:          #0f9e8e;
    --exp-teal-dim:      rgba(15,158,142,0.07);
    --exp-teal-border:   rgba(15,158,142,0.2);
    --exp-coral:         #e05252;
    --exp-coral-dim:     rgba(224,82,82,0.07);
    --exp-coral-border:  rgba(224,82,82,0.2);
}

/* ── Alert ── */
.alert-success {
    display: flex; align-items: center; gap: 10px;
    background: rgba(52,211,153,0.07);
    border: 1px solid rgba(52,211,153,0.2);
    border-left: 3px solid #34d399;
    color: #34d399;
    padding: 13px 16px;
    margin-bottom: 24px;
    font-size: 13px;
    animation: fadeUp 0.4s cubic-bezier(.22,.61,.36,1) both;
}

/* ── Page header ── */
.exp-page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 28px;
}
.exp-page-header__desc {
    font-size: 13px;
    color: var(--text2);
    margin-top: 6px;
    line-height: 1.6;
    max-width: 520px;
}
.exp-timestamp-block {
    text-align: right;
    font-family: 'DM Mono', monospace;
    flex-shrink: 0;
}
.exp-timestamp-label { font-size: 9px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--muted); margin-bottom: 2px; }
.exp-timestamp-date  { font-size: 14px; font-weight: 600; color: var(--text); }
.exp-timestamp-time  { font-size: 11px; color: var(--muted); }

/* ── Tenant badge ── */
.exp-tenant-badge {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 3px solid var(--crimson);
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.exp-tenant-badge__inner {
    display: flex;
    align-items: center;
    gap: 12px;
}
.exp-tenant-badge__logo {
    width: 40px; height: 40px;
    object-fit: contain;
    border: 1px solid var(--border2);
    padding: 4px;
    background: var(--surface2);
    flex-shrink: 0;
}
.exp-tenant-badge__logo-placeholder {
    width: 40px; height: 40px; flex-shrink: 0;
    background: var(--crimson-lo);
    border: 1px solid rgba(140,14,3,0.3);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 700;
    color: var(--crimson);
}
.exp-tenant-badge__name {
    font-size: 14px; font-weight: 600; color: var(--text);
    margin-bottom: 2px;
}
.exp-tenant-badge__sub {
    font-size: 11px; color: var(--muted);
    font-family: 'DM Mono', monospace;
}
.exp-tenant-badge__features {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.exp-tenant-feature {
    display: flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    color: #34d399;
    padding: 3px 8px;
    border: 1px solid rgba(52,211,153,0.25);
    background: rgba(52,211,153,0.06);
    letter-spacing: 0.04em;
}
.exp-tenant-feature svg { stroke: #34d399; flex-shrink: 0; }

/* ── Filter bar ── */
.exp-filter-bar {
    padding: 16px 20px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-left: 2px solid var(--crimson);
}
.exp-filter-bar__label {
    display: flex; align-items: center; gap: 8px;
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.2em; text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 12px;
}
.exp-filter-bar__active-pills { display: flex; gap: 6px; margin-left: auto; }
.exp-active-pill {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.06em;
    padding: 2px 8px;
    color: var(--crimson);
    border: 1px solid rgba(140,14,3,0.25);
    background: rgba(140,14,3,0.07);
}
.exp-filter-bar__fields {
    display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap;
}
.exp-filter-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
.exp-filter-bar__actions { display: flex; gap: 8px; padding-bottom: 1px; }

/* ── Section label ── */
.exp-section-label {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 16px;
}
.exp-section-label span:first-child {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.22em; text-transform: uppercase;
    color: var(--muted); white-space: nowrap;
}
.exp-section-label__line { flex: 1; height: 1px; background: var(--border); }
.exp-count-badge {
    font-family: 'DM Mono', monospace;
    font-size: 10px; color: var(--muted);
    border: 1px solid var(--border2);
    padding: 2px 8px; letter-spacing: 0.05em;
    white-space: nowrap;
}

/* ── Grid ── */
.exp-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* ── Card ── */
.exp-card {
    background: var(--surface);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
}
.exp-card:hover {
    border-color: var(--border2);
    transform: translateY(-2px);
}
[data-theme="light"] .exp-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
[data-theme="dark"]  .exp-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.3); }

.exp-card--wide { grid-column: span 2; }

/* Stripes */
.exp-card__stripe {
    position: absolute; top: 0; left: 0; right: 0;
    height: 2px;
}
.exp-card__stripe--coral   { background: var(--exp-coral); }
.exp-card__stripe--crimson { background: var(--crimson); }
.exp-card__stripe--teal    { background: var(--exp-teal); }

.exp-card__body { padding: 22px 24px; }
.exp-card__body--wide { display: flex; gap: 32px; align-items: flex-start; }

/* Top row */
.exp-card__top {
    display: flex; align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}
.exp-card__dots { display: flex; gap: 5px; }
.exp-card__dots span { width: 5px; height: 5px; background: var(--border2); }

/* Format pills */
.exp-format-pill {
    display: inline-flex; align-items: center; gap: 5px;
    font-family: 'DM Mono', monospace;
    font-size: 10px; font-weight: 500;
    letter-spacing: 0.12em;
    padding: 4px 10px;
    border: 1px solid;
}
.exp-format-pill--coral   { color: var(--exp-coral);   background: var(--exp-coral-dim);   border-color: var(--exp-coral-border); }
.exp-format-pill--crimson { color: var(--crimson);      background: var(--crimson-lo);       border-color: rgba(140,14,3,0.25); }
.exp-format-pill--teal    { color: var(--exp-teal);     background: var(--exp-teal-dim);     border-color: var(--exp-teal-border); }

/* Title block */
.exp-card__title {
    font-family: 'Playfair Display', serif;
    font-size: 16px; font-weight: 700;
    color: var(--text); margin-bottom: 4px;
}
.exp-card__title-block { margin-bottom: 14px; }
.exp-card__subtitle {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.06em;
    color: var(--muted); text-transform: lowercase;
}

/* Pills */
.exp-pills { display: flex; flex-wrap: wrap; gap: 6px; }
.exp-pill {
    font-size: 11px; padding: 3px 9px;
    border: 1px solid var(--border2);
    background: var(--surface2);
    color: var(--muted);
    font-family: 'Barlow Condensed', sans-serif;
    font-weight: 500; letter-spacing: 0.04em;
}
.exp-pill--active {
    color: var(--crimson);
    border-color: rgba(140,14,3,0.25);
    background: rgba(140,14,3,0.07);
}

/* Divider */
.exp-card__divider { height: 1px; background: var(--border); margin: 16px 0; }

/* Feature list */
.exp-card__preview { margin-bottom: 16px; }
.exp-card__preview-label {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.16em;
    text-transform: uppercase; color: var(--muted);
    margin-bottom: 10px;
}
.exp-card__feature-list {
    list-style: none; padding: 0; margin: 0;
    display: flex; flex-direction: column; gap: 6px;
}
.exp-card__feature-list li {
    display: flex; align-items: flex-start; gap: 7px;
    font-size: 12.5px; color: var(--text2); line-height: 1.4;
}
.exp-card__feature-list li svg { stroke: #34d399; flex-shrink: 0; margin-top: 2px; }

/* Footer */
.exp-card__footer {
    display: flex; align-items: center;
    justify-content: space-between; gap: 12px;
    flex-wrap: wrap;
}
.exp-field-list {
    display: flex; align-items: center; gap: 4px; flex-wrap: wrap;
    font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted);
}
.exp-field-list .sep { color: var(--border2); font-size: 12px; }

/* Buttons */
.exp-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.1em; text-transform: uppercase;
    text-decoration: none;
    border: 1px solid;
    transition: all 0.15s; white-space: nowrap;
}
.exp-btn--coral {
    color: var(--exp-coral);
    background: var(--exp-coral-dim);
    border-color: var(--exp-coral-border);
}
.exp-btn--coral:hover  { background: rgba(248,113,113,0.15); transform: translateY(-1px); }
.exp-btn--crimson {
    color: var(--crimson);
    background: var(--crimson-lo);
    border-color: rgba(140,14,3,0.25);
}
.exp-btn--crimson:hover { background: var(--crimson-md); transform: translateY(-1px); }
.exp-btn--teal {
    color: var(--bg);
    background: var(--exp-teal);
    border-color: var(--exp-teal);
}
.exp-btn--teal:hover { opacity: 0.88; transform: translateY(-1px); }
[data-theme="light"] .exp-btn--teal { color: #ffffff; }

/* ── Wide card internals ── */
.exp-wide-left { flex: 1; min-width: 0; }
.exp-desc { font-size: 13px; color: var(--text2); line-height: 1.65; }

.exp-sheet-grid {
    display: flex; align-items: stretch;
    border: 1px solid var(--border);
    background: var(--surface2);
}
.exp-sheet-item {
    display: flex; align-items: flex-start;
    gap: 10px; padding: 14px 16px; flex: 1;
}
.exp-sheet-div { width: 1px; background: var(--border); }
.exp-sheet-num {
    font-family: 'Playfair Display', serif;
    font-size: 18px; font-weight: 900;
    color: var(--exp-teal);
    line-height: 1; flex-shrink: 0; margin-top: 1px;
}
.exp-sheet-name {
    font-size: 13px; font-weight: 500;
    color: var(--text); margin-bottom: 3px;
}
.exp-sheet-detail {
    font-family: 'DM Mono', monospace;
    font-size: 10px; color: var(--muted); letter-spacing: 0.04em;
}

/* CTA block */
.exp-wide-cta {
    flex-shrink: 0; width: 168px;
    display: flex; flex-direction: column; align-items: center;
    padding: 20px 16px;
    border: 1px solid var(--border);
    background: var(--surface2);
    text-align: center;
}
.exp-wide-cta__icon {
    width: 56px; height: 56px;
    border: 1px solid var(--exp-teal-border);
    background: var(--exp-teal-dim);
    display: flex; align-items: center; justify-content: center;
    color: var(--exp-teal);
    margin-bottom: 14px;
}
.exp-wide-cta__format-label {
    font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em;
    color: var(--muted); text-transform: uppercase; margin-bottom: 2px;
}
.exp-wide-cta__format {
    font-family: 'Playfair Display', serif;
    font-size: 22px; font-weight: 900;
    color: var(--exp-teal); margin-bottom: 2px;
}
.exp-wide-cta__compat { font-size: 11px; color: var(--muted); margin-bottom: 20px; }
.exp-wide-cta__filter-badge {
    width: 100%; margin-bottom: 12px;
    padding: 8px 10px;
    background: rgba(140,14,3,0.06);
    border: 1px solid rgba(140,14,3,0.18);
    border-left: 2px solid var(--crimson);
    text-align: left;
}
.exp-wide-cta__filter-title {
    font-family: 'DM Mono', monospace;
    font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
    color: var(--crimson); margin-bottom: 4px;
}
.exp-wide-cta__filter-val { font-size: 11px; color: var(--text2); }
.exp-wide-cta__note { font-size: 11px; color: var(--muted); margin-top: 10px; }

/* ── Note ── */
.exp-note {
    display: flex; align-items: flex-start; gap: 10px;
    margin-top: 20px; padding: 14px 18px;
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--muted);
}
.exp-note p { font-size: 12px; line-height: 1.6; margin: 0; }
.exp-note strong { color: var(--text2); font-weight: 500; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .exp-page-header          { flex-direction: column; }
    .exp-card--wide           { grid-column: span 1; }
    .exp-card__body--wide     { flex-direction: column; }
    .exp-wide-cta             { width: 100% !important; }
    .exp-sheet-grid           { flex-direction: column; }
    .exp-sheet-div            { width: 100%; height: 1px; }
    .exp-tenant-badge         { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 680px) {
    .exp-grid                 { grid-template-columns: 1fr; }
    .exp-card--wide           { grid-column: span 1; }
}
</style>
@endpush

@endsection