@extends('layouts.app')
@section('title', 'Export Reports')
@section('page-title', 'Export Reports')

@section('content')

@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- STAT STRIP --}}
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $stats['total_students'] }}</div>
        <div class="stat-label">Student interns</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ number_format($stats['total_hours'], 1) }}</div>
        <div class="stat-label">Approved hours logged</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
        </div>
        <div class="stat-num">{{ $stats['total_evaluations'] }}</div>
        <div class="stat-label">Evaluations submitted</div>
    </div>
</div>

{{-- EXPORT CARDS GRID --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    {{-- PDF: Student Summary --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:7px;background:var(--coral-dim);display:flex;align-items:center;justify-content:center;color:var(--coral);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                </div>
                PDF — Student OJT Summary
            </div>
            <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:var(--coral-dim);color:var(--coral);font-weight:500;">PDF</span>
        </div>
        <div style="padding:18px 20px;">
            <p style="font-size:13px;color:var(--muted2);margin-bottom:16px;line-height:1.6;">
                Exports a PDF table of all approved student interns with their company, program, required hours, and approved hours logged.
            </p>
            <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">{{ $stats['total_applications'] }} applications</span>
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">{{ $stats['total_students'] }} students</span>
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Landscape A4</span>
            </div>
            <a href="{{ route('admin.export.pdf.students') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--coral);color:#fff;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;"
               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- PDF: Evaluations --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:7px;background:var(--coral-dim);display:flex;align-items:center;justify-content:center;color:var(--coral);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                PDF — Evaluations Summary
            </div>
            <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:var(--coral-dim);color:var(--coral);font-weight:500;">PDF</span>
        </div>
        <div style="padding:18px 20px;">
            <p style="font-size:13px;color:var(--muted2);margin-bottom:16px;line-height:1.6;">
                Exports a PDF table of all student evaluations including attendance rating, performance rating, overall grade, and pass/fail recommendation.
            </p>
            <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">{{ $stats['total_evaluations'] }} evaluations</span>
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Ratings + grades</span>
                <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Landscape A4</span>
            </div>
            <a href="{{ route('admin.export.pdf.evaluations') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--coral);color:#fff;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;"
               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download PDF
            </a>
        </div>
    </div>

    {{-- Excel: Full Report --}}
    <div class="card" style="grid-column:span 2;">
        <div class="card-header">
            <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:7px;background:var(--teal-dim);display:flex;align-items:center;justify-content:center;color:var(--teal);">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>
                </div>
                Excel — Full OJT Report
            </div>
            <span style="font-size:11px;padding:2px 8px;border-radius:10px;background:var(--teal-dim);color:var(--teal);font-weight:500;">XLSX</span>
        </div>
        <div style="padding:18px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
            <div style="flex:1;">
                <p style="font-size:13px;color:var(--muted2);margin-bottom:16px;line-height:1.6;">
                    Exports a full Excel workbook with 3 sheets — Students, Evaluations, and Hour Logs — with auto-sized columns and styled headers. Ideal for record-keeping and further analysis.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Sheet 1: Students ({{ $stats['total_applications'] }} rows)</span>
                    <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Sheet 2: Evaluations ({{ $stats['total_evaluations'] }} rows)</span>
                    <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">Sheet 3: Hour Logs</span>
                    <span style="font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);border:1px solid var(--border2);color:var(--muted2);">.xlsx format</span>
                </div>
            </div>
            <a href="{{ route('admin.export.excel') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:var(--teal);color:var(--bg);border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;white-space:nowrap;"
               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download Excel
            </a>
        </div>
    </div>

</div>

@endsection