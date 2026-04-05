@extends('layouts.superadmin-app')
@section('title', 'Tenant — ' . $tenant->id)
@section('page-title', $tenant->id)

@section('topbar-actions')
    <div style="display:flex;gap:8px;">
        <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-primary btn-sm">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit
        </a>
        <a href="{{ route('super_admin.tenants.index') }}" class="btn btn-ghost btn-sm">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
@endsection

@section('content')

@php
    $status   = $tenant->status ?? 'active';
    $isActive = $status === 'active';
    $planColors = [
        'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)', 'dot'=>'#5b8fb9'],
        'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)',   'dot'=>'#c0392b'],
        'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)',   'dot'=>'#c9a84c'],
    ];
    $pc = $planColors[$tenant->plan ?? ''] ?? null;

    // Plan requests
    $planRequests    = \App\Models\PlanRequest::where('tenant_id', $tenant->id)->latest()->get();
    $pendingRequests = $planRequests->where('status', 'pending');
@endphp

<div style="max-width:680px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:flicker 8s ease-in-out infinite;"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Super Admin / Tenants / {{ $tenant->id }}
        </span>
    </div>
    
     {{-- ── Plan Requests ── --}}
    @if($planRequests->count() > 0)
    <div class="card fade-up fade-up-2">
        <div class="card-header">
            <div>
                <div class="card-title-main">Plan Requests</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // {{ $pendingRequests->count() }} pending · {{ $planRequests->count() }} total
                </div>
            </div>
            @if($pendingRequests->count() > 0)
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid rgba(201,168,76,0.3);background:rgba(201,168,76,0.08);
                         font-family:'DM Mono',monospace;font-size:10px;color:#c9a84c;">
                <span style="width:5px;height:5px;border-radius:50%;background:#c9a84c;display:inline-block;animation:flicker 3s ease-in-out infinite;"></span>
                {{ $pendingRequests->count() }} pending
            </span>
            @endif
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Plan Change</th>
                        <th>Contact</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($planRequests as $pr)
                    @php
                        $sc = match($pr->status) {
                            'approved' => ['color'=>'#34d399','border'=>'rgba(52,211,153,0.25)','bg'=>'rgba(52,211,153,0.06)'],
                            'rejected' => ['color'=>'#f87171','border'=>'rgba(239,68,68,0.25)','bg'=>'rgba(239,68,68,0.06)'],
                            default    => ['color'=>'#c9a84c','border'=>'rgba(201,168,76,0.3)','bg'=>'rgba(201,168,76,0.08)'],
                        };
                    @endphp
                    <tr style="{{ $pr->status === 'pending' ? 'background:rgba(201,168,76,0.02);' : '' }}">
                        <td>
                            <span style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;
                                         letter-spacing:0.1em;text-transform:uppercase;
                                         color:{{ $pr->request_type === 'upgrade' ? '#34d399' : '#f87171' }};">
                                {{ $pr->request_type === 'upgrade' ? '↑' : '↓' }} {{ ucfirst($pr->request_type) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:6px;">
                                <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">{{ ucfirst($pr->current_plan) }}</span>
                                <svg width="10" height="10" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:600;color:var(--text);">{{ ucfirst($pr->requested_plan) }}</span>
                            </div>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $pr->contact_email }}
                        </td>
                        <td style="max-width:160px;">
                            <div style="font-size:12px;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"
                                 title="{{ $pr->message }}">{{ $pr->message }}</div>
                            @if($pr->admin_notes)
                            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;border-left:2px solid var(--border2);padding-left:6px;">
                                {{ $pr->admin_notes }}
                            </div>
                            @endif
                        </td>
                        <td>
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;
                                         border:1px solid {{ $sc['border'] }};background:{{ $sc['bg'] }};
                                         font-family:'DM Mono',monospace;font-size:9px;
                                         color:{{ $sc['color'] }};letter-spacing:0.1em;text-transform:uppercase;">
                                <span style="width:4px;height:4px;border-radius:50%;background:{{ $sc['color'] }};display:inline-block;{{ $pr->status === 'pending' ? 'animation:flicker 3s ease-in-out infinite;' : '' }}"></span>
                                {{ ucfirst($pr->status) }}
                            </span>
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                            {{ $pr->created_at->format('M d, Y') }}<br>
                            <span style="opacity:0.6;">{{ $pr->created_at->diffForHumans() }}</span>
                        </td>
                        <td style="text-align:right;">
                            @if($pr->status === 'pending')
                            <div style="display:flex;gap:4px;justify-content:flex-end;">
                                <button onclick="openApproveModal({{ $pr->id }})"
                                        class="btn btn-approve btn-sm">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                                    Approve
                                </button>
                                <button onclick="openRejectModal({{ $pr->id }})"
                                        class="btn btn-danger btn-sm">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Reject
                                </button>
                            </div>
                            @else
                            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                                {{ $pr->actioned_at?->format('M d, Y') ?? '—' }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Tenant Details Card ── --}}
    <div class="card fade-up fade-up-1">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:16px;color:var(--crimson);">
                        {{ strtoupper(substr($tenant->id, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <div class="card-title-main">{{ $tenant->id }}</div>
                    @if($tenant->name)
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">{{ $tenant->name }}</div>
                    @endif
                </div>
            </div>
            <span class="status-dot {{ $isActive ? 'active' : 'inactive' }}" style="font-size:13px;">
                {{ $isActive ? 'Active' : 'Inactive' }}
            </span>
        </div>

        @php
        $rows = [
            ['Tenant ID',    '<span style="font-family:\'DM Mono\',monospace;font-size:12px;color:var(--text2);">' . $tenant->id . '</span>'],
            ['Status',       $isActive ? '<span class="status-dot active">Active</span>' : '<span class="status-dot inactive">Inactive</span>'],
            ['Plan',         $pc
                ? '<span style="display:inline-flex;padding:3px 10px;border:1px solid '.$pc['border'].';background:'.$pc['bg'].';font-family:\'Barlow Condensed\',sans-serif;font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;color:'.$pc['color'].';">' . ucfirst($tenant->plan) . '</span>'
                : '<span style="font-family:\'DM Mono\',monospace;font-size:11px;color:var(--muted);">—</span>'],
            ['Created',      '<span style="font-family:\'DM Mono\',monospace;font-size:11px;color:var(--text2);">' . $tenant->created_at->format('F d, Y \a\t g:i A') . '</span>'],
            ['Last Updated', '<span style="font-family:\'DM Mono\',monospace;font-size:11px;color:var(--text2);">' . $tenant->updated_at->format('F d, Y \a\t g:i A') . '</span>'],
        ];
        @endphp

        @foreach($rows as [$label, $value])
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid var(--border);">
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.16em;text-transform:uppercase;">{{ $label }}</span>
            {!! $value !!}
        </div>
        @endforeach

        {{-- Domain row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);gap:16px;">
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);letter-spacing:0.16em;text-transform:uppercase;flex-shrink:0;">Domain</span>
            <div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:flex-end;">
                @forelse($tenant->domains as $domain)
                    <a href="https://{{ $domain->domain }}:8000" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;
                              border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.06);
                              font-family:'DM Mono',monospace;font-size:11px;color:rgba(200,100,90,0.9);
                              text-decoration:none;transition:border-color 0.2s,background 0.2s;"
                       onmouseover="this.style.borderColor='rgba(140,14,3,0.6)';this.style.background='rgba(140,14,3,0.12)'"
                       onmouseout="this.style.borderColor='rgba(140,14,3,0.3)';this.style.background='rgba(140,14,3,0.06)'">
                        <span style="width:5px;height:5px;background:var(--crimson);border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                        {{ $domain->domain }}
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.5;flex-shrink:0;">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                @empty
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">// No domain configured</span>
                @endforelse
            </div>
        </div>

        {{-- Card footer --}}
        <div style="padding:14px 20px;display:flex;gap:8px;">
            <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-ghost btn-sm">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Edit Tenant
            </a>
            <form method="POST" action="{{ route('super_admin.tenants.update', $tenant) }}" style="margin:0;">
                @csrf @method('PUT')
                <input type="hidden" name="status"      value="{{ $isActive ? 'inactive' : 'active' }}">
                <input type="hidden" name="plan"        value="{{ $tenant->plan ?? '' }}">
                <input type="hidden" name="redirect_to" value="show">
                <button type="submit" class="btn btn-sm {{ $isActive ? 'btn-danger' : 'btn-approve' }}">
                    {{ $isActive ? 'Deactivate' : 'Activate' }}
                </button>
            </form>
        </div>
    </div>

    {{-- ── Plan History ── --}}
    @if($planHistory->count() > 0)
    <div class="card fade-up fade-up-3">
        <div class="card-header">
            <div>
                <div class="card-title-main">Plan History</div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                    // {{ $planHistory->count() }} record{{ $planHistory->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Price Paid</th>
                        <th>Promotion</th>
                        <th>Changed By</th>
                        <th>Started</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($planHistory as $h)
                    @php $hpc = $planColors[$h->plan?->name ?? ''] ?? null; @endphp
                    <tr>
                        <td>
                            @if($hpc)
                            <span style="display:inline-flex;padding:2px 8px;border:1px solid {{ $hpc['border'] }};
                                         background:{{ $hpc['bg'] }};font-family:'Barlow Condensed',sans-serif;
                                         font-size:11px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;
                                         color:{{ $hpc['color'] }};">
                                {{ $h->plan?->label ?? '—' }}
                            </span>
                            @else
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td style="font-family:'Playfair Display',serif;font-weight:700;font-size:13px;color:var(--text);">
                            ₱{{ number_format($h->price_paid) }}
                        </td>
                        <td>
                            @if($h->promotion)
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:#c9a84c;background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);padding:2px 6px;">
                                {{ $h->promotion->code }}
                            </span>
                            @else
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">—</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--text2);">{{ $h->changedBy?->name ?? 'System' }}</td>
                        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                            {{ $h->starts_at->format('M d, Y') }}
                        </td>
                        <td style="font-size:12px;color:var(--muted);max-width:160px;">
                            {{ $h->notes ? \Illuminate\Support\Str::limit($h->notes, 50) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Danger Zone ── --}}
    <div class="card fade-up fade-up-4 danger-zone">
        <div class="card-header">
            <div>
                <div style="font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--crimson);">Danger Zone</div>
                <div style="font-size:12px;color:var(--muted);margin-top:3px;">Irreversible destructive actions</div>
            </div>
        </div>
        <div style="padding:16px 20px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:13px;font-weight:500;color:var(--text);">Delete this tenant</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;max-width:360px;line-height:1.6;">
                        Permanently drops the tenant database and removes all users, applications, hour logs, and evaluations.
                    </div>
                </div>
                <button onclick="document.getElementById('deleteModal').style.display='flex'" class="btn btn-danger btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3,6 5,6 21,6"/>
                        <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                    </svg>
                    Delete Tenant
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ══ Approve Modal ══ --}}
<div id="approveModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);z-index:999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid #34d399;
                width:100%;max-width:460px;margin:0 20px;animation:fadeUp 0.2s ease both;">
        <div style="padding:22px 24px 18px;border-bottom:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:#34d399;margin-bottom:6px;">↑ Approve</div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">Approve Plan Request</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;font-family:'DM Mono',monospace;">
                // This will immediately change the tenant's active plan.
            </div>
        </div>
        <form id="approveForm" method="POST" style="padding:20px 24px;">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Admin Notes <span style="text-transform:none;font-weight:300;letter-spacing:0;">(optional — sent to tenant)</span></label>
                <textarea name="admin_notes" rows="3" class="form-input"
                          placeholder="Any notes for the tenant about this approval..."
                          style="resize:none;"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:4px;border-top:1px solid var(--border);">
                <button type="button" onclick="document.getElementById('approveModal').style.display='none'" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-approve btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══ Reject Modal ══ --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);z-index:999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid var(--crimson);
                width:100%;max-width:460px;margin:0 20px;animation:fadeUp 0.2s ease both;">
        <div style="padding:22px 24px 18px;border-bottom:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;text-transform:uppercase;color:var(--crimson);margin-bottom:6px;">✕ Reject</div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">Reject Plan Request</div>
            <div style="font-size:12px;color:var(--muted);margin-top:4px;font-family:'DM Mono',monospace;">
                // The tenant will be notified of the rejection.
            </div>
        </div>
        <form id="rejectForm" method="POST" style="padding:20px 24px;">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Reason <span style="text-transform:none;font-weight:300;letter-spacing:0;">(optional — sent to tenant)</span></label>
                <textarea name="admin_notes" rows="3" class="form-input"
                          placeholder="Explain why this request was not approved..."
                          style="resize:none;"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;padding-top:4px;border-top:1px solid var(--border);">
                <button type="button" onclick="document.getElementById('rejectModal').style.display='none'" class="btn btn-ghost btn-sm">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);z-index:999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid var(--crimson);width:100%;max-width:440px;margin:0 20px;padding:28px;animation:fadeUp 0.2s ease both;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.8;margin-bottom:24px;">
            You are about to permanently delete <strong style="color:var(--text);">{{ $tenant->id }}</strong>. This will drop their entire database and remove all data.
            <span style="color:var(--crimson);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-ghost btn-sm">Cancel</button>
            <form method="POST" action="{{ route('super_admin.tenants.destroy', $tenant) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Yes, Delete</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openApproveModal(id) {
    document.getElementById('approveForm').action = '{{ url('super-admin/plan-requests') }}/' + id + '/approve';
    document.getElementById('approveModal').style.display = 'flex';
}
function openRejectModal(id) {
    document.getElementById('rejectForm').action = '{{ url('super-admin/plan-requests') }}/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}
</script>
@endpush