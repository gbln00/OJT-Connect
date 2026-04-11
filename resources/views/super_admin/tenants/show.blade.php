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

    $planRequests    = \App\Models\PlanRequest::where('tenant_id', $tenant->id)->latest()->get();
    $pendingRequests = $planRequests->where('status', 'pending');
    $domainCount     = $tenant->domains->count();
    $planRequestCount = $planRequests->count();
@endphp

{{-- ════════════════════════════════════════════════════
     BREADCRUMB
════════════════════════════════════════════════════ --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:24px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;animation:flicker 8s ease-in-out infinite;flex-shrink:0;"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        Super Admin / Tenants /
    </span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--text);">
        {{ $tenant->id }}
    </span>
</div>

{{-- ════════════════════════════════════════════════════
     HERO IDENTITY — matches index stat-card visual weight
════════════════════════════════════════════════════ --}}
<div class="card fade-up" style="margin-bottom:16px;overflow:hidden;position:relative;">

    {{-- Subtle background texture --}}
    <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 80% 50%, rgba(140,14,3,0.04) 0%, transparent 65%);pointer-events:none;"></div>

    <div style="padding:28px 28px 24px;position:relative;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;">

            {{-- Left: identity --}}
            <div style="display:flex;align-items:center;gap:18px;">
                <div style="width:60px;height:60px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.07);
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;position:relative;">
                    <span style="font-family:'Playfair Display',serif;font-weight:700;font-size:24px;color:var(--crimson);">
                        {{ strtoupper(substr($tenant->id, 0, 1)) }}
                    </span>
                    {{-- Active indicator --}}
                    <span style="position:absolute;bottom:-3px;right:-3px;width:10px;height:10px;border-radius:50%;
                                 background:{{ $isActive ? '#22c55e' : '#ef4444' }};border:2px solid var(--surface);
                                 {{ $isActive ? 'box-shadow:0 0 6px rgba(34,197,94,0.5);' : '' }}"></span>
                </div>
                <div>
                    <div style="font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--text);line-height:1.1;margin-bottom:4px;">
                        {{ $tenant->id }}
                    </div>
                    @if($tenant->name)
                    <div style="font-size:13px;color:var(--muted);">{{ $tenant->name }}</div>
                    @endif
                    @if($tenant->email)
                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:3px;opacity:0.7;">{{ $tenant->email }}</div>
                    @endif
                </div>
            </div>

            {{-- Right: plan + actions --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    {{-- Plan badge --}}
                    @if($pc)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};font-family:'Barlow Condensed',sans-serif;font-size:13px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:{{ $pc['color'] }};">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $pc['dot'] }};display:inline-block;"></span>
                        {{ ucfirst($tenant->plan) }}
                    </span>
                    @endif
                    {{-- Status badge --}}
                    <span class="status-dot {{ $isActive ? 'active' : 'inactive' }}" style="font-size:12.5px;">
                        {{ $isActive ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="{{ route('super_admin.tenants.edit', $tenant) }}" class="btn btn-ghost btn-sm">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                        Edit
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
        </div>
    </div>

    {{-- Divider --}}
    <div style="height:1px;background:var(--border);"></div>

    {{-- Meta strip --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;">
        <div style="padding:16px 24px;border-right:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Tenant ID</div>
            <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--text);">{{ $tenant->id }}</div>
        </div>
        <div style="padding:16px 24px;border-right:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Created</div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ $tenant->created_at->format('M d, Y') }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;opacity:0.6;">{{ $tenant->created_at->diffForHumans() }}</div>
        </div>
        <div style="padding:16px 24px;border-right:1px solid var(--border);">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Last Updated</div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text2);">{{ $tenant->updated_at->format('M d, Y') }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:2px;opacity:0.6;">{{ $tenant->updated_at->diffForHumans() }}</div>
        </div>
        <div style="padding:16px 24px;">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.16em;text-transform:uppercase;color:var(--muted);margin-bottom:6px;">Domain{{ $domainCount !== 1 ? 's' : '' }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:5px;">
                @forelse($tenant->domains as $domain)
                    <a href="https://{{ $domain->domain }}:8000" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:5px;padding:2px 8px;
                              border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.06);
                              font-family:'DM Mono',monospace;font-size:10.5px;color:rgba(200,100,90,0.9);
                              text-decoration:none;transition:all 0.15s;"
                       onmouseover="this.style.borderColor='rgba(140,14,3,0.6)';this.style.background='rgba(140,14,3,0.12)'"
                       onmouseout="this.style.borderColor='rgba(140,14,3,0.3)';this.style.background='rgba(140,14,3,0.06)'">
                        <span style="width:4px;height:4px;background:var(--crimson);border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                        {{ $domain->domain }}
                        <svg width="8" height="8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.4;flex-shrink:0;">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                @empty
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">// none</span>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════
     STAT STRIP — matching index page visual weight
════════════════════════════════════════════════════ --}}
<div class="stats-grid fade-up fade-up-1" style="grid-template-columns:repeat(4,1fr);margin-bottom:16px;">

    {{-- Plan requests --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:rgba(201,168,76,0.1);border-color:rgba(201,168,76,0.2);">
                <svg width="15" height="15" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
            </div>
            <span class="stat-tag">requests</span>
        </div>
        <div class="stat-num">{{ $planRequestCount }}</div>
        <div class="stat-label">Plan Requests</div>
    </div>

    {{-- Pending --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon" style="background:rgba(201,168,76,0.1);border-color:rgba(201,168,76,0.2);">
                <svg width="15" height="15" fill="none" stroke="#c9a84c" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            @if($pendingRequests->count() > 0)
            <span class="stat-tag" style="color:#c9a84c;animation:flicker 3s ease-in-out infinite;">pending</span>
            @else
            <span class="stat-tag">pending</span>
            @endif
        </div>
        <div class="stat-num" style="{{ $pendingRequests->count() > 0 ? 'color:#c9a84c;' : '' }}">{{ $pendingRequests->count() }}</div>
        <div class="stat-label">Awaiting Action</div>
    </div>

    {{-- Domains --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                </svg>
            </div>
            <span class="stat-tag">linked</span>
        </div>
        <div class="stat-num">{{ $domainCount }}</div>
        <div class="stat-label">Domain{{ $domainCount !== 1 ? 's' : '' }}</div>
    </div>

    {{-- Plan history --}}
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/>
                </svg>
            </div>
            <span class="stat-tag">history</span>
        </div>
        <div class="stat-num">{{ $planHistory->count() }}</div>
        <div class="stat-label">Plan Changes</div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════
     PLAN REQUESTS
════════════════════════════════════════════════════ --}}
@if($planRequests->count() > 0)
<div class="fade-up fade-up-2" style="margin-bottom:16px;">

    <div class="card-header" style="padding:18px 0 14px;border-bottom:none;margin-bottom:4px;">
        <div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">Plan Requests</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                // {{ $pendingRequests->count() }} pending · {{ $planRequests->count() }} total
            </div>
        </div>
        @if($pendingRequests->count() > 0)
        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                     border:1px solid rgba(201,168,76,0.3);background:rgba(201,168,76,0.06);
                     font-family:'DM Mono',monospace;font-size:10px;color:#c9a84c;">
            <span style="width:5px;height:5px;border-radius:50%;background:#c9a84c;display:inline-block;animation:flicker 3s ease-in-out infinite;"></span>
            {{ $pendingRequests->count() }} awaiting action
        </span>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;gap:12px;">
    @foreach($planRequests as $index => $pr)
    @php
        $sc = match($pr->status) {
            'approved' => ['color'=>'#34d399','border'=>'rgba(52,211,153,0.25)','bg'=>'rgba(52,211,153,0.05)','label'=>'Approved','accentLeft'=>'rgba(52,211,153,0.6)'],
            'rejected' => ['color'=>'#f87171','border'=>'rgba(248,113,113,0.25)','bg'=>'rgba(248,113,113,0.05)','label'=>'Rejected','accentLeft'=>'rgba(248,113,113,0.6)'],
            default    => ['color'=>'#c9a84c','border'=>'rgba(201,168,76,0.3)','bg'=>'rgba(201,168,76,0.05)','label'=>'Pending','accentLeft'=>'rgba(201,168,76,0.7)'],
        };
        $isPending = $pr->status === 'pending';
        $isUpgrade = $pr->request_type === 'upgrade';
    @endphp

    <div style="border:1px solid var(--border);border-left:3px solid {{ $sc['accentLeft'] }};background:var(--surface);overflow:hidden;">

        {{-- ── Request header ── --}}
        <div style="padding:18px 22px 16px;{{ $isPending ? 'background:rgba(201,168,76,0.02);' : '' }}border-bottom:1px solid var(--border);">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">

                <div style="display:flex;flex-direction:column;gap:10px;">
                    {{-- Badges row --}}
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 11px;
                                     border:1px solid {{ $isUpgrade ? 'rgba(52,211,153,0.35)' : 'rgba(248,113,113,0.35)' }};
                                     background:{{ $isUpgrade ? 'rgba(52,211,153,0.06)' : 'rgba(248,113,113,0.06)' }};
                                     font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;
                                     letter-spacing:0.12em;text-transform:uppercase;
                                     color:{{ $isUpgrade ? '#34d399' : '#f87171' }};">
                            {{ $isUpgrade ? '↑' : '↓' }} {{ ucfirst($pr->request_type) }}
                        </span>

                        {{-- Plan change --}}
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);padding:3px 9px;border:1px solid var(--border);background:var(--surface2);">{{ ucfirst($pr->current_plan) }}</span>
                            <span style="color:var(--muted);font-size:13px;line-height:1;">→</span>
                            <span style="font-family:'DM Mono',monospace;font-size:11px;font-weight:700;padding:3px 9px;border:1px solid {{ $sc['border'] }};background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">{{ ucfirst($pr->requested_plan) }}</span>
                        </div>

                        {{-- Status --}}
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;
                                     border:1px solid {{ $sc['border'] }};background:{{ $sc['bg'] }};
                                     font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;
                                     text-transform:uppercase;color:{{ $sc['color'] }};">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc['color'] }};display:inline-block;{{ $isPending ? 'animation:flicker 3s ease-in-out infinite;' : '' }}"></span>
                            {{ $sc['label'] }}
                        </span>
                    </div>

                    {{-- Meta line --}}
                    <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px;">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $pr->contact_email }}
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);display:flex;align-items:center;gap:5px;">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $pr->created_at->format('M d, Y') }}
                            <span style="opacity:0.5;">· {{ $pr->created_at->diffForHumans() }}</span>
                        </span>
                    </div>
                </div>

                @if(!$isPending)
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.12em;text-transform:uppercase;color:var(--muted);margin-bottom:4px;">{{ $sc['label'] }} on</div>
                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:{{ $sc['color'] }};">{{ $pr->actioned_at?->format('M d, Y \a\t g:i A') ?? '—' }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Message + Admin notes ── --}}
        @if($pr->message || $pr->admin_notes)
        <div style="display:grid;grid-template-columns:{{ ($pr->message && $pr->admin_notes) ? '1fr 1fr' : '1fr' }};gap:0;border-bottom:1px solid var(--border);">
            @if($pr->message)
            <div style="padding:16px 22px;{{ $pr->admin_notes ? 'border-right:1px solid var(--border);' : '' }}background:var(--surface2);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Message from tenant</div>
                <div style="font-size:12.5px;color:var(--text2);line-height:1.7;">{{ $pr->message }}</div>
            </div>
            @endif
            @if($pr->admin_notes)
            <div style="padding:16px 22px;background:{{ $sc['bg'] }};">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:{{ $sc['color'] }};margin-bottom:8px;opacity:0.8;">Admin notes</div>
                <div style="font-size:12.5px;color:var(--text2);line-height:1.7;">{{ $pr->admin_notes }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- ── Inline action forms (pending only) ── --}}
        @if($isPending)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;">

            {{-- Approve --}}
            <div style="padding:20px 22px;border-right:1px solid var(--border);background:rgba(52,211,153,0.02);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:#34d399;margin-bottom:12px;display:flex;align-items:center;gap:5px;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    Approve request
                </div>
                <form method="POST" action="{{ url('super-admin/plan-requests') }}/{{ $pr->id }}/approve">
                    @csrf
                    <textarea name="admin_notes" rows="3"
                              placeholder="Optional note to tenant about this approval..."
                              style="width:100%;padding:10px 12px;border:1px solid var(--border2);background:var(--surface);color:var(--text);font-size:12px;font-family:inherit;resize:none;outline:none;margin-bottom:10px;box-sizing:border-box;transition:border-color 0.15s;"
                              onfocus="this.style.borderColor='#34d399'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    <button type="submit" class="btn btn-approve btn-sm" style="width:100%;justify-content:center;">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        Confirm Approval
                    </button>
                </form>
            </div>

            {{-- Reject --}}
            <div style="padding:20px 22px;background:rgba(248,113,113,0.02);">
                <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;text-transform:uppercase;color:#f87171;margin-bottom:12px;display:flex;align-items:center;gap:5px;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Reject request
                </div>
                <form method="POST" action="{{ url('super-admin/plan-requests') }}/{{ $pr->id }}/reject">
                    @csrf
                    <textarea name="admin_notes" rows="3"
                              placeholder="Reason for rejection (optional, sent to tenant)..."
                              style="width:100%;padding:10px 12px;border:1px solid var(--border2);background:var(--surface);color:var(--text);font-size:12px;font-family:inherit;resize:none;outline:none;margin-bottom:10px;box-sizing:border-box;transition:border-color 0.15s;"
                              onfocus="this.style.borderColor='#f87171'" onblur="this.style.borderColor='var(--border2)'"></textarea>
                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Confirm Rejection
                    </button>
                </form>
            </div>

        </div>
        @else
        {{-- Resolved footer --}}
        <div style="padding:12px 22px;display:flex;align-items:center;gap:7px;background:{{ $sc['bg'] }};">
            <svg width="11" height="11" fill="none" stroke="{{ $sc['color'] }}" stroke-width="2.5" viewBox="0 0 24 24">
                @if($pr->status === 'approved')
                    <polyline points="20,6 9,17 4,12"/>
                @else
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                @endif
            </svg>
            <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                {{ $sc['label'] }} · no further action required
            </span>
        </div>
        @endif

    </div>
    @endforeach
    </div>
</div>
@endif


{{-- ════════════════════════════════════════════════════
     PLAN HISTORY
════════════════════════════════════════════════════ --}}
@if($planHistory->count() > 0)
<div class="card fade-up fade-up-3" style="margin-bottom:16px;">
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
                    <td style="font-size:12px;color:var(--muted);max-width:180px;">
                        {{ $h->notes ? \Illuminate\Support\Str::limit($h->notes, 55) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif


{{-- ════════════════════════════════════════════════════
     DANGER ZONE
════════════════════════════════════════════════════ --}}
<div class="card fade-up fade-up-4 danger-zone" style="border-color:rgba(140,14,3,0.2);">
    <div class="card-header">
        <div>
            <div style="font-family:'Playfair Display',serif;font-size:14px;font-weight:700;color:var(--crimson);">Danger Zone</div>
            <div style="font-size:12px;color:var(--muted);margin-top:3px;">Irreversible destructive actions</div>
        </div>
    </div>
    <div style="padding:18px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
            <div>
                <div style="font-size:13px;font-weight:500;color:var(--text);margin-bottom:4px;">Delete this tenant</div>
                <div style="font-size:12px;color:var(--muted);line-height:1.65;max-width:440px;">
                    Permanently drops the tenant database and removes all users, applications, hour logs, and evaluations.
                    <span style="color:var(--crimson);"> This cannot be undone.</span>
                </div>
            </div>
            <button onclick="document.getElementById('deleteModal').style.display='flex'" class="btn btn-danger btn-sm" style="flex-shrink:0;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                </svg>
                Delete Tenant
            </button>
        </div>
    </div>
</div>


{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(4px);z-index:999;align-items:center;justify-content:center;"
     onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--surface);border:1px solid var(--border2);border-top:2px solid var(--crimson);width:100%;max-width:440px;margin:0 20px;padding:32px;animation:fadeUp 0.2s ease both;">
        <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--text);margin-bottom:14px;">Delete Tenant?</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.8;margin-bottom:28px;">
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