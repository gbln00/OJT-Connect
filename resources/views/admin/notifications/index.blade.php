@extends('layouts.app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')

@php
$typeColors = [
    'success' => ['border'=>'rgba(52,211,153,0.25)', 'bg'=>'rgba(52,211,153,0.06)', 'color'=>'#34d399', 'dot'=>'#22c55e'],
    'warning' => ['border'=>'rgba(201,168,76,0.3)',  'bg'=>'rgba(201,168,76,0.08)', 'color'=>'#c9a84c', 'dot'=>'#c9a84c'],
    'info'    => ['border'=>'rgba(96,165,250,0.3)',  'bg'=>'rgba(96,165,250,0.07)', 'color'=>'#60a5fa', 'dot'=>'#60a5fa'],
];
@endphp

{{-- Flash --}}
@if(session('success'))
<div class="flash flash-success fade-up" style="margin-bottom:16px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ── Stat Strip ── --}}
<div class="stats-grid fade-up" style="grid-template-columns:repeat(3,1fr);margin-bottom:20px;">
    @foreach([
        [$notifications->total(),              'Total',  'stat-icon steel', '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>'],
        [$unreadCount,                         'Unread', 'stat-icon gold',  '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
        [$notifications->total()-$unreadCount, 'Read',   'stat-icon teal',  '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
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
    <div class="card-header">
        <div>
            <div class="card-title">Notifications</div>
            <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                // {{ $notifications->total() }} total · {{ $unreadCount }} unread
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            @if($unreadCount > 0)
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         border:1px solid var(--gold-border);background:var(--gold-dim);
                         font-family:'DM Mono',monospace;font-size:10px;color:var(--gold-color);">
                <span style="width:5px;height:5px;border-radius:50%;background:var(--gold-color);animation:flicker 3s ease-in-out infinite;display:inline-block;"></span>
                {{ $unreadCount }} unread
            </span>
            <form method="POST" action="{{ route('admin.notifications.markAllRead') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                    Mark all read
                </button>
            </form>
            @endif
            <form method="POST" action="{{ route('admin.notifications.clearRead') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--muted);">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3,6 5,6 21,6"/>
                        <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                    </svg>
                    Clear read
                </button>
            </form>
        </div>
    </div>

    @if($notifications->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);margin-bottom:8px;">All clear</div>
            <div style="font-size:13px;color:var(--muted);font-family:'DM Mono',monospace;">// No notifications from your system administrator.</div>
        </div>
    @else
        @foreach($notifications as $notif)
        @php
            $tc = $typeColors[$notif->type] ?? $typeColors['info'];
        @endphp
        <div style="display:flex;align-items:flex-start;gap:14px;padding:14px 20px;
                    background:{{ !$notif->is_read ? 'rgba(140,14,3,0.03)' : 'transparent' }};
                    border-left:2px solid {{ !$notif->is_read ? 'var(--crimson)' : 'transparent' }};
                    border-bottom:1px solid var(--border);transition:background 0.12s;"
             onmouseover="this.style.background='var(--surface2)'"
             onmouseout="this.style.background='{{ !$notif->is_read ? 'rgba(140,14,3,0.03)' : 'transparent' }}'">

            {{-- Icon --}}
            <div style="width:32px;height:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                        border:1px solid {{ $tc['border'] }};background:{{ $tc['bg'] }};margin-top:1px;">
                @if($notif->type === 'success')
                <svg width="13" height="13" fill="none" stroke="{{ $tc['color'] }}" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                @elseif($notif->type === 'warning')
                <svg width="13" height="13" fill="none" stroke="{{ $tc['color'] }}" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                @else
                <svg width="13" height="13" fill="none" stroke="{{ $tc['color'] }}" stroke-width="2.2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                @endif
            </div>

            {{-- Content --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;flex-wrap:wrap;">
                    <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:14px;
                                 color:{{ !$notif->is_read ? 'var(--text)' : 'var(--text2)' }};">
                        {{ $notif->title }}
                    </span>
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 7px;
                                 border:1px solid {{ $tc['border'] }};background:{{ $tc['bg'] }};
                                 font-family:'DM Mono',monospace;font-size:9px;
                                 color:{{ $tc['color'] }};letter-spacing:0.1em;text-transform:uppercase;">
                        <span style="width:4px;height:4px;border-radius:50%;background:{{ $tc['dot'] }};display:inline-block;"></span>
                        {{ $notif->type }}
                    </span>
                    @if(!$notif->is_read)
                        <span style="width:5px;height:5px;border-radius:50%;background:var(--crimson);flex-shrink:0;display:inline-block;"></span>
                    @endif
                </div>
                <div style="font-size:13px;color:var(--text2);line-height:1.6;margin-bottom:4px;">
                    {{ $notif->message }}
                </div>
                <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);opacity:0.7;">
                    {{ $notif->created_at->format('M d, Y \a\t g:i A') }}
                    · {{ $notif->created_at->diffForHumans() }}
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:4px;flex-shrink:0;align-items:center;">
                @if(!$notif->is_read)
                <button onclick="markReadInline({{ $notif->id }}, this)"
                        class="btn btn-approve btn-sm" title="Mark read" style="padding:5px 8px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                </button>
                @endif
                <button onclick="deleteNotif({{ $notif->id }}, this)"
                        class="btn btn-danger btn-sm" title="Delete" style="padding:5px 8px;">
                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3,6 5,6 21,6"/>
                        <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach

        @if($notifications->hasPages())
        <div class="pagination">
            <span class="pagination-info">Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}</span>
            <div style="display:flex;gap:4px;">
                @if($notifications->onFirstPage())
                    <span class="page-link disabled">← Prev</span>
                @else
                    <a href="{{ $notifications->previousPageUrl() }}" class="page-link">← Prev</a>
                @endif
                @if($notifications->hasMorePages())
                    <a href="{{ $notifications->nextPageUrl() }}" class="page-link">Next →</a>
                @else
                    <span class="page-link disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const BASE  = '{{ url('admin/notifications') }}';

function markReadInline(id, btn) {
    fetch(`${BASE}/${id}/read`, {
        method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        const row = btn.closest('div[style]');
        row.style.background = 'transparent';
        row.style.borderLeft = '2px solid transparent';
        btn.remove();
        const dot = row.querySelector('span[style*="border-radius:50%;background:var(--crimson)"]');
        if (dot) dot.remove();
    });
}

function deleteNotif(id, btn) {
    fetch(`${BASE}/${id}`, {
        method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        const row = btn.closest('div[style]');
        row.style.opacity = '0';
        row.style.transition = 'opacity 0.25s';
        setTimeout(() => row.remove(), 260);
    });
}
</script>
@endpush