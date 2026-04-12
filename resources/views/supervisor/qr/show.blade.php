@extends('layouts.supervisor-app')
@section('title', 'Company QR Clock-In')
@section('page-title', 'QR Clock-In Code')

@section('content')

<div class="fade-up" style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
    <span style="width:5px;height:5px;background:var(--crimson);display:inline-block;" class="flicker"></span>
    <span style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--muted);">
        QR Clock-In / {{ auth()->user()->company->name }}
    </span>
</div>

<div style="max-width:520px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">

    @if(session('success'))
    <div style="background:rgba(52,211,153,0.07);border:1px solid rgba(52,211,153,0.2);color:#34d399;padding:12px 16px;font-family:'DM Mono',monospace;font-size:12px;" class="fade-up">
        ✓ {{ session('success') }}
    </div>
    @endif

    {{-- How it works info banner --}}
    <div style="background:rgba(96,165,250,0.07);border:1px solid rgba(96,165,250,0.2);padding:14px 16px;" class="fade-up fade-up-1">
        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.12em;text-transform:uppercase;color:#60a5fa;margin-bottom:6px;">How it works</div>
        <div style="font-size:13px;color:var(--text2);line-height:1.6;">
            Print this QR and display it at your office entrance.
            Each intern scans it from their phone <strong style="color:var(--text);">while logged in</strong> to OJTConnect.
            The system identifies who they are and logs their hours automatically.
        </div>
    </div>

    {{-- QR Card --}}
    <div class="card fade-up fade-up-2" id="qr-print-card">
        <div class="card-header">
            <div class="card-title">{{ auth()->user()->company->name }}</div>
            <span class="status-pill {{ $qr->is_active ? 'green' : 'steel' }}">
                {{ $qr->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div style="padding:32px;text-align:center;">
            @if($qr->is_active)
                <div style="display:inline-block;padding:14px;background:#fff;border:1px solid var(--border2);">
                    <img src="{{ $qrImageUrl }}"
                         alt="QR Code for {{ auth()->user()->company->name }}"
                         width="240" height="240"
                         style="display:block;">
                </div>

                <div style="margin-top:20px;">
                    <div style="font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:var(--text);">
                        {{ auth()->user()->company->name }}
                    </div>
                    <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted);margin-top:6px;letter-spacing:0.06em;">
                        OJT Clock-In · Must be logged in to scan
                    </div>
                    <div style="display:flex;justify-content:center;gap:16px;margin-top:14px;">
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:#D97706;background:rgba(254,243,199,0.12);border:1px solid rgba(217,119,6,0.3);padding:3px 10px;">
                            AM · before 12:00
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:10px;color:#3B82F6;background:rgba(219,234,254,0.12);border:1px solid rgba(59,130,246,0.3);padding:3px 10px;">
                            PM · from 12:00
                        </span>
                    </div>
                </div>
            @else
                <div style="padding:40px 0;">
                    <div style="font-family:'DM Mono',monospace;font-size:12px;color:var(--muted);margin-bottom:16px;">
                        // QR code is currently deactivated
                    </div>
                    <form method="POST" action="{{ route('supervisor.qr.toggle') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-approve btn-sm">Activate QR Code</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="card fade-up fade-up-3">
        <div class="card-header">
            <div class="card-title">Actions</div>
        </div>
        <div style="padding:16px 20px;display:flex;flex-wrap:wrap;gap:10px;">

            <button onclick="printQr()" class="btn btn-ghost btn-sm">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6,9 6,2 18,2 18,9"/>
                    <path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print QR
            </button>

            <form method="POST" action="{{ route('supervisor.qr.regenerate') }}"
                  onsubmit="return confirm('This will invalidate the current QR. All interns will need to scan the new one. Continue?')">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="23,4 23,10 17,10"/>
                        <path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/>
                    </svg>
                    Regenerate Token
                </button>
            </form>

            <form method="POST" action="{{ route('supervisor.qr.toggle') }}">
                @csrf
                <button type="submit" class="btn btn-sm {{ $qr->is_active ? 'btn-danger' : 'btn-approve' }}">
                    {{ $qr->is_active ? 'Deactivate' : 'Activate' }}
                </button>
            </form>

        </div>
    </div>

    {{-- Info note --}}
    <div style="font-family:'DM Mono',monospace;font-size:10px;color:var(--muted);text-align:center;padding:8px 0;" class="fade-up fade-up-4">
        // One QR per company · All interns share the same code · Session auto-detected by time
    </div>

</div>

@push('scripts')
<script>
function printQr() {
    const content = document.getElementById('qr-print-card').innerHTML;
    const win = window.open('', '_blank');
    win.document.write(`
        <html><head><title>QR Clock-In — {{ auth()->user()->company->name }}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 60px; text-align: center; }
            img  { display: block; margin: 24px auto; }
        </style>
        </head><body>${content}</body></html>
    `);
    win.document.close();
    win.focus();
    win.print();
    win.close();
}
</script>
@endpush

@endsection