@extends('layouts.superadmin-app')
@section('title', 'Tenant — ' . $tenant->id)
@section('page-title', $tenant->id)

@section('topbar-actions')
    <div style="display:flex;gap:8px;">
        <a href="{{ route('super_admin.tenants.edit', $tenant) }}"
           style="display:inline-flex;align-items:center;padding:7px 16px;border:1px solid rgba(171,171,171,0.15);
                  background:transparent;color:rgba(171,171,171,0.6);font-size:12px;font-weight:700;
                  letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;font-family:'Barlow Condensed',sans-serif;">
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
<div style="max-width:640px;display:flex;flex-direction:column;gap:1px;">

    {{-- Tenant Details --}}
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);border-top:2px solid #8C0E03;padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
            <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Tenant Details</span>
        </div>

        @foreach([
            ['Tenant ID', '<span style="font-family:\'Barlow Condensed\',sans-serif;font-weight:700;font-size:15px;color:#fff;letter-spacing:0.05em;">' . $tenant->id . '</span>'],
            ['Name', '<span style="font-size:14px;color:rgba(171,171,171,0.7);">' . ($tenant->name ?? '—') . '</span>'],
            ['Email', '<span style="font-size:14px;color:rgba(171,171,171,0.7);font-family:monospace;">' . ($tenant->email ?? '—') . '</span>'],
            ['Plan', '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border:1px solid rgba(140,14,3,0.3);background:rgba(140,14,3,0.08);font-size:11px;color:rgba(200,100,90,0.8);font-family:monospace;letter-spacing:0.08em;text-transform:uppercase;">' . ($tenant->plan ?? '—') . '</span>'],
            ['Status', '<span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border:1px solid rgba(50,150,80,0.3);background:rgba(50,150,80,0.08);font-size:11px;color:rgba(80,180,100,0.8);font-family:monospace;letter-spacing:0.08em;text-transform:uppercase;">' . ($tenant->status ?? 'active') . '</span>'],
            ['Created', '<span style="font-size:13px;color:rgba(171,171,171,0.4);font-family:monospace;">' . $tenant->created_at->format('F d, Y \a\t g:i A') . '</span>'],
            ['Last Updated', '<span style="font-size:13px;color:rgba(171,171,171,0.4);font-family:monospace;">' . $tenant->updated_at->format('F d, Y \a\t g:i A') . '</span>'],
        ] as [$key, $val])
        <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
            <span style="font-size:11px;color:rgba(171,171,171,0.3);letter-spacing:0.15em;text-transform:uppercase;font-family:monospace;">{{ $key }}</span>
            <div>{!! $val !!}</div>
        </div>
        @endforeach
    </div>

    {{-- Domains --}}
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.08);padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid rgba(171,171,171,0.06);">
            <div style="width:20px;height:2px;background:#8C0E03;flex-shrink:0;"></div>
            <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:#fff;">Domains</span>
        </div>
        @forelse($tenant->domains as $domain)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 0;border-bottom:1px solid rgba(171,171,171,0.05);">
                <span style="font-size:11px;color:rgba(171,171,171,0.3);letter-spacing:0.15em;text-transform:uppercase;font-family:monospace;">Domain</span>
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.1);font-size:12px;color:rgba(200,100,90,0.9);font-family:monospace;">
                    <span style="width:5px;height:5px;background:#8C0E03;display:inline-block;flex-shrink:0;"></span>
                    {{ $domain->domain }}
                </span>
            </div>
        @empty
            <div style="font-size:13px;color:rgba(171,171,171,0.25);padding:12px 0;font-family:monospace;">// No domains configured.</div>
        @endforelse
    </div>

    {{-- Danger Zone --}}
    <div style="background:#0E1126;border:1px solid rgba(140,14,3,0.2);padding:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
            <div style="width:20px;height:2px;background:rgba(200,80,70,0.6);flex-shrink:0;"></div>
            <span style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;color:rgba(220,100,90,0.8);">Danger Zone</span>
        </div>
        <div style="font-size:13px;color:rgba(171,171,171,0.35);margin-bottom:20px;line-height:1.7;font-family:monospace;">
            // Deleting this tenant will permanently drop their database and remove all associated data including users, applications, hour logs, and evaluations.
        </div>
        <button onclick="document.getElementById('deleteModal').style.display='flex'"
                style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;
                       border:1px solid rgba(140,14,3,0.4);background:rgba(140,14,3,0.1);
                       color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;
                       letter-spacing:0.1em;text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;
                       transition:background 0.2s,border-color 0.2s;"
                onmouseover="this.style.background='rgba(140,14,3,0.2)';this.style.borderColor='rgba(140,14,3,0.7)'"
                onmouseout="this.style.background='rgba(140,14,3,0.1)';this.style.borderColor='rgba(140,14,3,0.4)'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="3,6 5,6 21,6"/><path d="M19,6l-1,14a2,2,0,0,1-2,2H8a2,2,0,0,1-2-2L5,6"/>
                <path d="M10,11v6M14,11v6"/><path d="M9,6V4a1,1,0,0,1,1-1h4a1,1,0,0,1,1,1v2"/>
            </svg>
            Delete Tenant
        </button>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(13,13,13,0.85);z-index:999;align-items:center;justify-content:center;">
    <div style="background:#0E1126;border:1px solid rgba(171,171,171,0.1);border-top:2px solid #8C0E03;width:100%;max-width:440px;margin:0 20px;padding:28px;">
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:12px;">Delete Tenant?</div>
        <div style="font-size:13px;color:rgba(171,171,171,0.5);line-height:1.7;margin-bottom:24px;">
            You are about to permanently delete
            <strong style="color:#fff;font-family:'Barlow Condensed',sans-serif;font-size:15px;">{{ $tenant->id }}</strong>.
            This will drop their entire database.
            <span style="color:rgba(200,80,70,0.8);">This action cannot be undone.</span>
        </div>
        <div style="display:flex;gap:10px;justify-content:flex-end;">
            <button onclick="document.getElementById('deleteModal').style.display='none'"
                    style="padding:8px 18px;border:1px solid rgba(171,171,171,0.15);background:transparent;
                           color:rgba(171,171,171,0.5);font-size:12px;font-weight:700;letter-spacing:0.1em;
                           text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                Cancel
            </button>
            <form method="POST" action="{{ route('super_admin.tenants.destroy', $tenant) }}" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit"
                        style="padding:8px 18px;border:1px solid rgba(140,14,3,0.5);background:rgba(140,14,3,0.15);
                               color:rgba(220,100,90,0.9);font-size:12px;font-weight:700;letter-spacing:0.1em;
                               text-transform:uppercase;cursor:pointer;font-family:'Barlow Condensed',sans-serif;">
                    Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection