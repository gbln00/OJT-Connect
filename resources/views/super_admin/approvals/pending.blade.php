@extends('layouts.superadmin-app')
@section('title', 'Tenant Approvals')
@section('page-title', 'Tenant Approvals')

@section('content')

@php
$planColors = [
    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.8)'],
    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
];

$pendingCount  = \App\Models\TenantRegistration::where('status','pending')->count();
$approvedCount = \App\Models\TenantRegistration::where('status','approved')->count();
$rejectedCount = \App\Models\TenantRegistration::where('status','rejected')->count();
$totalCount    = \App\Models\TenantRegistration::count();
@endphp

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(4,1fr);">
    @foreach([
        [$pendingCount,  'Pending',         'stat-icon gold',   '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$approvedCount, 'Approved',        'stat-icon green',  '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$rejectedCount, 'Rejected',        'stat-icon crimson','<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$totalCount,    'Total Submitted', 'stat-icon steel',  '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>'],
    ] as [$val, $label, $iconClass, $iconSvg])
    <div class="stat-card">
        <div class="stat-top">
            <div class="{{ $iconClass }}">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">{!! $iconSvg !!}</svg>
            </div>
            <span class="stat-tag">{{ strtolower($label) }}</span>
        </div>
        <div class="stat-num">{{ $val }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── Main Card ── --}}
<div class="card fade-up fade-up-1">

    {{-- Header --}}
    <div class="card-header">
        <div>
            <div class="card-title-main">Pending Registrations</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                // {{ $pendingCount }} registration{{ $pendingCount !== 1 ? 's' : '' }} awaiting review
            </div>
        </div>
        {{-- Plan legend --}}
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
            @foreach(['basic'=>'Basic','standard'=>'Standard','premium'=>'Premium'] as $key => $lbl)
            @php $c = $planColors[$key]; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid {{ $c['border'] }};background:{{ $c['bg'] }};
                         font-family:'Barlow Condensed',sans-serif;font-size:10px;font-weight:600;
                         letter-spacing:0.1em;text-transform:uppercase;color:{{ $c['color'] }};">
                {{ $lbl }}
            </span>
            @endforeach
        </div>
    </div>

    @if($registrations->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:8px;">All caught up</div>
            <div style="font-size:13px;color:var(--muted);font-family:'DM Mono',monospace;">// No pending registrations at the moment.</div>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Institution</th>
                        <th>Contact</th>
                        <th>Subdomain</th>
                        <th>Plan</th>
                        <th>Submitted</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                    @php
                        $pc = $planColors[$reg->plan] ?? ['border'=>'var(--border2)','bg'=>'transparent','color'=>'var(--muted)'];
                    @endphp
                    <tr style="cursor:pointer;"
                        onclick="openDetailModal(
                            '{{ addslashes($reg->company_name) }}',
                            '{{ addslashes($reg->subdomain) }}',
                            '{{ addslashes($reg->email) }}',
                            '{{ addslashes($reg->contact_person) }}',
                            '{{ addslashes($reg->phone ?? '—') }}',
                            '{{ ucfirst($reg->plan) }}',
                            '{{ $reg->created_at->diffForHumans() }}',
                            {{ $reg->id }}
                        )">

                        {{-- Institution --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;background:rgba(140,14,3,0.08);border:1px solid rgba(140,14,3,0.2);
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:var(--crimson);">
                                        {{ strtoupper(substr($reg->company_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div style="font-weight:500;color:var(--text);">{{ $reg->company_name }}</div>
                                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $reg->email }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td>
                            <div style="color:var(--text2);">{{ $reg->contact_person }}</div>
                            @if($reg->phone)
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $reg->phone }}</div>
                            @endif
                        </td>

                        {{-- Subdomain --}}
                        <td>
                            <span class="domain-pill" style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border:1px solid var(--border2);background:var(--surface2);font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">
                                <span style="width:4px;height:4px;background:var(--crimson);display:inline-block;border-radius:50%;"></span>
                                {{ $reg->subdomain }}.ojt-connect.xyz
                            </span>
                        </td>

                        {{-- Plan --}}
                        <td>
                            <span class="status-pill" style="border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};color:{{ $pc['color'] }};">
                                {{ ucfirst($reg->plan) }}
                            </span>
                        </td>

                        {{-- Submitted --}}
                        <td>
                            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ $reg->created_at->format('M d, Y') }}</div>
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;">{{ $reg->created_at->diffForHumans() }}</div>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align:right;" onclick="event.stopPropagation()">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <form method="POST" action="{{ route('super_admin.approvals.approve', $reg) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn btn-approve btn-sm">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm"
                                        onclick="openRejectModal({{ $reg->id }}, '{{ addslashes($reg->company_name) }}')">
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($registrations->hasPages())
        <div class="pagination">
            <span class="pagination-info">Showing {{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} of {{ $registrations->total() }}</span>
            <div style="display:flex;gap:4px;">
                @if($registrations->onFirstPage())
                    <span class="page-link disabled">← Prev</span>
                @else
                    <a href="{{ $registrations->previousPageUrl() }}" class="page-link">← Prev</a>
                @endif
                @if($registrations->hasMorePages())
                    <a href="{{ $registrations->nextPageUrl() }}" class="page-link">Next →</a>
                @else
                    <span class="page-link disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

{{-- Info strip --}}
<div class="flash flash-info fade-up fade-up-2" style="margin-top:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    Approving a registration provisions an isolated database, runs all tenant migrations, and seeds the admin account automatically. Rejection notifies the applicant with an optional reason.
</div>


{{-- ══ Detail Modal ══ --}}
<div id="detail-modal" class="modal-backdrop" onclick="closeDetailModal()">
    <div class="modal-box" onclick="event.stopPropagation()">
        <div class="modal-header">
            <div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.16em;text-transform:uppercase;margin-bottom:6px;">Registration Details</div>
                <div id="dm-company" style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--text);line-height:1.2;"></div>
            </div>
            <button onclick="closeDetailModal()" class="modal-close-btn">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div style="padding:4px 24px;">
            @foreach([['dm-subdomain','Subdomain'],['dm-email','Email'],['dm-contact','Contact Person'],['dm-phone','Phone'],['dm-plan','Plan'],['dm-submitted','Submitted']] as [$id,$label])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid var(--border);">
                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.15em;text-transform:uppercase;">{{ $label }}</span>
                <span id="{{ $id }}" style="font-size:13px;color:var(--text2);font-family:'DM Mono',monospace;text-align:right;max-width:280px;word-break:break-all;"></span>
            </div>
            @endforeach
        </div>
        <div class="modal-footer">
            <button onclick="closeDetailModal()" class="btn btn-ghost btn-sm">Close</button>
            <button type="button" class="btn btn-danger btn-sm"
                    onclick="closeDetailModal(); openRejectModal(window._dmId, window._dmCompany)">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reject
            </button>
            <form id="dm-approve-form" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-approve btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Approve Tenant
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══ Reject Modal ══ --}}
<div id="reject-modal" class="modal-backdrop" onclick="closeRejectModal()">
    <div class="modal-box" style="border-color:rgba(140,14,3,0.2);border-top-color:var(--crimson);" onclick="event.stopPropagation()">
        <div class="modal-header" style="border-bottom-color:rgba(140,14,3,0.12);">
            <div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.16em;text-transform:uppercase;margin-bottom:6px;">Rejection</div>
                <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">Reject Registration</div>
                <div style="font-size:12px;color:var(--muted);margin-top:4px;font-family:'DM Mono',monospace;">
                    // Rejecting <span id="rm-company" style="color:var(--crimson);"></span>. Applicant will be notified.
                </div>
            </div>
            <button onclick="closeRejectModal()" class="modal-close-btn">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="reject-form" method="POST">
            @csrf
            <div style="padding:20px 24px;">
                <label class="form-label">Reason <span style="text-transform:none;letter-spacing:0;color:var(--muted);font-size:10px;">(optional)</span></label>
                <textarea name="rejection_reason" rows="4" class="form-input form-textarea"
                          placeholder="Explain why this registration was rejected…"
                          style="resize:none;line-height:1.6;"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeRejectModal()" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.modal-backdrop {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(3px);
    z-index: 999; align-items: center; justify-content: center;
}
.modal-box {
    background: var(--surface);
    border: 1px solid var(--border2);
    border-top: 2px solid var(--crimson);
    width: 100%; max-width: 500px; margin: 0 20px;
    animation: fadeUp 0.2s cubic-bezier(.22,.61,.36,1) both;
}
.modal-header {
    padding: 22px 24px 18px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
}
.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex; gap: 8px; justify-content: flex-end;
}
.modal-close-btn {
    background: none; border: none; cursor: pointer;
    color: var(--muted); padding: 4px;
    display: flex; align-items: center; transition: color 0.2s;
}
.modal-close-btn:hover { color: var(--text); }

.domain-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 2px 8px;
    border: 1px solid var(--border2);
    background: var(--surface2);
    font-family: 'DM Mono', monospace;
    font-size: 10.5px; color: var(--text2);
}
</style>
@endpush

@push('scripts')
<script>
window._dmId = null;
window._dmCompany = '';

function openDetailModal(company, subdomain, email, contact, phone, plan, submitted, id) {
    window._dmId      = id;
    window._dmCompany = company;
    document.getElementById('dm-company').textContent   = company;
    document.getElementById('dm-subdomain').textContent = subdomain + '.ojt-connect.xyz';
    document.getElementById('dm-email').textContent     = email;
    document.getElementById('dm-contact').textContent   = contact;
    document.getElementById('dm-phone').textContent     = phone;
    document.getElementById('dm-plan').textContent      = plan;
    document.getElementById('dm-submitted').textContent = submitted;
    document.getElementById('dm-approve-form').action   = '{{ url('super-admin/approvals') }}/' + id + '/approve';
    const modal = document.getElementById('detail-modal');
    modal.style.display = 'flex';
}
function closeDetailModal() { document.getElementById('detail-modal').style.display = 'none'; }

function openRejectModal(id, company) {
    document.getElementById('rm-company').textContent = company;
    document.getElementById('reject-form').action     = '{{ url('super-admin/approvals') }}/' + id + '/reject';
    document.getElementById('reject-modal').style.display = 'flex';
}
function closeRejectModal() { document.getElementById('reject-modal').style.display = 'none'; }

document.addEventListener('DOMContentLoaded', function () {
    @if(session('success')) showFlash(@json(session('success')), 'success'); @endif
    @if(session('error'))   showFlash(@json(session('error')),   'error');   @endif
    @if(session('info'))    showFlash(@json(session('info')),    'info');    @endif
});

function showFlash(msg, type) {
    const el = document.createElement('div');
    el.className = `flash flash-${type}`;
    el.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:1000;max-width:380px;animation:fadeUp 0.3s ease both;';
    el.innerHTML = msg;
    document.body.appendChild(el);
    setTimeout(() => { el.style.opacity = '0'; el.style.transition = 'opacity 0.3s'; setTimeout(() => el.remove(), 300); }, 3800);
}
</script>
@endpush