@extends('layouts.app')
@section('title', '2FA Verification')
@section('page-title', 'Two-Factor Verification')

@section('content')
<div style="max-width:400px;margin:60px auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Verify your identity</div>
        </div>
        <div style="padding:24px;">
            <p style="font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.6;">
                Enter the 6-digit code from your authenticator app to continue.
            </p>

            @if($errors->any())
            <div style="color:var(--crimson);font-size:12px;margin-bottom:16px;padding:10px 14px;
                        border:1px solid rgba(140,14,3,0.25);background:rgba(140,14,3,0.06);">
                {{ $errors->first('code') }}
            </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label class="form-label">Authenticator Code</label>
                    <input type="text" name="code" class="form-input"
                           placeholder="000000" maxlength="6"
                           autocomplete="off" autofocus
                           style="letter-spacing:0.3em;font-size:18px;text-align:center;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    Verify
                </button>
            </form>
        </div>
    </div>
</div>
@endsection