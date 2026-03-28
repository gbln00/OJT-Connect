@extends('layouts.superadmin-app')
@section('title', 'Tenant — ' . $tenant->id)
@section('page-title', $tenant->id)

@section('topbar-actions')
    <div style="display:flex;gap:8px;">
        <a href="{{ route('super_admin.tenants.edit', $tenant) }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#8C0E03;
                  color:rgba(255,255,255,0.92);font-size:12px;font-weight:700;
                  letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;
                  transition:background 0.2s;"
           onmouseover="this.style.background='#a81004'" onmouseout="this.style.background='#8C0E03'">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Edit
        </a>
        <a href="{{ route('super_admin.tenants.index') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;border:1px solid rgba(171,171,171,0.15);
                  background:transparent;color:rgba(171,171,171,0.6);font-size:12px;font-weight:700;
                  letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>
@endsection

@section('content')

@php
    $status   = $tenant->status ?? 'active';
    $isActive = $status === 'active';

    $planColors = [
        'free'       => ['border'=>'rgba(171,171,171,0.2)',  'bg'=>'rgba(171,171,171,0.05)', 'color'=>'rgba(171,171,171,0.6)'],
        'basic'      => ['border'=>'rgba(80,150,220,0.35)',  'bg'=>'rgba(80,150,220,0.08)',  'color'=>'rgba(100,170,240,0.85)'],
        'pro'        => ['border'=>'rgba(140,14,3,0.4)',     'bg'=>'rgba(140,14,3,0.1)',     'color'=>'rgba(200,100,90,0.9)'],
        'enterprise' => ['border'=>'rgba(160,120,40,0.45)', 'bg'=>'rgba(160,120,40,0.1)',   'color'=>'rgba(210,170,70,0.9)'],
    ];
    $pc = $planColors[$tenant->plan] ?? null;
@endphp

<div style="max-width:640px;display:flex;flex-direction:column;gap:1px;">

    {{-- ── Tenant Details Card ── --}}
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">

        {{-- Card header --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
            <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Tenant Details</span>
        </div>

        {{-- Tenant ID --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;">Tenant ID</span>
            <span style="font-family:'Barlow Condensed',sans-serif;font-weight:700;font-size:16px;color:#fff;letter-spacing:0.05em;">{{ $tenant->id }}</span>
        </div>

        {{-- Domain — prominent inline row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid rgba(171,171,171,0.05);gap:16px;">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;flex-shrink:0;">Domain</span>
            <div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:flex-end;">
                @forelse($tenant->domains as $domain)
                    <a href="https://{{ $domain->domain }}" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:7px;padding:5px 12px;
                              border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.08);
                              font-size:12px;color:rgba(200,100,90,0.9);font-family:monospace;
                              letter-spacing:0.04em;text-decoration:none;
                              transition:border-color 0.2s,background 0.2s,color 0.2s;"
                       onmouseover="this.style.borderColor='rgba(140,14,3,0.7)';this.style.background='rgba(140,14,3,0.15)';this.style.color='rgba(220,120,110,1)'"
                       onmouseout="this.style.borderColor='rgba(140,14,3,0.4)';this.style.background='rgba(140,14,3,0.08)';this.style.color='rgba(200,100,90,0.9)'">
                        <span style="width:5px;height:5px;background:#8C0E03;flex-shrink:0;display:inline-block;border-radius:50%;"></span>
                        {{ $domain->domain }}
                        <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="opacity:0.5;flex-shrink:0;">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15,3 21,3 21,9"/><line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                    </a>
                @empty
                    <span style="font-size:12px;color:rgba(171,171,171,0.2);font-family:monospace;">// No domain configured</span>
                @endforelse
            </div>
        </div>

        {{-- Status --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;">Status</span>
            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;
                         border:1px solid {{ $isActive ? 'rgba(34,197,94,0.25)' : 'rgba(239,68,68,0.2)' }};
                         background:{{ $isActive ? 'rgba(34,197,94,0.06)' : 'rgba(239,68,68,0.06)' }};
                         font-size:10px;color:{{ $isActive ? 'rgba(74,222,128,0.85)' : 'rgba(252,165,165,0.75)' }};
                         font-family:monospace;letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                <span style="width:6px;height:6px;border-radius:50%;flex-shrink:0;
                             background:{{ $isActive ? '#22c55e' : '#ef4444' }};
                             {{ $isActive ? 'box-shadow:0 0 7px rgba(34,197,94,0.65);' : '' }}">
                </span>
                {{ $isActive ? 'Active' : 'Inactive' }}
            </span>
        </div>

        {{-- Plan --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;">Plan</span>
            @if($pc)
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;
                             border:1px solid {{ $pc['border'] }};background:{{ $pc['bg'] }};
                             font-size:10px;color:{{ $pc['color'] }};font-family:monospace;
                             letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">
                    {{ ucfirst($tenant->plan) }}
                </span>
            @else
                <span style="font-size:12px;color:rgba(171,171,171,0.2);font-family:monospace;">—</span>
            @endif
        </div>

        {{-- Created / Updated --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;">Created</span>
            <span style="font-size:12px;color:rgba(171,171,171,0.4);font-family:monospace;">{{ $tenant->created_at->format('F d, Y \a\t g:i A') }}</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;">
            <span style="font-size:10px;color:rgba(171,171,171,0.28);letter-spacing:0.16em;text-transform:uppercase;font-family:monospace;">Last Updated</span>
            <span style="font-size:12px;color:rgba(171,171,171,0.4);font-family:monospace;">{{ $tenant->updated_at->format('F d, Y \a\t g:i A') }}</span>
        </div>
    </div>

    {{-- ── Danger Zone ── --}}
    <div style="background:#0E1126;border:1px solid rgba(140,14,3,0.18);padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:20px;height:2px;background:rgba(200,80,70,0.55);flex-shrink:0;"></div>
            <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:rgba(220,100,90,0.8);">Danger Zone</span>
        </div>
        <div style="font-size:12px;color:rgba(171,171,171,0.3);margin-bottom:20px;line-height:1.8;font-family:monospace;">
            // Deleting this tenant permanently drops their database and removes all associated data — users, applications, hour logs, and evaluations.
        </div>
        <button onclick="document.getElementById('deleteModal').style.display='flex'"
                style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;
                       border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.08);
                       color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;
                       letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                       transition:background 0.2s,border-color 0.2s;"
                onmouseover="this.style.background='rgba(140,14,3,0.2)';this.style.borderColor='rgba(140,14,3,0.7)'"
                onmouseout="this.style.background='rgba(140,14,3,0.08)';this.style.borderColor='rgba(140,14,3,0.4)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="3,6 5,6 21,6"/>
                <path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                <path d="M10,11v6M14,11v6"/>
                <path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
            </svg>
            Delete Tenant
        </button>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.88);z-index:999;
                              align-items:center;justify-content:center;backdrop-filter:blur(2px);">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.1);border-top:2px solid #8C0E03;
                width:100%;max-width:440px;margin:0 20px;padding:28px;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:rgba(171,171,171,0.5);line-height:1.8;margin-bottom:24px;font-family:monospace;">
            // You are about to permanently delete<br>
            <strong style="color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:15px;letter-spacing:0.05em;">{{ $tenant->id }}</strong>.<br>
            This will drop their entire database.
            <span style="color:rgba(200,80,70,0.8);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="document.getElementById('deleteModal').style.display='none'"
                    style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                           transition:border-color 0.2s,color 0.2s;"
                    onmouseover="this.style.borderColor='rgba(171,171,171,0.3)';this.style.color='rgba(171,171,171,0.85)'"
                    onmouseout="this.style.borderColor='rgba(171,171,171,0.15)';this.style.color='rgba(171,171,171,0.5)'">
                Cancel
            </button>
            <form method="POST" action="{{ route('super_admin.tenants.destroy', $tenant) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:8px 18px;border:1px solid rgba(140,14,3,0.5);background:rgba(140,14,3,0.15);
                               color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;letter-spacing:0.1em;
                               text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                               transition:background 0.2s,border-color 0.2s;"
                        onmouseover="this.style.background='rgba(140,14,3,0.28)';this.style.borderColor='rgba(140,14,3,0.75)'"
                        onmouseout="this.style.background='rgba(140,14,3,0.15)';this.style.borderColor='rgba(140,14,3,0.5)'">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection