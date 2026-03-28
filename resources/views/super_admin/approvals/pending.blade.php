@extends('layouts.superadmin-app')
@section('title', 'Tenant Approvals')
@section('page-title', 'Tenant Approvals')

@section('content')

@php
$planColors = [
    'basic'   => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.8)'],
    'pro'     => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
    'premium' => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
];

$pendingCount  = \App\Models\TenantRegistration::where('status','pending')->count();
$approvedCount = \App\Models\TenantRegistration::where('status','approved')->count();
$rejectedCount = \App\Models\TenantRegistration::where('status','rejected')->count();
$totalCount    = \App\Models\TenantRegistration::count();
@endphp

{{-- ── Stat Strip ── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:rgba(171,171,171,0.06);margin-bottom:1px;">
    @foreach([
        [$pendingCount,  'Pending',        'rgba(210,170,70,0.9)'],
        [$approvedCount, 'Approved',       'rgba(34,197,94,0.85)'],
        [$rejectedCount, 'Rejected',       'rgba(239,68,68,0.75)'],
        [$totalCount,    'Total Submitted','#fff'],
    ] as [$val, $label, $color])
    <div style="background:#0E1126;padding:18px 22px;transition:background 0.2s;"
         onmouseover="this.style.background='rgba(14,17,38,0.8)'"
         onmouseout="this.style.background='#0E1126'">
        <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:{{ $color }};line-height:1;letter-spacing:-0.02em;">{{ $val }}</div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:6px;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- ── Main Card ── --}}
<div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Pending Registrations</span>
            </div>
            <div style="font-size:12px;color:rgba(171,171,171,0.35);margin-top:2px;font-family:monospace;">
                // {{ $pendingCount }} registration{{ $pendingCount !== 1 ? 's' : '' }} awaiting review
            </div>
        </div>

        {{-- Plan legend --}}
        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
            @foreach(['basic'=>'Basic','pro'=>'Pro','premium'=>'Premium'] as $key => $lbl)
            @php $c = $planColors[$key]; @endphp
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid {{ $c['border'] }};background:{{ $c['bg'] }};
                         font-size:10px;color:{{ $c['color'] }};font-family:monospace;letter-spacing:0.1em;text-transform:uppercase;">
                {{ $lbl }}
            </span>
            @endforeach
        </div>
    </div>

    @if($registrations->isEmpty())
        {{-- Empty state --}}
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid rgba(171,171,171,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="rgba(171,171,171,0.25)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">All caught up</div>
            <div style="font-size:13px;color:rgba(171,171,171,0.35);font-family:monospace;">// No pending registrations at the moment.</div>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:820px;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.08);">
                        @foreach(['Institution', 'Contact', 'Subdomain', 'Plan', 'Submitted', 'Actions'] as $th)
                        <th style="font-family:monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;
                                   color:rgba(171,171,171,0.28);font-weight:600;padding:0 14px 12px 0;text-align:left;
                                   {{ $loop->last ? 'text-align:right;padding-right:0;' : '' }}">
                            {{ $th }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                    @php
                        $pc = $planColors[$reg->plan] ?? ['border'=>'rgba(171,171,171,0.12)','bg'=>'transparent','color'=>'rgba(171,171,171,0.3)'];
                    @endphp
                    <tr style="border-bottom:1px solid rgba(171,171,171,0.05);transition:background 0.15s;cursor:pointer;"
                        onmouseover="this.style.background='rgba(171,171,171,0.018)'"
                        onmouseout="this.style.background='transparent'"
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
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:2px;height:36px;background:#8C0E03;flex-shrink:0;"></div>
                                <div style="width:34px;height:34px;background:rgba(140,14,3,0.12);border:1px solid rgba(140,14,3,0.25);
                                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:14px;color:rgba(200,100,90,0.9);">
                                        {{ strtoupper(substr($reg->company_name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:15px;color:#fff;letter-spacing:0.05em;line-height:1.2;">
                                        {{ $reg->company_name }}
                                    </div>
                                    <div style="font-size:11px;color:rgba(171,171,171,0.3);font-family:monospace;margin-top:2px;">
                                        {{ $reg->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Contact --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="font-size:13px;color:rgba(171,171,171,0.7);">{{ $reg->contact_person }}</div>
                            @if($reg->phone)
                            <div style="font-size:11px;color:rgba(171,171,171,0.3);font-family:monospace;margin-top:2px;">{{ $reg->phone }}</div>
                            @endif
                        </td>

                        {{-- Subdomain --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 9px;
                                         border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.08);
                                         font-size:11px;color:rgba(200,100,90,0.85);font-family:monospace;letter-spacing:0.04em;">
                                <span style="width:4px;height:4px;background:#8C0E03;flex-shrink:0;display:inline-block;border-radius:50%;"></span>
                                {{ $reg->subdomain }}.ojtconnect.com
                            </span>
                        </td>

                        {{-- Plan --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <span style="display:inline-flex;align-items:center;padding:3px 10px;
                                         border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                                         font-size:10px;color:{{ $pc['color'] }};font-family:monospace;
                                         letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                                {{ ucfirst($reg->plan) }}
                            </span>
                        </td>

                        {{-- Submitted --}}
                        <td style="padding:15px 14px 15px 0;vertical-align:middle;">
                            <div style="font-size:12px;color:rgba(171,171,171,0.35);font-family:monospace;">
                                {{ $reg->created_at->format('M d, Y') }}
                            </div>
                            <div style="font-size:10px;color:rgba(171,171,171,0.2);font-family:monospace;margin-top:2px;">
                                {{ $reg->created_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td style="padding:15px 0;vertical-align:middle;text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;"
                                 onclick="event.stopPropagation()">

                                {{-- Approve --}}
                                <form method="POST" action="{{ route('super_admin.approvals.approve', $reg) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit"
                                            title="Approve"
                                            style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
                                                   border:1px solid rgba(34,197,94,0.25);background:rgba(34,197,94,0.06);
                                                   color:rgba(74,222,128,0.85);font-size:11px;font-weight:700;
                                                   letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                                                   font-family:'Barlow Condensed',sans-serif;
                                                   transition:border-color 0.2s,background 0.2s,color 0.2s;"
                                            onmouseover="this.style.borderColor='rgba(34,197,94,0.5)';this.style.background='rgba(34,197,94,0.12)';this.style.color='rgba(74,222,128,1)'"
                                            onmouseout="this.style.borderColor='rgba(34,197,94,0.25)';this.style.background='rgba(34,197,94,0.06)';this.style.color='rgba(74,222,128,0.85)'">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <button type="button"
                                        title="Reject"
                                        onclick="openRejectModal({{ $reg->id }}, '{{ addslashes($reg->company_name) }}')"
                                        style="display:inline-flex;align-items:center;gap:5px;padding:6px 12px;
                                               border:1px solid rgba(239,68,68,0.2);background:transparent;
                                               color:rgba(252,165,165,0.6);font-size:11px;font-weight:700;
                                               letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                                               font-family:'Barlow Condensed',sans-serif;
                                               transition:border-color 0.2s,background 0.2s,color 0.2s;"
                                        onmouseover="this.style.borderColor='rgba(239,68,68,0.5)';this.style.background='rgba(239,68,68,0.08)';this.style.color='rgba(252,165,165,0.95)'"
                                        onmouseout="this.style.borderColor='rgba(239,68,68,0.2)';this.style.background='transparent';this.style.color='rgba(252,165,165,0.6)'">
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
        <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(171,171,171,0.06);
                    display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;">
                // Showing {{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} of {{ $registrations->total() }}
            </span>
            <div>{{ $registrations->links() }}</div>
        </div>
        @endif
    @endif
</div>

{{-- Info strip --}}
<div style="margin-top:8px;padding:14px 20px;border:1px solid rgba(140,14,3,0.15);background:rgba(140,14,3,0.03);
            font-size:12px;color:rgba(171,171,171,0.35);line-height:1.7;font-family:monospace;">
    <span style="color:rgba(200,90,80,0.6);font-weight:700;">// note:</span>
    Approving a registration provisions an isolated database, runs all tenant migrations, and seeds the admin account automatically.
    Rejection notifies the applicant with an optional reason.
</div>


{{-- ══ Detail Modal ══ --}}
<div id="detail-modal"
     style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.88);z-index:999;
            align-items:center;justify-content:center;backdrop-filter:blur(2px);"
     onclick="closeDetailModal()">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.1);border-top:2px solid #8C0E03;
                width:100%;max-width:500px;margin:0 20px;"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div style="padding:24px 28px 20px;border-bottom:1px solid rgba(171,171,171,0.06);
                    display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
            <div>
                <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;margin-bottom:6px;">
                    Registration Details
                </div>
                <div id="dm-company"
                     style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:#fff;line-height:1.2;">
                </div>
            </div>
            <button onclick="closeDetailModal()"
                    style="background:none;border:none;cursor:pointer;color:rgba(171,171,171,0.3);padding:4px;flex-shrink:0;
                           transition:color 0.2s;"
                    onmouseover="this.style.color='rgba(171,171,171,0.8)'"
                    onmouseout="this.style.color='rgba(171,171,171,0.3)'">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <div style="padding:4px 28px;">
            @foreach([
                ['dm-subdomain', 'Subdomain'],
                ['dm-email',     'Email'],
                ['dm-contact',   'Contact Person'],
                ['dm-phone',     'Phone'],
                ['dm-plan',      'Plan'],
                ['dm-submitted', 'Submitted'],
            ] as [$id, $label])
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:12px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
                <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;
                             text-transform:uppercase;font-family:monospace;">{{ $label }}</span>
                <span id="{{ $id }}"
                      style="font-size:13px;color:rgba(171,171,171,0.8);font-family:monospace;
                             text-align:right;max-width:280px;word-break:break-all;">
                </span>
            </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div style="padding:20px 28px;display:flex;gap:8px;justify-content:flex-end;border-top:1px solid rgba(171,171,171,0.06);">
            <button onclick="closeDetailModal()"
                    style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                           transition:border-color 0.2s,color 0.2s;"
                    onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                    onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                Close
            </button>
            <button type="button"
                    onclick="closeDetailModal(); openRejectModal(window._dmId, window._dmCompany)"
                    style="display:inline-flex;align-items:center;gap:5px;padding:8px 18px;
                           border:1px solid rgba(239,68,68,0.2);background:transparent;
                           color:rgba(252,165,165,0.6);font-size:12px;font-weight:700;
                           letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                           font-family:'Barlow Condensed',sans-serif;transition:border-color 0.2s,background 0.2s,color 0.2s;"
                    onmouseover="this.style.borderColor='rgba(239,68,68,0.5)';this.style.background='rgba(239,68,68,0.08)';this.style.color='rgba(252,165,165,0.95)'"
                    onmouseout="this.style.borderColor='rgba(239,68,68,0.2)';this.style.background='transparent';this.style.color='rgba(252,165,165,0.6)'">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reject
            </button>
            <form id="dm-approve-form" method="POST" style="margin:0;">
                @csrf
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:5px;padding:8px 18px;
                               border:1px solid rgba(34,197,94,0.25);background:rgba(34,197,94,0.06);
                               color:rgba(74,222,128,0.85);font-size:12px;font-weight:700;
                               letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                               font-family:'Barlow Condensed',sans-serif;transition:border-color 0.2s,background 0.2s;"
                        onmouseover="this.style.borderColor='rgba(34,197,94,0.5)';this.style.background='rgba(34,197,94,0.12)'"
                        onmouseout="this.style.borderColor='rgba(34,197,94,0.25)';this.style.background='rgba(34,197,94,0.06)'">
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
<div id="reject-modal"
     style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.88);z-index:999;
            align-items:center;justify-content:center;backdrop-filter:blur(2px);"
     onclick="closeRejectModal()">
    <div style="background:#0E1126;border:1px solid rgba(239,68,68,0.2);border-top:2px solid rgba(239,68,68,0.6);
                width:100%;max-width:440px;margin:0 20px;"
         onclick="event.stopPropagation()">

        {{-- Header --}}
        <div style="padding:24px 28px 20px;border-bottom:1px solid rgba(239,68,68,0.08);">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div style="width:20px;height:2px;background:rgba(239,68,68,0.6);flex-shrink:0;"></div>
                <span style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;">
                    Reject Registration
                </span>
            </div>
            <div style="font-size:13px;color:rgba(171,171,171,0.4);font-family:monospace;line-height:1.6;">
                // Rejecting <span id="rm-company" style="color:rgba(252,165,165,0.7);"></span>.
                The applicant will be notified.
            </div>
        </div>

        <form id="reject-form" method="POST">
            @csrf
            {{-- Body --}}
            <div style="padding:24px 28px;">
                <label style="display:block;font-size:10px;color:rgba(171,171,171,0.4);letter-spacing:0.18em;
                              text-transform:uppercase;font-family:monospace;margin-bottom:8px;">
                    Reason
                    <span style="text-transform:none;letter-spacing:0;color:rgba(171,171,171,0.2);font-size:10px;">
                        (optional)
                    </span>
                </label>
                <textarea name="rejection_reason" rows="4"
                          placeholder="Explain why this registration was rejected…"
                          style="width:100%;padding:10px 14px;background:#0D0D0D;border:1px solid rgba(171,171,171,0.12);
                                 color:#fff;font-size:13px;font-family:'Barlow',sans-serif;outline:none;resize:none;
                                 transition:border-color 0.2s,box-shadow 0.2s;box-sizing:border-box;line-height:1.6;"
                          onfocus="this.style.borderColor='rgba(239,68,68,0.4)';this.style.boxShadow='0 0 0 3px rgba(239,68,68,0.08)'"
                          onblur="this.style.borderColor='rgba(171,171,171,0.12)';this.style.boxShadow='none'">
                </textarea>
            </div>

            {{-- Footer --}}
            <div style="padding:16px 28px;border-top:1px solid rgba(239,68,68,0.08);
                        display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeRejectModal()"
                        style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                               color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                               text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                               transition:border-color 0.2s,color 0.2s;"
                        onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                        onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                    Cancel
                </button>
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;
                               border:1px solid rgba(239,68,68,0.4);background:rgba(239,68,68,0.1);
                               color:rgba(252,165,165,0.9);font-size:12px;font-weight:700;
                               letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                               font-family:'Barlow Condensed',sans-serif;transition:background 0.2s,border-color 0.2s;"
                        onmouseover="this.style.background='rgba(239,68,68,0.2)';this.style.borderColor='rgba(239,68,68,0.6)'"
                        onmouseout="this.style.background='rgba(239,68,68,0.1)';this.style.borderColor='rgba(239,68,68,0.4)'">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="apr-toast"
     style="position:fixed;bottom:24px;right:24px;z-index:1000;
            transition:opacity 0.3s,transform 0.3s;opacity:0;transform:translateY(8px);pointer-events:none;">
    <div id="apr-toast-inner"
         style="display:flex;align-items:center;gap:10px;padding:12px 20px;
                border:1px solid;font-size:13px;font-weight:600;font-family:'Barlow Condensed',sans-serif;
                letter-spacing:0.05em;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
    </div>
</div>

<style>
@keyframes fadeUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
#detail-modal[style*="flex"],
#reject-modal[style*="flex"] {
    animation: fadeUp 0.2s cubic-bezier(.22,.61,.36,1) both;
}
</style>

@endsection

@push('scripts')
<script>
window._dmId      = null;
window._dmCompany = '';

function openDetailModal(company, subdomain, email, contact, phone, plan, submitted, id) {
    window._dmId      = id;
    window._dmCompany = company;

    document.getElementById('dm-company').textContent   = company;
    document.getElementById('dm-subdomain').textContent = subdomain + '.ojtconnect.com';
    document.getElementById('dm-email').textContent     = email;
    document.getElementById('dm-contact').textContent   = contact;
    document.getElementById('dm-phone').textContent     = phone;
    document.getElementById('dm-plan').textContent      = plan;
    document.getElementById('dm-submitted').textContent = submitted;

    document.getElementById('dm-approve-form').action =
        '{{ url('super-admin/approvals') }}/' + id + '/approve';

    const modal = document.getElementById('detail-modal');
    modal.style.display = 'flex';
}

function closeDetailModal() {
    document.getElementById('detail-modal').style.display = 'none';
}

function openRejectModal(id, company) {
    document.getElementById('rm-company').textContent = company;
    document.getElementById('reject-form').action     =
        '{{ url('super-admin/approvals') }}/' + id + '/reject';

    const modal = document.getElementById('reject-modal');
    modal.style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('reject-modal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
        showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
        showToast(@json(session('error')), 'error');
    @endif
    @if(session('info'))
        showToast(@json(session('info')), 'info');
    @endif
});

function showToast(msg, type) {
    const inner = document.getElementById('apr-toast-inner');
    const toast  = document.getElementById('apr-toast');

    const styles = {
        success: { bg:'#0E1126', border:'rgba(34,197,94,0.3)',   color:'rgba(74,222,128,0.9)' },
        error:   { bg:'#0E1126', border:'rgba(239,68,68,0.3)',   color:'rgba(252,165,165,0.9)' },
        info:    { bg:'#0E1126', border:'rgba(171,171,171,0.2)', color:'rgba(171,171,171,0.7)' },
    };
    const icons = {
        success: '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
        error:   '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        info:    '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
    };

    const s = styles[type] || styles.info;
    inner.style.background   = s.bg;
    inner.style.borderColor  = s.border;
    inner.style.color        = s.color;
    inner.innerHTML          = (icons[type] || '') + msg;

    toast.style.opacity   = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity   = '0';
        toast.style.transform = 'translateY(8px)';
    }, 3800);
}
</script>
@endpush