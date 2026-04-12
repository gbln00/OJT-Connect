{{--
    resources/views/qr/result.blade.php
    Shown after a student scans the QR.
    Extends student-app layout since the student is logged in.
--}}
@extends('layouts.student-app')
@section('title', 'QR Clock-In')
@section('page-title', 'QR Clock-In')

@section('content')

<div style="max-width:420px;margin:60px auto;">

    <div class="card fade-up">

        {{-- Colored top bar --}}
        <div style="height:4px;background:
            {{ $status === 'success' ? '#34d399' : ($status === 'info' ? '#60a5fa' : 'var(--crimson)') }};
        "></div>

        <div style="padding:36px;text-align:center;">

            {{-- Icon --}}
            <div style="width:64px;height:64px;margin:0 auto 20px;
                border:1px solid {{ $status === 'success' ? 'rgba(52,211,153,0.3)' : ($status === 'info' ? 'rgba(96,165,250,0.3)' : 'rgba(140,14,3,0.3)') }};
                background:{{ $status === 'success' ? 'rgba(52,211,153,0.08)' : ($status === 'info' ? 'rgba(96,165,250,0.08)' : 'rgba(140,14,3,0.08)') }};
                display:flex;align-items:center;justify-content:center;
                color:{{ $status === 'success' ? '#34d399' : ($status === 'info' ? '#60a5fa' : 'var(--crimson)') }};">
                @if($status === 'success')
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12"/></svg>
                @elseif($status === 'info')
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @else
                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                @endif
            </div>

            <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--text);margin-bottom:10px;">
                {{ $title }}
            </div>
            <div style="font-size:13px;color:var(--text2);line-height:1.6;margin-bottom:24px;">
                {{ $message }}
            </div>

            {{-- Details (success / info only) --}}
            @if(isset($student))
            <div style="text-align:left;border-top:1px solid var(--border);padding-top:16px;">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span class="form-label" style="margin:0;">Student</span>
                    <span style="font-size:13px;color:var(--text);font-weight:500;">{{ $student->name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span class="form-label" style="margin:0;">Company</span>
                    <span style="font-size:13px;color:var(--text);">{{ $application->company->name }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border);">
                    <span class="form-label" style="margin:0;">Session</span>
                    <span style="font-family:'DM Mono',monospace;font-size:10px;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;padding:3px 10px;border:1px solid;
                        {{ $session === 'morning'
                            ? 'background:rgba(254,243,199,0.12);color:#D97706;border-color:rgba(217,119,6,0.3);'
                            : 'background:rgba(219,234,254,0.12);color:#3B82F6;border-color:rgba(59,130,246,0.3);' }}">
                        {{ $session === 'morning' ? 'AM · Morning' : 'PM · Afternoon' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;">
                    <span class="form-label" style="margin:0;">Date & Time</span>
                    <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--text);">
                        {{ now()->format('M d, Y · h:i A') }}
                    </span>
                </div>
            </div>
            @endif

            {{-- Back button --}}
            <div style="margin-top:20px;">
                <a href="{{ route('student.hours.index') }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">
                    View my hour logs →
                </a>
            </div>

        </div>
    </div>

    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-align:center;margin-top:14px;">
        // Pending supervisor approval
    </div>

</div>

@endsection