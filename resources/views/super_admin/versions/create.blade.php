@extends('layouts.superadmin-app')
@section('title', 'New Version')
@section('page-title', 'New Version')

@section('content')

<div style="display:grid;grid-template-columns:1fr 400px;gap:20px;align-items:flex-start;">

    {{-- ── Left: Form ── --}}
    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 7h.01M3 3h8l10 10a2 2 0 010 2.828l-5.172 5.172a2 2 0 01-2.828 0L3 11V3z"/>
                </svg>
                <div class="card-title">Create New Version</div>
            </div>
            <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.1em;
                         text-transform:uppercase;color:var(--muted);
                         padding:2px 8px;border:1px solid var(--border2);">Draft</span>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('super_admin.versions.store') }}" id="version-form">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div>
                        <label class="form-label">
                            Version Number
                            <span style="color:var(--crimson)">*</span>
                        </label>
                        <input type="text" name="version" id="inp-version"
                               value="{{ old('version') }}"
                               class="form-input" placeholder="e.g. 1.4.0"
                               oninput="updatePreview()">
                        @error('version')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                        <div class="form-hint">Use semantic versioning: MAJOR.MINOR.PATCH</div>
                    </div>
                    <div>
                        <label class="form-label">
                            Release Type
                            <span style="color:var(--crimson)">*</span>
                        </label>
                        <select name="type" id="inp-type" class="form-select" onchange="updatePreview()">
                            @foreach(['patch' => '🔧 Patch', 'minor' => '✨ Minor', 'major' => '🚀 Major', 'hotfix' => '🩹 Hotfix'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-hint">Affects the badge color shown to tenants</div>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="form-label">Release Label</label>
                    <input type="text" name="label" id="inp-label"
                           value="{{ old('label') }}"
                           class="form-input"
                           placeholder="e.g. Calendar & Bulk Actions Release"
                           oninput="updatePreview()">
                    <div class="form-hint">Short headline shown to admins. Leave blank to use default.</div>
                </div>

                <div style="margin-bottom:24px;">
                    <label class="form-label">
                        Changelog
                        <span style="color:var(--crimson)">*</span>
                        <span style="color:var(--muted);margin-left:4px;">(plain text, one item per line)</span>
                    </label>
                    <textarea name="changelog" id="inp-changelog"
                              class="form-textarea" rows="14"
                              placeholder="## What's New&#10;- Feature A added&#10;- Feature B improved&#10;&#10;## Bug Fixes&#10;- Fixed issue with hour logs&#10;- Fixed export error"
                              oninput="updatePreview()">{{ old('changelog') }}</textarea>
                    @error('changelog')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                    <div class="form-hint">
                        Use ## for section headings and - for bullet points.
                        This is displayed as-is to tenant admins.
                    </div>
                </div>

                <div style="display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/>
                            <polyline points="17,21 17,13 7,13 7,21"/>
                            <polyline points="7,3 7,8 15,8"/>
                        </svg>
                        Save as Draft
                    </button>
                    <a href="{{ route('super_admin.versions.index') }}" class="btn btn-ghost">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Right: Live Preview ── --}}
    <div style="position:sticky;top:90px;">
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
                        v—
                    </span>
                    <span id="prev-type-pill"
                          class="status-pill steel">—</span>
                    <span class="status-pill gold">New</span>
                </div>
                <span style="font-size:11px;color:var(--muted);font-family:'DM Mono',monospace;">
                    Today
                </span>
            </div>
            <div style="padding:14px 16px;">
                <div id="prev-label"
                     style="font-weight:600;font-size:13px;color:var(--text);margin-bottom:8px;"></div>
                <div id="prev-changelog"
                     style="font-size:12px;color:var(--text2);line-height:1.8;
                            white-space:pre-wrap;max-height:300px;overflow-y:auto;"></div>
            </div>
        </div>

        {{-- Type guide --}}
        <div style="margin-top:16px;background:var(--surface);border:1px solid var(--border);padding:14px 16px;">
            <div style="font-family:'DM Mono',monospace;font-size:9px;letter-spacing:0.15em;
                        text-transform:uppercase;color:var(--muted);margin-bottom:10px;">
                Type Guide
            </div>
            @foreach(['major' => ['coral','🚀','Breaking changes or large feature sets'], 'minor' => ['blue','✨','New features, no breaking changes'], 'patch' => ['teal','🔧','Small fixes and improvements'], 'hotfix' => ['gold','🩹','Critical emergency fix']] as $type => $info)
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:7px;">
                <span style="font-size:12px;">{{ $info[1] }}</span>
                <span class="status-pill {{ $info[0] }}" style="font-size:9px;">{{ ucfirst($type) }}</span>
                <span style="font-size:11px;color:var(--muted);">{{ $info[2] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
const typeColors = {
    patch:  'teal',
    minor:  'blue',
    major:  'coral',
    hotfix: 'gold',
};
const typeIcons = {
    patch: '🔧', minor: '✨', major: '🚀', hotfix: '🩹',
};

function updatePreview() {
    const version  = document.getElementById('inp-version').value || '—';
    const type     = document.getElementById('inp-type').value;
    const label    = document.getElementById('inp-label').value;
    const changelog = document.getElementById('inp-changelog').value;

    document.getElementById('prev-version').textContent  = 'v' + version;
    document.getElementById('prev-label').textContent    = label;
    document.getElementById('prev-changelog').textContent = changelog;

    const pill = document.getElementById('prev-type-pill');
    pill.className = 'status-pill ' + (typeColors[type] || 'steel');
    pill.textContent = (typeIcons[type] || '') + ' ' + type.charAt(0).toUpperCase() + type.slice(1);

    // Left border color by type
    const borderColors = { coral:'#f87171', blue:'#60a5fa', teal:'#2dd4bf', gold:'#c9a84c', steel:'var(--border2)' };
    document.getElementById('preview-card').style.borderLeftColor = borderColors[typeColors[type]] || 'var(--crimson)';
}

updatePreview();
</script>
@endpush
@endsection