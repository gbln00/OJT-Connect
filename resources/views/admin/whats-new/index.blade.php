@extends('layouts.app')
@section('title', "What's New")
@section('page-title', "What's New")
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Playfair Display',serif;">OJTConnect Updates</h2>
        <p style="color:var(--muted);font-size:13px;">
            {{ $unreadCount > 0 ? $unreadCount . ' unread update(s)' : 'All caught up!' }}
        </p>
    </div>
</div>

@forelse($versions as $v)
@php
    $isRead = $v->isReadByTenant($tenantId, $userEmail);
@endphp
<div @class(['card', 'card-unread' => !$isRead]) style="margin-bottom:16px;"
     id="version-{{ $v->id }}">
    <div class="card-header">
        <div>
            <span style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;">
                v{{ $v->version }}
            </span>
            <span class="status-pill {{ $v->typeColor() }}" style="margin-left:8px;">
                {{ ucfirst($v->type) }}
            </span>
            @if(!$isRead)
            <span class="status-pill gold" style="margin-left:6px;">New</span>
            @endif
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="font-size:12px;color:var(--muted);">
                {{ $v->published_at->format('M d, Y') }}
            </span>
            @if(!$isRead)
            <button data-version-id="{{ $v->id }}"
                    class="btn btn-ghost btn-sm mark-read-btn" id="mark-btn-{{ $v->id }}">
                ✓ Mark as Read
            </button>
            @endif
        </div>
    </div>
    <div style="padding:20px;">
        @if($v->label)
        <p style="font-weight:600;margin-bottom:12px;">{{ $v->label }}</p>
        @endif
        <div style="font-size:13px;color:var(--text2);line-height:1.8;
                    white-space:pre-wrap;">{{ $v->changelog }}</div>
    </div>
</div>
@empty
<div style="text-align:center;padding:80px;color:var(--muted);">
    No updates yet. Check back soon.
</div>
@endforelse

{{ $versions->links() }}

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// Event delegation for mark-read buttons
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('mark-read-btn')) {
        const id = e.target.dataset.versionId;
        markRead(id);
    }
});

function markRead(id) {
    fetch(`/admin/whats-new/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => {
        const btn = document.getElementById(`mark-btn-${id}`);
        const card = document.getElementById(`version-${id}`);
        if (btn) btn.remove();
        if (card) card.style.borderLeft = '';
    });
}
</script>
@endpush

@endsection