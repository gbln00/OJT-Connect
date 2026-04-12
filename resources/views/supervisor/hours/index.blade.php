{{-- resources/views/supervisor/hours/index.blade.php --}}
@extends('layouts.supervisor-app')
@section('title', 'Hour Logs')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Management / Intern Hour Logs
    </span>
</div>

{{-- Page heading --}}
<div class="fade-up" style="margin-bottom:20px;">
    <h1 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--text);margin:0 0 4px;">Hour Logs</h1>
    <p style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin:0;">Review and approve submitted intern time records</p>
</div>

{{-- Stats --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <span class="stat-tag">review</span>
        </div>
        <div class="stat-num" style="{{ $pending > 0 ? 'color:#D97706;' : '' }}">{{ $pending }}</div>
        <div class="stat-label">Pending logs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
            </div>
            <span class="stat-tag">done</span>
        </div>
        <div class="stat-num">{{ $approved }}</div>
        <div class="stat-label">Approved logs</div>
    </div>
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <span class="stat-tag">roster</span>
        </div>
        <div class="stat-num">{{ $interns->count() }}</div>
        <div class="stat-label">Active interns</div>
    </div>
</div>

{{-- Filters --}}
<div class="card fade-up fade-up-1" style="margin-bottom:20px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" style="color:var(--muted);"><polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46"/></svg>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Filter</span>
    </div>
    <form method="GET" action="{{ route('supervisor.hours.index') }}" style="padding:16px 20px;">
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div style="flex:1;min-width:160px;">
                <label class="form-label" style="display:block;margin-bottom:6px;">Intern</label>
                <div style="position:relative;">
                    <select name="student_id" class="form-select">
                        <option value="">All interns</option>
                        @foreach ($interns as $intern)
                            <option value="{{ $intern->id }}" {{ request('student_id') == $intern->id ? 'selected' : '' }}>{{ $intern->name }}</option>
                        @endforeach
                    </select>
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted);"><polyline points="6,9 12,15 18,9"/></svg>
                </div>
            </div>
            <div style="display:flex;gap:6px;align-items:flex-end;">
                <button type="submit" class="btn btn-ghost btn-sm" style="display:flex;align-items:center;gap:5px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
                @if(request('student_id'))
                    <a href="{{ route('supervisor.hours.index') }}" class="btn btn-ghost btn-sm" style="color:var(--muted);">✕ Clear</a>
                @endif
            </div>
        </div>
    </form>
</div>

@php
    $pendingLogs  = $logs->getCollection()->where('status', 'pending');
    $approvedLogs = $logs->getCollection()->where('status', 'approved');
    $rejectedLogs = $logs->getCollection()->where('status', 'rejected');
@endphp

{{-- ── PENDING TABLE ─────────────────────────────────────────────── --}}
@if($pendingLogs->isNotEmpty())
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:8px;height:8px;background:#D97706;display:inline-block;flex-shrink:0;"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Pending Review</span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;background:rgba(217,119,6,0.1);color:#D97706;border:1px solid rgba(217,119,6,0.3);padding:2px 8px;">
            {{ $pendingLogs->count() }} log{{ $pendingLogs->count() !== 1 ? 's' : '' }}
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Intern</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingLogs as $log)
                <tr class="log-row"
                    data-log-id="{{ $log->id }}"
                    data-student="{{ $log->student->name }}"
                    data-date="{{ $log->date->format('l, F j, Y') }}"
                    data-session="{{ $log->session }}"
                    data-time-in="{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}"
                    data-time-out="{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}"
                    data-hours="{{ number_format($log->total_hours, 1) }}"
                    data-description="{{ $log->description ?? '' }}"
                    data-status="pending"
                    data-approve-url="{{ route('supervisor.hours.approve', $log) }}"
                    data-reject-url="{{ route('supervisor.hours.reject', $log) }}"
                    data-show-url="{{ route('supervisor.hours.show', $log->student_id) }}">
                    <td>
                        <button type="button" onclick="openLogModal(this.closest('tr'))"
                                style="background:none;border:none;cursor:pointer;padding:0;text-align:left;display:flex;align-items:center;gap:9px;">
                            <div style="width:30px;height:30px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($log->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $log->student->name }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--crimson);">click to review →</div>
                            </div>
                        </button>
                    </td>
                    <td>
                        <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $log->date->format('M d, Y') }}</div>
                        <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">{{ $log->date->format('l') }}</div>
                    </td>
                    <td><span class="session-pill {{ $log->session }}">{{ $log->session === 'morning' ? '☀ AM' : '◑ PM' }}</span></td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                    </td>
                    <td style="text-align:right;">
                        <div style="display:flex;gap:4px;justify-content:flex-end;">
                            <form method="POST" action="{{ route('supervisor.hours.approve', $log) }}">
                                @csrf
                                <button type="submit" class="btn btn-approve btn-sm">✓ Approve</button>
                            </form>
                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="openLogModal(this.closest('tr'))">
                                ✕ Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── APPROVED TABLE ────────────────────────────────────────────── --}}
@if($approvedLogs->isNotEmpty())
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:8px;height:8px;background:#34d399;display:inline-block;flex-shrink:0;"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Approved</span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;background:rgba(52,211,153,0.08);color:#34d399;border:1px solid rgba(52,211,153,0.3);padding:2px 8px;">
            {{ $approvedLogs->count() }} log{{ $approvedLogs->count() !== 1 ? 's' : '' }}
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Intern</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Approved At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($approvedLogs as $log)
                <tr class="log-row"
                    data-student="{{ $log->student->name }}"
                    data-date="{{ $log->date->format('l, F j, Y') }}"
                    data-session="{{ $log->session }}"
                    data-time-in="{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}"
                    data-time-out="{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}"
                    data-hours="{{ number_format($log->total_hours, 1) }}"
                    data-description="{{ $log->description ?? '' }}"
                    data-status="approved"
                    data-show-url="{{ route('supervisor.hours.show', $log->student_id) }}">
                    <td>
                        <button type="button" onclick="openLogModal(this.closest('tr'))"
                                style="background:none;border:none;cursor:pointer;padding:0;text-align:left;display:flex;align-items:center;gap:9px;">
                            <div style="width:30px;height:30px;flex-shrink:0;border:1px solid rgba(52,211,153,0.25);background:rgba(52,211,153,0.06);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:#34d399;">
                                {{ strtoupper(substr($log->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $log->student->name }}</div>
                                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">view detail →</div>
                            </div>
                        </button>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $log->date->format('M d, Y') }}</td>
                    <td><span class="session-pill {{ $log->session }}">{{ $log->session === 'morning' ? '☀ AM' : '◑ PM' }}</span></td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_in)->format('h:i A') }}</td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ \Carbon\Carbon::parse($log->time_out)->format('h:i A') }}</td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $log->approved_at ? $log->approved_at->format('M d, Y') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── REJECTED TABLE ────────────────────────────────────────────── --}}
@if($rejectedLogs->isNotEmpty())
<div class="card fade-up fade-up-2" style="margin-bottom:16px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="width:8px;height:8px;background:var(--crimson);display:inline-block;flex-shrink:0;"></span>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Rejected</span>
        </div>
        <span style="font-family:'DM Mono',monospace;font-size:10px;background:rgba(140,14,3,0.07);color:var(--crimson);border:1px solid rgba(140,14,3,0.25);padding:2px 8px;">
            {{ $rejectedLogs->count() }} log{{ $rejectedLogs->count() !== 1 ? 's' : '' }}
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Intern</th>
                    <th>Date</th>
                    <th>Session</th>
                    <th>Hours</th>
                    <th>Rejection Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rejectedLogs as $log)
                <tr class="log-row">
                    <td>
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:30px;height:30px;flex-shrink:0;border:1px solid rgba(140,14,3,0.2);background:rgba(140,14,3,0.05);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:11px;font-weight:700;color:var(--crimson);">
                                {{ strtoupper(substr($log->student->name, 0, 2)) }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:var(--text);">{{ $log->student->name }}</div>
                                <a href="{{ route('supervisor.hours.show', $log->student_id) }}" style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-decoration:none;">view logs →</a>
                            </div>
                        </div>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text2);">{{ $log->date->format('M d, Y') }}</td>
                    <td><span class="session-pill {{ $log->session }}">{{ $log->session === 'morning' ? '☀ AM' : '◑ PM' }}</span></td>
                    <td>
                        <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:15px;color:var(--text);">{{ number_format($log->total_hours, 1) }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);"> hrs</span>
                    </td>
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);max-width:220px;">
                        {{ $log->rejection_reason ?? '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Empty state --}}
@if($pendingLogs->isEmpty() && $approvedLogs->isEmpty() && $rejectedLogs->isEmpty())
<div class="card fade-up fade-up-2" style="padding:64px 32px;text-align:center;">
    <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" style="color:var(--muted);opacity:0.4;"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>
        <div style="font-family:'Playfair Display',serif;font-size:16px;color:var(--text2);">No logs found</div>
        <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">No hour logs have been submitted yet</div>
    </div>
</div>
@endif

{{-- Pagination --}}
@if ($logs->hasPages())
<div class="pagination fade-up" style="margin-top:8px;">
    <span class="pagination-info">Showing <strong>{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</strong> of <strong>{{ $logs->total() }}</strong></span>
    <div style="display:flex;gap:4px;">
        @if ($logs->onFirstPage())
            <span class="page-link disabled">← Prev</span>
        @else
            <a href="{{ $logs->previousPageUrl() }}" class="page-link">← Prev</a>
        @endif
        @if ($logs->hasMorePages())
            <a href="{{ $logs->nextPageUrl() }}" class="page-link">Next →</a>
        @else
            <span class="page-link disabled">Next →</span>
        @endif
    </div>
</div>
@endif


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- LOG DETAIL / APPROVE·REJECT MODAL                            --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div id="log-modal-backdrop"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:900;align-items:center;justify-content:center;padding:20px;">
    <div id="log-modal"
         style="background:var(--surface);border:1px solid var(--border2);width:100%;max-width:460px;position:relative;">

        {{-- Header --}}
        <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div id="modal-avatar"
                     style="width:38px;height:38px;flex-shrink:0;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:13px;font-weight:700;color:var(--crimson);">
                </div>
                <div>
                    <div id="modal-name" style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:var(--text);line-height:1.2;"></div>
                    <div id="modal-sub" style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;"></div>
                </div>
            </div>
            <button onclick="closeLogModal()" style="background:none;border:none;cursor:pointer;color:var(--muted);padding:4px;line-height:1;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        {{-- Details grid --}}
        <div style="padding:18px 20px 0;">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:14px;">
                <div style="background:var(--surface2);border:1px solid var(--border);padding:10px 12px;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Session</div>
                    <div id="modal-session" style="font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:var(--text);"></div>
                </div>
                <div style="background:var(--surface2);border:1px solid var(--border);padding:10px 12px;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Time In</div>
                    <div id="modal-timein" style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);"></div>
                </div>
                <div style="background:var(--surface2);border:1px solid var(--border);padding:10px 12px;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">Time Out</div>
                    <div id="modal-timeout" style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);"></div>
                </div>
            </div>

            {{-- Hours hero --}}
            <div style="background:var(--surface2);border:1px solid var(--border);padding:14px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);">Total Hours</span>
                <div>
                    <span id="modal-hours" style="font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--text);"></span>
                    <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);"> hrs</span>
                </div>
            </div>

            {{-- Description --}}
            <div id="modal-desc-wrap" style="margin-bottom:14px;display:none;">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Work Description</div>
                <div id="modal-desc" style="background:var(--surface2);border:1px solid var(--border);padding:12px;font-size:13px;color:var(--text2);line-height:1.55;font-family:'Barlow',sans-serif;"></div>
            </div>

            {{-- Rejection reason textarea (pending only) --}}
            <div id="modal-reject-reason-wrap" style="display:none;margin-bottom:14px;">
                <label style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.14em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:6px;">
                    Rejection Reason <span style="opacity:0.6;">(optional)</span>
                </label>
                <textarea id="modal-reject-reason" rows="2"
                          placeholder="Explain why this log is being rejected..."
                          style="width:100%;background:var(--surface2);border:1px solid var(--border2);color:var(--text);font-family:'Barlow',sans-serif;font-size:13px;padding:10px 12px;resize:none;outline:none;box-sizing:border-box;transition:border-color 0.15s;border-radius:0;"
                          onfocus="this.style.borderColor='var(--crimson)'"
                          onblur="this.style.borderColor='var(--border2)'"></textarea>
            </div>

        </div>

        {{-- Action footer --}}
        <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;gap:8px;" id="modal-actions"></div>
    </div>
</div>


@push('styles')
<style>
.session-pill {
    display:inline-flex;align-items:center;gap:4px;padding:3px 10px;
    font-family:'DM Mono',monospace;font-size:10px;font-weight:600;
    letter-spacing:0.08em;text-transform:uppercase;border:1px solid;
}
.session-pill.morning   { background:rgba(254,243,199,0.12);color:#D97706;border-color:rgba(217,119,6,0.3); }
.session-pill.afternoon { background:rgba(219,234,254,0.12);color:#3B82F6;border-color:rgba(59,130,246,0.3); }
.form-label { font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted); }
.form-select {
    width:100%;padding:9px 32px 9px 14px;background:var(--surface2);border:1px solid var(--border2);
    color:var(--text);font-size:13px;font-family:'Barlow',sans-serif;outline:none;
    transition:border-color 0.15s;border-radius:0;appearance:none;-webkit-appearance:none;
}
.form-select:focus { border-color:var(--crimson); }
.log-row { transition:background 0.15s; }
.log-row:hover { background:var(--surface2); }
#log-modal-backdrop.open { display:flex !important; }
@keyframes modalSlideIn {
    from { opacity:0;transform:translateY(12px); }
    to   { opacity:1;transform:translateY(0); }
}
#log-modal { animation: modalSlideIn 0.18s ease; }
</style>
@endpush

@push('scripts')
<script>
let _approveUrl = null;
let _rejectUrl  = null;
let _status     = null;

function openLogModal(row) {
    const d = row.dataset;
    _approveUrl = d.approveUrl || null;
    _rejectUrl  = d.rejectUrl  || null;
    _status     = d.status;

    const name = d.student || '';
    document.getElementById('modal-avatar').textContent = name.slice(0, 2).toUpperCase();
    document.getElementById('modal-name').textContent   = name;
    document.getElementById('modal-sub').textContent    = d.date + ' · ' + (d.session === 'morning' ? 'Morning' : 'Afternoon');
    document.getElementById('modal-session').textContent = d.session === 'morning' ? '☀ AM · Morning' : '◑ PM · Afternoon';
    document.getElementById('modal-timein').textContent  = d.timeIn;
    document.getElementById('modal-timeout').textContent = d.timeOut;
    document.getElementById('modal-hours').textContent   = d.hours;

    const desc = (d.description || '').trim();
    const dw   = document.getElementById('modal-desc-wrap');
    if (desc) { document.getElementById('modal-desc').textContent = desc; dw.style.display = 'block'; }
    else       { dw.style.display = 'none'; }

    const rw = document.getElementById('modal-reject-reason-wrap');
    rw.style.display = _status === 'pending' ? 'block' : 'none';
    if (_status === 'pending') document.getElementById('modal-reject-reason').value = '';

    // Build action buttons
    const actions = document.getElementById('modal-actions');
    actions.innerHTML = '';

    if (_status === 'pending') {
        const approveBtn = mkBtn('✓ Approve', 'btn btn-approve btn-sm', submitApprove, '1');
        const rejectBtn  = mkBtn('✕ Reject',  'btn btn-danger btn-sm',  submitReject,  '1');
        const cancelBtn  = mkBtn('Cancel',    'btn btn-ghost btn-sm',   closeLogModal, '0');
        actions.appendChild(cancelBtn);
        actions.appendChild(approveBtn);
        actions.appendChild(rejectBtn);
    } else {
        const closeBtn = mkBtn('Close', 'btn btn-ghost btn-sm', closeLogModal, '1');
        const viewBtn  = document.createElement('a');
        viewBtn.textContent = 'Full history →';
        viewBtn.className   = 'btn btn-ghost btn-sm';
        viewBtn.href        = d.showUrl || '#';
        viewBtn.style.flex  = '1';
        actions.appendChild(closeBtn);
        actions.appendChild(viewBtn);
    }

    document.getElementById('log-modal-backdrop').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function mkBtn(text, cls, fn, flex) {
    const b = document.createElement('button');
    b.textContent = text;
    b.className   = cls;
    b.style.flex  = flex;
    b.onclick     = fn;
    return b;
}

function closeLogModal() {
    document.getElementById('log-modal-backdrop').classList.remove('open');
    document.body.style.overflow = '';
}

function submitApprove() {
    if (!_approveUrl) return;
    postToUrl(_approveUrl, {});
}

function submitReject() {
    if (!_rejectUrl) return;
    const reason = document.getElementById('modal-reject-reason').value.trim();
    postToUrl(_rejectUrl, { rejection_reason: reason });
}

function postToUrl(url, fields) {
    const form = document.createElement('form');
    form.method = 'POST'; form.action = url;
    const tok = document.createElement('input');
    tok.type = 'hidden'; tok.name = '_token';
    tok.value = document.querySelector('meta[name="csrf-token"]')?.content || '';
    form.appendChild(tok);
    for (const [k, v] of Object.entries(fields)) {
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = k; i.value = v;
        form.appendChild(i);
    }
    document.body.appendChild(form);
    form.submit();
}

document.getElementById('log-modal-backdrop').addEventListener('click', function(e) {
    if (e.target === this) closeLogModal();
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLogModal(); });
</script>
@endpush

@endsection