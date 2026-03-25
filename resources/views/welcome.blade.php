<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJTConnect — OJT Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;0,9..144,700;0,9..144,800;1,9..144,300;1,9..144,400&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mint:  '#D4ECDD',
                        teal:  '#345B63',
                        deep:  '#152D35',
                        navy:  '#112031',
                    },
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        body:    ['DM Sans', 'sans-serif'],
                        mono:    ['DM Mono', 'monospace'],
                    },
                    keyframes: {
                        fadeUp:   { from: { opacity: '0', transform: 'translateY(24px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                        fadeDown: { from: { opacity: '0', transform: 'translateY(-12px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                        pulse2:   { '0%,100%': { opacity: '1', transform: 'scale(1)' }, '50%': { opacity: '0.35', transform: 'scale(0.7)' } },
                        drift1:   { to: { transform: 'translate(-60px, 70px)' } },
                        drift2:   { to: { transform: 'translate(70px, -50px)' } },
                        shimmer:  { '0%': { backgroundPosition: '-200% center' }, '100%': { backgroundPosition: '200% center' } },
                    },
                    animation: {
                        fadeUp:   'fadeUp 0.6s ease both',
                        fadeDown: 'fadeDown 0.5s ease both',
                        pulse2:   'pulse2 2.4s ease-in-out infinite',
                        drift1:   'drift1 16s ease-in-out infinite alternate',
                        drift2:   'drift2 20s ease-in-out infinite alternate',
                        shimmer:  'shimmer 4s linear infinite',
                    },
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        html, body { font-family: 'DM Sans', sans-serif; background: #112031; color: #D4ECDD; overflow-x: hidden; }

        body::after {
            content:''; position:fixed; inset:0;
            background:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.72' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            pointer-events:none; z-index:998; opacity:0.4;
        }

        .grid-bg {
            position:fixed; inset:0; pointer-events:none; z-index:0;
            background-image:
                linear-gradient(rgba(212,236,221,0.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212,236,221,0.025) 1px, transparent 1px);
            background-size: 80px 80px;
        }
        .grid-bg::after {
            content:''; position:absolute; inset:0;
            background: radial-gradient(ellipse 80% 55% at 50% 0%, rgba(52,91,99,0.5) 0%, transparent 70%);
        }

        .orb { position:fixed; border-radius:50%; filter:blur(130px); pointer-events:none; z-index:0; }
        .orb-1 { width:700px; height:700px; background:rgba(52,91,99,0.3); top:-280px; right:-180px; animation: drift1 16s ease-in-out infinite alternate; }
        .orb-2 { width:600px; height:600px; background:rgba(21,45,53,0.7); bottom:-200px; left:-180px; animation: drift2 20s ease-in-out infinite alternate; }
        .orb-3 { width:350px; height:350px; background:rgba(212,236,221,0.03); top:45%; left:42%; animation: drift1 24s ease-in-out infinite alternate; }

        @keyframes drift1 { to { transform:translate(-60px,70px); } }
        @keyframes drift2 { to { transform:translate(70px,-50px); } }
        @keyframes shimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
        @keyframes fadeDown { from{opacity:0;transform:translateY(-12px)} to{opacity:1;transform:translateY(0)} }
        @keyframes pulse2 { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.35;transform:scale(.7)} }

        .anim-nav { animation: fadeDown 0.5s ease both; }
        .anim-1 { animation: fadeUp 0.6s 0.1s ease both; }
        .anim-2 { animation: fadeUp 0.6s 0.2s ease both; }
        .anim-3 { animation: fadeUp 0.6s 0.3s ease both; }
        .anim-4 { animation: fadeUp 0.6s 0.4s ease both; }
        .anim-5 { animation: fadeUp 0.6s 0.5s ease both; }
        .anim-6 { animation: fadeUp 0.6s 0.6s ease both; }

        .shimmer-text {
            background: linear-gradient(90deg, #D4ECDD 0%, #fff 40%, #D4ECDD 60%, #8BB5A0 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 4s linear infinite;
        }

        .card-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card-hover::before {
            content:''; position:absolute; top:0; left:0; right:0; height:1.5px;
            background: linear-gradient(90deg, transparent, rgba(212,236,221,0.25), transparent);
            opacity:0; transition: opacity 0.3s;
        }
        .card-hover:hover { border-color: rgba(212,236,221,0.2) !important; transform: translateY(-5px); box-shadow: 0 20px 60px rgba(17,32,49,0.7); }
        .card-hover:hover::before { opacity:1; }

        .form-input {
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: rgba(212,236,221,0.35);
            background: rgba(17,32,49,0.7);
            box-shadow: 0 0 0 3px rgba(212,236,221,0.06);
        }

        select option { background: #152D35; color: #D4ECDD; }

        .plan-card { transition: all 0.3s ease; }
        .plan-card:hover { transform: translateY(-6px); }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>

<!-- ═══════════ NAV ═══════════ -->
<nav class="anim-nav fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-6 md:px-12 py-4 border-b border-white/[0.06] bg-[#112031]/75 backdrop-blur-xl">
    <div class="flex items-center gap-3">
        <a href="/" class="flex items-center gap-2.5 no-underline">
            <div class="w-9 h-9 rounded-xl bg-[#345B63] flex items-center justify-center font-['Fraunces'] font-bold text-base text-[#D4ECDD] shadow-lg">O</div>
            <span class="font-['Fraunces'] font-semibold text-lg text-[#D4ECDD] tracking-tight">OJT<span class="opacity-50">Connect</span></span>
        </a>
        <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full border border-[#D4ECDD]/15 bg-[#D4ECDD]/5 font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/50 uppercase">BukSU</span>
    </div>
    <div class="flex items-center gap-3">
        <a href="#register" class="hidden sm:inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-[#D4ECDD]/15 bg-[#D4ECDD]/5 text-[#D4ECDD]/70 text-sm font-medium hover:border-[#D4ECDD]/30 hover:bg-[#D4ECDD]/10 transition-all duration-200">
            Register Institution
        </a>
        <a href="/login" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-[#345B63] text-[#D4ECDD] font-semibold text-sm hover:bg-[#345B63]/80 transition-all duration-200 shadow-lg">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            Log in
        </a>
    </div>
</nav>

<!-- ═══════════ HERO ═══════════ -->
<section class="relative z-10 min-h-screen flex flex-col items-center justify-center text-center px-6 pt-32 pb-24">

    <div class="anim-1 inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-[#D4ECDD]/15 bg-[#D4ECDD]/5 font-['DM_Mono'] text-[11px] tracking-[0.18em] text-[#D4ECDD]/60 uppercase mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-[#D4ECDD] inline-block" style="animation: pulse2 2.4s ease-in-out infinite;"></span>
        Bukidnon State University · OJT Management System
    </div>

    <h1 class="anim-2 font-['Fraunces'] font-bold leading-[1.0] tracking-tight mb-6 max-w-4xl" style="font-size: clamp(50px, 8vw, 94px);">
        <span class="text-[#D4ECDD]/25 italic font-light">The smarter way</span><br>
        to manage <span class="shimmer-text">OJT</span>
    </h1>

    <p class="anim-3 text-lg font-light italic text-[#D4ECDD]/45 max-w-md leading-relaxed mb-10">
        From application to completion — OJTConnect handles everything digitally so you can focus on what matters.
    </p>

    <div class="anim-4 flex flex-col sm:flex-row items-center gap-3 mb-5">
        <a href="/login" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-xl bg-[#345B63] text-[#D4ECDD] font-semibold text-base hover:bg-[#345B63]/75 transition-all duration-300 hover:-translate-y-1 shadow-xl shadow-[#345B63]/30 group">
            Access the System
            <svg class="transition-transform group-hover:translate-x-1" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
        </a>
        <a href="#register" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-xl border border-[#D4ECDD]/15 bg-[#D4ECDD]/5 text-[#D4ECDD]/70 font-semibold text-base hover:border-[#D4ECDD]/30 hover:bg-[#D4ECDD]/10 transition-all duration-300 hover:-translate-y-1">
            Register Your Institution
        </a>
    </div>
    <p class="anim-5 text-xs text-[#D4ECDD]/25 font-['DM_Mono']">Contact your coordinator if you don't have an account yet.</p>

    <div class="anim-6 mt-14 flex flex-wrap justify-center border border-[#D4ECDD]/[0.08] rounded-xl overflow-hidden bg-[#D4ECDD]/[0.02]">
        @foreach(['College of Technology', 'College of Business', 'College of Education'] as $college)
        <div class="flex items-center gap-2 px-5 py-2.5 text-xs text-[#D4ECDD]/45 font-medium border-r border-[#D4ECDD]/[0.08] last:border-r-0">
            <span class="w-1.5 h-1.5 rounded-full bg-[#345B63] flex-shrink-0"></span>
            {{ $college }}
        </div>
        @endforeach
    </div>
</section>

<!-- ═══════════ STATS ═══════════ -->
<div class="relative z-10 grid grid-cols-2 md:grid-cols-4 border-y border-[#D4ECDD]/[0.07] bg-[#D4ECDD]/[0.015]">
    @foreach([['4','User Roles'],['3','Colleges'],['100%','Paperless'],['∞','Students Supported']] as [$num,$label])
    <div class="py-8 text-center border-r border-[#D4ECDD]/[0.07] last:border-r-0">
        <div class="font-['Fraunces'] font-bold text-4xl text-[#D4ECDD] tracking-tight leading-none mb-2">{{ $num }}</div>
        <div class="text-xs text-[#D4ECDD]/35 font-['DM_Mono'] tracking-widest uppercase">{{ $label }}</div>
    </div>
    @endforeach
</div>

<!-- ═══════════ FEATURES ═══════════ -->
<section class="relative z-10 px-6 md:px-12 py-24 max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
        <span class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/30 uppercase">What We Offer</span>
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
    </div>
    <h2 class="font-['Fraunces'] font-bold text-center text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-14">Everything OJT needs,<br><span class="text-[#D4ECDD]/35 font-light italic">in one place</span></h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @php
        $features = [
            ['icon' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/>', 'title' => 'OJT Application', 'desc' => 'Students apply to partner companies, upload documents, and track status in real time.', 'ibg' => 'rgba(212,236,221,0.1)', 'iclr' => '#D4ECDD'],
            ['icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>', 'title' => 'Hours Monitoring', 'desc' => 'Log daily time in/out, track approved hours, and visualize progress against required hours.', 'ibg' => 'rgba(52,91,99,0.4)', 'iclr' => '#9BCCCC'],
            ['icon' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>', 'title' => 'Weekly Reports', 'desc' => 'Submit weekly accomplishment reports with file attachments for coordinator review and feedback.', 'ibg' => 'rgba(212,236,221,0.07)', 'iclr' => '#D4ECDD'],
            ['icon' => '<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>', 'title' => 'Student Evaluation', 'desc' => 'Supervisors submit attendance and performance ratings with an overall grade and recommendation.', 'ibg' => 'rgba(52,91,99,0.4)', 'iclr' => '#9BCCCC'],
            ['icon' => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/>', 'title' => 'Export Reports', 'desc' => 'Generate PDF and Excel reports for student records, accreditation, and administrative use.', 'ibg' => 'rgba(212,236,221,0.1)', 'iclr' => '#D4ECDD'],
            ['icon' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>', 'title' => 'Role-based Access', 'desc' => 'Dedicated dashboards for admins, coordinators, supervisors, and students with proper permissions.', 'ibg' => 'rgba(52,91,99,0.4)', 'iclr' => '#9BCCCC'],
        ];
        @endphp
        @foreach($features as $f)
        <div class="card-hover p-6 rounded-2xl border border-[#D4ECDD]/[0.08] bg-[#152D35]/60">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" style="background:{{ $f['ibg'] }}">
                <svg width="20" height="20" fill="none" stroke="{{ $f['iclr'] }}" stroke-width="1.8" viewBox="0 0 24 24">{!! $f['icon'] !!}</svg>
            </div>
            <div class="font-['Fraunces'] font-semibold text-[#D4ECDD] text-[15px] mb-2">{{ $f['title'] }}</div>
            <div class="text-[#D4ECDD]/40 text-[13px] leading-relaxed">{{ $f['desc'] }}</div>
        </div>
        @endforeach
    </div>
</section>

<!-- ═══════════ ROLES ═══════════ -->
<section class="relative z-10 px-6 md:px-12 pb-24 max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
        <span class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/30 uppercase">User Roles</span>
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
    </div>
    <h2 class="font-['Fraunces'] font-bold text-center text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-14">Built for <span class="text-[#D4ECDD]/35 font-light italic">every stakeholder</span></h2>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $roles = [
            ['name'=>'Admin','desc'=>'Creates users and companies, monitors the full system, and exports reports','stroke'=>'#D4ECDD','bg'=>'rgba(212,236,221,0.08)','icon'=>'<circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/>'],
            ['name'=>'OJT Coordinator','desc'=>'Approves applications, monitors hours and reports, and marks OJT completion','stroke'=>'#9BCCCC','bg'=>'rgba(52,91,99,0.4)','icon'=>'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/>'],
            ['name'=>'Company Supervisor','desc'=>'Validates attendance, monitors intern performance, and submits evaluations','stroke'=>'#D4ECDD','bg'=>'rgba(212,236,221,0.06)','icon'=>'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>'],
            ['name'=>'Student Intern','desc'=>'Applies for OJT, logs daily hours, submits weekly reports, and views evaluation','stroke'=>'#9BCCCC','bg'=>'rgba(52,91,99,0.4)','icon'=>'<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>'],
        ];
        @endphp
        @foreach($roles as $r)
        <div class="card-hover p-6 rounded-2xl border border-[#D4ECDD]/[0.08] bg-[#152D35]/60 text-center">
            <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:{{ $r['bg'] }}">
                <svg width="24" height="24" fill="none" stroke="{{ $r['stroke'] }}" stroke-width="1.7" viewBox="0 0 24 24">{!! $r['icon'] !!}</svg>
            </div>
            <div class="font-['Fraunces'] font-semibold text-[#D4ECDD] text-[13.5px] mb-2">{{ $r['name'] }}</div>
            <div class="text-[#D4ECDD]/40 text-[12px] leading-relaxed">{{ $r['desc'] }}</div>
        </div>
        @endforeach
    </div>
</section>

<!-- ═══════════ FLOW ═══════════ -->
<section class="relative z-10 px-6 md:px-12 pb-24 max-w-6xl mx-auto">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
        <span class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/30 uppercase">How It Works</span>
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
    </div>
    <h2 class="font-['Fraunces'] font-bold text-center text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-14">End-to-end <span class="text-[#D4ECDD]/35 font-light italic">OJT process</span></h2>

    <div class="flex flex-wrap justify-center gap-2">
        @php
        $steps = [
            ['n'=>'1','label'=>'Admin creates student account','role'=>'Admin','bg'=>'rgba(212,236,221,0.1)','clr'=>'#D4ECDD'],
            ['n'=>'2','label'=>'Student applies for OJT','role'=>'Student','bg'=>'rgba(52,91,99,0.35)','clr'=>'#9BCCCC'],
            ['n'=>'3','label'=>'Coordinator approves application','role'=>'Coordinator','bg'=>'rgba(212,236,221,0.08)','clr'=>'#D4ECDD'],
            ['n'=>'4','label'=>'Student logs hours + reports','role'=>'Student','bg'=>'rgba(52,91,99,0.35)','clr'=>'#9BCCCC'],
            ['n'=>'5','label'=>'Coordinator monitors progress','role'=>'Coordinator','bg'=>'rgba(212,236,221,0.08)','clr'=>'#D4ECDD'],
            ['n'=>'6','label'=>'Supervisor evaluates student','role'=>'Supervisor','bg'=>'rgba(52,91,99,0.35)','clr'=>'#9BCCCC'],
            ['n'=>'7','label'=>'Coordinator marks complete','role'=>'Coordinator','bg'=>'rgba(212,236,221,0.1)','clr'=>'#D4ECDD'],
        ];
        @endphp
        @foreach($steps as $i => $step)
        <div class="flex items-center gap-2">
            <div class="flex flex-col items-center text-center w-28 px-2 py-4 rounded-xl border border-[#D4ECDD]/[0.07] bg-[#152D35]/50">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-['Fraunces'] font-bold text-sm mb-2 flex-shrink-0" style="background:{{ $step['bg'] }}; color:{{ $step['clr'] }}">{{ $step['n'] }}</div>
                <div class="text-[11px] text-[#D4ECDD]/55 leading-tight mb-1">{{ $step['label'] }}</div>
                <div class="font-['DM_Mono'] text-[10px] opacity-60" style="color:{{ $step['clr'] }}">{{ $step['role'] }}</div>
            </div>
            @if($i < count($steps) - 1)
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" class="flex-shrink-0 opacity-15"><line x1="5" y1="12" x2="19" y2="12" stroke="#D4ECDD" stroke-width="2.5"/><polyline points="12,5 19,12 12,19" stroke="#D4ECDD" stroke-width="2.5"/></svg>
            @endif
        </div>
        @endforeach
    </div>
</section>

<!-- ═══════════ PRICING ═══════════ -->
<section class="relative z-10 px-6 md:px-12 pb-24 max-w-6xl mx-auto" id="pricing">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
        <span class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/30 uppercase">Pricing</span>
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
    </div>
    <h2 class="font-['Fraunces'] font-bold text-center text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-3">Simple, transparent <span class="text-[#D4ECDD]/35 font-light italic">pricing</span></h2>
    <p class="text-center text-[#D4ECDD]/40 text-base mb-14 font-light">Choose the plan that fits your institution</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Basic -->
        <div class="plan-card relative p-7 rounded-2xl border border-[#D4ECDD]/[0.09] bg-[#152D35]/70">
            <div class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/35 uppercase mb-4">Basic Plan</div>
            <div class="font-['Fraunces'] font-bold text-5xl text-[#D4ECDD] tracking-tight leading-none mb-1">₱10k</div>
            <div class="text-[#D4ECDD]/30 text-sm font-['DM_Mono'] mb-7">per year</div>
            <ul class="space-y-3 mb-8">
                @foreach(['Student registration','Internship application','OJT hour monitoring','Basic reports'] as $item)
                <li class="flex items-center gap-2.5 text-sm text-[#D4ECDD]/55">
                    <svg class="flex-shrink-0" width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4" stroke="#345B63" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="#register" class="block w-full text-center py-3 rounded-xl border border-[#D4ECDD]/15 text-[#D4ECDD]/65 text-sm font-semibold hover:border-[#D4ECDD]/30 hover:bg-[#D4ECDD]/5 transition-all duration-200">Get started</a>
        </div>

        <!-- Standard (featured) -->
        <div class="plan-card relative p-7 rounded-2xl border border-[#345B63]/60" style="background: linear-gradient(145deg, rgba(52,91,99,0.55) 0%, rgba(21,45,53,0.9) 100%); box-shadow: 0 20px 60px rgba(52,91,99,0.15);">
            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full bg-[#345B63] text-[#D4ECDD] text-[11px] font-['DM_Mono'] tracking-widest uppercase font-semibold whitespace-nowrap">Most Popular</div>
            <div class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#9BCCCC]/70 uppercase mb-4">Standard Plan</div>
            <div class="font-['Fraunces'] font-bold text-5xl text-[#D4ECDD] tracking-tight leading-none mb-1">₱20k</div>
            <div class="text-[#D4ECDD]/30 text-sm font-['DM_Mono'] mb-7">per year</div>
            <ul class="space-y-3 mb-8">
                @foreach(['Everything in Basic','Online report submission','Student evaluation system','Internship progress monitoring'] as $item)
                <li class="flex items-center gap-2.5 text-sm text-[#D4ECDD]/80">
                    <svg class="flex-shrink-0" width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4" stroke="#9BCCCC" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="#register" class="block w-full text-center py-3 rounded-xl bg-[#345B63] text-[#D4ECDD] text-sm font-semibold hover:bg-[#345B63]/75 transition-all duration-200 shadow-lg">Get started</a>
        </div>

        <!-- Premium -->
        <div class="plan-card relative p-7 rounded-2xl border border-[#D4ECDD]/[0.09] bg-[#152D35]/70">
            <div class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/35 uppercase mb-4">Premium Plan</div>
            <div class="font-['Fraunces'] font-bold text-5xl text-[#D4ECDD] tracking-tight leading-none mb-1">₱30k</div>
            <div class="text-[#D4ECDD]/30 text-sm font-['DM_Mono'] mb-7">per year</div>
            <ul class="space-y-3 mb-8">
                @foreach(['Everything in Standard','Unlimited student records','Advanced reports & analytics','Automated PDF generation'] as $item)
                <li class="flex items-center gap-2.5 text-sm text-[#D4ECDD]/55">
                    <svg class="flex-shrink-0" width="15" height="15" fill="none" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4" stroke="#345B63" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    {{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="#register" class="block w-full text-center py-3 rounded-xl border border-[#D4ECDD]/15 text-[#D4ECDD]/65 text-sm font-semibold hover:border-[#D4ECDD]/30 hover:bg-[#D4ECDD]/5 transition-all duration-200">Get started</a>
        </div>
    </div>
</section>

<!-- ═══════════ TENANT SIGNUP ═══════════ -->
<section class="relative z-10 px-6 md:px-12 pb-28 max-w-4xl mx-auto" id="register">
    <div class="flex items-center gap-4 mb-12">
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
        <span class="font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#D4ECDD]/30 uppercase">Register</span>
        <div class="h-px flex-1 bg-[#D4ECDD]/[0.07]"></div>
    </div>
    <h2 class="font-['Fraunces'] font-bold text-center text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-3">Register your <span class="text-[#D4ECDD]/35 font-light italic">institution</span></h2>
    <p class="text-center text-[#D4ECDD]/40 mb-12 font-light text-base">Submit your details and our team will set up your OJTConnect workspace within 24 hours.</p>

    <div class="relative p-8 md:p-10 rounded-3xl border border-[#D4ECDD]/[0.09] overflow-hidden" style="background: linear-gradient(145deg, rgba(21,45,53,0.92) 0%, rgba(17,32,49,0.97) 100%);">
        <!-- decorative glow -->
        <div class="absolute top-0 right-0 w-72 h-72 rounded-full pointer-events-none opacity-10" style="background: radial-gradient(circle, #345B63, transparent 70%); transform: translate(35%, -35%);"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full pointer-events-none opacity-5" style="background: radial-gradient(circle, #D4ECDD, transparent 70%); transform: translate(-30%, 30%);"></div>

        @if(session('success'))
        <div class="mb-6 p-4 rounded-xl border border-[#D4ECDD]/20 bg-[#D4ECDD]/10 flex items-start gap-3">
            <svg class="flex-shrink-0 mt-0.5" width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4" stroke="#D4ECDD" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <div>
                <div class="font-semibold text-[#D4ECDD] text-sm mb-0.5">Registration Submitted!</div>
                <div class="text-[#D4ECDD]/60 text-sm">{{ session('success') }}</div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 p-4 rounded-xl border border-red-400/20 bg-red-400/5">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-sm text-red-300/80">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('tenant.register.submit') }}" method="POST" class="relative">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

                <!-- Institution Name -->
                <div class="md:col-span-2">
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-2">Institution / Company Name <span class="text-red-400/60">*</span></label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                        placeholder="e.g. Bukidnon State University – College of Technology"
                        class="form-input w-full px-4 py-3 rounded-xl border border-[#D4ECDD]/[0.11] bg-[#112031]/60 text-[#D4ECDD] placeholder-[#D4ECDD]/20 text-sm"
                        required>
                </div>

                <!-- Contact Person -->
                <div>
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-2">Contact Person <span class="text-red-400/60">*</span></label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                        placeholder="Full name"
                        class="form-input w-full px-4 py-3 rounded-xl border border-[#D4ECDD]/[0.11] bg-[#112031]/60 text-[#D4ECDD] placeholder-[#D4ECDD]/20 text-sm"
                        required>
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-2">Email Address <span class="text-red-400/60">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="admin@institution.edu.ph"
                        class="form-input w-full px-4 py-3 rounded-xl border border-[#D4ECDD]/[0.11] bg-[#112031]/60 text-[#D4ECDD] placeholder-[#D4ECDD]/20 text-sm"
                        required>
                </div>

                <!-- Subdomain -->
                <div>
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-2">Preferred Subdomain <span class="text-red-400/60">*</span></label>
                    <div class="flex items-center rounded-xl border border-[#D4ECDD]/[0.11] bg-[#112031]/60 overflow-hidden focus-within:border-[#D4ECDD]/30 transition-all duration-200">
                        <input type="text" name="subdomain" value="{{ old('subdomain') }}"
                            placeholder="yourschool"
                            class="flex-1 px-4 py-3 bg-transparent text-[#D4ECDD] placeholder-[#D4ECDD]/20 text-sm focus:outline-none"
                            required>
                        <span class="px-3 py-3 text-[#D4ECDD]/25 text-xs font-['DM_Mono'] border-l border-[#D4ECDD]/[0.08] bg-[#112031]/40 whitespace-nowrap">.ojtconnect.edu</span>
                    </div>
                    <p class="mt-1.5 text-[11px] text-[#D4ECDD]/20 font-['DM_Mono']">Letters, numbers, hyphens. Min 3 characters.</p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                        placeholder="+63 9XX XXX XXXX"
                        class="form-input w-full px-4 py-3 rounded-xl border border-[#D4ECDD]/[0.11] bg-[#112031]/60 text-[#D4ECDD] placeholder-[#D4ECDD]/20 text-sm">
                </div>

                <!-- Plan selection -->
                <div class="md:col-span-2">
                    <label class="block font-['DM_Mono'] text-[11px] tracking-[0.16em] text-[#D4ECDD]/35 uppercase mb-3">Select Plan <span class="text-red-400/60">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" id="plan-cards">
                        @php
                        $plans = [
                            ['val'=>'basic','name'=>'Basic','price'=>'₱10,000 / yr','perks'=>'Student reg, applications, hour monitoring, basic reports'],
                            ['val'=>'standard','name'=>'Standard','price'=>'₱20,000 / yr','perks'=>'Everything in Basic + online reports, evaluation, progress monitoring'],
                            ['val'=>'premium','name'=>'Premium','price'=>'₱30,000 / yr','perks'=>'Everything in Standard + unlimited records, advanced analytics, PDF export'],
                        ];
                        @endphp
                        @foreach($plans as $p)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="plan" value="{{ $p['val'] }}" class="sr-only plan-radio"
                                {{ old('plan', 'standard') === $p['val'] ? 'checked' : '' }}>
                            <div class="plan-option p-4 rounded-xl border transition-all duration-200 cursor-pointer
                                {{ old('plan', 'standard') === $p['val']
                                    ? 'border-[#345B63]/70 bg-[#345B63]/15'
                                    : 'border-[#D4ECDD]/[0.09] bg-[#112031]/50 hover:border-[#D4ECDD]/20' }}">
                                <div class="font-semibold text-[#D4ECDD] text-sm mb-0.5">{{ $p['name'] }}</div>
                                <div class="font-['DM_Mono'] text-[#9BCCCC]/70 text-xs mb-2">{{ $p['price'] }}</div>
                                <div class="text-[#D4ECDD]/30 text-[11px] leading-relaxed">{{ $p['perks'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-5 border-t border-[#D4ECDD]/[0.07]">
                <button type="submit" class="w-full flex items-center justify-center gap-2.5 py-4 rounded-xl bg-[#345B63] text-[#D4ECDD] font-semibold text-base hover:bg-[#345B63]/75 transition-all duration-300 hover:-translate-y-0.5 shadow-xl shadow-[#345B63]/20 group">
                    Submit Registration Request
                    <svg class="transition-transform group-hover:translate-x-1" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/></svg>
                </button>
                <p class="text-center text-[#D4ECDD]/20 text-xs font-['DM_Mono'] mt-4">We'll review your request and notify you within 24 hours.</p>
            </div>
        </form>
    </div>
</section>

<!-- ═══════════ CTA ═══════════ -->
<div class="relative z-10 px-6 md:px-12 pb-24 max-w-6xl mx-auto">
    <div class="relative p-16 rounded-3xl border border-[#345B63]/25 overflow-hidden text-center" style="background: linear-gradient(135deg, rgba(52,91,99,0.2) 0%, rgba(21,45,53,0.85) 100%);">
        <div class="absolute inset-0 pointer-events-none opacity-25" style="background: radial-gradient(ellipse at 50% 0%, rgba(52,91,99,0.9) 0%, transparent 65%);"></div>
        <div class="relative">
            <h2 class="font-['Fraunces'] font-bold text-4xl md:text-5xl text-[#D4ECDD] tracking-tight mb-4">Ready to get started?</h2>
            <p class="text-[#D4ECDD]/40 text-lg font-light mb-10">Log in or register your institution to begin your OJT journey.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-xl bg-[#345B63] text-[#D4ECDD] font-semibold text-base hover:bg-[#345B63]/75 transition-all duration-300 hover:-translate-y-1 shadow-xl group">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
                    Log in to OJTConnect
                </a>
                <a href="#register" class="inline-flex items-center gap-2.5 px-8 py-3.5 rounded-xl border border-[#D4ECDD]/15 text-[#D4ECDD]/65 font-semibold text-base hover:border-[#D4ECDD]/30 hover:bg-[#D4ECDD]/5 transition-all duration-300 hover:-translate-y-1">
                    Register Institution
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ FOOTER ═══════════ -->
<footer class="relative z-10 border-t border-[#D4ECDD]/[0.07] px-6 md:px-12 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 flex-wrap">
    <div class="flex items-center gap-2.5">
        <div class="w-6 h-6 rounded-md bg-[#345B63] flex items-center justify-center font-['Fraunces'] font-bold text-xs text-[#D4ECDD]">O</div>
        <span class="text-sm text-[#D4ECDD]/40">
            <strong class="text-[#D4ECDD]/65 font-['Fraunces'] font-semibold">OJTConnect</strong>
            · OJT Management System · Bukidnon State University
        </span>
    </div>
    <div class="flex gap-5 text-xs text-[#D4ECDD]/25 font-['DM_Mono']">
        <span>College of Technology</span>
        <span class="opacity-40">·</span>
        <span>College of Business</span>
        <span class="opacity-40">·</span>
        <span>College of Education</span>
    </div>
</footer>

<script>
    // Plan card interactive selection
    document.querySelectorAll('.plan-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-option').forEach(card => {
                card.classList.remove('border-[#345B63]/70', 'bg-[#345B63]/15');
                card.classList.add('border-[#D4ECDD]/[0.09]', 'bg-[#112031]/50');
            });
            if (this.checked) {
                const option = this.closest('label').querySelector('.plan-option');
                option.classList.remove('border-[#D4ECDD]/[0.09]', 'bg-[#112031]/50');
                option.classList.add('border-[#345B63]/70', 'bg-[#345B63]/15');
            }
        });
    });
</script>

</body>
</html>