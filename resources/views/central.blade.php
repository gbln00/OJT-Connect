<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OJTConnect — OJT Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400;1,700&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        crimson: '#8C0E03',
                        void:    '#0D0D0D',
                        night:   '#0E1126',
                        steel:   '#333740',
                        ash:     '#ABABAB',
                    },
                    fontFamily: {
                        display:   ['Playfair Display', 'serif'],
                        body:      ['Barlow', 'sans-serif'],
                        condensed: ['Barlow Condensed', 'sans-serif'],
                        mono:      ['DM Mono', 'monospace'],
                    },
                    keyframes: {
                        fadeUp:   { from: { opacity: '0', transform: 'translateY(20px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                        fadeDown: { from: { opacity: '0', transform: 'translateY(-10px)' }, to: { opacity: '1', transform: 'translateY(0)' } },
                        drift1:   { '0%': { transform: 'translate(0,0)' }, '100%': { transform: 'translate(-40px,55px)' } },
                        drift2:   { '0%': { transform: 'translate(0,0)' }, '100%': { transform: 'translate(50px,-35px)' } },
                        flicker:  { '0%,100%': { opacity: '1' }, '92%': { opacity: '1' }, '93%': { opacity: '0.4' }, '94%': { opacity: '1' }, '96%': { opacity: '0.6' }, '97%': { opacity: '1' } },
                        scandown: { '0%': { transform: 'translateY(-100%)', opacity: '0' }, '5%': { opacity: '1' }, '95%': { opacity: '1' }, '100%': { transform: 'translateY(100vh)', opacity: '0' } },
                    },
                    animation: {
                        fadeUp:   'fadeUp 0.65s cubic-bezier(.22,.61,.36,1) both',
                        fadeDown: 'fadeDown 0.45s ease both',
                        drift1:   'drift1 20s ease-in-out infinite alternate',
                        drift2:   'drift2 25s ease-in-out infinite alternate',
                        flicker:  'flicker 8s ease-in-out infinite',
                        scandown: 'scandown 12s linear infinite',
                    },
                }
            }
        }
    </script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --crimson: #8C0E03;
            --void:    #0D0D0D;
            --night:   #0E1126;
            --steel:   #333740;
            --ash:     #ABABAB;
        }

        html, body {
            font-family: 'Barlow', sans-serif;
            background: var(--void);
            color: var(--ash);
            overflow-x: hidden;
        }

        /* Grain */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.055'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 999; opacity: 0.5;
        }

        /* Scan line */
        .scanline {
            position: fixed; left: 0; right: 0; height: 200px;
            background: linear-gradient(to bottom, transparent, rgba(171,171,171,0.012), transparent);
            pointer-events: none; z-index: 1;
            animation: scandown 12s linear infinite;
        }

        /* Grid */
        .grid-bg {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0;
            background-image:
                linear-gradient(rgba(171,171,171,0.028) 1px, transparent 1px),
                linear-gradient(90deg, rgba(171,171,171,0.028) 1px, transparent 1px);
            background-size: 60px 60px;
        }
        .grid-bg::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 45% at 50% 0%, rgba(140,14,3,0.12) 0%, transparent 65%);
        }

        /* Orbs */
        .orb { position: fixed; border-radius: 50%; filter: blur(120px); pointer-events: none; z-index: 0; }
        .orb-a { width: 700px; height: 700px; background: rgba(14,17,38,0.9); top: -300px; right: -200px; animation: drift1 20s ease-in-out infinite alternate; }
        .orb-b { width: 500px; height: 500px; background: rgba(13,13,13,0.95); bottom: -180px; left: -160px; animation: drift2 25s ease-in-out infinite alternate; }
        .orb-c { width: 400px; height: 400px; background: rgba(140,14,3,0.06); top: 35%; left: 40%; animation: drift1 30s ease-in-out infinite alternate; }

        /* Section rule */
        .section-rule { display: flex; align-items: center; gap: 16px; margin-bottom: 52px; }
        .section-rule::before { content: ''; width: 32px; height: 2px; background: var(--crimson); flex-shrink: 0; }
        .section-rule::after  { content: ''; flex: 1; height: 1px; background: rgba(171,171,171,0.08); }
        .section-rule span {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.22em; text-transform: uppercase;
            color: rgba(171,171,171,0.35);
        }

        /* Card lift */
        .card-lift {
            transition: transform 0.3s cubic-bezier(.22,.61,.36,1), border-color 0.25s;
            position: relative; overflow: hidden;
        }
        .card-lift::before {
            content: ''; position: absolute;
            top: 0; left: 0; width: 2px; bottom: 0;
            background: var(--crimson);
            transform: scaleY(0); transform-origin: bottom;
            transition: transform 0.3s cubic-bezier(.22,.61,.36,1);
        }
        .card-lift:hover { transform: translateY(-4px); border-color: rgba(171,171,171,0.15) !important; }
        .card-lift:hover::before { transform: scaleY(1); }

        /* Form inputs */
        .form-input { font-family: 'Barlow', sans-serif; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s; }
        .form-input:focus {
            outline: none;
            border-color: rgba(140,14,3,0.7) !important;
            background: rgba(14,17,38,0.8) !important;
            box-shadow: 0 0 0 3px rgba(140,14,3,0.1);
        }
        input::placeholder { color: rgba(171,171,171,0.22); }
        select option { background: #0E1126; color: #ABABAB; }

        /* Buttons */
        .btn-crimson {
            background: var(--crimson); color: rgba(255,255,255,0.92);
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }
        .btn-crimson:hover { background: #a81004; transform: translateY(-2px); box-shadow: 0 8px 32px rgba(140,14,3,0.35); }
        .btn-crimson:active { transform: scale(0.98); }

        .btn-ghost {
            border: 1px solid rgba(171,171,171,0.15); color: rgba(171,171,171,0.6);
            transition: border-color 0.2s, color 0.2s, background 0.2s, transform 0.15s;
        }
        .btn-ghost:hover { border-color: rgba(171,171,171,0.3); color: rgba(171,171,171,0.9); background: rgba(171,171,171,0.05); transform: translateY(-2px); }

        /* Plan selected */
        .plan-option.is-selected { border-color: rgba(140,14,3,0.6) !important; background: rgba(140,14,3,0.08) !important; }
        .plan-option.is-selected .plan-name { color: #c9372b !important; }

        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
    </style>
</head>
<body>

<div class="grid-bg"></div>
<div class="scanline"></div>
<div class="orb orb-a"></div>
<div class="orb orb-b"></div>
<div class="orb orb-c"></div>

{{-- ══ NAV ══ --}}
<nav style="animation: fadeDown 0.45s ease both;"
     class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between
            px-6 md:px-14 py-3.5
            border-b border-white/[0.05] bg-[#0D0D0D]/80 backdrop-blur-xl">

    <a href="/" class="flex items-center gap-3 no-underline group">
        <div class="w-8 h-8 flex items-center justify-center border border-[#8C0E03]/60 relative">
            <span class="font-['Playfair_Display'] font-black text-sm text-[#8C0E03] leading-none">O</span>
            <span class="absolute -top-px -right-px w-1.5 h-1.5 bg-[#8C0E03]"></span>
        </div>
        <span class="font-['Barlow_Condensed'] font-bold text-[17px] text-white tracking-[0.12em] uppercase">OJT<span class="text-[#ABABAB]/45">Connect</span></span>
        <span class="hidden md:flex items-center gap-1.5 px-2 py-0.5 border border-[#ABABAB]/10 bg-[#ABABAB]/[0.04]
                     font-['DM_Mono'] text-[10px] tracking-[0.15em] text-[#ABABAB]/35 uppercase">
            <span class="w-1 h-1 bg-[#8C0E03] inline-block"></span>BukSU
        </span>
    </a>

    <div class="flex items-center gap-2.5">
        <a href="{{ route('tenant.register') }}"
           class="hidden sm:inline-flex items-center gap-2 px-4 py-2 btn-ghost
                  font-['Barlow_Condensed'] font-semibold text-sm tracking-wider uppercase">
            Register Institution
        </a>
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 px-5 py-2 btn-crimson
                  font-['Barlow_Condensed'] font-semibold text-sm tracking-wider uppercase">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/>
            </svg>
            Super Admin Log In
        </a>
    </div>
</nav>

{{-- ══ HERO ══ --}}
<section class="relative z-10 min-h-screen flex flex-col items-center justify-center text-center px-6 pt-36 pb-24">

    <div style="animation: fadeUp 0.6s 0.05s cubic-bezier(.22,.61,.36,1) both;"
         class="inline-flex items-center gap-3 px-4 py-2 mb-10
                border border-[#ABABAB]/10 bg-[#ABABAB]/[0.03]
                font-['DM_Mono'] text-[10px] tracking-[0.18em] text-[#ABABAB]/40 uppercase">
        <span class="w-1.5 h-1.5 bg-[#8C0E03] flex-shrink-0 animate-flicker"></span>
        Bukidnon State University · OJT Management System
        <span class="hidden sm:inline text-[#8C0E03]/50">// v2.0</span>
    </div>

    <h1 style="animation: fadeUp 0.7s 0.15s cubic-bezier(.22,.61,.36,1) both;
               font-size: clamp(42px, 7vw, 88px);"
        class="font-['Playfair_Display'] font-black leading-[0.98] tracking-tight mb-8 max-w-5xl">
        <span class="block text-[#ABABAB]/20 font-normal italic" style="font-size: 0.6em;">The authoritative system for</span>
        <span class="block text-white">On-the-Job</span>
        <span class="block text-white">Training <span class="text-[#8C0E03] animate-flicker">Management</span></span>
    </h1>

    <p style="animation: fadeUp 0.7s 0.28s cubic-bezier(.22,.61,.36,1) both;"
       class="text-[16px] font-light text-[#ABABAB]/50 max-w-[44ch] leading-relaxed mb-12 italic">
        From application to completion — OJTConnect handles every stage of the internship cycle so coordinators can focus on students, not paperwork.
    </p>

    {{-- CTA BUTTONS --}}
    <div style="animation: fadeUp 0.7s 0.40s cubic-bezier(.22,.61,.36,1) both;"
         class="flex flex-col sm:flex-row items-center gap-3 mb-6">

        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2.5 px-9 py-3.5 btn-crimson
                  font-['Barlow_Condensed'] font-bold text-[15px] tracking-[0.1em] uppercase group">
            Access the System
            <svg class="transition-transform group-hover:translate-x-1.5" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
            </svg>
        </a>

        {{-- NEW: Dedicated register page button --}}
        <a href="{{ route('tenant.register') }}"
           class="inline-flex items-center gap-2.5 px-9 py-3.5 btn-ghost
                  font-['Barlow_Condensed'] font-bold text-[15px] tracking-[0.1em] uppercase group">
            <svg class="opacity-60 group-hover:opacity-100 transition-opacity" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M12 10v6M9 13h6"/>
            </svg>
            Register Your Institution
        </a>
    </div>

    <p style="animation: fadeUp 0.7s 0.52s cubic-bezier(.22,.61,.36,1) both;"
       class="font-['DM_Mono'] text-[11px] text-[#ABABAB]/22 tracking-wider">
        Contact your OJT coordinator if you don't have an account yet.
    </p>

    <div style="animation: fadeUp 0.7s 0.62s cubic-bezier(.22,.61,.36,1) both;"
         class="mt-14 inline-flex flex-wrap justify-center border border-[#ABABAB]/[0.06] bg-[#ABABAB]/[0.015]">
        @foreach(['College of Technology', 'College of Business', 'College of Education'] as $college)
        <div class="flex items-center gap-2 px-5 py-2.5 text-[12px] text-[#ABABAB]/35
                    border-r border-[#ABABAB]/[0.06] last:border-r-0
                    hover:bg-[#ABABAB]/[0.03] transition-colors cursor-default
                    font-['Barlow_Condensed'] tracking-wider uppercase font-medium">
            <span class="w-1 h-1 bg-[#8C0E03] flex-shrink-0"></span>
            {{ $college }}
        </div>
        @endforeach
    </div>

    <div style="animation: fadeUp 0.6s 1.1s ease both;"
         class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-20">
        <div class="w-px h-10 bg-gradient-to-b from-[#ABABAB] to-transparent"></div>
        <span class="font-['DM_Mono'] text-[9px] tracking-[0.25em] uppercase text-[#ABABAB]">scroll</span>
    </div>
</section>

{{-- ══ STATS ══ --}}
<div class="relative z-10 border-y border-[#ABABAB]/[0.06] bg-[#ABABAB]/[0.01]">
    <div class="grid grid-cols-2 md:grid-cols-4 max-w-6xl mx-auto">
        @foreach([['4','User Roles'],['3','Colleges'],['100%','Paperless'],['∞','Students Supported']] as [$num,$label])
        <div class="py-10 text-center border-r border-[#ABABAB]/[0.06] last:border-r-0
                    hover:bg-[#ABABAB]/[0.02] transition-colors group cursor-default">
            <div class="font-['Playfair_Display'] font-black text-[40px] text-white tracking-tight leading-none mb-2
                        group-hover:text-[#8C0E03] transition-colors duration-300">{{ $num }}</div>
            <div class="font-['Barlow_Condensed'] text-[11px] text-[#ABABAB]/30 tracking-[0.2em] uppercase font-semibold">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══ FEATURES ══ --}}
<section class="relative z-10 px-6 md:px-14 py-28 max-w-6xl mx-auto">
    <div class="section-rule"><span>What We Offer</span></div>
    <h2 class="font-['Playfair_Display'] font-black text-[clamp(28px,4.5vw,48px)] text-white tracking-tight mb-16 leading-tight max-w-2xl">
        Everything OJT needs,<br><em class="font-normal text-[#ABABAB]/30">in one place.</em>
    </h2>
    @php
    $features = [
        ['svg'=>'<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>','title'=>'OJT Application','tag'=>'Student','desc'=>'Students apply to partner companies, upload required documents, and track their application status in real time.'],
        ['svg'=>'<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>','title'=>'Hours Monitoring','tag'=>'Tracking','desc'=>'Log daily time in/out, track approved hours, and visualize progress against the required completion hours.'],
        ['svg'=>'<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>','title'=>'Weekly Reports','tag'=>'Reporting','desc'=>'Submit weekly accomplishment reports with file attachments for coordinator review and approval.'],
        ['svg'=>'<path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>','title'=>'Student Evaluation','tag'=>'Supervisor','desc'=>'Supervisors submit attendance and performance ratings with an overall grade and formal recommendation.'],
        ['svg'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="3" x2="9" y2="21"/>','title'=>'Export Reports','tag'=>'Admin','desc'=>'Generate PDF and Excel reports for student records, accreditation, and administrative documentation.'],
        ['svg'=>'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>','title'=>'Role-based Access','tag'=>'Security','desc'=>'Dedicated dashboards for admins, coordinators, supervisors, and students with scoped permissions.'],
    ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-[#ABABAB]/[0.06]">
        @foreach($features as $f)
        <div class="card-lift p-7 bg-[#0D0D0D] hover:bg-[#0E1126]/60 transition-colors">
            <div class="flex items-start justify-between mb-6">
                <div class="w-10 h-10 border border-[#ABABAB]/10 flex items-center justify-center flex-shrink-0">
                    <svg width="17" height="17" fill="none" stroke="#ABABAB" stroke-width="1.6" viewBox="0 0 24 24">{!! $f['svg'] !!}</svg>
                </div>
                <span class="font-['Barlow_Condensed'] text-[10px] tracking-[0.18em] uppercase text-[#ABABAB]/28 font-semibold border border-[#ABABAB]/08 px-2 py-0.5">{{ $f['tag'] }}</span>
            </div>
            <div class="font-['Playfair_Display'] font-bold text-white text-[15px] mb-2.5 leading-snug">{{ $f['title'] }}</div>
            <div class="text-[#ABABAB]/42 text-[13px] leading-relaxed font-light">{{ $f['desc'] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ ROLES ══ --}}
<section class="relative z-10 px-6 md:px-14 pb-28 max-w-6xl mx-auto">
    <div class="section-rule"><span>User Roles</span></div>
    <h2 class="font-['Playfair_Display'] font-black text-[clamp(28px,4.5vw,48px)] text-white tracking-tight mb-16 leading-tight max-w-2xl">
        Built for <em class="font-normal text-[#ABABAB]/30">every stakeholder.</em>
    </h2>
    @php
    $roles = [
        ['num'=>'01','name'=>'Admin','desc'=>'Creates user accounts and companies, monitors the full system, and exports institutional reports.','svg'=>'<circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 10-16 0"/>'],
        ['num'=>'02','name'=>'OJT Coordinator','desc'=>'Approves applications, monitors hours and weekly reports, and marks student OJT completion.','svg'=>'<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/>'],
        ['num'=>'03','name'=>'Company Supervisor','desc'=>'Validates student attendance, monitors intern performance, and submits evaluations at completion.','svg'=>'<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>'],
        ['num'=>'04','name'=>'Student Intern','desc'=>'Applies for OJT, logs daily hours, submits weekly reports, and views supervisor evaluations.','svg'=>'<path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>'],
    ];
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px bg-[#ABABAB]/[0.06]">
        @foreach($roles as $r)
        <div class="card-lift p-7 bg-[#0D0D0D] hover:bg-[#0E1126]/60 transition-colors group">
            <div class="flex items-center justify-between mb-7">
                <div class="w-11 h-11 border border-[#ABABAB]/10 flex items-center justify-center group-hover:border-[#8C0E03]/40 transition-colors">
                    <svg width="19" height="19" fill="none" stroke="#ABABAB" stroke-width="1.6" viewBox="0 0 24 24">{!! $r['svg'] !!}</svg>
                </div>
                <span class="font-['Playfair_Display'] font-black text-[32px] text-[#ABABAB]/06 select-none">{{ $r['num'] }}</span>
            </div>
            <div class="font-['Playfair_Display'] font-bold text-white text-[15px] mb-2.5 leading-snug">{{ $r['name'] }}</div>
            <div class="text-[#ABABAB]/40 text-[13px] leading-relaxed font-light">{{ $r['desc'] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ══ FLOW ══ --}}
<section class="relative z-10 px-6 md:px-14 pb-28 max-w-6xl mx-auto">
    <div class="section-rule"><span>How It Works</span></div>
    <h2 class="font-['Playfair_Display'] font-black text-[clamp(28px,4.5vw,48px)] text-white tracking-tight mb-16 leading-tight">
        End-to-end <em class="font-normal text-[#ABABAB]/30">OJT process.</em>
    </h2>
    @php
    $steps = [
        ['n'=>'1','label'=>'Admin creates student account','role'=>'Admin'],
        ['n'=>'2','label'=>'Student applies for OJT','role'=>'Student'],
        ['n'=>'3','label'=>'Coordinator approves application','role'=>'Coordinator'],
        ['n'=>'4','label'=>'Student logs hours + reports','role'=>'Student'],
        ['n'=>'5','label'=>'Coordinator monitors progress','role'=>'Coordinator'],
        ['n'=>'6','label'=>'Supervisor evaluates student','role'=>'Supervisor'],
        ['n'=>'7','label'=>'Coordinator marks complete','role'=>'Coordinator'],
    ];
    @endphp
    <div class="flex flex-wrap items-center gap-2">
        @foreach($steps as $i => $step)
        <div class="flex items-center gap-2">
            <div class="group flex flex-col items-center text-center w-[108px] px-3 py-5
                        border border-[#ABABAB]/[0.07] bg-[#0D0D0D]
                        hover:border-[#8C0E03]/30 hover:bg-[#0E1126]/50 transition-all cursor-default">
                <div class="w-8 h-8 border border-[#ABABAB]/12 flex items-center justify-center mb-3 flex-shrink-0 group-hover:border-[#8C0E03]/50 transition-colors">
                    <span class="font-['Playfair_Display'] font-bold text-[13px] text-[#ABABAB]/50 group-hover:text-[#8C0E03] transition-colors">{{ $step['n'] }}</span>
                </div>
                <div class="text-[11px] text-[#ABABAB]/40 leading-snug mb-1.5 font-light">{{ $step['label'] }}</div>
                <div class="font-['Barlow_Condensed'] text-[10px] text-[#ABABAB]/25 tracking-wider uppercase font-semibold">{{ $step['role'] }}</div>
            </div>
            @if($i < count($steps) - 1)
            <svg class="flex-shrink-0 opacity-12" width="12" height="12" viewBox="0 0 24 24" fill="none">
                <line x1="5" y1="12" x2="19" y2="12" stroke="#ABABAB" stroke-width="2"/>
                <polyline points="12,5 19,12 12,19" stroke="#ABABAB" stroke-width="2"/>
            </svg>
            @endif
        </div>
        @endforeach
    </div>
</section>

{{-- ══ PRICING ══ --}}
<section class="relative z-10 px-6 md:px-14 pb-28 max-w-6xl mx-auto" id="pricing">
    <div class="section-rule"><span>Pricing</span></div>
    <h2 class="font-['Playfair_Display'] font-black text-[clamp(28px,4.5vw,48px)] text-white tracking-tight mb-4 leading-tight">
        Simple, transparent <em class="font-normal text-[#ABABAB]/30">pricing.</em>
    </h2>
    <p class="text-[#ABABAB]/38 text-[15px] font-light mb-16">Choose the plan that fits your institution.</p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-[#ABABAB]/[0.06]">

        <div class="relative p-8 bg-[#0D0D0D] hover:bg-[#0E1126]/40 transition-colors">
            <div class="font-['Barlow_Condensed'] text-[10px] tracking-[0.22em] text-[#ABABAB]/30 uppercase font-semibold mb-5">Basic Plan</div>
            <div class="font-['Playfair_Display'] font-black text-[52px] text-white tracking-tight leading-none mb-1">₱10k</div>
            <div class="font-['DM_Mono'] text-[#ABABAB]/25 text-[12px] mb-9">per year</div>
            <ul class="space-y-3 mb-9">
                @foreach(['Student registration','Internship application','OJT hour monitoring','Basic reports'] as $item)
                <li class="flex items-center gap-2.5 text-[13px] text-[#ABABAB]/45 font-light">
                    <span class="w-1 h-1 bg-[#333740] flex-shrink-0"></span>{{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="{{ route('tenant.register') }}" class="block w-full text-center py-3 btn-ghost font-['Barlow_Condensed'] font-bold text-[13px] tracking-[0.12em] uppercase">Get started</a>
        </div>

        <div class="relative p-8 bg-[#0E1126] border-t-2 border-t-[#8C0E03] hover:bg-[#0E1126]/80 transition-colors"
             style="box-shadow: 0 -2px 40px rgba(140,14,3,0.1);">
            <div class="absolute -top-4 left-6 px-3 py-1 bg-[#8C0E03]
                        font-['Barlow_Condensed'] text-[10px] tracking-[0.2em] text-white uppercase font-bold">
                Most Popular
            </div>
            <div class="font-['Barlow_Condensed'] text-[10px] tracking-[0.22em] text-[#ABABAB]/45 uppercase font-semibold mb-5">Standard Plan</div>
            <div class="font-['Playfair_Display'] font-black text-[52px] text-white tracking-tight leading-none mb-1">₱20k</div>
            <div class="font-['DM_Mono'] text-[#ABABAB]/25 text-[12px] mb-9">per year</div>
            <ul class="space-y-3 mb-9">
                @foreach(['Everything in Basic','Online report submission','Student evaluation system','Internship progress monitoring'] as $item)
                <li class="flex items-center gap-2.5 text-[13px] text-[#ABABAB]/75 font-light">
                    <span class="w-1 h-1 bg-[#8C0E03] flex-shrink-0"></span>{{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="{{ route('tenant.register') }}" class="block w-full text-center py-3 btn-crimson font-['Barlow_Condensed'] font-bold text-[13px] tracking-[0.12em] uppercase">Get started</a>
        </div>

        <div class="relative p-8 bg-[#0D0D0D] hover:bg-[#0E1126]/40 transition-colors">
            <div class="font-['Barlow_Condensed'] text-[10px] tracking-[0.22em] text-[#ABABAB]/30 uppercase font-semibold mb-5">Premium Plan</div>
            <div class="font-['Playfair_Display'] font-black text-[52px] text-white tracking-tight leading-none mb-1">₱30k</div>
            <div class="font-['DM_Mono'] text-[#ABABAB]/25 text-[12px] mb-9">per year</div>
            <ul class="space-y-3 mb-9">
                @foreach(['Everything in Standard','Unlimited student records','Advanced reports & analytics','Automated PDF generation'] as $item)
                <li class="flex items-center gap-2.5 text-[13px] text-[#ABABAB]/45 font-light">
                    <span class="w-1 h-1 bg-[#333740] flex-shrink-0"></span>{{ $item }}
                </li>
                @endforeach
            </ul>
            <a href="{{ route('tenant.register') }}" class="block w-full text-center py-3 btn-ghost font-['Barlow_Condensed'] font-bold text-[13px] tracking-[0.12em] uppercase">Get started</a>
        </div>
    </div>

    {{-- ★ Register Institution CTA strip beneath pricing ★ --}}
    <div class="mt-6 p-6 border border-[#8C0E03]/22 bg-[#8C0E03]/[0.04]
                flex flex-col sm:flex-row items-center justify-between gap-4">
        <div>
            <p class="font-['Barlow_Condensed'] font-bold text-[15px] tracking-wider uppercase text-white/75 mb-1">
                Ready to onboard your institution?
            </p>
            <p class="text-[#ABABAB]/40 text-[13px] font-light">
                Skip the inline form — go straight to the dedicated registration page.
            </p>
        </div>
        <a href="{{ route('tenant.register') }}"
           class="flex-shrink-0 inline-flex items-center gap-2 px-7 py-3 btn-crimson
                  font-['Barlow_Condensed'] font-bold text-[13px] tracking-[0.12em] uppercase group whitespace-nowrap">
            Register Institution
            <svg class="transition-transform group-hover:translate-x-1" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
            </svg>
        </a>
    </div>
</section>


{{-- ══ FINAL CTA ══ --}}
<div class="relative z-10 px-6 md:px-14 pb-28 max-w-6xl mx-auto">
    <div class="relative border border-[#ABABAB]/[0.06] bg-[#0E1126]/50 p-16 md:p-20 text-center overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-[2px] bg-[#8C0E03]"></div>
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none overflow-hidden select-none">
            <span class="font-['Playfair_Display'] font-black text-[180px] text-white/[0.015] leading-none whitespace-nowrap">OJT</span>
        </div>
        <div class="relative">
            <h2 class="font-['Playfair_Display'] font-black text-[clamp(26px,4.5vw,48px)] text-white tracking-tight mb-5 leading-tight">
                Ready to get started?
            </h2>
            <p class="text-[#ABABAB]/38 text-[16px] font-light mb-12 max-w-[38ch] mx-auto">
                Log in to your existing account or register your institution to begin the OJT journey.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">   
                <a href="{{ route('tenant.register') }}"
                   class="inline-flex items-center gap-2.5 px-10 py-3.5 btn-ghost
                          font-['Barlow_Condensed'] font-bold text-[14px] tracking-[0.12em] uppercase group">
                    Register Institution
                    <svg class="opacity-60 transition-transform group-hover:translate-x-1" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12,5 19,12 12,19"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ══ FOOTER ══ --}}
<footer class="relative z-10 border-t border-[#ABABAB]/[0.06] px-6 md:px-14 py-7
               flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="w-6 h-6 border border-[#8C0E03]/50 flex items-center justify-center">
            <span class="font-['Playfair_Display'] font-black text-[11px] text-[#8C0E03]">O</span>
        </div>
        <span class="text-[13px] text-[#ABABAB]/35 font-light">
            <strong class="text-[#ABABAB]/55 font-['Barlow_Condensed'] font-bold tracking-wider uppercase text-[12px]">OJTConnect</strong>
            <span class="opacity-40"> · </span>OJT Management System<span class="opacity-40"> · </span>Bukidnon State University
        </span>
    </div>
    <div class="flex flex-wrap justify-center gap-x-5 gap-y-1 font-['DM_Mono'] text-[11px] text-[#ABABAB]/22">
        <span>College of Technology</span>
        <span class="opacity-30">·</span>
        <span>College of Business</span>
        <span class="opacity-30">·</span>
        <span>College of Education</span>
    </div>
</footer>

<script>
    document.querySelectorAll('.plan-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.plan-option').forEach(card => {
                card.classList.remove('border-[#8C0E03]/60', 'bg-[#8C0E03]/08', 'is-selected');
                card.classList.add('border-[#ABABAB]/[0.08]', 'bg-[#0D0D0D]/60');
            });
            if (this.checked) {
                const opt = this.closest('label').querySelector('.plan-option');
                opt.classList.remove('border-[#ABABAB]/[0.08]', 'bg-[#0D0D0D]/60');
                opt.classList.add('border-[#8C0E03]/60', 'bg-[#8C0E03]/08', 'is-selected');
            }
        });
    });

    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.addEventListener('mousemove', e => {
            const x = (e.clientX / window.innerWidth  - 0.5) * 20;
            const y = (e.clientY / window.innerHeight - 0.5) * 20;
            const a = document.querySelector('.orb-a');
            const c = document.querySelector('.orb-c');
            if (a) a.style.transform = `translate(${x * 0.6}px, ${y * 0.6}px)`;
            if (c) c.style.transform = `translate(${-x * 0.3}px, ${-y * 0.3}px)`;
        });
    }
</script>

</body>
</html>