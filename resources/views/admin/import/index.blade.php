@extends('layouts.admintor-app')
@section('title', 'CSV Import')
@section('page-title', 'CSV Bulk Import')

@section('content')

@if(session('import_results'))
@php $res = session('import_results'); @endphp
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><div class="card-title">Import Results</div></div>
    <div style="padding:20px;">
        <p style="color:var(--teal);">✓ Created: {{ $res['created'] }}</p>
        <p style="color:var(--muted);">⊘ Skipped: {{ $res['skipped'] }}</p>
        @if(!empty($res['errors']))
        <ul style="margin-top:12px;color:var(--coral);font-size:13px;">
            @foreach($res['errors'] as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        @endif
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><div class="card-title">Import Students or Companies via CSV</div></div>
    <div style="padding:24px;">

        {{-- Template Downloads --}}
        <div style="margin-bottom:24px;">
            <div class="form-label">Download Templates</div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <a href="{{ route('admintor.import.template', 'students') }}"
                   class="btn btn-ghost btn-sm">⬇ Student Template</a>
                <a href="{{ route('admintor.import.template', 'companies') }}"
                   class="btn btn-ghost btn-sm">⬇ Company Template</a>
            </div>
        </div>

        {{-- Upload Form --}}
        <form method="POST" action="{{ route('admintor.import.store') }}"
              enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:16px;">
                <label class="form-label">Import Type</label>
                <select name="type" class="form-select">
                    <option value="students">Students</option>
                    <option value="companies">Companies</option>
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label class="form-label">CSV File</label>
                <input type="file" name="file" accept=".csv,.txt"
                       class="form-input">
                @error('file')
                <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Import CSV</button>
        </form>
    </div>
</div>

@endsection

