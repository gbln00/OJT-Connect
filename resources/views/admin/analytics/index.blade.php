@extends('layouts.app')
@section('title', 'Analytics')
@section('page-title', 'Analytics')
 
@section('topbar-actions')
    {{-- Premium badge in topbar --}}
    <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
         border:1px solid var(--gold-border);background:var(--gold-dim);
         font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
         text-transform:uppercase;color:var(--gold-color);">
        ✦ Premium Feature
    </span>
@endsection
 
@section('content')
 
{{-- ── Eyebrow ── --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:20px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;
          animation:flicker 8s ease-in-out infinite;"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;
          text-transform:uppercase;color:var(--muted);">
        Analytics Dashboard — Premium
    </span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
        // data as of {{ now()->format('M d, Y g:i A') }}
    </span>
</div>
 
{{-- ── KPI Strip — Row 1 (4 cards) ── --}}
<div class="stats-grid fade-up fade-up-1"
     style="grid-template-columns:repeat(4,1fr);margin-bottom:12px;">
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <span class="stat-tag">students</span>
        </div>
        <div class="stat-num">{{ number_format($kpis['total_students']) }}</div>
        <div class="stat-label">Total Interns</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon crimson">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <span class="stat-tag">approved</span>
        </div>
        <div class="stat-num">{{ number_format($kpis['approved_apps']) }}</div>
        <div class="stat-label">Approved Applications</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
            <span class="stat-tag">approved hrs</span>
        </div>
        <div class="stat-num">{{ number_format($kpis['total_hours']) }}</div>
        <div class="stat-label">Total Hours Logged</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon night">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <span class="stat-tag">partners</span>
        </div>
        <div class="stat-num">{{ number_format($kpis['total_companies']) }}</div>
        <div class="stat-label">Partner Companies</div>
    </div>
</div>
 
{{-- ── KPI Strip — Row 2 (4 cards) ── --}}
<div class="stats-grid fade-up fade-up-2"
     style="grid-template-columns:repeat(4,1fr);">
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
            </div>
            <span class="stat-tag">pass rate</span>
        </div>
        <div class="stat-num"
             style="color:{{ $kpis['pass_rate'] >= 70
                 ? 'var(--teal-color)'
                 : ($kpis['pass_rate'] >= 50 ? 'var(--gold-color)' : 'var(--coral-color)') }};">
            {{ $kpis['pass_rate'] }}%
        </div>
        <div class="stat-label">Overall Pass Rate</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <line x1="18" y1="20" x2="18" y2="10"/>
                    <line x1="12" y1="20" x2="12" y2="4"/>
                    <line x1="6"  y1="20" x2="6"  y2="14"/>
                </svg>
            </div>
            <span class="stat-tag">avg grade</span>
        </div>
        <div class="stat-num">{{ $kpis['avg_grade'] }}</div>
        <div class="stat-label">Avg Evaluation Grade</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon coral">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="stat-tag">queue</span>
        </div>
        <div class="stat-num"
             style="color:{{ $kpis['pending_apps'] > 0 ? 'var(--crimson)' : 'var(--text)' }};">
            {{ number_format($kpis['pending_apps']) }}
        </div>
        <div class="stat-label">Pending Applications</div>
    </div>
 
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon steel">
                <svg width="15" height="15" fill="none" stroke="currentColor"
                     stroke-width="1.8" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8"  y1="2" x2="8"  y2="6"/>
                    <line x1="3"  y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <span class="stat-tag">all time</span>
        </div>
        <div class="stat-num">{{ number_format($kpis['total_logs']) }}</div>
        <div class="stat-label">Hour Log Entries</div>
    </div>
</div>
{{-- ── Chart Row 1: Applications Bar + Hour Logs Line ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;"
     class="fade-up fade-up-3">
 
    {{-- Applications per Month --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Applications per Month</div>
            <span style="font-family:'DM Mono',monospace;font-size:9px;
                 color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">
                last 12 months
            </span>
        </div>
        <div style="padding:20px;">
            <canvas id="chartApps" height="160"></canvas>
        </div>
    </div>
 
    {{-- Hour Logs per Week --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Hour Logs per Week</div>
            <span style="font-family:'DM Mono',monospace;font-size:9px;
                 color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">
                last 12 weeks
            </span>
        </div>
        <div style="padding:20px;">
            <canvas id="chartLogs" height="160"></canvas>
        </div>
    </div>
</div>
 
{{-- ── Chart Row 2: Pass/Fail Doughnut + Company Progress ── --}}
<div style="display:grid;grid-template-columns:340px 1fr;gap:16px;margin-bottom:16px;"
     class="fade-up fade-up-4">
 
    {{-- Pass / Fail Doughnut --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pass / Fail Rate</div>
            <span style="font-family:'DM Mono',monospace;font-size:9px;
                 color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">
                all evaluations
            </span>
        </div>
        <div style="padding:20px;display:flex;flex-direction:column;align-items:center;gap:16px;">
            <div style="position:relative;width:180px;height:180px;">
                <canvas id="chartPassFail" width="180" height="180"></canvas>
                {{-- Centre label --}}
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;
                     align-items:center;justify-content:center;pointer-events:none;">
                    <span style="font-family:'Playfair Display',serif;font-size:26px;
                         font-weight:900;color:var(--text);">
                        {{ $passFailRate['pct_pass'] }}%
                    </span>
                    <span style="font-family:'DM Mono',monospace;font-size:9px;
                         color:var(--muted);letter-spacing:0.12em;text-transform:uppercase;">
                        pass rate
                    </span>
                </div>
            </div>
            {{-- Legend --}}
            <div style="display:flex;gap:20px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:10px;height:10px;background:var(--teal-color);
                         display:inline-block;flex-shrink:0;"></span>
                    <span style="font-size:12px;color:var(--text2);">
                        Pass — {{ $passFailRate['pass'] }}
                    </span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:10px;height:10px;background:var(--coral-color);
                         display:inline-block;flex-shrink:0;"></span>
                    <span style="font-size:12px;color:var(--text2);">
                        Fail — {{ $passFailRate['fail'] }}
                    </span>
                </div>
            </div>
        </div>
    </div>
 
    {{-- Company Progress --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Avg. Intern Progress by Company</div>
            <span style="font-family:'DM Mono',monospace;font-size:9px;
                 color:var(--muted);letter-spacing:0.1em;text-transform:uppercase;">
                top 8 companies
            </span>
        </div>
        <div style="padding:20px;">
            <canvas id="chartCompany" height="180"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
 
    const style   = getComputedStyle(document.documentElement);
    const teal    = style.getPropertyValue('--teal-color').trim();
    const coral   = style.getPropertyValue('--coral-color').trim();
    const gold    = style.getPropertyValue('--gold-color').trim();
    const blue    = style.getPropertyValue('--blue-color').trim();
    const crimson = style.getPropertyValue('--crimson').trim();
    const text2   = style.getPropertyValue('--text2').trim();
    const border  = style.getPropertyValue('--border2').trim();
 
    Chart.defaults.font.family = "'Barlow', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = text2;
 
    const gridOpts = { color: border, drawBorder: false };
    const tickOpts = { color: text2, font: { size: 10 } };
 
    // Chart 1: Applications per Month (Grouped Bar)
    new Chart(document.getElementById('chartApps'), {
        type: 'bar',
        data: {
            labels: @json($appsPerMonth['months']),
            datasets: [
                { label: 'Approved', data: @json($appsPerMonth['approved']),
                  backgroundColor: teal+'99', borderColor: teal, borderWidth: 1 },
                { label: 'Pending', data: @json($appsPerMonth['pending'] ?? []),
                  backgroundColor: gold+'99', borderColor: gold, borderWidth: 1 },
                { label: 'Rejected', data: @json($appsPerMonth['rejected']),
                  backgroundColor: coral+'88', borderColor: coral, borderWidth: 1 },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { grid: gridOpts, ticks: tickOpts },
                y: { grid: gridOpts, ticks: tickOpts, beginAtZero: true },
            },
        },
    });
 
    // Chart 2: Hour Logs per Week (Line)
    new Chart(document.getElementById('chartLogs'), {
        type: 'line',
        data: {
            labels: @json($logsPerWeek['weeks']),
            datasets: [
                { label: 'Submitted', data: @json($logsPerWeek['logged']),
                  borderColor: blue, backgroundColor: blue+'22',
                  fill: true, tension: 0.4, pointRadius: 3 },
                { label: 'Approved', data: @json($logsPerWeek['approved']),
                  borderColor: teal, backgroundColor: teal+'22',
                  fill: true, tension: 0.4, pointRadius: 3 },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { grid: gridOpts, ticks: tickOpts },
                y: { grid: gridOpts, ticks: tickOpts, beginAtZero: true },
            },
        },
    });
 
    // Chart 3: Pass/Fail Doughnut
    new Chart(document.getElementById('chartPassFail'), {
        type: 'doughnut',
        data: {
            labels: ['Pass', 'Fail'],
            datasets: [{ data: [{{ $passFailRate['pass'] }}, {{ $passFailRate['fail'] }}],
                backgroundColor: [teal+'CC', coral+'CC'],
                borderColor: [teal, coral], borderWidth: 2 }],
        },
        options: { responsive: false, cutout: '72%',
            plugins: { legend: { display: false } } },
    });
 
    // Chart 4: Company Progress (Horizontal Bar)
    new Chart(document.getElementById('chartCompany'), {
        type: 'bar',
        data: {
            labels: @json($perCompany['labels']),
            datasets: [
                { label: 'Avg Progress %', data: @json($perCompany['avgPct']),
                  backgroundColor: crimson+'AA', borderColor: crimson, borderWidth: 1 },
                { label: 'Student Count', data: @json($perCompany['students']),
                  backgroundColor: gold+'88', borderColor: gold, borderWidth: 1,
                  yAxisID: 'y2' },
            ],
        },
        options: {
            indexAxis: 'y', responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                x: { grid: gridOpts, ticks: tickOpts, beginAtZero: true, max: 100 },
                y: { grid: gridOpts, ticks: tickOpts },
                y2: { display: false },
            },
        },
    });
 
});
</script>
<script>
// Re-draw charts when theme is toggled (dark <-> light)
// Wait for DOM + charts to be ready
document.addEventListener('DOMContentLoaded', function () {
    const originalToggle = window.toggleTheme;
    window.toggleTheme = function () {
        if (originalToggle) originalToggle();
        // Small delay to let CSS variables update before reading them
        setTimeout(() => {
            Chart.instances && Object.values(Chart.instances).forEach(chart => {
                const s = getComputedStyle(document.documentElement);
                chart.options.scales?.x && (
                    chart.options.scales.x.grid.color  = s.getPropertyValue('--border2').trim(),
                    chart.options.scales.x.ticks.color = s.getPropertyValue('--text2').trim()
                );
                chart.options.scales?.y && (
                    chart.options.scales.y.grid.color  = s.getPropertyValue('--border2').trim(),
                    chart.options.scales.y.ticks.color = s.getPropertyValue('--text2').trim()
                );
                chart.options.plugins.legend.labels.color =
                    s.getPropertyValue('--text2').trim();
                chart.update();
            });
        }, 60);
    };
});
</script>

@endpush
