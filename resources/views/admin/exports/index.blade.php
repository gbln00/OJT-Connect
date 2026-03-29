@extends('layouts.app')
@section('title', 'Export Reports')
@section('page-title', 'Export Reports')

@section('content')

@if(session('success'))
<div style="
    display:flex;align-items:center;gap:10px;
    background:rgba(52,211,153,0.07);
    border:1px solid rgba(52,211,153,0.2);
    border-left:3px solid #34d399;
    color:#34d399;
    padding:13px 16px;
    margin-bottom:24px;
    font-size:13px;
    animation: fadeUp 0.4s cubic-bezier(.22,.61,.36,1) both;
">
    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/>
    </svg>
    {{ session('success') }}
</div>
@endif

{{-- PAGE HEADER --}}
<div class="export-page-header fade-up">
    <div>
        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">
            Data Export Center
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:clamp(20px,2.5vw,26px);font-weight:900;color:var(--text);line-height:1.1;">
            Export <span style="color:var(--crimson);font-style:italic;">Reports</span>
        </h1>
    </div>
    <div style="font-size:12px;color:var(--muted);font-family:'DM Mono',monospace;text-align:right;line-height:1.8;">
        <div>Generated {{ now()->format('M d, Y') }}</div>
        <div style="color:var(--muted2);">{{ now()->format('H:i') }} local time</div>
    </div>
</div>

{{-- STAT STRIP --}}
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

{{-- SECTION LABEL --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;" class="fade-up fade-up-2">
    <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.22em;text-transform:uppercase;color:var(--muted);">Available exports</span>
    <div style="flex:1;height:1px;background:var(--border);"></div>
    <span class="export-count-badge">{{ 3 }} formats</span>
</div>

{{-- EXPORT CARDS --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="fade-up fade-up-2">

    {{-- ── PDF: Student Summary ── --}}
    <div class="export-card" data-accent="coral">
        <div class="export-card-stripe coral-stripe"></div>

        <div class="export-card-inner">
            {{-- Header --}}
            <div class="export-card-header">
                <div class="export-format-pill pdf-pill">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    PDF
                </div>
                <div class="export-meta-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Title --}}
            <div style="margin-bottom:14px;">
                <div class="export-title">Student OJT Summary</div>
                <div class="export-subtitle">Approved interns · company placement · hour progress</div>
            </div>

            {{-- Data pills --}}
            <div class="export-pills">
                <span class="export-pill">{{ $stats['total_applications'] }} applications</span>
                <span class="export-pill">{{ $stats['total_students'] }} students</span>
                <span class="export-pill">Landscape A4</span>
                <span class="export-pill">Table format</span>
            </div>

            {{-- Divider --}}
            <div style="height:1px;background:var(--border);margin:16px 0;"></div>

            {{-- Description --}}
            <p class="export-desc">
                A PDF table of all approved student interns with their company, program, required hours, and approved hours logged. Ideal for coordinators and faculty review.
            </p>

            {{-- Footer --}}
            <div class="export-card-footer">
                <div class="export-field-list">
                    <span class="export-field">Name</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Company</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Program</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Hours</span>
                </div>
                <a href="{{ route('admin.export.pdf.students') }}" class="export-btn coral-btn">
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

    {{-- ── PDF: Evaluations ── --}}
    <div class="export-card" data-accent="crimson">
        <div class="export-card-stripe crimson-stripe"></div>

        <div class="export-card-inner">
            {{-- Header --}}
            <div class="export-card-header">
                <div class="export-format-pill crimson-pill">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                    PDF
                </div>
                <div class="export-meta-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Title --}}
            <div style="margin-bottom:14px;">
                <div class="export-title">Evaluations Summary</div>
                <div class="export-subtitle">Attendance · performance · overall grade · recommendation</div>
            </div>

            {{-- Data pills --}}
            <div class="export-pills">
                <span class="export-pill">{{ $stats['total_evaluations'] }} evaluations</span>
                <span class="export-pill">Ratings + grades</span>
                <span class="export-pill">Landscape A4</span>
                <span class="export-pill">Pass/Fail</span>
            </div>

            {{-- Divider --}}
            <div style="height:1px;background:var(--border);margin:16px 0;"></div>

            {{-- Description --}}
            <p class="export-desc">
                A PDF table of all student evaluations including attendance rating, performance rating, overall grade, and pass/fail recommendation. Perfect for end-of-term review.
            </p>

            {{-- Footer --}}
            <div class="export-card-footer">
                <div class="export-field-list">
                    <span class="export-field">Student</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Attendance</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Performance</span>
                    <span class="export-field-sep">·</span>
                    <span class="export-field">Grade</span>
                </div>
                <a href="{{ route('admin.export.pdf.evaluations') }}" class="export-btn crimson-btn">
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

    {{-- ── Excel: Full Report (full-width) ── --}}
    <div class="export-card export-card-wide" style="grid-column:span 2;" data-accent="teal">
        <div class="export-card-stripe teal-stripe"></div>

        <div class="export-card-inner" style="display:flex;gap:32px;align-items:flex-start;">

            {{-- Left --}}
            <div style="flex:1;min-width:0;">
                <div class="export-card-header" style="margin-bottom:14px;">
                    <div class="export-format-pill teal-pill">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="1"/>
                            <line x1="3" y1="9" x2="21" y2="9"/>
                            <line x1="3" y1="15" x2="21" y2="15"/>
                            <line x1="9" y1="3" x2="9" y2="21"/>
                            <line x1="15" y1="3" x2="15" y2="21"/>
                        </svg>
                        XLSX
                    </div>
                    <div class="export-meta-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>

                <div class="export-title" style="font-size:17px;">Full OJT Report</div>
                <div class="export-subtitle" style="margin-bottom:16px;">Complete workbook · 3 sheets · styled headers · auto-sized columns</div>

                <p class="export-desc" style="margin-bottom:18px;max-width:480px;">
                    Exports a full Excel workbook with three sheets — Students, Evaluations, and Hour Logs — with auto-sized columns and styled headers. Ideal for record-keeping, data analysis, and institutional archiving.
                </p>

                {{-- Sheet breakdown --}}
                <div class="sheet-breakdown">
                    <div class="sheet-item">
                        <div class="sheet-num teal-text">01</div>
                        <div>
                            <div class="sheet-name">Students</div>
                            <div class="sheet-detail">{{ $stats['total_applications'] }} rows · placement + hour data</div>
                        </div>
                    </div>
                    <div class="sheet-divider"></div>
                    <div class="sheet-item">
                        <div class="sheet-num teal-text">02</div>
                        <div>
                            <div class="sheet-name">Evaluations</div>
                            <div class="sheet-detail">{{ $stats['total_evaluations'] }} rows · grades + ratings</div>
                        </div>
                    </div>
                    <div class="sheet-divider"></div>
                    <div class="sheet-item">
                        <div class="sheet-num teal-text">03</div>
                        <div>
                            <div class="sheet-name">Hour Logs</div>
                            <div class="sheet-detail">All log entries · timestamps</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Download CTA --}}
            <div class="excel-cta-block">
                <div class="excel-icon-wrap">
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="3" y1="15" x2="21" y2="15"/>
                        <line x1="9" y1="3" x2="9" y2="21"/>
                        <line x1="15" y1="3" x2="15" y2="21"/>
                    </svg>
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;color:var(--muted);text-transform:uppercase;margin-bottom:4px;">Format</div>
                <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--teal-color);margin-bottom:2px;">.xlsx</div>
                <div style="font-size:11px;color:var(--muted);margin-bottom:20px;">Excel 2007+</div>
                <a href="{{ route('admin.export.excel') }}" class="export-btn teal-btn" style="width:100%;justify-content:center;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="7,10 12,15 17,10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download Excel
                </a>
                <div style="font-size:11px;color:var(--muted);margin-top:10px;text-align:center;">
                    3 sheets · auto-sized
                </div>
            </div>

        </div>
    </div>

</div>

{{-- EXPORT NOTE --}}
<div style="margin-top:20px;padding:14px 18px;border:1px solid var(--border);background:var(--surface);display:flex;align-items:flex-start;gap:10px;" class="fade-up fade-up-3">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:var(--muted);flex-shrink:0;margin-top:1px;">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <p style="font-size:12px;color:var(--muted);line-height:1.6;">
        All exports reflect <strong style="color:var(--text2);font-weight:500;">live data</strong> at the time of download. Re-download periodically to keep records current. Data includes only approved applications unless otherwise noted.
    </p>
</div>

{{-- SCOPED STYLES --}}
<style>
    /* ── Teal color token (not in base layout) ── */
    :root {
        --teal-color:  #2dd4bf;
        --teal-dim:    rgba(45,212,191,0.08);
        --teal-border: rgba(45,212,191,0.2);
        --coral-color: #f87171;
        --coral-dim:   rgba(248,113,113,0.08);
        --coral-border:rgba(248,113,113,0.2);
        --muted2:      rgba(171,171,171,0.55);
    }
    [data-theme="light"] {
        --teal-color:  #0f9e8e;
        --teal-dim:    rgba(15,158,142,0.07);
        --teal-border: rgba(15,158,142,0.2);
        --coral-color: #e05252;
        --coral-dim:   rgba(224,82,82,0.07);
        --coral-border:rgba(224,82,82,0.2);
        --muted2:      #6b7280;
    }

    /* ── Page header ── */
    .export-page-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    /* ── Export count badge ── */
    .export-count-badge {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--muted);
        border: 1px solid var(--border2);
        padding: 2px 8px;
        letter-spacing: 0.05em;
    }

    /* ── Export card ── */
    .export-card {
        background: var(--surface);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
        transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    }
    .export-card:hover {
        border-color: var(--border2);
        transform: translateY(-2px);
    }
    [data-theme="light"] .export-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }
    [data-theme="dark"] .export-card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.35);
    }

    /* Accent stripe */
    .export-card-stripe {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
    }
    .coral-stripe   { background: var(--coral-color); }
    .crimson-stripe { background: var(--crimson); }
    .teal-stripe    { background: var(--teal-color); }

    .export-card-inner { padding: 22px 24px; }

    /* Card header row */
    .export-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    /* Format pill */
    .export-format-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.12em;
        padding: 4px 10px;
        border: 1px solid;
    }
    .pdf-pill     { color: var(--coral-color); background: var(--coral-dim); border-color: var(--coral-border); }
    .crimson-pill { color: var(--crimson);     background: var(--crimson-lo); border-color: rgba(140,14,3,0.25); }
    .teal-pill    { color: var(--teal-color);  background: var(--teal-dim);   border-color: var(--teal-border); }

    /* Meta dots */
    .export-meta-dots { display: flex; gap: 5px; }
    .export-meta-dots span {
        width: 5px; height: 5px;
        background: var(--border2);
    }

    /* Title / subtitle */
    .export-title {
        font-family: 'Playfair Display', serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }
    .export-subtitle {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        letter-spacing: 0.06em;
        color: var(--muted);
        text-transform: lowercase;
    }

    /* Pills row */
    .export-pills { display: flex; flex-wrap: wrap; gap: 6px; }
    .export-pill {
        font-size: 11px;
        padding: 3px 9px;
        border: 1px solid var(--border2);
        background: var(--surface2);
        color: var(--muted2);
        font-family: 'Barlow Condensed', sans-serif;
        font-weight: 500;
        letter-spacing: 0.05em;
    }

    /* Description */
    .export-desc {
        font-size: 13px;
        color: var(--text2);
        line-height: 1.65;
    }
    [data-theme="light"] .export-desc { color: var(--muted2); }

    /* Footer row */
    .export-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Field list */
    .export-field-list {
        display: flex;
        align-items: center;
        gap: 4px;
        flex-wrap: wrap;
    }
    .export-field {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--muted);
        letter-spacing: 0.04em;
    }
    .export-field-sep { color: var(--border2); font-size: 12px; }

    /* Download buttons */
    .export-btn {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        text-decoration: none;
        border: 1px solid;
        transition: all 0.15s;
        white-space: nowrap;
    }
    .coral-btn {
        color: var(--coral-color);
        background: var(--coral-dim);
        border-color: var(--coral-border);
    }
    .coral-btn:hover {
        background: rgba(248,113,113,0.15);
        transform: translateY(-1px);
    }
    [data-theme="light"] .coral-btn:hover { background: rgba(224,82,82,0.12); }

    .crimson-btn {
        color: #c0392b;
        background: var(--crimson-lo);
        border-color: rgba(140,14,3,0.25);
    }
    .crimson-btn:hover {
        background: var(--crimson-md);
        transform: translateY(-1px);
    }

    .teal-btn {
        color: var(--bg);
        background: var(--teal-color);
        border-color: var(--teal-color);
    }
    .teal-btn:hover {
        opacity: 0.88;
        transform: translateY(-1px);
    }
    [data-theme="light"] .teal-btn { color: #ffffff; }

    /* ── Sheet breakdown (Excel card) ── */
    .sheet-breakdown {
        display: flex;
        align-items: stretch;
        gap: 0;
        border: 1px solid var(--border);
        background: var(--surface2);
    }
    .sheet-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px 16px;
        flex: 1;
    }
    .sheet-divider {
        width: 1px;
        background: var(--border);
    }
    .sheet-num {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 900;
        line-height: 1;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .teal-text { color: var(--teal-color); }
    .sheet-name {
        font-size: 13px;
        font-weight: 500;
        color: var(--text);
        margin-bottom: 3px;
    }
    .sheet-detail {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        color: var(--muted);
        letter-spacing: 0.04em;
    }

    /* ── Excel CTA block ── */
    .excel-cta-block {
        flex-shrink: 0;
        width: 160px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        border: 1px solid var(--border);
        background: var(--surface2);
        text-align: center;
    }
    .excel-icon-wrap {
        width: 52px;
        height: 52px;
        border: 1px solid var(--teal-border);
        background: var(--teal-dim);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--teal-color);
        margin-bottom: 14px;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .export-page-header { flex-direction: column; align-items: flex-start; gap: 6px; }
        div[style*="grid-column:span 2"] { grid-column: span 1 !important; }
        .export-card-inner[style*="display:flex;gap:32px"] {
            flex-direction: column !important;
        }
        .excel-cta-block { width: 100% !important; }
        .sheet-breakdown { flex-direction: column; }
        .sheet-divider { width: 100%; height: 1px; }
    }
    @media (max-width: 680px) {
        div[style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        div[style*="grid-column:span 2"] { grid-column: span 1 !important; }
    }
</style>

@endsection