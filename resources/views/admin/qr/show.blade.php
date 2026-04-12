@extends('layouts.app')
@section('title', 'QR Code — ' . $application->student->name)
@section('page-title', 'QR Clock-In Code')

@section('content')

{{-- Eyebrow --}}
<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        QR Clock-In / {{ $application->student->name }}
    </span>
</div>

<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.2);color:#34d399;padding:12px 16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;" class="fade-up fade-up-1">
        <div>
            <div style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--text);">{{ $application->student->name }}</div>
            <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:3px;">{{ $application->company->name }}</div>
        </div>
        <a href="{{ route('admin.qr.index') }}" class="btn btn-ghost btn-sm">← Back</a>
    </div>

    {{-- QR Card --}}
    <div class="card fade-up fade-up-2" id="qr-print-card">
        <div class="card-header">
            <div class="card-title">Clock-In QR Code</div>
            <span class="status-pill {{ $qr->is_active ? 'green' : 'steel' }}">
                {{ $qr->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div style="padding:32px;text-align:center;">
            @if($qr->is_active)
                {{-- QR Image via qrserver.com — free, no API key needed --}}
                <div style="display:inline-block;padding:12px;background:#fff;border:1px solid var(--border2);">
                    <img src="{{ $qrImageUrl }}"
                         alt="QR Code for {{ $application->student->name }}"
                         width="220" height="220"
                         style="display:block;">
                </div>
                <div style="margin-top:20px;">
                    <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:var(--text);">{{ $application->student->name }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:4px;">{{ $application->company->name }}</div>
                    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);margin-top:8px;letter-spacing:0.06em;">
                        Scan to clock in · AM before 12:00 · PM from 12:00
                    </div>
                </div>
            @else
                <div style="padding:32px 0;">
                    <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);margin-bottom:12px;">// QR code is currently deactivated</div>
                    <form method="POST" action="{{ route('admin.qr.toggle', $application) }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-approve btn-sm">Activate QR Code</button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Meta info --}}
        <div style="padding:16px 20px;border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div class="form-label">Semester</div>
                <div style="font-size:13px;color:var(--text);">{{ $application->semester }}</div>
            </div>
            <div>
                <div class="form-label">School Year</div>
                <div style="font-size:13px;color:var(--text);">{{ $application->school_year }}</div>
            </div>
            <div>
                <div class="form-label">Last Scanned</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);">
                    {{ $qr->last_scanned_at?->format('M d, Y h:i A') ?? 'Never' }}
                </div>
            </div>
            <div>
                <div class="form-label">Token Created</div>
                <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);">
                    {{ $qr->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="card fade-up fade-up-3">
        <div class="card-header">
            <div class="card-title">Actions</div>
        </div>
        <div style="padding:16px 20px;display:flex;flex-wrap:wrap;gap:10px;">

            {{-- Print --}}
            <button onclick="printQr()" class="btn btn-ghost btn-sm">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6,9 6,2 18,2 18,9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print QR
            </button>

            {{-- Regenerate --}}
            <form method="POST" action="{{ route('admin.qr.regenerate', $application) }}"
                  onsubmit="return confirm('Regenerate will invalidate the current QR code. Continue?')">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="23,4 23,10 17,10"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                    </svg>
                    Regenerate Token
                </button>
            </form>

            {{-- Toggle --}}
            <form method="POST" action="{{ route('admin.qr.toggle', $application) }}">
                @csrf
                <button type="submit" class="btn btn-sm {{ $qr->is_active ? 'btn-danger' : 'btn-approve' }}">
                    {{ $qr->is_active ? 'Deactivate QR' : 'Activate QR' }}
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function printQr() {
    const card = document.getElementById('qr-print-card').innerHTML;
    const win  = window.open('', '_blank');
    win.document.write(`
        <html><head><title>QR Code — {{ $application->student->name }}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
            img  { display: block; margin: 20px auto; }
        </style>
        </head><body>${card}</body></html>
    `);
    win.document.close();
    win.focus();
    win.print();
    win.close();
}
</script>
@endpush

@endsection