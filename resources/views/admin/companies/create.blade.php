{{-- resources/views/admin/companies/create.blade.php --}}
@extends('layouts.app')
@section('title', 'Add Company')
@section('page-title', 'Add Company')

@section('content')
<div style="max-width:600px;margin:0 auto;">

    <a href="{{ route('admin.companies.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13px;color:var(--muted2);text-decoration:none;margin-bottom:24px;transition:color 0.15s;"
       onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted2)'">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to companies
    </a>

    <div class="card fade-up">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:32px;height:32px;border-radius:8px;background:rgba(240,180,41,0.1);border:1px solid rgba(240,180,41,0.2);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                </div>
                <div>
                    <div class="card-title">Add partner company</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:1px;">Fill in the company details below.</div>
                </div>
            </div>
        </div>

        <div style="padding:24px;">

            @if($errors->any())
                <div style="background:rgba(248,113,113,0.06);border:1px solid rgba(248,113,113,0.3);color:var(--coral);padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;">
                    <div style="display:flex;align-items:center;gap:6px;font-weight:600;margin-bottom:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        Please fix the following errors:
                    </div>
                    @foreach($errors->all() as $error)<div style="margin-top:2px;">· {{ $error }}</div>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.companies.store') }}">
                @csrf

                {{-- Section header --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:16px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Company information
                </div>

                {{-- Name --}}
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">
                        Company name <span style="color:var(--coral);">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. Aboitiz Power Corporation"
                           required
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid {{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }};background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='{{ $errors->has('name') ? 'var(--coral)' : 'var(--border2)' }}'">
                    @error('name')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                {{-- Industry + Address --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Industry</label>
                        <input type="text" name="industry" value="{{ old('industry') }}"
                               placeholder="e.g. Technology, Education"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        @error('industry')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Address</label>
                        <input type="text" name="address" value="{{ old('address') }}"
                               placeholder="City, Province"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        @error('address')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Contact section --}}
                <div style="font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:16px;margin-top:24px;padding-bottom:8px;border-bottom:1px solid var(--border2);">
                    Contact details
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Contact person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                           placeholder="Full name of OJT coordinator"
                           style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                    @error('contact_person')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Contact email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email') }}"
                               placeholder="company@example.com"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        @error('contact_email')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Contact phone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone') }}"
                               placeholder="+63 900 000 0000"
                               style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;outline:none;transition:border 0.15s;box-sizing:border-box;"
                               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">
                        @error('contact_phone')<div style="font-size:11.5px;color:var(--coral);margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary btn-sm">Add company</button>
                    <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
