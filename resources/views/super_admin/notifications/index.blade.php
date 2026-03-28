@extends('layouts.superadmin-app')
@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('topbar-actions')
    <div style="display:flex;gap:8px;">
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('super_admin.notifications.markAllRead') }}">
            @csrf
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                           border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:11px;font-weight:700;
                           letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                           font-family:'Barlow Condensed',sans-serif;transition:border-color 0.2s,color 0.2s;"
                    onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                    onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                Mark All Read
            </button>
        </form>
        @endif
        <form method="POST" action="{{ route('super_admin.notifications.clearRead') }}">
            @csrf
            <button type="submit"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;
                           border:1px solid rgba(140,14,3,0.2);background:transparent;
                           color:rgba(200,80,70,0.5);font-size:11px;font-weight:700;
                           letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;
                           font-family:'Barlow Condensed',sans-serif;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                    onmouseover="this.style.borderColor='rgba(140,14,3,0.5)';this.style.color='rgba(220,100,90,0.9)';this.style.background='rgba(140,14,3,0.06)'"
                    onmouseout="this.style.borderColor='rgba(140,14,3,0.2)';this.style.color='rgba(200,80,70,0.5)';this.style.background='transparent'">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="3,6 5,6 21,6"/>
                    <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                </svg>
                Clear Read
            </button>
        </form>
    </div>
@endsection

@section('content')

@php
$typeColors = [
    'registration' => ['border'=>'rgba(210,170,70,0.3)',  'bg'=>'rgba(160,120,40,0.08)',  'color'=>'rgba(210,170,70,0.85)'],
    'approval'     => ['border'=>'rgba(34,197,94,0.25)',  'bg'=>'rgba(34,197,94,0.06)',   'color'=>'rgba(74,222,128,0.85)'],
    'tenant'       => ['border'=>'rgba(80,150,220,0.3)',  'bg'=>'rgba(80,150,220,0.07)',  'color'=>'rgba(100,170,240,0.85)'],
    'status'       => ['border'=>'rgba(171,171,171,0.15)','bg'=>'rgba(171,171,171,0.04)', 'color'=>'rgba(171,171,171,0.6)'],
];
$icons = [
    'bell'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
    'check'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>',
    'x'      => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>',
    'plus'   => '<line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>',
    'toggle' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
];
@endphp

{{-- Stat strip --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:rgba(171,171,171,0.06);margin-bottom:1px;">
    @foreach([
        [$notifications->total(), 'Total',  '#fff'],
        [$unreadCount,            'Unread', 'rgba(210,170,70,0.9)'],
        [$notifications->total() - $unreadCount, 'Read', 'rgba(74,222,128,0.75)'],
    ] as [$val, $label, $color])
    <div style="background:#0E1126;padding:18px 22px;">
        <div style="font-family:'Playfair Display',serif;font-size:32px;font-weight:900;color:{{ $color }};line-height:1;">{{ $val }}</div>
        <div style="font-size:10px;color:rgba(171,171,171,0.3);letter-spacing:0.18em;text-transform:uppercase;font-family:monospace;margin-top:6px;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Main card --}}
<div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
        <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
        <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">All Notifications</span>
        <span style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;margin-left:4px;">
            // {{ $notifications->total() }} total
        </span>
    </div>

    @if($notifications->isEmpty())
        <div style="text-align:center;padding:80px 20px;">
            <div style="width:56px;height:56px;border:1px solid rgba(171,171,171,0.08);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <svg width="22" height="22" fill="none" stroke="rgba(171,171,171,0.25)" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">No notifications</div>
            <div style="font-size:13px;color:rgba(171,171,171,0.35);font-family:monospace;">// You're all caught up.</div>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:1px;">
            @foreach($notifications as $notif)
            @php
                $tc = $typeColors[$notif->type] ?? $typeColors['status'];
                $ic = $icons[$notif->icon]     ?? $icons['bell'];
            @endphp
            <div style="display:flex;align-items:flex-start;gap:14px;padding:16px;
                        background:{{ !$notif->is_read ? 'rgba(140,14,3,0.04)' : 'transparent' }};
                        border-left:2px solid {{ !$notif->is_read ? '#8C0E03' : 'transparent' }};
                        border-bottom:1px solid rgba(171,171,171,0.05);
                        transition:background 0.15s;"
                 onmouseover="this.style.background='rgba(171,171,171,0.02)'"
                 onmouseout="this.style.background='{{ !$notif->is_read ? 'rgba(140,14,3,0.04)' : 'transparent' }}'">

                {{-- Icon --}}
                <div style="width:32px;height:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;
                            border:1px solid {{ $tc['border'] }};background:{{ $tc['bg'] }};color:{{ $tc['color'] }};margin-top:2px;">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        {!! $ic !!}
                    </svg>
                </div>

                {{-- Content --}}
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                        <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:14px;
                                     color:{{ $notif->is_read ? 'rgba(171,171,171,0.6)' : '#fff' }};">
                            {{ $notif->title }}
                        </span>
                        <span style="display:inline-flex;padding:2px 7px;border:1px solid {{ $tc['border'] }};
                                     background:{{ $tc['bg'] }};font-size:9px;color:{{ $tc['color'] }};
                                     font-family:monospace;letter-spacing:0.1em;text-transform:uppercase;">
                            {{ $notif->type }}
                        </span>
                        @if(!$notif->is_read)
                            <span style="width:5px;height:5px;border-radius:50%;background:#8C0E03;flex-shrink:0;"></span>
                        @endif
                    </div>
                    <div style="font-size:13px;color:rgba(171,171,171,0.45);font-family:monospace;line-height:1.5;">
                        {{ $notif->message }}
                    </div>
                    <div style="font-size:10px;color:rgba(171,171,171,0.2);font-family:monospace;margin-top:5px;">
                        {{ $notif->created_at->format('M d, Y \a\t g:i A') }}
                        <span style="opacity:0.5;margin-left:6px;">· {{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div style="display:flex;gap:5px;flex-shrink:0;">
                    @if($notif->link)
                    <a href="{{ $notif->link }}"
                       onclick="markReadAndGo(event, {{ $notif->id }}, '{{ $notif->link }}')"
                       title="View"
                       style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;
                              border:1px solid rgba(171,171,171,0.1);background:transparent;color:rgba(171,171,171,0.35);
                              text-decoration:none;transition:border-color 0.2s,color 0.2s;"
                       onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.8)'"
                       onmouseout="this.style.borderColor='rgba(171,171,171,0.1)';this.style.color='rgba(171,171,171,0.35)'">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                    @endif
                    @if(!$notif->is_read)
                    <button onclick="markReadInline({{ $notif->id }}, this)"
                            title="Mark read"
                            style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;
                                   border:1px solid rgba(34,197,94,0.15);background:transparent;color:rgba(74,222,128,0.4);
                                   cursor:pointer;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                            onmouseover="this.style.borderColor='rgba(34,197,94,0.4)';this.style.color='rgba(74,222,128,0.85)';this.style.background='rgba(34,197,94,0.06)'"
                            onmouseout="this.style.borderColor='rgba(34,197,94,0.15)';this.style.color='rgba(74,222,128,0.4)';this.style.background='transparent'">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="20,6 9,17 4,12"/>
                        </svg>
                    </button>
                    @endif
                    <button onclick="deleteNotif({{ $notif->id }}, this)"
                            title="Delete"
                            style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;
                                   border:1px solid rgba(140,14,3,0.15);background:transparent;color:rgba(200,80,70,0.35);
                                   cursor:pointer;transition:border-color 0.2s,color 0.2s,background 0.2s;"
                            onmouseover="this.style.borderColor='rgba(140,14,3,0.45)';this.style.color='rgba(220,100,90,0.85)';this.style.background='rgba(140,14,3,0.06)'"
                            onmouseout="this.style.borderColor='rgba(140,14,3,0.15)';this.style.color='rgba(200,80,70,0.35)';this.style.background='transparent'">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="3,6 5,6 21,6"/>
                            <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($notifications->hasPages())
        <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(171,171,171,0.06);
                    display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;color:rgba(171,171,171,0.3);font-family:monospace;">
                // Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}
            </span>
            {{ $notifications->links() }}
        </div>
        @endif
    @endif
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const BASE  = '{{ url('super-admin/notifications') }}';

function markReadInline(id, btn) {
    fetch(`${BASE}/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        const row = btn.closest('div[style]');
        // Remove unread styling
        row.style.background  = 'transparent';
        row.style.borderLeft  = '2px solid transparent';
        btn.remove();
        // Remove red dot
        const dot = row.querySelector('span[style*="border-radius:50%"]');
        if (dot) dot.remove();
    });
}

function markReadAndGo(e, id, link) {
    e.preventDefault();
    fetch(`${BASE}/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => { window.location.href = link; });
}

function deleteNotif(id, btn) {
    fetch(`${BASE}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        const row = btn.closest('div[style]');
        row.style.opacity    = '0';
        row.style.transition = 'opacity 0.25s';
        setTimeout(() => row.remove(), 260);
    });
}
</script>
@endpush