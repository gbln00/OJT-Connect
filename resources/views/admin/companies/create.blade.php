{{-- resources/views/admin/companies/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Company')
@section('page-title', 'Add Company')

@section('content')
<div style="max-width:580px;">
    <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom:20px;display:inline-flex;">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to companies
    </a>

    <div class="card fade-up">
        <div class="card-header">
            <div class="card-title">Add partner company</div>
        </div>
        <div style="padding:24px;">

            @if($errors->any())
                <div style="background:rgba(140,14,3,0.08);border:1px solid rgba(140,14,3,0.25);color:var(--crimson);padding:12px 16px;margin-bottom:20px;font-size:13px;">
                    @foreach($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.companies.store') }}">
                @csrf

                @foreach([
                    ['name', 'Company name', 'text', 'e.g. Aboitiz Power Corporation', true],
                    ['industry', 'Industry', 'text', 'e.g. Technology, Education, Government', false],
                    ['address', 'Address', 'text', 'City, Province', false],
                    ['contact_person', 'Contact person', 'text', 'Full name of OJT coordinator', false],
                    ['contact_email', 'Contact email', 'email', 'company@example.com', false],
                    ['contact_phone', 'Contact phone', 'text', '+63 900 000 0000', false],
                ] as [$field, $lbl, $type, $placeholder, $required])
                <div style="margin-bottom:16px;">
                    <label class="form-label">
                        {{ $lbl }} @if($required)<span style="color:var(--crimson);">*</span>@endif
                    </label>
                    <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field) }}"
                           placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                           class="form-input"
                           style="{{ $errors->has($field) ? 'border-color:var(--crimson);' : '' }}">
                    @error($field)<div class="form-error">{{ $message }}</div>@enderror
                </div>
                @endforeach

                <div style="display:flex;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary btn-sm">Add company</button>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection