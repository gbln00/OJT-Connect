@extends('layouts.superadmin-app')
@section('title', 'Edit Version — v' . $version->version)
@section('page-title', 'Edit Version')

@section('content')

<div style="display:grid;grid-template-columns:1fr 400px;gap:20px;align-items:flex-start;">

    {{-- ── Left: Form ── --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                <div class="card-title">Edit v{{ $version->version }}</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                @if($version->is_current)
                <span style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.1em;
                             text-transform:uppercase;padding:2px 8px;
                             background:rgba(45,212,191,0.1);color:#2dd4bf;
                             border:1px solid rgba(45,212,191,0.25);">● Live</span>
                @endif
                @if($version->is_published)
                <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;
                             text-transform:uppercase;color:#2dd4bf;
                             padding:2px 8px;border:1px solid rgba(45,212,191,0.25);">Published</span>
                @else
                <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;
                             text-transform:uppercase;color:var(--muted);
                             padding:2px 8px;border:1px solid var(--border2);">Draft</span>
                @endif
            </div>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('super_admin.versions.update', $version) }}">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">
                            Version Number
                            <span style="color:var(--crimson)">*</span>
                        </label>
                        <input type="text" name="version" id="inp-version"
                               value="{{ old('version', $version->version) }}"
                               class="form-input" oninput="updatePreview()">
                        @error('version')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">
                            Release Type
                            <span style="color:var(--crimson)">*</span>
                        </label>
                        <select name="type" id="inp-type" class="form-select" onchange="updatePreview()">
                            @foreach(['patch' => '🔧 Patch', 'minor' => '✨ Minor', 'major' => '🚀 Major', 'hotfix' => '🩹 Hotfix'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $version->type) === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Release Label</label>
                    <input type="text" name="label" id="inp-label"
                           value="{{ old('label', $version->label) }}"
                           class="form-input"
                           placeholder="e.g. Calendar & Bulk Actions Release"
                           oninput="updatePreview()">
                </div>

                <div style="margin-bottom:24px;">
                    <label class="form-label">
                        Changelog <span style="color:var(--crimson)">*</span>
                    </label>
                    <textarea name="changelog" id="inp-changelog"
                              class="form-textarea" rows="14"
                              oninput="updatePreview()">{{ old('changelog', $version->changelog) }}</textarea>
                    @error('changelog')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>

                    @if(!$version->is_published)
                    <form method="POST" action="{{ route('super_admin.versions.publish', $version) }}"
                          style="margin:0;">
                        @csrf
                        <button class="btn btn-approve"
                                onclick="return confirm('Publish v{{ $version->version }} and notify all tenants?')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.437L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            Publish & Notify
                        </button>
                    </form>
                    @endif

                    <a href="{{ route('super_admin.versions.index') }}" class="btn btn-ghost">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Right: Preview + Meta ── --}}
    <div style="position:sticky;top:90px;display:flex;flex-direction:column;gap:16px;">

        {{-- Live preview --}}
        <div>
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.2em;
                        text-transform:uppercase;color:var(--muted);margin-bottom:10px;padding:0 2px;">
                Tenant Preview
            </div>
            <div id="preview-card" style="background:var(--surface);border:1px solid var(--border);
                                          border-left:3px solid var(--crimson);overflow:hidden;">
                <div style="padding:14px 16px;border-bottom:1px solid var(--border);
                            display:flex;align-items:center;justify-content:space-between;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span id="prev-version"
                              style="font-family:'Playfair Display',serif;font-size:16px;font-weight:700;">
                            v{{ $version->version }}
                        </span>
                        <span id="prev-type-pill" class="status-pill {{ $version->typeColor() }}">
                            {{ $version->typeIcon() }} {{ ucfirst($version->type) }}
                        </span>
                    </div>
                    <span style="font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;">
                        {{ $version->published_at?->format('M d, Y') ?? 'Not published' }}
                    </span>
                </div>
                <div style="padding:14px 16px;">
                    <div id="prev-label"
                         style="font-weight:600;font-size:13px;color:var(--text);margin-bottom:8px;">
                        {{ $version->label }}
                    </div>
                    <div id="prev-changelog"
                         style="font-size:12px;color:var(--text2);line-height:1.8;
                                white-space:pre-wrap;max-height:260px;overflow-y:auto;">{{ $version->changelog }}</div>
                </div>
            </div>
        </div>

        {{-- Read receipt stats if published --}}
        @if($version->is_published)
        @php $readCount = $version->readTenantCount(); $totalTenants = \App\Models\Tenant::where('status','active')->orWhereNull('status')->count(); @endphp
        <div style="background:var(--surface);border:1px solid var(--border);padding:16px;">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                        text-transform:uppercase;color:var(--muted);margin-bottom:12px;">
                Tenant Acknowledgment
            </div>
            <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:8px;">
                <div>
                    <span style="font-family:'Playfair Display',serif;font-size:22px;
                                 font-weight:900;color:var(--text);">{{ $readCount }}</span>
                    <span style="font-size:13px;color:var(--muted);">/ {{ $totalTenants }}</span>
                </div>
                <span style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);">
                    @if($totalTenants > 0)
                        {{ round($readCount / $totalTenants * 100) }}% read
                    @else
                        No tenants
                    @endif
                </span>
            </div>
            <div style="height:4px;background:var(--border2);overflow:hidden;">
                @if($totalTenants > 0)
                <div style="height:100%;background:#2dd4bf;
                            width:{{ min(100, round($readCount / $totalTenants * 100)) }}%;
                            transition:width 0.5s;"></div>
                @endif
            </div>
            @if($totalTenants - $readCount > 0)
            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                {{ $totalTenants - $readCount }} tenant(s) haven't read this yet
            </div>
            @else
            <div style="font-size:11px;color:#2dd4bf;margin-top:8px;">
                ✓ All tenants have acknowledged this update
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const typeColors = { patch:'teal', minor:'blue', major:'coral', hotfix:'gold' };
const typeIcons  = { patch:'🔧', minor:'✨', major:'🚀', hotfix:'🩹' };
const borderPx   = { teal:'#2dd4bf', blue:'#60a5fa', coral:'#f87171', gold:'#c9a84c' };

function updatePreview() {
    const version   = document.getElementById('inp-version').value || '—';
    const type      = document.getElementById('inp-type').value;
    const label     = document.getElementById('inp-label').value;
    const changelog = document.getElementById('inp-changelog').value;

    document.getElementById('prev-version').textContent   = 'v' + version;
    document.getElementById('prev-label').textContent     = label;
    document.getElementById('prev-changelog').textContent = changelog;

    const pill = document.getElementById('prev-type-pill');
    pill.className   = 'status-pill ' + (typeColors[type] || 'steel');
    pill.textContent = (typeIcons[type] || '') + ' ' + type.charAt(0).toUpperCase() + type.slice(1);

    document.getElementById('preview-card').style.borderLeftColor =
        borderPx[typeColors[type]] || 'var(--crimson)';
}

updatePreview();
</script>
@endpush
@endsection