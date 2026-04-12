{{--
    Tenant Announcement Banner partial.
    Place inside <main class="main-content"> at the top, before @yield('content').

    Usage:  @include('layouts.partials.tenant_announcement')
--}}
@if(!empty($tenantAnnouncementActive) && !empty($tenantAnnouncement))
@php
    $hex = $tenantBrandColor ?? '8C0E03';
    $rgb = implode(',', array_map('hexdec', str_split(ltrim($hex,'#'), 2)));
@endphp
<div id="ojt-announcement"
     style="background:rgba({{ $rgb }},0.08);
            border:1px solid rgba({{ $rgb }},0.25);
            border-left:3px solid #{{ $hex }};
            padding:10px 16px;margin-bottom:20px;
            display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <div style="display:flex;align-items:center;gap:8px;">
        <svg width="14" height="14" fill="none" stroke="#{{ $hex }}" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path d="M22 17H2a3 3 0 000 6h20a3 3 0 000-6z"/>
            <path d="M21 10H3V3l9 4 9-4v7z"/>
        </svg>
        <span style="font-size:13px;color:var(--text);">{{ $tenantAnnouncement }}</span>
    </div>
    <button onclick="this.parentElement.style.display='none'"
            style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:18px;line-height:1;flex-shrink:0;padding:0 2px;"
            aria-label="Dismiss announcement">✕</button>
</div>
@endif