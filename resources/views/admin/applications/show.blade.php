@extends('layouts.app')
@section('title', 'Application Details')
@section('page-title', 'Application Details')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- BACK + STATUS HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <a href="{{ route('admin.applications.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;transition:color 0.15s;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
        Back to applications
    </a>
    <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:500;
        background:var(--{{ $application->status_class }}-dim);color:var(--{{ $application->status_class }});">
        {{ $application->status_label }}
    </span>
</div>

<div style="max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

    {{-- LEFT: APPLICATION DETAILS --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info --}}
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--crimson-dim,rgba(140,14,3,0.08));display:flex;align-items:center;justify-content:center;color:var(--crimson);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    Student information
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Full name</div>
                        <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $application->student->name }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Email</div>
                        <div style="font-size:13px;color:var(--muted2);font-family:'DM Mono',monospace;">{{ $application->student->email }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Program / Course</div>
                        <div style="font-size:13px;color:var(--text);">{{ $application->program }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Account status</div>
                        <span class="status-dot {{ $application->student->is_active ? 'active' : 'inactive' }}">
                            {{ $application->student->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- OJT details --}}
        <div class="card fade-up fade-up-1">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:rgba(240,180,41,0.1);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                    </div>
                    OJT details
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Company</div>
                        <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $application->company->name }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Industry</div>
                        <div style="font-size:13px;color:var(--muted2);">{{ $application->company->industry ?? '—' }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">School year</div>
                        <div style="font-size:13px;color:var(--text);font-family:'DM Mono',monospace;">{{ $application->school_year }}</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Semester</div>
                        <div style="font-size:13px;color:var(--text);">{{ $application->semester }} Semester</div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Required hours</div>
                        <div style="font-size:20px;font-weight:700;color:var(--gold);font-family:'Playfair Display',serif;">{{ number_format($application->required_hours) }}<span style="font-size:12px;font-weight:400;color:var(--muted);font-family:inherit;margin-left:4px;">hrs</span></div>
                    </div>
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:8px;padding:14px 16px;">
                        <div style="font-size:10px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.07em;font-weight:600;">Date submitted</div>
                        <div style="font-size:13px;color:var(--muted2);font-family:'DM Mono',monospace;">{{ $application->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Document --}}
        <div class="card fade-up fade-up-2">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:rgba(96,165,250,0.1);display:flex;align-items:center;justify-content:center;color:var(--blue);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    </div>
                    Submitted document
                </div>
            </div>
            <div style="padding:20px;">
                @if($application->document_path)
                    <a href="{{ Storage::url($application->document_path) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:8px;border:1px solid var(--border2);color:var(--blue);font-size:13px;text-decoration:none;background:rgba(96,165,250,0.06);transition:all 0.15s;"
                       onmouseover="this.style.background='rgba(96,165,250,0.12)';this.style.borderColor='var(--blue)'" onmouseout="this.style.background='rgba(96,165,250,0.06)';this.style.borderColor='var(--border2)'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        View document
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.5;"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    </a>
                @else
                    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);background:var(--surface2);border:1px dashed var(--border2);border-radius:8px;padding:14px 16px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        No document uploaded.
                    </div>
                @endif
            </div>
        </div>

        {{-- Remarks --}}
        @if($application->remarks)
        <div class="card fade-up fade-up-3">
            <div class="card-header">
                <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--surface2);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;color:var(--muted2);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </div>
                    Remarks
                </div>
            </div>
            <div style="padding:20px;font-size:13px;color:var(--muted2);line-height:1.7;background:var(--surface2);margin:0 16px 16px;border-radius:8px;border:1px solid var(--border);">
                {{ $application->remarks }}
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: ACTIONS --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Review info --}}
        <div class="card fade-up">
            <div class="card-header">
                <div class="card-title">Review status</div>
            </div>
            <div style="padding:4px 0;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Status</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--{{ $application->status_class }});">
                        {{ $application->status_label }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed by</span>
                    <span style="font-size:12.5px;color:var(--text);">
                        {{ $application->reviewer?->name ?? '—' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed at</span>
                    <span style="font-size:12.5px;color:var(--text);font-family:'DM Mono',monospace;font-size:11px;">
                        {{ $application->reviewed_at?->format('M d, Y') ?? '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        @if($application->isPending())
        <div class="card fade-up fade-up-1">
            <div class="card-header">
                <div class="card-title">Actions</div>
                <span style="font-size:10px;padding:2px 8px;border-radius:10px;background:var(--gold-dim,rgba(240,180,41,0.1));color:var(--gold);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;">Pending</span>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:5px;font-weight:500;">Remarks <span style="font-weight:400;color:var(--muted);">(optional)</span></label>
                        <textarea name="remarks" rows="2"
                            placeholder="Add a note..."
                            style="width:100%;padding:9px 11px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;transition:border 0.15s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--teal);color:var(--bg);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;transition:opacity 0.15s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Approve application
                    </button>
                </form>

                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                    <span style="font-size:11px;color:var(--muted);">or</span>
                    <div style="flex:1;height:1px;background:var(--border);"></div>
                </div>

                {{-- Reject --}}
                <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:5px;font-weight:500;">Reason for rejection <span style="color:var(--coral);">*</span></label>
                        <textarea name="remarks" rows="2" required
                            placeholder="Explain the reason..."
                            style="width:100%;padding:9px 11px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;transition:border 0.15s;box-sizing:border-box;"
                            onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:1px solid var(--coral);background:none;color:var(--coral);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;transition:background 0.15s;"
                        onmouseover="this.style.background='rgba(248,113,113,0.08)'" onmouseout="this.style.background='none'">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Reject application
                    </button>
                </form>

            </div>
        </div>
        @endif

        {{-- Delete --}}
        <div class="card fade-up fade-up-2" style="border-color:rgba(248,113,113,0.15);">
            <div style="padding:16px;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                    <svg width="13" height="13" fill="none" stroke="var(--coral)" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span style="font-size:11px;font-weight:600;color:var(--coral);text-transform:uppercase;letter-spacing:0.06em;">Danger zone</span>
                </div>
                <div style="font-size:12.5px;color:var(--muted2);margin-bottom:12px;line-height:1.5;">Permanently deletes this application and any uploaded document. This cannot be undone.</div>
                <form method="POST" action="{{ route('admin.applications.destroy', $application) }}"
                      onsubmit="return confirm('Delete this application? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="width:100%;padding:9px;border-radius:8px;border:1px solid rgba(248,113,113,0.4);background:none;color:var(--coral);font-size:13px;cursor:pointer;font-family:inherit;transition:all 0.15s;"
                        onmouseover="this.style.background='rgba(248,113,113,0.08)';this.style.borderColor='var(--coral)'" onmouseout="this.style.background='none';this.style.borderColor='rgba(248,113,113,0.4)'">
                        Delete application
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection