@extends('layouts.app')
@section('title', 'Setup 2FA')
@section('page-title', 'Setup Two-Factor Authentication')

@section('content')
<div style="max-width:480px;margin:40px auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Enable Two-Factor Authentication</div>
        </div>
        <div style="padding:24px;">

            @if(session('success'))
            <div style="color:#34d399;font-size:12px;margin-bottom:16px;padding:10px 14px;
                        border:1px solid rgba(52,211,153,0.2);background:rgba(52,211,153,0.07);">
                {{ session('success') }}
            </div>
            @endif

            <p style="font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.6;">
                Scan this QR code with your authenticator app (Google Authenticator, Authy, etc.), then enter the 6-digit code below to confirm.
            </p>

            {{-- QR Code (rendered via Google Charts API) --}}
            <div style="text-align:center;margin-bottom:24px;">
                <img src="https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl={{ urlencode($qrCodeUrl) }}"
                     alt="QR Code" style="border:4px solid var(--border);padding:8px;background:#fff;">
            </div>

            @if($errors->any())
            <div style="color:var(--crimson);font-size:12px;margin-bottom:16px;padding:10px 14px;
                        border:1px solid rgba(140,14,0.25);background:rgba(140,14,3,0.06);">
                {{ $errors->first('code') }}
            </div>
            @endif

            @if(!auth()->user()->two_factor_enabled)
            <form method="POST" action="{{ route('2fa.enable') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label class="form-label">Confirm with your code</label>
                    <input type="text" name="code" class="form-input"
                           placeholder="000000" maxlength="6"
                           autocomplete="off"
                           style="letter-spacing:0.3em;font-size:18px;text-align:center;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Enable 2FA
                </button>
            </form>
            @else
            <div style="padding:12px 14px;border:1px solid rgba(52,211,153,0.2);
                        background:rgba(52,211,153,0.07);color:#34d399;font-size:13px;margin-bottom:16px;">
                2FA is currently <strong>enabled</strong> on your account.
            </div>
            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label class="form-label">Enter code to disable</label>
                    <input type="text" name="code" class="form-input"
                           placeholder="000000" maxlength="6"
                           autocomplete="off"
                           style="letter-spacing:0.3em;font-size:18px;text-align:center;">
                </div>
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
                    Disable 2FA
                </button>
            </form>
            @endif

        </div>
    </div>
</div>
@endsection