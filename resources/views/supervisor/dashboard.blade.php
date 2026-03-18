@extends('layouts.app')
@section('title', 'Supervisor Dashboard')
@section('page-title', 'Dashboard')
@section('content')
<div class="greeting">
    <div class="greeting-sub">{{ now()->format('l, F j, Y') }}</div>
    <div class="greeting-title">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }}, <span>{{ explode(' ', auth()->user()->name)[0] }}</span></div>
</div>
<div class="card" style="padding:32px;text-align:center;color:var(--muted);">
    Supervisor module coming soon.
</div>
@endsection