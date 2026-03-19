@extends('layouts.student-app')
@section('title', 'Log Hours')
@section('page-title', 'Log Hours')
@section('content')
<div style="max-width:520px;">
    <a href="{{ route('student.hours.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--muted);font-size:13px;text-decoration:none;margin-bottom:20px;">← Back</a>
    <div class="card">
        <div class="card-header"><div class="card-title">Log Daily Hours</div></div>
        <div style="padding:24px;">
            @if($errors->any())
            <div style="background:var(--coral-dim);border:1px solid var(--coral);color:var(--coral);padding:12px;border-radius:8px;margin-bottom:16px;font-size:13px;">
                @foreach($errors->all() as $error)<div>· {{ $error }}</div>@endforeach
            </div>
            @endif
            <form method="POST" action="{{ route('student.hours.store') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Date *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}"
                        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Time In *</label>
                        <input type="time" name="time_in" value="{{ old('time_in') }}"
                            style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">Time Out *</label>
                        <input type="time" name="time_out" value="{{ old('time_out') }}"
                            style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;">
                    </div>
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:12px;font-weight:500;color:var(--muted2);margin-bottom:6px;">What did you do today? (optional)</label>
                    <textarea name="description" rows="3"
                        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;resize:vertical;font-family:inherit;">{{ old('description') }}</textarea>
                </div>
                <button type="submit" style="padding:10px 28px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Submit Log</button>
            </form>
        </div>
    </div>
</div>
@endsection
