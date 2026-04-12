{{--
    Tenant Sidebar Brand partial.
    Replaces the hardcoded .sidebar-brand block in every layout.

    Usage:  @include('layouts.partials.tenant_brand', ['dashboardRoute' => 'admin.dashboard'])

    The $dashboardRoute variable sets where the brand link points.
--}}
<a href="{{ route($dashboardRoute ?? 'admin.dashboard') }}" class="sidebar-brand">
    @if(!empty($tenantLogoUrl))
        <img src="{{ $tenantLogoUrl }}" alt="Logo"
             style="height:32px;width:32px;object-fit:contain;flex-shrink:0;
                    border:1px solid rgba(255,255,255,0.12);padding:2px;">
    @else
        <div class="brand-icon"><span>O</span></div>
    @endif

    <div class="brand-text" style="overflow:hidden;">
        @if(!empty($tenantBrandName))
            <span style="font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                         max-width:150px;display:block;letter-spacing:0.06em;line-height:1.2;">
                {{ $tenantBrandName }}
            </span>
        @else
            OJT<em>Connect</em>
        @endif
    </div>
</a>