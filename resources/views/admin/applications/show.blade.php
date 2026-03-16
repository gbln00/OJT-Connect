@extends('layouts.app')
@section('title', 'Application Details')
@section('page-title', 'Application Details')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:var(--teal-dim);border:1px solid var(--teal);color:var(--teal);padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;">
        {{ session('success') }}
    </div>
@endif

{{-- BACK + STATUS HEADER --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <a href="{{ route('admin.applications.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15,18 9,12 15,6"/></svg>
        Back to applications
    </a>
    <span style="display:inline-flex;align-items:center;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:500;
        background:var(--{{ $application->status_class }}-dim);color:var(--{{ $application->status_class }});">
        {{ $application->status_label }}
    </span>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

    {{-- LEFT: APPLICATION DETAILS --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Student info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Student information</div>
            </div>
            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Full name</div>
                    <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $application->student->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Email</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $application->student->email }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Program / Course</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->program }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Account status</div>
                    <span class="status-dot {{ $application->student->is_active ? 'active' : 'inactive' }}">
                        {{ $application->student->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- OJT details --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">OJT details</div>
            </div>
            <div style="padding:20px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Company</div>
                    <div style="font-size:13.5px;color:var(--text);font-weight:500;">{{ $application->company->name }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Industry</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $application->company->industry ?? '—' }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">School year</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->school_year }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Semester</div>
                    <div style="font-size:13px;color:var(--text);">{{ $application->semester }} Semester</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Required hours</div>
                    <div style="font-size:13px;color:var(--text);">{{ number_format($application->required_hours) }} hours</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Date submitted</div>
                    <div style="font-size:13px;color:var(--muted2);">{{ $application->created_at->format('M d, Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Document --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Submitted document</div>
            </div>
            <div style="padding:20px;">
                @if($application->document_path)
                    <a href="{{ Storage::url($application->document_path) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 16px;border-radius:8px;border:1px solid var(--border2);color:var(--blue);font-size:13px;text-decoration:none;background:var(--blue-dim);"
                       onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14,2 14,8 20,8"/>
                        </svg>
                        View document
                    </a>
                @else
                    <div style="font-size:13px;color:var(--muted);">No document uploaded.</div>
                @endif
            </div>
        </div>

        {{-- Remarks --}}
        @if($application->remarks)
        <div class="card">
            <div class="card-header">
                <div class="card-title">Remarks</div>
            </div>
            <div style="padding:20px;font-size:13px;color:var(--muted2);line-height:1.6;">
                {{ $application->remarks }}
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: ACTIONS --}}
    <div style="display:flex;flex-direction:column;gap:16px;">

        {{-- Review info --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Review status</div>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Status</span>
                    <span style="font-size:12.5px;font-weight:500;color:var(--{{ $application->status_class }});">
                        {{ $application->status_label }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed by</span>
                    <span style="font-size:12.5px;color:var(--text);">
                        {{ $application->reviewer?->name ?? '—' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                    <span style="font-size:12.5px;color:var(--muted2);">Reviewed at</span>
                    <span style="font-size:12.5px;color:var(--text);">
                        {{ $application->reviewed_at?->format('M d, Y') ?? '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        @if($application->isPending())
        <div class="card">
            <div class="card-header">
                <div class="card-title">Actions</div>
            </div>
            <div style="padding:16px;display:flex;flex-direction:column;gap:10px;">

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.applications.approve', $application) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:4px;">Remarks (optional)</label>
                        <textarea name="remarks" rows="2"
                            placeholder="Add a note..."
                            style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;"
                            onfocus="this.style.borderColor='var(--teal)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--teal);color:var(--bg);font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                        ✓ Approve application
                    </button>
                </form>

                <div style="height:1px;background:var(--border);"></div>

                {{-- Reject --}}
                <form method="POST" action="{{ route('admin.applications.reject', $application) }}">
                    @csrf
                    <div style="margin-bottom:8px;">
                        <label style="display:block;font-size:12px;color:var(--muted2);margin-bottom:4px;">Reason for rejection <span style="color:var(--coral);">*</span></label>
                        <textarea name="remarks" rows="2" required
                            placeholder="Explain the reason..."
                            style="width:100%;padding:8px 10px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:12px;resize:none;font-family:inherit;outline:none;"
                            onfocus="this.style.borderColor='var(--coral)'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    </div>
                    <button type="submit"
                        style="width:100%;padding:10px;border-radius:8px;border:none;background:var(--coral);color:#fff;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;">
                        ✕ Reject application
                    </button>
                </form>

            </div>
        </div>
        @endif

        {{-- Delete --}}
        <div class="card" style="border-color:rgba(248,113,113,0.2);">
            <div style="padding:16px;">
                <div style="font-size:12.5px;color:var(--muted2);margin-bottom:10px;">Permanently delete this application and its document.</div>
                <form method="POST" action="{{ route('admin.applications.destroy', $application) }}"
                      onsubmit="return confirm('Delete this application? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        style="width:100%;padding:9px;border-radius:8px;border:1px solid var(--coral);background:none;color:var(--coral);font-size:13px;cursor:pointer;font-family:inherit;"
                        onmouseover="this.style.background='var(--coral-dim)'" onmouseout="this.style.background='none'">
                        Delete application
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection