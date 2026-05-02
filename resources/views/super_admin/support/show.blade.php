@extends('layouts.superadmin-app')
@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)

@section('content')

<div style="max-width:860px;">

    {{-- Back --}}
    <div style="margin-bottom:20px;">
        <a href="{{ route('super_admin.support.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;
                  font-size:11px;letter-spacing:0.1em;text-transform:uppercase;
                  color:var(--muted);text-decoration:none;transition:color 0.15s;"
           onmouseover="this.style.color='var(--text)'"
           onmouseout="this.style.color='var(--muted)'">
            ← All tickets
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(45,212,191,0.07);border:1px solid rgba(45,212,191,0.2);
                color:#2dd4bf;padding:12px 16px;margin-bottom:16px;
                display:flex;align-items:center;gap:10px;font-size:13px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <polyline points="20,6 9,17 4,12"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:flex-start;">

        {{-- ── Left: ticket thread ── --}}
        <div>

            {{-- Header card --}}
            <div class="card fade-up" style="margin-bottom:16px;">
                <div style="padding:20px 24px;">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                        <span style="font-family:'DM Mono',monospace;font-size:11px;
                                     color:var(--muted);letter-spacing:0.1em;">
                            {{ $ticket->ref }}
                        </span>
                        <span class="status-pill {{ $ticket->status_color }}">
                            {{ $ticket->status_label }}
                        </span>
                        <span class="status-pill {{ $ticket->priority_color }}">
                            {{ $ticket->priority_label }} Priority
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);border:1px solid var(--border2);padding:2px 7px;">
                            {{ $ticket->type_label }}
                        </span>
                        @if($ticket->module)
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);border:1px solid var(--border2);padding:2px 7px;">
                            {{ str_replace('_', ' ', ucfirst($ticket->module)) }}
                        </span>
                        @endif
                    </div>

                    <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;
                               color:var(--text);line-height:1.3;margin-bottom:8px;">
                        {{ $ticket->subject }}
                    </h2>

                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                                letter-spacing:0.04em;display:flex;gap:16px;flex-wrap:wrap;">
                        <span>Tenant: <strong style="color:var(--text2);">{{ $ticket->tenant_name }}</strong></span>
                        <span>From: <strong style="color:var(--text2);">{{ $ticket->user_name ?? '—' }}</strong></span>
                        <span>Submitted {{ $ticket->created_at->format('M d, Y \a\t H:i') }}</span>
                        @if($ticket->resolved_at)
                            <span>Resolved {{ $ticket->resolved_at->format('M d, Y \a\t H:i') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Original message --}}
                <div style="border-top:1px solid var(--border);padding:20px 24px;background:var(--surface2);">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                        <div style="width:26px;height:26px;border:1px solid rgba(140,14,3,0.4);
                                    background:rgba(140,14,3,0.08);display:flex;align-items:center;
                                    justify-content:center;font-family:'Playfair Display',serif;
                                    font-size:10px;font-weight:700;color:var(--crimson);flex-shrink:0;">
                            {{ strtoupper(substr($ticket->user_name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--text);">
                                {{ $ticket->user_name ?? 'User' }}
                                <span style="font-family:'DM Mono',monospace;font-size:10px;
                                             color:var(--muted);font-weight:400;margin-left:6px;">
                                    (tenant user)
                                </span>
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
            <div style="margin-bottom:16px;display:flex;flex-direction:column;gap:10px;">
                @foreach($replies as $reply)
                <div class="card"
                     style="{{ $reply->is_from_support
                        ? 'border-left:3px solid rgba(45,212,191,0.3);'
                        : 'border-left:3px solid var(--border2);' }}">
                    <div style="padding:16px 20px;">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            @if($reply->is_from_support)
                            <div style="width:26px;height:26px;border:1px solid rgba(45,212,191,0.25);
                                        background:rgba(45,212,191,0.08);display:flex;align-items:center;
                                        justify-content:center;flex-shrink:0;">
                                <svg width="12" height="12" fill="none" stroke="#2dd4bf" stroke-width="2" viewBox="0 0 24 24">
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
                                            color:{{ $reply->is_from_support ? '#2dd4bf' : 'var(--text)' }};">
                                    {{ $reply->display_name }}
                                </div>
                                <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--muted);">
                                    {{ $reply->created_at->diffForHumans() }}
                                    · {{ $reply->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <div style="font-size:13.5px;line-height:1.7;color:var(--text);white-space:pre-wrap;">{{ $reply->message }}</div>

                        @if($reply->attachment_path)
                        <div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border);">
                            <a href="{{ Storage::url($reply->attachment_path) }}"
                               target="_blank"
                               style="display:inline-flex;align-items:center;gap:6px;font-size:12px;
                                      color:#60a5fa;text-decoration:none;
                                      border:1px solid rgba(96,165,250,0.2);padding:4px 10px;
                                      background:rgba(96,165,250,0.06);">
                                📎 {{ $reply->attachment_name ?? 'Attachment' }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Reply form --}}
            @if(!$ticket->is_closed)
            <div class="card fade-up">
                <div class="card-header">
                    <span class="card-title">Reply as Support Team</span>
                </div>
                <form method="POST"
                      action="{{ route('super_admin.support.reply', ['tenantId' => $ticket->tenant_id, 'ticketId' => $ticket->id]) }}"
                      style="padding:20px;">
                    @csrf

                    <div style="margin-bottom:14px;">
                        <label class="form-label">Message *</label>
                        <textarea name="message" rows="6" class="form-textarea"
                                  placeholder="Type your reply to the tenant..."
                                  required minlength="5" maxlength="3000">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
                        <div>
                            <label class="form-label">Update Status *</label>
                            <select name="status" class="form-select" required>
                                @foreach([
                                    'open'            => 'Open',
                                    'in_progress'     => 'In Progress',
                                    'waiting_on_user' => 'Waiting on User',
                                    'resolved'        => 'Resolved',
                                    'closed'          => 'Closed',
                                ] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ (old('status', $ticket->status) === $val) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">
                                Internal Note
                                <span style="color:var(--muted);font-weight:400;">(not shown to tenant)</span>
                            </label>
                            <input type="text" name="internal_note"
                                   class="form-input"
                                   value="{{ old('internal_note') }}"
                                   placeholder="Private note for your team...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22,2 15,22 11,13 2,9"/>
                        </svg>
                        Send Reply
                    </button>
                </form>
            </div>
            @else
            <div style="padding:16px;border:1px solid var(--border2);background:var(--surface2);
                        display:flex;align-items:center;gap:10px;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8"
                     viewBox="0 0 24 24" style="color:var(--muted);flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="font-size:13px;color:var(--muted);">
                    This ticket is closed. Use the status update form on the right to reopen it.
                </span>
            </div>
            @endif

        </div>

        {{-- ── Right: meta + quick actions ── --}}
        <div style="position:sticky;top:90px;display:flex;flex-direction:column;gap:14px;">

            {{-- Ticket meta --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Ticket Info</span>
                </div>
                <div style="padding:14px 16px;display:flex;flex-direction:column;gap:10px;">
                    @foreach([
                        ['Tenant',    $ticket->tenant_name],
                        ['From',      $ticket->user_name ?? '—'],
                        ['Email',     $ticket->user_email ?? '—'],
                        ['Type',      $ticket->type_label],
                        ['Module',    $ticket->module ? str_replace('_', ' ', ucfirst($ticket->module)) : '—'],
                        ['Submitted', $ticket->created_at->format('M d, Y')],
                    ] as [$label, $value])
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;
                                gap:8px;font-size:12px;">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);letter-spacing:0.06em;flex-shrink:0;">
                            {{ $label }}
                        </span>
                        <span style="color:var(--text2);text-align:right;word-break:break-word;">
                            {{ $value }}
                        </span>
                    </div>
                    @endforeach

                    @if($ticket->internal_note)
                    <div style="margin-top:4px;padding:8px 10px;background:rgba(201,168,76,0.07);
                                border:1px solid rgba(201,168,76,0.2);">
                        <div style="font-family:'DM Mono',monospace;font-size:9px;color:var(--gold-color);
                                    letter-spacing:0.1em;text-transform:uppercase;margin-bottom:4px;">
                            Internal Note
                        </div>
                        <div style="font-size:12px;color:var(--text2);line-height:1.5;">
                            {{ $ticket->internal_note }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Quick status update --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Quick Status</span>
                </div>
                <form method="POST"
                      action="{{ route('super_admin.support.updateStatus', ['tenantId' => $ticket->tenant_id, 'ticketId' => $ticket->id]) }}"
                      style="padding:14px 16px;">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select" style="margin-bottom:10px;">
                        @foreach([
                            'open'            => 'Open',
                            'in_progress'     => 'In Progress',
                            'waiting_on_user' => 'Waiting on User',
                            'resolved'        => 'Resolved',
                            'closed'          => 'Closed',
                        ] as $val => $label)
                        <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">
                        Update Status
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>

@endsection
