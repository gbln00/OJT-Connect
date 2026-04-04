{{--
    Plan-required upgrade wall — shown when a tenant hits a route
    blocked by CheckTenantPlan middleware (HTTP 403).
    Extends the tenant app layout so it inherits all CSS variables,
    sidebar, and topbar of the authenticated user's shell.
--}}
@php
    // Detect which layout to use based on the authenticated user's role
    $role   = auth()->user()?->role ?? 'student_intern';
    $layout = match($role) {
        'admin'              => 'layouts.admin-app',
        'ojt_coordinator'    => 'layouts.coordinator-app',
        'company_supervisor' => 'layouts.supervisor-app',
        default              => 'layouts.student-app',
    };
@endphp

@extends($layout)

@section('title', 'Upgrade Required')
@section('page-title', 'Upgrade Required')

@section('content')

<div style="
    min-height: 60vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
">

    <div style="width: 100%; max-width: 520px;">

        {{-- Lock icon --}}
        <div style="
            width: 56px; height: 56px;
            border: 1px solid rgba(140,14,3,0.3);
            background: rgba(140,14,3,0.07);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        ">
            <svg width="22" height="22" fill="none" stroke="var(--crimson)"
                 stroke-width="1.8" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="1"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
        </div>

        {{-- Eyebrow --}}
        <div style="
            text-align: center;
            font-family: 'DM Mono', monospace;
            font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase;
            color: var(--muted); margin-bottom: 12px;
        ">// 403 — Feature restricted</div>

        {{-- Heading --}}
        <div style="
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 4vw, 30px); font-weight: 900;
            color: var(--text); text-align: center;
            line-height: 1.15; margin-bottom: 14px;
        ">
            {{ $feature ?? 'This feature' }}<br>
            <span style="color: var(--crimson); font-style: italic;">
                requires {{ $requiredLabel ?? $required }} plan
            </span>
        </div>

        {{-- Description --}}
        <p style="
            font-size: 14px; color: var(--text2); text-align: center;
            line-height: 1.75; margin-bottom: 28px;
        ">
            Your institution is currently on the
            <span style="
                display: inline-flex; align-items: center;
                padding: 1px 8px;
                border: 1px solid var(--border2);
                font-family: 'Barlow Condensed', sans-serif;
                font-size: 12px; font-weight: 600;
                letter-spacing: 0.08em; text-transform: uppercase;
                color: var(--muted);
            ">{{ $currentLabel ?? $current }}</span>
            plan. To access {{ strtolower($feature ?? 'this feature') }},
            please ask your administrator to upgrade to the
            <strong style="color: var(--text);">{{ $requiredLabel ?? $required }}</strong> plan
            or higher.
        </p>

        {{-- Plan comparison strip --}}
        <div style="
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 1px; background: var(--border);
            border: 1px solid var(--border);
            margin-bottom: 28px;
        ">
            @php
                $plans = [
                    ['basic',    'Basic',    '₱10k',  false],
                    ['standard', 'Standard', '₱20k',  false],
                    ['premium',  'Premium',  '₱30k',  false],
                ];
                $planColors = [
                    'basic'    => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)'],
                    'standard' => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
                    'premium'  => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
                ];
                $requiredName = strtolower($required ?? 'standard');
                $currentName  = strtolower($current  ?? 'basic');
                $planLevels   = ['basic'=>1,'standard'=>2,'premium'=>3];
            @endphp

            @foreach($plans as [$key, $name, $price, $_])
            @php
                $pc          = $planColors[$key];
                $isRequired  = $key === $requiredName;
                $isCurrent   = $key === $currentName;
                $isAvailable = ($planLevels[$key] ?? 0) >= ($planLevels[$requiredName] ?? 0);
            @endphp
            <div style="
                background: var(--surface2);
                padding: 14px 10px; text-align: center;
                {{ $isRequired ? 'border: 1.5px solid '.$pc['border'].'; background: '.$pc['bg'].';' : '' }}
                position: relative;
            ">
                @if($isRequired)
                <div style="
                    position: absolute; top: -1px; left: 0; right: 0; height: 2px;
                    background: {{ $pc['color'] }};
                "></div>
                @endif

                <div style="
                    font-family: 'Barlow Condensed', sans-serif;
                    font-size: 10px; font-weight: 700;
                    letter-spacing: 0.15em; text-transform: uppercase;
                    color: {{ $isRequired ? $pc['color'] : 'var(--muted)' }};
                    margin-bottom: 4px;
                ">{{ $name }}</div>

                <div style="
                    font-family: 'Playfair Display', serif;
                    font-weight: 900; font-size: 1.2rem;
                    color: var(--text); line-height: 1;
                    margin-bottom: 6px;
                ">{{ $price }}</div>

                @if($isCurrent)
                <div style="
                    display: inline-flex; align-items: center; gap: 4px;
                    padding: 2px 7px;
                    border: 1px solid var(--border2);
                    font-family: 'DM Mono', monospace;
                    font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
                    color: var(--muted);
                ">Current</div>
                @elseif($isRequired)
                <div style="
                    display: inline-flex; align-items: center; gap: 4px;
                    padding: 2px 7px;
                    border: 1px solid {{ $pc['border'] }};
                    background: {{ $pc['bg'] }};
                    font-family: 'DM Mono', monospace;
                    font-size: 9px; letter-spacing: 0.1em; text-transform: uppercase;
                    color: {{ $pc['color'] }};
                ">Required</div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Actions --}}
        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">

            {{-- Go back button --}}
            <a href="javascript:history.back()" style="
                display: inline-flex; align-items: center; gap: 6px;
                padding: 9px 20px;
                border: 1px solid var(--border2);
                background: transparent; color: var(--text2);
                font-family: 'Barlow Condensed', sans-serif;
                font-size: 12px; font-weight: 600;
                letter-spacing: 0.1em; text-transform: uppercase;
                text-decoration: none;
                transition: all 0.15s;
            "
            onmouseover="this.style.background='var(--surface2)';this.style.color='var(--text)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--text2)'">
                <svg width="12" height="12" fill="none" stroke="currentColor"
                     stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Go back
            </a>

            {{-- Contact admin note --}}
            <p style="
                font-family: 'DM Mono', monospace;
                font-size: 10px; letter-spacing: 0.06em; text-transform: uppercase;
                color: var(--muted); margin-top: 4px;
            ">
                // Contact your system administrator to upgrade
            </p>

        </div>

    </div>
</div>

@endsection