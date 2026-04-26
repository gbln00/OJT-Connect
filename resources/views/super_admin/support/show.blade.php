@extends('layouts.superadmin-app')

@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)

@section('content')

<div style="max-width:900px;">

    {{-- ── Back + breadcrumb ───────────────────────────────────── --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
        <a href="{{ route('super_admin.support.index') }}"
           style="display:inline-flex;align-items:center;gap:5px;font-family:'DM Mono',monospace;
                  font-size:11px;letter-spacing:0.1em;text-transform:uppercase;
                  color:var(--muted);text-decoration:none;transition:color 0.15s;"
           onmouseover="this.style.color='var(--text)'"
           onmouseout="this.style.color='var(--muted)'">
            ← All Tickets
        </a>
        <span style="color:var(--border2);">/</span>
        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
            {{ $ticket->tenant_name ?? $tenant->id }}
        </span>
        <span style="color:var(--border2);">/</span>
        <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text);">
            {{ $ticket->ref }}
        </span>
    </div>

    <div style="display:grid;grid-template-columns:1fr 260px;gap:20px;align-items:start;">

        {{-- ── Left: ticket thread ──────────────────────────── --}}
        <div>

            {{-- Ticket header --}}
            <div class="card fade-up" style="margin-bottom:14px;">
                <div style="padding:20px 24px;">

                    {{-- Status + priority badges --}}
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;
                                     letter-spacing:0.1em;color:var(--muted);">
                            {{ $ticket->ref }}
                        </span>
                        <span class="status-pill {{ $ticket->status_color }}">{{ $ticket->status_label }}</span>
                        @php $pc = match($ticket->priority) {'urgent'=>'coral','high'=>'gold','normal'=>'blue',default=>'steel'}; @endphp
                        <span class="status-pill {{ $pc }}">{{ $ticket->priority_label }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                                     border:1px solid var(--border2);padding:2px 7px;">
                            {{ $ticket->type_label }}
                        </span>
                        @if($ticket->module)
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                                     border:1px solid var(--border2);padding:2px 7px;">
                            {{ str_replace('_',' ',ucfirst($ticket->module)) }}
                        </span>
                        @endif
                    </div>

                    <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;
                               color:var(--text);line-height:1.3;margin-bottom:8px;">
                        {{ $ticket->subject }}
                    </h2>

                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                                letter-spacing:0.04em;display:flex;gap:16px;flex-wrap:wrap;">
                        <span>From: <strong style="color:var(--text2);">{{ $ticket->user?->name ?? 'Unknown' }}</strong>
                              ({{ str_replace('_',' ',ucfirst($ticket->user?->role ?? '')) }})</span>
                        <span>Tenant: <strong style="color:var(--text2);">{{ $ticket->tenant_name ?? $tenant->id }}</strong></span>
                        <span>{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                {{-- Original message --}}
                <div style="border-top:1px solid var(--border);padding:20px 24px;background:var(--surface2);">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <div style="width:26px;height:26px;border:1px solid rgba(140,14,3,0.4);
                                    background:rgba(140,14,3,0.08);display:flex;align-items:center;
                                    justify-content:center;font-family:'Playfair Display',serif;
                                    font-size:10px;font-weight:700;color:var(--crimson);flex-shrink:0;">
                            {{ strtoupper(substr($ticket->user?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--text);">
                                {{ $ticket->user?->name ?? 'User' }}
                            </div>
                            <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">
                                {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div style="font-size:13.5px;line-height:1.7;color:var(--text);white-space:pre-wrap;">{{ $ticket->message }}</div>
                </div>
            </div>

            {{-- Reply thread --}}
            @if($replies->isNotEmpty())
            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:14px;">
                @foreach($replies as $reply)
                @php $isSupport = $reply->isFromSupport(); @endphp
                <div class="card fade-up"
                     style="{{ $isSupport
                         ? 'border-left:3px solid var(--teal-border);'
                         : 'border-left:3px solid var(--border2);' }}">
                    <div style="padding:16px 20px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            @if($isSupport)
                            <div style="width:26px;height:26px;border:1px solid var(--teal-border);
                                        background:var(--teal-dim);display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0;">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" style="color:var(--teal-color,#2dd4bf);">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            @else
                            <div style="width:26px;height:26px;border:1px solid rgba(140,14,3,0.4);
                                        background:rgba(140,14,3,0.08);display:flex;align-items:center;
                                        justify-content:center;font-family:'Playfair Display',serif;
                                        font-size:10px;font-weight:700;color:var(--crimson);flex-shrink:0;">
                                {{ strtoupper(substr($reply->sender_name ?? 'U', 0, 2)) }}
                            </div>
                            @endif

                            <div>
                                <div style="font-size:12px;font-weight:600;
                                            color:{{ $isSupport ? 'var(--teal-color,#2dd4bf)' : 'var(--text)' }};">
                                    {{ $reply->display_name }}
                                    @if($isSupport)
                                    <span style="font-family:'DM Mono',monospace;font-size:9px;
                                                 background:var(--teal-dim);color:var(--teal-color,#2dd4bf);
                                                 border:1px solid var(--teal-border);padding:1px 5px;
                                                 margin-left:5px;">Support</span>
                                    @endif
                                </div>
                                <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">
                                    {{ $reply->created_at->diffForHumans() }}
                                    · {{ $reply->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <div style="font-size:13.5px;line-height:1.7;color:var(--text);white-space:pre-wrap;">{{ $reply->message }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── Reply form ─────────────────────────────────────── --}}
            <div class="card fade-up">
                <div class="card-header">
                    <span class="card-title">Reply to Tenant</span>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                        Sends as "Support Team"
                    </span>
                </div>
                <form method="POST"
                      action="{{ route('super_admin.support.reply', ['tenantId' => $tenant->id, 'ticketId' => $ticket->id]) }}"
                      style="padding:20px;">
                    @csrf

                    {{-- Flash messages --}}
                    @if(session('success'))
                    <div class="flash flash-success" style="margin-bottom:16px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.25);
                                padding:12px 16px;margin-bottom:16px;">
                        <ul style="margin:0;padding:0 0 0 16px;font-size:13px;color:var(--crimson);">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Reply message --}}
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Reply Message *</label>
                        <textarea name="message" rows="6" class="form-textarea"
                                  placeholder="Write your support response here. Be clear, helpful, and professional."
                                  required minlength="5" maxlength="3000">{{ old('message') }}</textarea>
                        @error('message')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Status selector --}}
                    <div style="margin-bottom:16px;">
                        <label class="form-label">Update Ticket Status *</label>
                        <select name="status" class="form-select" required>
                            @foreach([
                                'open'            => 'Open — Leave open for further discussion',
                                'in_progress'     => 'In Progress — Being actively worked on',
                                'waiting_on_user' => 'Waiting on User — Need more info from tenant',
                                'resolved'        => 'Resolved — Issue has been fixed / answered',
                                'closed'          => 'Closed — No further action needed',
                            ] as $v => $l)
                            <option value="{{ $v }}"
                                {{ old('status', $ticket->status) === $v ? 'selected' : '' }}>
                                {{ $l }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')<p class="form-error">{{ $message }}</p>@enderror
                    </div>

                    {{-- Internal note --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">
                            Internal Note
                            <span style="color:var(--muted);font-weight:400;">(not visible to tenant)</span>
                        </label>
                        <textarea name="internal_note" rows="3" class="form-textarea"
                                  placeholder="Optional internal notes for your team only..."
                                  maxlength="2000">{{ old('internal_note', $ticket->internal_note) }}</textarea>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/>
                            </svg>
                            Send Reply &amp; Update Status
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- ── Right sidebar: ticket info ───────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:14px;">

            {{-- Ticket info card --}}
            <div class="card fade-up">
                <div class="card-header">
                    <span class="card-title">Ticket Info</span>
                </div>
                <div style="padding:16px;">
                    @foreach([
                        ['Ref',       $ticket->ref],
                        ['Tenant',    $ticket->tenant_name ?? $tenant->id],
                        ['From',      $ticket->user?->name ?? '—'],
                        ['Role',      str_replace('_',' ',ucfirst($ticket->user?->role ?? '—'))],
                        ['Type',      $ticket->type_label],
                        ['Module',    $ticket->module ? str_replace('_',' ',ucfirst($ticket->module)) : '—'],
                        ['Submitted', $ticket->created_at->format('M d, Y')],
                        ['Replies',   $replies->count()],
                    ] as [$label, $value])
                    <div style="display:flex;justify-content:space-between;align-items:center;
                                padding:7px 0;border-bottom:1px solid var(--border);">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     letter-spacing:0.08em;text-transform:uppercase;color:var(--muted);">
                            {{ $label }}
                        </span>
                        <span style="font-size:12px;color:var(--text2);text-align:right;max-width:140px;
                                     overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            {{ $value }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick status change --}}
            <div class="card fade-up">
                <div class="card-header">
                    <span class="card-title">Quick Status</span>
                </div>
                <div style="padding:12px;">
                    <form method="POST"
                          action="{{ route('super_admin.support.updateStatus', ['tenantId' => $tenant->id, 'ticketId' => $ticket->id]) }}">
                        @csrf
                        @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="form-select"
                                style="margin-bottom:0;font-size:12px;">
                            @foreach(['open','in_progress','waiting_on_user','resolved','closed'] as $s)
                            <option value="{{ $s }}" {{ $ticket->status === $s ? 'selected' : '' }}>
                                {{ str_replace('_',' ',ucfirst($s)) }}
                            </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            {{-- Internal note display --}}
            @if($ticket->internal_note)
            <div class="card fade-up"
                 style="border:1px solid rgba(201,168,76,0.25);background:rgba(201,168,76,0.04);">
                <div class="card-header" style="border-bottom-color:rgba(201,168,76,0.15);">
                    <span class="card-title" style="color:var(--gold,#c9a84c);">Internal Note</span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">
                        Not visible to tenant
                    </span>
                </div>
                <div style="padding:14px 16px;font-size:13px;color:var(--text2);line-height:1.6;white-space:pre-wrap;">{{ $ticket->internal_note }}</div>
            </div>
            @endif

            {{-- View tenant link --}}
            <a href="{{ route('super_admin.tenants.show', $tenant->id) }}"
               class="btn btn-ghost btn-sm" style="justify-content:center;">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3"/>
                </svg>
                View Tenant Profile
            </a>

        </div>
    </div>
</div>

@endsection