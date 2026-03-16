@extends('layouts.app')
@section('title', 'Edit Company')
@section('page-title', 'Edit Company')

@section('content')
<div style="max-width:560px;">
    <a href="{{ route('admin.companies.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to companies
    </a>

    <div class="card">
        <div class="card-header"><div class="card-title">Edit — {{ $company->name }}</div></div>
        <div style="padding:20px;">

            @if($errors->any())
                <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                    @foreach($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.companies.update', $company) }}">
                @csrf @method('PUT')

                @foreach([
                    ['name', 'Company name', 'text', true],
                    ['industry', 'Industry', 'text', false],
                    ['address', 'Address', 'text', false],
                    ['contact_person', 'Contact person', 'text', false],
                    ['contact_email', 'Contact email', 'email', false],
                    ['contact_phone', 'Contact phone', 'text', false],
                ] as [$field, $lbl, $type, $required])
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        {{ $lbl }} @if($required)<span style="color:var(--coral);">*</span>@endif
                    </label>
                    <input type="{{ $type }}" name="{{ $field }}"
                           value="{{ old($field, $company->$field) }}"
                           {{ $required ? 'required' : '' }}
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>
                @endforeach

                <div style="display:flex;gap:10px;margin-top:8px;">
                    <button type="submit" style="padding:10px 24px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;">
                        Save changes
                    </button>
                    <a href="{{ route('admin.companies.index') }}" style="padding:10px 20px;border:1px solid var(--border2);border-radius:8px;color:var(--muted2);font-size:13px;text-decoration:none;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection