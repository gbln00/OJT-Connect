@extends('layouts.app')
@section('title', 'Mock Feature Lab')
@section('page-title', 'Mock Feature Lab')

@section('content')
<div class="greeting">
    <div class="greeting-sub">Demo-only tenant module</div>
    <h1 class="greeting-title">
        Update-gated <span>Mock Feature</span> is now active.
    </h1>
</div>

<div class="stats-grid fade-up" style="grid-template-columns: repeat(3, minmax(0, 1fr));">
    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon teal">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20,6 9,17 4,12"/>
                </svg>
            </div>
        </div>
        <div class="stat-num" style="font-size:18px;">v{{ $requiredVersion }}</div>
        <div class="stat-label">Required Update Installed</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon blue">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="stat-num" style="font-size:18px;">Feature Flag</div>
        <div class="stat-label">mock_feature.enabled = true</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            <div class="stat-icon gold">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12,6 12,12 16,14"/>
                </svg>
            </div>
        </div>
        <div class="stat-num" style="font-size:18px;">{{ $activatedAt ?? 'n/a' }}</div>
        <div class="stat-label">Activated At</div>
    </div>
</div>

<div class="card fade-up fade-up-2" style="margin-top:18px;">
    <div class="card-header">
        <div class="card-title">What this demonstrates</div>
    </div>
    <div style="padding:16px 20px;color:var(--text2);font-size:13px;line-height:1.8;">
        This page represents a tenant feature that is blocked until a required update is installed from
        <strong style="color:var(--text);">What's New</strong>. Use it during demos to prove update enforcement:
        tenants can see the update, install it, and unlock new functionality.
    </div>
</div>
@endsection
