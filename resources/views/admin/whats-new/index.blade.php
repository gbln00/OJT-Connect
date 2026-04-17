@extends('layouts.app')
@section('title', "What's New")
@section('page-title', "What's New")

@section('content')

{{-- ── Page Header ── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:28px;">
    <div>
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:6px;">
            OJTConnect · Release Notes
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:24px;font-weight:900;
                   color:var(--text);margin-bottom:6px;">
            What's New
        </h2>
        <div style="display:flex;align-items:center;gap:12px;">
            @php $currentVer = \App\Models\SystemVersion::current(); @endphp
            @if($currentVer)
            <span style="display:inline-flex;align-items:center;gap:5px;
                         font-family:'DM Mono',monospace;font-size:11px;color:var(--muted2);">
                <span style="width:5px;height:5px;border-radius:50%;background:var(--teal-color);
                             flex-shrink:0;"></span>
                Current: v{{ $currentVer->version }}
            </span>
            <span style="color:var(--border2);">·</span>
            @endif
            <span style="font-size:13px;color:var(--muted);">
                @if($unreadCount > 0)
                    <span style="color:var(--crimson);font-weight:600;">{{ $unreadCount }}</span>
                    unread update{{ $unreadCount > 1 ? 's' : '' }}
                @else
                    <span style="color:var(--teal-color);">✓ All caught up</span>
                @endif
            </span>
        </div>
    </div>

    {{-- Mark all as read --}}
    @if($unreadCount > 0)
    <button id="mark-all-btn" onclick="markAllRead()"
            class="btn btn-ghost btn-sm"
            style="border-color:var(--border2);">
        ✓ Mark all as read
    </button>
    @endif
</div>

{{-- ── Unread Banner ── --}}
@if($unreadCount > 0)
<div style="background:rgba(140,14,3,0.04);border:1px solid rgba(140,14,3,0.15);
            padding:11px 16px;margin-bottom:20px;
            display:flex;align-items:center;gap:10px;">
    <svg width="14" height="14" fill="none" stroke="var(--crimson)" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M5 3v4M3 5h4M6 17v4M4 19h4M13 3l1.5 4.5L19 9l-4.5 1.5L13 15l-1.5-4.5L7 9l4.5-1.5L13 3z"/>
    </svg>
    <span style="font-size:13px;color:var(--text);">
        You have <strong style="color:var(--crimson);">{{ $unreadCount }}</strong>
        unread update{{ $unreadCount > 1 ? 's' : '' }}.
        Review the changes below to stay up to date with OJTConnect.
    </span>
</div>
@endif

{{-- ── Version Cards ── --}}
<div style="display:flex;flex-direction:column;gap:14px;">
@forelse($versions as $v)
@php $isRead = $v->isReadByTenant($tenantId, $userEmail); @endphp

<div @class(['version-card', 'version-unread' => !$isRead])
     id="version-{{ $v->id }}"
     style="background:var(--surface);border:1px solid {{ !$isRead ? 'rgba(140,14,3,0.2)' : 'var(--border)' }};
            border-left:3px solid {{ !$isRead ? 'var(--crimson)' : 'transparent' }};
            overflow:hidden;transition:border-color 0.3s,border-left-color 0.3s;">

    {{-- Card Header --}}
    <div style="padding:16px 20px 14px;border-bottom:1px solid var(--border);
                display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">

            {{-- Version + icon --}}
            <div style="display:flex;align-items:center;gap:6px;">
                <span style="font-size:16px;line-height:1;">{{ $v->typeIcon() }}</span>
                <span style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;
                             color:var(--text);">
                    v{{ $v->version }}
                </span>
            </div>

            {{-- Type pill --}}
            <span class="status-pill {{ $v->typeColor() }}">
                {{ ucfirst($v->type) }}
            </span>

            {{-- Unread badge --}}
            @if(!$isRead)
            <span class="status-pill gold" id="new-pill-{{ $v->id }}">New</span>
            @endif

            {{-- Current version badge --}}
            @if($v->is_current ?? false)
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;
                         background:rgba(45,212,191,0.08);border:1px solid rgba(45,212,191,0.25);
                         font-family:'DM Mono',monospace;font-size:9px;
                         letter-spacing:0.08em;text-transform:uppercase;color:var(--teal-color);">
                <span style="width:4px;height:4px;border-radius:50%;background:var(--teal-color);"></span>
                Current
            </span>
            @endif
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                {{ $v->published_at->format('M d, Y') }}
            </span>

            @if(!$isRead)
            <button data-version-id="{{ $v->id }}"
                    data-mark-url="{{ route('admin.whats-new.markRead', $v) }}"
                    class="btn btn-ghost btn-sm mark-read-btn"
                    id="mark-btn-{{ $v->id }}">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                Mark as read
            </button>
            @else
            <span style="display:inline-flex;align-items:center;gap:4px;
                         font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);
                         letter-spacing:0.05em;">
                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
                Read
            </span>
            @endif
        </div>
    </div>

    {{-- Card Body --}}
    <div style="padding:18px 20px;">
        @if($v->label)
        <p style="font-size:14px;font-weight:600;color:var(--text);margin-bottom:12px;">
            {{ $v->label }}
        </p>
        @endif

        {{-- Changelog - render section headings and bullets nicely --}}
        <div class="changelog-body" style="font-size:13px;color:var(--text2);line-height:1.9;">
            @foreach(explode("\n", $v->changelog) as $line)
                @php $line = rtrim($line); @endphp
                @if(str_starts_with($line, '## '))
                    <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.18em;
                                text-transform:uppercase;color:var(--muted);
                                margin-top:14px;margin-bottom:6px;padding-bottom:4px;
                                border-bottom:1px solid var(--border);">
                        {{ substr($line, 3) }}
                    </div>
                @elseif(str_starts_with($line, '- ') || str_starts_with($line, '* '))
                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:3px;">
                        <span style="width:4px;height:4px;border-radius:50%;
                                     background:var(--muted);flex-shrink:0;margin-top:8px;"></span>
                        <span>{{ substr($line, 2) }}</span>
                    </div>
                @elseif(trim($line) !== '')
                    <div style="margin-bottom:4px;">{{ $line }}</div>
                @endif
            @endforeach
        </div>
    </div>
</div>

@empty
<div style="text-align:center;padding:80px 20px;color:var(--muted);">
    <div style="font-family:'DM Mono',monospace;font-size:11px;letter-spacing:0.1em;
                margin-bottom:8px;">// No updates yet</div>
    <p style="font-size:13px;">Check back soon for the latest OJTConnect updates.</p>
</div>
@endforelse
</div>

{{-- Pagination --}}
@if($versions->hasPages())
<div style="margin-top:20px;">{{ $versions->links() }}</div>
@endif

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Single mark read
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.mark-read-btn');
    if (btn) markRead(btn.dataset.versionId, btn.dataset.markUrl);
});

function markRead(id, url) {
    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(r => {
        if (!r.ok) return;
        applyReadState(id);
        updateUnreadUI();
    });
}

function applyReadState(id) {
    const card = document.getElementById(`version-${id}`);
    const btn  = document.getElementById(`mark-btn-${id}`);
    const pill = document.getElementById(`new-pill-${id}`);

    if (pill) pill.remove();
    if (btn)  btn.remove();

    if (card) {
        card.style.borderLeftColor  = 'transparent';
        card.style.borderColor      = 'var(--border)';

        // Replace button area with "Read" checkmark
        const headerRight = card.querySelector('[data-read-slot]');
        if (headerRight) {
            headerRight.innerHTML = `
                <span style="display:inline-flex;align-items:center;gap:4px;
                             font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                    Read
                </span>`;
        }
    }
}

// Mark all as read
function markAllRead() {
    const btns = document.querySelectorAll('.mark-read-btn');
    const promises = Array.from(btns).map(btn =>
        fetch(btn.dataset.markUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF }
        }).then(() => applyReadState(btn.dataset.versionId))
    );

    Promise.all(promises).then(() => {
        updateUnreadUI();
        document.getElementById('mark-all-btn')?.remove();
        document.querySelector('[data-unread-banner]')?.remove();
    });
}

function updateUnreadUI() {
    const remaining = document.querySelectorAll('.mark-read-btn').length;
    const countEl   = document.querySelector('[data-unread-count]');
    if (countEl) {
        if (remaining === 0) {
            countEl.innerHTML = `<span style="color:var(--teal-color);">✓ All caught up</span>`;
            document.getElementById('mark-all-btn')?.remove();
            document.querySelector('[data-unread-banner]')?.remove();
        } else {
            countEl.innerHTML = `<span style="color:var(--crimson);font-weight:600;">${remaining}</span> unread update${remaining > 1 ? 's' : ''}`;
        }
    }
}
</script>
@endpush

@endsection