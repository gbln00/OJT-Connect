@extends('layouts.superadmin-app')
@section('title', 'System Versions')
@section('page-title', 'Version Control')

@section('content')

{{-- ── Header ── --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;">
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;">
                Release Changelog
            </h2>
            @if($currentVersion)
            <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
                         background:rgba(45,212,191,0.08);border:1px solid rgba(45,212,191,0.25);
                         font-family:'DM Mono',monospace;font-size:11px;color:#2dd4bf;">
                <span style="width:5px;height:5px;border-radius:50%;background:#2dd4bf;
                             animation:pulse 2s ease-in-out infinite;flex-shrink:0;"></span>
                Live: v{{ $currentVersion->version }}
            </span>
            @endif
        </div>
        <p style="color:var(--muted);font-size:13px;">
            Manage drafts, publish releases, and track tenant acknowledgment.
            @if($totalTenants > 0)
                <span style="color:var(--text2);">{{ $totalTenants }} active tenant(s).</span>
            @endif
        </p>
    </div>
    <a href="{{ route('super_admin.versions.create') }}" class="btn btn-primary">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="12" y1="4" x2="12" y2="20"/><line x1="4" y1="12" x2="20" y2="12"/>
        </svg>
        New Version
    </a>
</div>

{{-- ── Stats row ── --}}
@php
    $totalPublished = $versions->where('is_published', true)->count();
    $totalDrafts    = $versions->where('is_published', false)->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px;">
    <div style="background:var(--surface);border:1px solid var(--border);padding:16px 18px;
                position:relative;overflow:hidden;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Total Releases</div>
        <div style="font-family:'Playfair Display',serif;font-size:26px;font-weight:900;
                    color:var(--text);line-height:1;">{{ $versions->total() }}</div>
        <div style="position:absolute;right:16px;top:50%;transform:translateY(-50%);
                    opacity:0.06;font-size:48px;font-family:'Playfair Display',serif;font-weight:900;">
            #
        </div>
    </div>
    <div style="background:var(--surface);border:1px solid rgba(45,212,191,0.2);padding:16px 18px;
                position:relative;overflow:hidden;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Published</div>
        <div style="font-family:'Playfair Display',serif;font-size:26px;font-weight:900;
                    color:#2dd4bf;line-height:1;">{{ $totalPublished }}</div>
        <div style="position:absolute;top:0;right:0;bottom:0;width:3px;background:#2dd4bf;opacity:0.4;"></div>
    </div>
    <div style="background:var(--surface);border:1px solid var(--border);padding:16px 18px;
                position:relative;overflow:hidden;">
        <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:8px;">Drafts</div>
        <div style="font-family:'Playfair Display',serif;font-size:26px;font-weight:900;
                    color:var(--text2);line-height:1;">{{ $totalDrafts }}</div>
        <div style="position:absolute;top:0;right:0;bottom:0;width:3px;
                    background:var(--border2);"></div>
    </div>
</div>

{{-- ── Version Table ── --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Type</th>
                    <th>Label</th>
                    <th>Status</th>
                    <th>Published</th>
                    <th>Tenants Read</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($versions as $v)
                <tr style="{{ $v->is_current ? 'background:rgba(45,212,191,0.03);' : '' }}">

                    {{-- Version number --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($v->is_current)
                            <span style="width:6px;height:6px;border-radius:50%;background:#2dd4bf;
                                         flex-shrink:0;animation:pulse 2s ease-in-out infinite;"
                                  title="Current live version"></span>
                            @else
                            <span style="width:6px;height:6px;flex-shrink:0;"></span>
                            @endif
                            <span style="font-family:'DM Mono',monospace;font-weight:600;
                                         color:var(--text);font-size:13px;">
                                v{{ $v->version }}
                            </span>
                            @if($v->is_current)
                            <span style="font-family:'DM Mono',monospace;font-size:8px;
                                         padding:1px 5px;background:rgba(45,212,191,0.1);
                                         color:#2dd4bf;border:1px solid rgba(45,212,191,0.25);
                                         letter-spacing:0.08em;text-transform:uppercase;">
                                LIVE
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- Type --}}
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:5px;">
                            <span style="font-size:12px;">{{ $v->typeIcon() }}</span>
                            <span class="status-pill {{ $v->typeColor() }}">{{ ucfirst($v->type) }}</span>
                        </span>
                    </td>

                    {{-- Label --}}
                    <td style="max-width:200px;">
                        <span style="color:var(--text2);font-size:13px;
                                     white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
                                     display:block;max-width:200px;">
                            {{ $v->label ?? '—' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($v->is_published)
                            <span class="status-pill teal">Published</span>
                        @else
                            <span class="status-pill steel">Draft</span>
                        @endif
                    </td>

                    {{-- Published date --}}
                    <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                        {{ $v->published_at?->format('M d, Y') ?? '—' }}
                    </td>

                    {{-- Tenants read count --}}
                    <td>
                        @if($v->is_published)
                        @php $readCount = $v->readTenantCount(); @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;max-width:80px;height:3px;background:var(--border2);overflow:hidden;">
                                @if($totalTenants > 0)
                                <div style="height:100%;background:#2dd4bf;width:{{ min(100, round($readCount / $totalTenants * 100)) }}%;
                                            transition:width 0.4s;"></div>
                                @endif
                            </div>
                            <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);">
                                {{ $readCount }}/{{ $totalTenants }}
                            </span>
                        </div>
                        @else
                        <span style="color:var(--muted);font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;justify-content:flex-end;">
                            <a href="{{ route('super_admin.versions.edit', $v) }}"
                               class="btn btn-ghost btn-sm">Edit</a>

                            @if(!$v->is_published)
                            <form method="POST" action="{{ route('super_admin.versions.publish', $v) }}">
                                @csrf
                                <button class="btn btn-approve btn-sm" title="Publish & notify all tenants">
                                    Publish
                                </button>
                            </form>
                            @elseif(!$v->is_current)
                            <form method="POST" action="{{ route('super_admin.versions.setCurrent', $v) }}">
                                @csrf
                                <button class="btn btn-ghost btn-sm"
                                        style="border-color:rgba(45,212,191,0.3);color:#2dd4bf;"
                                        title="Set as current live version">
                                    Set Live
                                </button>
                            </form>
                            @endif

                            <form method="POST" action="{{ route('super_admin.versions.destroy', $v) }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete v{{ $v->version }}? This cannot be undone.')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:60px;color:var(--muted);">
                        <div style="font-family:'DM Mono',monospace;font-size:11px;margin-bottom:12px;
                                    letter-spacing:0.1em;">// No versions yet</div>
                        <a href="{{ route('super_admin.versions.create') }}" class="btn btn-primary btn-sm">
                            Create first version
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($versions->hasPages())
    <div style="padding:12px 18px;border-top:1px solid var(--border);
                display:flex;align-items:center;justify-content:space-between;">
        <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
            {{ $versions->firstItem() }}–{{ $versions->lastItem() }} of {{ $versions->total() }}
        </span>
        {{ $versions->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: 0.5; transform: scale(1.3); }
}
</style>
@endpush
@endsection