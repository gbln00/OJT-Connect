@extends('layouts.app')
@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
<div style="max-width:640px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Eyebrow --}}
    <div class="fade-up" style="display:flex;align-items:center;gap:8px;">
        <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
        <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
            Companies / Edit
        </span>
    </div>

    <div class="card fade-up fade-up-1">

        <div class="card-header">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:40px;height:40px;flex-shrink:0;border:1px solid rgba(140,14,3,0.35);background:rgba(140,14,3,0.07);display:flex;align-items:center;justify-content:center;font-family:'Playfair Display',serif;font-size:14px;font-weight:900;color:var(--crimson);">
                    {{ strtoupper(substr($company->name, 0, 2)) }}
                </div>
                <div>
                    <div class="card-title">{{ $company->name }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:3px;">
                        // Editing company profile and contact details
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.companies.update', $company) }}" style="padding:24px;">
            @csrf @method('PUT')

            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.3);color:var(--crimson);padding:13px 16px;margin-bottom:24px;">
                <strong style="display:block;margin-bottom:6px;font-family:'Barlow Condensed',sans-serif;letter-spacing:0.08em;text-transform:uppercase;font-size:11px;">
                    Please fix the following:
                </strong>
                @foreach($errors->all() as $error)
                    <div style="margin-top:3px;font-size:12.5px;">· {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <div class="form-section-divider"><span>Company Information</span></div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Company name <span style="color:var(--crimson);">✦</span></label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}"
                       required
                       class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $company->industry) }}"
                           class="form-input {{ $errors->has('industry') ? 'is-invalid' : '' }}">
                    @error('industry')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Address</label>
                    <input type="text" name="address" value="{{ old('address', $company->address) }}"
                           class="form-input {{ $errors->has('address') ? 'is-invalid' : '' }}">
                    @error('address')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-section-divider"><span>Contact Details</span></div>

            <div style="margin-bottom:16px;">
                <label class="form-label">Contact person</label>
                <input type="text" name="contact_person" value="{{ old('contact_person', $company->contact_person) }}"
                       class="form-input {{ $errors->has('contact_person') ? 'is-invalid' : '' }}">
                @error('contact_person')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:28px;">
                <div>
                    <label class="form-label">Contact email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $company->contact_email) }}"
                           class="form-input {{ $errors->has('contact_email') ? 'is-invalid' : '' }}">
                    @error('contact_email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Contact phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $company->contact_phone) }}"
                           class="form-input {{ $errors->has('contact_phone') ? 'is-invalid' : '' }}">
                    @error('contact_phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:4px;">
                <p class="form-hint">Fields marked <span style="color:var(--crimson);">✦</span> are required.</p>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-sm">Save changes</button>
                </div>
            </div>

        </form>
    </div>

</div>

@push('styles')
<style>
.form-section-divider {
    display: flex; align-items: center; gap: 14px; margin-bottom: 16px;
}
.form-section-divider::before { content: ''; width: 20px; height: 2px; background: var(--crimson); flex-shrink: 0; }
.form-section-divider::after  { content: ''; flex: 1; height: 1px; background: var(--border); }
.form-section-divider span {
    font-family: 'Barlow Condensed', sans-serif; font-size: 10px; font-weight: 600;
    letter-spacing: 0.22em; text-transform: uppercase; color: var(--muted);
}
.form-input {
    width: 100%; padding: 10px 14px;
    background: var(--surface2); border: 1px solid var(--border2);
    color: var(--text); font-size: 13px; font-family: 'Barlow', sans-serif;
    outline: none; transition: border-color 0.15s;
    box-sizing: border-box; border-radius: 0;
}
.form-input:focus { border-color: var(--crimson); }
.form-input.is-invalid { border-color: var(--crimson); }
.form-label {
    display: block; font-family: 'DM Mono', monospace;
    font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase;
    color: var(--muted); margin-bottom: 6px;
}
.form-hint  { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--muted); letter-spacing: 0.04em; }
.form-error { font-family: 'DM Mono', monospace; font-size: 10px; color: var(--crimson); margin-top: 4px; letter-spacing: 0.04em; }
</style>
@endpush
@endsection