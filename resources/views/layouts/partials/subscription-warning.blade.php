{{--
    resources/views/partials/subscription-warning.blade.php
    Include in layouts/app.blade.php inside content area:
        @if(session('subscription_warning'))
            @include('partials.subscription-warning', ['warning' => session('subscription_warning')])
        @endif
--}}
@php
    $expiredAt = $warning['expired_at'] ?? null;
    $graceEnd  = $warning['grace_end'] ?? null;
    $daysLeft  = $warning['days_left'] ?? 0;
    $urgent    = $daysLeft <= 2;
@endphp

<div style="border:1px solid {{ $urgent ? 'rgba(239,68,68,0.4)' : 'rgba(245,158,11,0.35)' }};
            background:{{ $urgent ? 'rgba(239,68,68,0.07)' : 'rgba(245,158,11,0.06)' }};
            padding:12px 20px;margin-bottom:16px;
            display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:10px;">
        <svg width="15" height="15" fill="none" stroke="{{ $urgent ? '#ef4444' : '#f59e0b' }}" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <span style="font-family:'Barlow Condensed',sans-serif;font-size:12px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;
                         color:{{ $urgent ? '#ef4444' : '#f59e0b' }};margin-right:8px;">
                {{ $urgent ? 'URGENT: ' : '' }}Grace Period Active
            </span>
            <span style="font-size:12px;color:var(--text2);">
                Subscription expired {{ $expiredAt instanceof \Carbon\Carbon ? $expiredAt->format('M d') : $expiredAt }}.
                Access ends {{ $graceEnd instanceof \Carbon\Carbon ? $graceEnd->format('M d, Y') : $graceEnd }}
                @if($daysLeft > 0)
                    ({{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }} remaining).
                @elseif($daysLeft === 0)
                    — last day today.
                @endif
            </span>
        </div>
    </div>
    <a href="{{ route('admin.plan.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;
              border:1px solid {{ $urgent ? 'rgba(239,68,68,0.5)' : 'rgba(245,158,11,0.4)' }};
              background:{{ $urgent ? 'rgba(239,68,68,0.1)' : 'rgba(245,158,11,0.1)' }};
              font-family:'Barlow Condensed',sans-serif;font-size:11px;font-weight:700;
              letter-spacing:0.1em;text-transform:uppercase;
              color:{{ $urgent ? '#ef4444' : '#f59e0b' }};text-decoration:none;white-space:nowrap;flex-shrink:0;">
        ↻ Renew Subscription
    </a>
</div>