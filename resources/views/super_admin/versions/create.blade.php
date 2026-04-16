@extends('layouts.superadmin-app')
@section('title', 'New Version')
@section('page-title', 'New Version')
@section('content')

<div class="card" style="max-width:720px;">
    <div class="card-header"><div class="card-title">Create Version Entry</div></div>
    <div style="padding:24px;">
        <form method="POST" action="{{ route('super_admin.versions.store') }}">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label class="form-label">Version Number <span style="color:var(--crimson)">*</span></label>
                    <input type="text" name="version" value="{{ old('version') }}"
                           class="form-input" placeholder="e.g. 1.4.0">
                    @error('version')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Type <span style="color:var(--crimson)">*</span></label>
                    <select name="type" class="form-select">
                        @foreach(['patch','minor','major','hotfix'] as $t)
                        <option value="{{ $t }}" {{ old('type')===$t?'selected':'' }}>
                            {{ ucfirst($t) }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Release Label</label>
                <input type="text" name="label" value="{{ old('label') }}"
                       class="form-input" placeholder="e.g. Calendar & Bulk Actions Release">
            </div>

            <div style="margin-bottom:24px;">
                <label class="form-label">Changelog (Markdown supported) <span style="color:var(--crimson)">*</span></label>
                <textarea name="changelog" class="form-textarea" rows="12"
                          placeholder="## What's New&#10;- Feature A&#10;- Bug fix B&#10;&#10;## Bug Fixes&#10;- ...">{{ old('changelog') }}</textarea>
                @error('changelog')<div class="form-error">{{ $message }}</div>@enderror
                <div class="form-hint">Markdown is supported. Use ## headings and - bullet lists.</div>
            </div>

            <div style="display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">Save as Draft</button>
                <a href="{{ route('super_admin.versions.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

