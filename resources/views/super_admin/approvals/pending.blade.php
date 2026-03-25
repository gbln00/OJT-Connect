@extends('layouts.superadmin-app')

@section('title', 'Tenant Approvals')
@section('page-title', 'Tenant Approvals')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<style>
  .apr-wrap { font-family: 'DM Sans', sans-serif; }

  .apr-stat {
    background: #0e1c28;
    border: 1px solid rgba(52,91,99,.35);
    border-radius: 14px;
    padding: 20px 24px;
    animation: aprCardIn .35s cubic-bezier(.22,.68,0,1.2) both;
  }
  @keyframes aprCardIn {
    from { opacity: 0; transform: translateY(14px) scale(.98); }
    to   { opacity: 1; transform: none; }
  }

  .apr-stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: .1em; color: rgba(212,236,221,.4); font-weight: 600; margin-bottom: 10px; }
  .apr-stat-value { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 700; color: #D4ECDD; line-height: 1; }
  .apr-stat-sub   { font-size: 12px; color: rgba(212,236,221,.35); margin-top: 5px; }

  .apr-tabs { display: flex; border-bottom: 1px solid rgba(52,91,99,.35); margin-bottom: 20px; }
  .apr-tab {
    padding: 10px 20px; font-size: 14px; font-weight: 500; cursor: pointer;
    border-bottom: 2px solid transparent; color: rgba(212,236,221,.4);
    background: none; border-top: none; border-left: none; border-right: none;
    transition: all .15s; text-decoration: none; display: inline-block;
  }
  .apr-tab:hover  { color: rgba(212,236,221,.75); }
  .apr-tab.active { color: #D4ECDD; border-bottom-color: #345B63; }
  .apr-tab-badge  {
    display: inline-block; margin-left: 6px;
    background: rgba(52,91,99,.5); color: #D4ECDD;
    font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 20px;
  }

  .apr-card {
    background: #0e1c28;
    border: 1px solid rgba(52,91,99,.35);
    border-radius: 16px;
    overflow: hidden;
    animation: aprCardIn .35s .2s cubic-bezier(.22,.68,0,1.2) both;
  }

  .apr-table { width: 100%; border-collapse: collapse; }
  .apr-table th {
    text-align: left; font-size: 10px; text-transform: uppercase;
    letter-spacing: .1em; color: rgba(212,236,221,.3); font-weight: 600;
    padding: 12px 16px; border-bottom: 1px solid rgba(52,91,99,.3);
    background: none;
  }
  .apr-table td {
    padding: 16px; font-size: 14px;
    border-bottom: 1px solid rgba(52,91,99,.15);
    vertical-align: middle; color: #D4ECDD;
    background: none;
  }
  .apr-table tbody tr:last-child td { border-bottom: none; }
  .apr-table tbody tr { transition: background .12s, transform .12s; cursor: pointer; }
  .apr-table tbody tr:hover td { background: rgba(212,236,221,.03); }
  .apr-table tbody tr:hover { transform: translateX(2px); }

  .apr-avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: rgba(52,91,99,.5);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Playfair Display', serif; font-weight: 700;
    font-size: 15px; color: #D4ECDD; flex-shrink: 0;
  }

  .apr-code {
    font-family: monospace; font-size: 12px;
    background: rgba(52,91,99,.2); border: 1px solid rgba(52,91,99,.35);
    color: #6fa8b5; padding: 3px 8px; border-radius: 6px;
  }

  .apr-badge-pending  { background: rgba(212,236,221,.1);  color: #a8d5bc; border: 1px solid rgba(212,236,221,.2); font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 20px; display: inline-block; }
  .apr-badge-pro      { background: rgba(160,154,255,.1);  color: #a09aff; border: 1px solid rgba(160,154,255,.2); font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-block; }
  .apr-badge-basic    { background: rgba(212,236,221,.06); color: rgba(212,236,221,.5); border: 1px solid rgba(212,236,221,.15); font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 20px; display: inline-block; }

  .apr-btn-approve {
    display: inline-flex; align-items: center; gap: 6px;
    background: linear-gradient(135deg, #345B63, #2a4a51);
    color: #D4ECDD; font-size: 12px; font-weight: 600;
    padding: 6px 14px; border-radius: 8px; border: none; cursor: pointer;
    transition: all .2s; white-space: nowrap;
  }
  .apr-btn-approve:hover {
    background: linear-gradient(135deg, #3d6b74, #345B63);
    box-shadow: 0 0 16px rgba(52,91,99,.5);
    transform: translateY(-1px);
  }

  .apr-btn-reject {
    display: inline-flex; align-items: center; gap: 6px;
    background: transparent; color: #ff7a94;
    border: 1px solid rgba(255,77,109,.3);
    font-size: 12px; font-weight: 600;
    padding: 6px 14px; border-radius: 8px; cursor: pointer;
    transition: all .2s; white-space: nowrap;
  }
  .apr-btn-reject:hover { background: rgba(255,77,109,.1); border-color: rgba(255,77,109,.6); }

  .apr-empty { text-align: center; padding: 64px 24px; color: rgba(212,236,221,.35); }
  .apr-empty-icon { font-size: 42px; margin-bottom: 14px; opacity: .35; }
  .apr-empty-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #D4ECDD; margin-bottom: 6px; }

  .apr-pagination { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; border-top: 1px solid rgba(52,91,99,.25); }
  .apr-pagination-info { font-size: 12px; color: rgba(212,236,221,.3); }

  /* Override Laravel's default pagination to match the design */
  .apr-pagination nav { display: flex; gap: 0; }
  .apr-pagination nav > div:first-child { display: none; } /* hide "Showing X to Y" text from Laravel since we show our own */
  .apr-pagination nav svg { display: none; }
  .apr-pagination .pagination { display: flex; gap: 6px; align-items: center; margin: 0; }
  .apr-pagination .page-item .page-link {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px; border-radius: 7px;
    font-size: 13px; text-decoration: none;
    border: 1px solid rgba(52,91,99,.35) !important; color: rgba(212,236,221,.4);
    background: transparent; transition: all .15s;
  }
  .apr-pagination .page-item.active .page-link {
    background: #345B63 !important; border-color: #345B63 !important; color: #D4ECDD !important;
  }
  .apr-pagination .page-item .page-link:hover { border-color: #345B63 !important; color: #D4ECDD; }
  .apr-pagination .page-item.disabled .page-link { opacity: .35; cursor: not-allowed; }

  /* Modals */
  .apr-modal-overlay {
    display: none; position: fixed; inset: 0;
    background: rgba(0,0,0,.65);
    backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);
    z-index: 300; align-items: center; justify-content: center;
  }
  .apr-modal-overlay.open { display: flex; }

  .apr-modal {
    background: #0e1c28; border-radius: 18px; width: 100%; margin: 16px;
    overflow: hidden; animation: aprFadeIn .2s ease;
  }
  @keyframes aprFadeIn { from { opacity: 0; transform: scale(.97); } to { opacity: 1; transform: none; } }

  .apr-modal-header { padding: 24px 28px 20px; border-bottom: 1px solid rgba(52,91,99,.25); }
  .apr-modal-body   { padding: 20px 28px; }
  .apr-modal-footer { padding: 16px 28px; border-top: 1px solid rgba(52,91,99,.25); display: flex; justify-content: flex-end; gap: 10px; }

  .apr-detail-row { display: flex; padding: 12px 0; border-bottom: 1px solid rgba(52,91,99,.15); font-size: 14px; }
  .apr-detail-row:last-child { border-bottom: none; }
  .apr-detail-key { width: 150px; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: rgba(212,236,221,.35); font-weight: 600; flex-shrink: 0; padding-top: 2px; }
  .apr-detail-val { color: #D4ECDD; font-weight: 500; word-break: break-all; }

  .apr-btn-close {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 500; color: rgba(212,236,221,.4);
    padding: 7px 16px; border-radius: 8px; cursor: pointer;
    border: 1px solid rgba(52,91,99,.35); background: transparent;
    transition: all .15s;
  }
  .apr-btn-close:hover { color: #D4ECDD; border-color: rgba(52,91,99,.7); }

  /* Toast */
  .apr-toast {
    position: fixed; bottom: 24px; right: 24px; z-index: 400;
    transition: all .3s; opacity: 0; transform: translateY(8px); pointer-events: none;
  }
  .apr-toast.show { opacity: 1; transform: translateY(0); }
  .apr-toast-inner {
    display: flex; align-items: center; gap: 10px;
    padding: 13px 20px; border-radius: 12px; border: 1px solid;
    font-size: 13px; font-weight: 500; box-shadow: 0 8px 32px rgba(0,0,0,.4);
  }
  .apr-toast-success { background: #0e1c28; border-color: rgba(45,212,160,.3); color: #2dd4a0; }
  .apr-toast-error   { background: #0e1c28; border-color: rgba(255,77,109,.3); color: #ff7a94; }
</style>
@endpush

@section('content')
<div class="apr-wrap">

  {{-- ── Stats ── --}}
  <div style="display:grid; grid-template-columns: repeat(4,1fr); gap:16px; margin-bottom:28px;">
    <div class="apr-stat" style="animation-delay:.04s">
      <div class="apr-stat-label">Pending</div>
      <div class="apr-stat-value">{{ $registrations->total() }}</div>
      <div class="apr-stat-sub">Awaiting review</div>
    </div>
    <div class="apr-stat" style="animation-delay:.08s">
      <div class="apr-stat-label">Approved</div>
      <div class="apr-stat-value" style="color:#2dd4a0;">{{ \App\Models\TenantRegistration::where('status','approved')->count() }}</div>
      <div class="apr-stat-sub">All time</div>
    </div>
    <div class="apr-stat" style="animation-delay:.12s">
      <div class="apr-stat-label">Rejected</div>
      <div class="apr-stat-value" style="color:#ff7a94;">{{ \App\Models\TenantRegistration::where('status','rejected')->count() }}</div>
      <div class="apr-stat-sub">All time</div>
    </div>
    <div class="apr-stat" style="animation-delay:.16s">
      <div class="apr-stat-label">Total Submitted</div>
      <div class="apr-stat-value">{{ \App\Models\TenantRegistration::count() }}</div>
      <div class="apr-stat-sub">Since launch</div>
    </div>
  </div>

  {{-- ── Tabs ── --}}
  <div class="apr-tabs">
    <span class="apr-tab active">
      Pending
      <span class="apr-tab-badge">{{ $registrations->total() }}</span>
    </span>
  </div>

  {{-- ── Table card ── --}}
  <div class="apr-card">

    @if($registrations->isEmpty())
      <div class="apr-empty">
        <div class="apr-empty-icon">🎉</div>
        <div class="apr-empty-title">All caught up!</div>
        <div style="font-size:14px;">No pending registrations at the moment.</div>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="apr-table">
          <thead>
            <tr>
              <th style="padding-left:24px;">Company</th>
              <th>Contact</th>
              <th>Subdomain</th>
              <th>Plan</th>
              <th>Submitted</th>
              <th>Status</th>
              <th style="text-align:right; padding-right:24px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($registrations as $reg)
            <tr onclick="openDetailModal(
                  '{{ addslashes($reg->company_name) }}',
                  '{{ addslashes($reg->subdomain) }}',
                  '{{ addslashes($reg->email) }}',
                  '{{ addslashes($reg->contact_person) }}',
                  '{{ addslashes($reg->phone ?? '—') }}',
                  '{{ ucfirst($reg->plan) }}',
                  '{{ $reg->created_at->diffForHumans() }}',
                  {{ $reg->id }}
                )">
              <td style="padding-left:24px;">
                <div style="display:flex; align-items:center; gap:12px;">
                  <div class="apr-avatar">{{ strtoupper(substr($reg->company_name, 0, 1)) }}</div>
                  <div>
                    <div style="font-weight:600;">{{ $reg->company_name }}</div>
                    <div style="font-size:12px; color:rgba(212,236,221,.4); margin-top:2px;">{{ $reg->email }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="color:rgba(212,236,221,.8);">{{ $reg->contact_person }}</div>
                @if($reg->phone)
                  <div style="font-size:12px; color:rgba(212,236,221,.4); margin-top:2px;">{{ $reg->phone }}</div>
                @endif
              </td>
              <td><span class="apr-code">{{ $reg->subdomain }}</span></td>
              <td>
                    @if($reg->plan === 'premium')
                        <span class="apr-badge-pro">Premium</span>
                    @elseif($reg->plan === 'standard')
                        <span class="apr-badge-standard">Standard</span>
                    @else
                        <span class="apr-badge-basic">Basic</span>
                    @endif
                </td>
              <td style="color:rgba(212,236,221,.45); font-size:13px;">{{ $reg->created_at->diffForHumans() }}</td>
              <td><span class="apr-badge-pending">Pending</span></td>
              <td style="text-align:right; padding-right:24px;">
                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">

                  {{-- Approve --}}
                  <form method="POST"
                        action="{{ route('super_admin.approvals.approve', $reg) }}"
                        onsubmit="event.stopPropagation();"
                        onclick="event.stopPropagation();">
                    @csrf
                    <button type="submit" class="apr-btn-approve">
                      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                      Approve
                    </button>
                  </form>

                  {{-- Reject --}}
                  <button type="button"
                          class="apr-btn-reject"
                          onclick="event.stopPropagation(); openRejectModal({{ $reg->id }}, '{{ addslashes($reg->company_name) }}')">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reject
                  </button>

                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($registrations->hasPages())
      <div class="apr-pagination">
        <span class="apr-pagination-info">
          Showing {{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} of {{ $registrations->total() }} pending
        </span>
        <div>
          {{ $registrations->links() }}
        </div>
      </div>
      @endif
    @endif

  </div>{{-- /.apr-card --}}

</div>{{-- /.apr-wrap --}}


{{-- ══════════════════════════════════════════
     Detail Modal
══════════════════════════════════════════ --}}
<div id="detail-modal" class="apr-modal-overlay" onclick="closeDetailModal()">
  <div class="apr-modal" style="max-width:520px; border:1px solid rgba(52,91,99,.4);" onclick="event.stopPropagation()">

    <div class="apr-modal-header" style="display:flex; align-items:flex-start; justify-content:space-between;">
      <div>
        <div style="font-size:10px; text-transform:uppercase; letter-spacing:.1em; color:rgba(212,236,221,.3); font-weight:600; margin-bottom:4px;">Registration Details</div>
        <div id="dm-company" style="font-family:'Playfair Display',serif; font-size:22px; font-weight:700; color:#D4ECDD;"></div>
      </div>
      <button type="button" onclick="closeDetailModal()"
        style="background:none;border:none;cursor:pointer;color:rgba(212,236,221,.3);margin-top:4px;padding:4px;"
        onmouseover="this.style.color='#D4ECDD'" onmouseout="this.style.color='rgba(212,236,221,.3)'">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <div class="apr-modal-body">
      <div class="apr-detail-row"><span class="apr-detail-key">Subdomain</span><span id="dm-subdomain" class="apr-detail-val" style="font-family:monospace; color:#6fa8b5;"></span></div>
      <div class="apr-detail-row"><span class="apr-detail-key">Email</span><span id="dm-email" class="apr-detail-val"></span></div>
      <div class="apr-detail-row"><span class="apr-detail-key">Contact Person</span><span id="dm-contact" class="apr-detail-val"></span></div>
      <div class="apr-detail-row"><span class="apr-detail-key">Phone</span><span id="dm-phone" class="apr-detail-val"></span></div>
      <div class="apr-detail-row"><span class="apr-detail-key">Plan</span><span id="dm-plan" class="apr-detail-val"></span></div>
      <div class="apr-detail-row"><span class="apr-detail-key">Submitted</span><span id="dm-submitted" class="apr-detail-val"></span></div>
    </div>

    <div class="apr-modal-footer">
      <button type="button" class="apr-btn-close" onclick="closeDetailModal()">Close</button>
      <button type="button" class="apr-btn-reject"
        onclick="closeDetailModal(); openRejectModal(window._dmId, window._dmCompany)">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        Reject
      </button>
      <form id="dm-approve-form" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="apr-btn-approve">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          Approve Tenant
        </button>
      </form>
    </div>

  </div>
</div>


{{-- ══════════════════════════════════════════
     Reject Modal
══════════════════════════════════════════ --}}
<div id="reject-modal" class="apr-modal-overlay" onclick="closeRejectModal()">
  <div class="apr-modal" style="max-width:440px; border:1px solid rgba(255,77,109,.25);" onclick="event.stopPropagation()">

    <div class="apr-modal-header" style="border-bottom-color:rgba(255,77,109,.15);">
      <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
        <div style="width:32px;height:32px;border-radius:50%;background:rgba(255,77,109,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#ff7a94" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </div>
        <div style="font-family:'Playfair Display',serif; font-size:20px; font-weight:700; color:#D4ECDD;">Reject Registration</div>
      </div>
      <p style="font-size:13px; color:rgba(212,236,221,.4); margin-top:6px;">
        Rejecting <span id="rm-company" style="color:rgba(212,236,221,.75); font-weight:500;"></span>. The applicant will be notified.
      </p>
    </div>

    <form id="reject-form" method="POST">
      @csrf
      <div class="apr-modal-body">
        <label style="display:block; font-size:11px; text-transform:uppercase; letter-spacing:.1em; color:rgba(212,236,221,.4); font-weight:600; margin-bottom:8px;">
          Reason <span style="color:rgba(212,236,221,.25); text-transform:none; letter-spacing:0; font-weight:400;">(optional)</span>
        </label>
        <textarea name="rejection_reason" rows="4"
          placeholder="Explain why this registration was rejected…"
          style="width:100%; background:#112031; border:1px solid rgba(52,91,99,.35); border-radius:10px; color:#D4ECDD; font-size:13px; padding:12px 14px; resize:none; outline:none; font-family:'DM Sans',sans-serif; transition:border-color .15s;"
          onfocus="this.style.borderColor='rgba(255,77,109,.4)'"
          onblur="this.style.borderColor='rgba(52,91,99,.35)'"></textarea>
      </div>

      <div class="apr-modal-footer" style="border-top-color:rgba(255,77,109,.12);">
        <button type="button" class="apr-btn-close" onclick="closeRejectModal()">Cancel</button>
        <button type="submit"
          style="display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;padding:8px 18px;border-radius:8px;cursor:pointer;border:1px solid rgba(255,77,109,.3);background:rgba(255,77,109,.1);color:#ff7a94;transition:all .15s;"
          onmouseover="this.style.background='rgba(255,77,109,.2)'"
          onmouseout="this.style.background='rgba(255,77,109,.1)'">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          Confirm Rejection
        </button>
      </div>
    </form>

  </div>
</div>

{{-- Toast --}}
<div id="apr-toast" class="apr-toast">
  <div id="apr-toast-inner" class="apr-toast-inner"></div>
</div>

@endsection

@push('scripts')
<script>
  window._dmId      = null;
  window._dmCompany = '';

  const BASE_DOMAIN = '{{ config('app.base_domain', 'yourapp.com') }}';

  function openDetailModal(company, subdomain, email, contact, phone, plan, submitted, id) {
    window._dmId      = id;
    window._dmCompany = company;

    document.getElementById('dm-company').textContent   = company;
    document.getElementById('dm-subdomain').textContent = subdomain + '.' + BASE_DOMAIN;
    document.getElementById('dm-email').textContent     = email;
    document.getElementById('dm-contact').textContent   = contact;
    document.getElementById('dm-phone').textContent     = phone;
    document.getElementById('dm-plan').textContent      = plan;
    document.getElementById('dm-submitted').textContent = submitted;

    document.getElementById('dm-approve-form').action =
      '{{ url('super-admin/approvals') }}/' + id + '/approve';

    document.getElementById('detail-modal').classList.add('open');
  }

  function closeDetailModal() {
    document.getElementById('detail-modal').classList.remove('open');
  }

  function openRejectModal(id, company) {
    document.getElementById('rm-company').textContent = company;
    document.getElementById('reject-form').action     =
      '{{ url('super-admin/approvals') }}/' + id + '/reject';

    document.getElementById('reject-modal').classList.add('open');
  }

  function closeRejectModal() {
    document.getElementById('reject-modal').classList.remove('open');
  }

  // Show session flash messages as toasts
  document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
      showToast(@json(session('success')), 'success');
    @endif
    @if(session('info'))
      showToast(@json(session('info')), 'error');
    @endif
  });

  function showToast(msg, type) {
    const toast = document.getElementById('apr-toast');
    const inner = document.getElementById('apr-toast-inner');
    inner.className = 'apr-toast-inner ' + (type === 'success' ? 'apr-toast-success' : 'apr-toast-error');
    const icon = type === 'success'
      ? '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
      : '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    inner.innerHTML = icon + msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3800);
  }
</script>
@endpush