@extends($layout.coordinator-app)

@section('title', 'Ticket ' . $ticket->ref)
@section('page-title', 'Ticket ' . $ticket->ref)

@section('content')

@php
    $indexRoute = match(true) {
        request()->routeIs('admin.*')       => 'admin.support.index',
        request()->routeIs('coordinator.*') => 'coordinator.support.index',
        request()->routeIs('supervisor.*')  => 'supervisor.support.index',
        default                             => 'student.support.index',
    };
    $replyRoute = match(true) {
        request()->routeIs('admin.*')       => 'admin.support.reply',
        request()->routeIs('coordinator.*') => 'coordinator.support.reply',
        request()->routeIs('supervisor.*')  => 'supervisor.support.reply',
        default                             => 'student.support.reply',
    };
    $closeRoute = match(true) {
        request()->routeIs('admin.*')       => 'admin.support.close',
        request()->routeIs('coordinator.*') => 'coordinator.support.close',
        request()->routeIs('supervisor.*')  => 'supervisor.support.close',
        default                             => 'student.support.close',
    };
@endphp

<div style="max-width:800px;">

    {{-- Back --}}
    <div style="margin-bottom:20px;">
        <a href="{{ route($indexRoute) }}"
           style="display:inline-flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;
                  font-size:11px;letter-spacing:0.1em;text-transform:uppercase;
                  color:var(--muted);text-decoration:none;transition:color 0.15s;"
           onmouseover="this.style.color='var(--text)'"
           onmouseout="this.style.color='var(--muted)'">
            ← All tickets
        </a>
    </div>

    {{-- Ticket header card --}}
    <div class="card fade-up" style="margin-bottom:16px;">
        <div style="padding:20px 24px;">
            {{-- Ref + status row --}}
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;flex-wrap:wrap;">
                <span style="font-family:'DM Mono',monospace;font-size:11px;
                             color:var(--muted);letter-spacing:0.1em;">
                    {{ $ticket->ref }}
                </span>
                <span class="status-pill {{ $ticket->status_color }}">{{ $ticket->status_label }}</span>
                @php $pc = match($ticket->priority) {'urgent'=>'coral','high'=>'gold','normal'=>'blue',default=>'steel'}; @endphp
                <span class="status-pill {{ $pc }}">{{ $ticket->priority_label }} Priority</span>
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

            {{-- Subject --}}
            <h2 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;
                       color:var(--text);line-height:1.3;margin-bottom:8px;">
                {{ $ticket->subject }}
            </h2>

            {{-- Meta --}}
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                        letter-spacing:0.04em;display:flex;gap:16px;flex-wrap:wrap;">
                <span>Submitted {{ $ticket->created_at->format('M d, Y \a\t H:i') }}</span>
                @if($ticket->resolved_at)
                    <span>Resolved {{ $ticket->resolved_at->format('M d, Y \a\t H:i') }}</span>
                @endif
                <span>{{ $ticket->replies->count() }} {{ Str::plural('reply', $ticket->replies->count()) }}</span>
            </div>
        </div>

        {{-- Original message --}}
        <div style="border-top:1px solid var(--border);padding:20px 24px;background:var(--surface2);">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <div style="width:26px;height:26px;border:1px solid rgba(140,14,3,0.4);
                            background:rgba(140,14,3,0.08);display:flex;align-items:center;
                            justify-content:center;font-family:'Playfair Display',serif;
                            font-size:10px;font-weight:700;color:var(--crimson);flex-shrink:0;">
                    {{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:12px;font-weight:600;color:var(--text);">
                        {{ $ticket->user->name ?? 'You' }}
                        <span style="font-family:'DM Mono',monospace;font-size:10px;
                                     color:var(--muted);font-weight:400;margin-left:6px;">
                            (you)
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
    @if($ticket->replies->isNotEmpty())
    <div style="margin-bottom:16px;display:flex;flex-direction:column;gap:10px;">
        @foreach($ticket->replies as $reply)
        @php $isSupport = $reply->isFromSupport(); @endphp
        <div class="card fade-up"
             style="{{ $isSupport ? 'border-left:3px solid var(--teal-border);' : 'border-left:3px solid var(--border2);' }}">
            <div style="padding:16px 20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    {{-- Avatar --}}
                    @if($isSupport)
                    <div style="width:26px;height:26px;border:1px solid var(--teal-border);
                                background:var(--teal-dim);display:flex;align-items:center;
                                justify-content:center;flex-shrink:0;">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24" style="color:var(--teal-color);">
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
                                    color:{{ $isSupport ? 'var(--teal-color)' : 'var(--text)' }};">
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
                              color:var(--blue-color);text-decoration:none;
                              border:1px solid var(--blue-border);padding:4px 10px;
                              background:var(--blue-dim);">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                        </svg>
                        {{ $reply->attachment_name ?? 'Attachment' }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Reply form --}}
    @if($ticket->isActive())
    <div class="card fade-up" style="margin-bottom:16px;">
        <div class="card-header">
            <span class="card-title">Add a Reply</span>
        </div>
        <form method="POST"
              action="{{ route($replyRoute, $ticket) }}"
              enctype="multipart/form-data"
              style="padding:20px;">
            @csrf

            <div style="margin-bottom:14px;">
                <label class="form-label">Your message</label>
                <textarea name="message" rows="5" class="form-textarea"
                          placeholder="Type your reply here..."
                          required minlength="5" maxlength="3000">{{ old('message') }}</textarea>
                @error('message')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom:18px;">
                <label class="form-label">Attachment <span style="color:var(--muted);font-weight:400;">(optional, max 5 MB)</span></label>
                <input type="file" name="attachment" class="form-input" style="padding:6px 10px;"
                       accept=".pdf,.jpg,.jpeg,.png,.webp,.txt,.doc,.docx">
                <p class="form-hint">Supported: PDF, JPG, PNG, WEBP, TXT, DOC, DOCX</p>
            </div>

            <div style="display:flex;align-items:center;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/>
                    </svg>
                    Send Reply
                </button>
            </div>
        </form>
    </div>

    {{-- Close ticket --}}
    <div style="margin-top:6px;padding:14px 0;border-top:1px solid var(--border);">
        <form method="POST" action="{{ route($closeRoute, $ticket) }}"
              onsubmit="return confirm('Close this ticket? You can always open a new one.');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-ghost btn-sm"
                    style="color:var(--muted);border-color:var(--border2);">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                Close this ticket
            </button>
        </form>
    </div>

    @else
    {{-- Ticket is closed / resolved --}}
    <div style="padding:18px;border:1px solid var(--border2);background:var(--surface2);
                display:flex;align-items:center;gap:10px;margin-bottom:16px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8"
             viewBox="0 0 24 24" style="color:var(--muted);flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <div style="font-size:13px;color:var(--text);font-weight:500;">
                This ticket is {{ $ticket->status_label }}.
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                If you still need help, please open a new ticket.
            </div>
        </div>
        <a href="{{ route(match(true) {
            request()->routeIs('admin.*')       => 'admin.support.create',
            request()->routeIs('coordinator.*') => 'coordinator.support.create',
            request()->routeIs('supervisor.*')  => 'supervisor.support.create',
            default                             => 'student.support.create',
        }) }}" class="btn btn-ghost btn-sm" style="margin-left:auto;flex-shrink:0;">
            New Ticket
        </a>
    </div>
    @endif

</div>

@endsection