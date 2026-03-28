@extends('layouts.superadmin-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('super_admin.tenants.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
              bg-[#27374D] text-[#DDE6ED] hover:bg-[#1e2d40] hover:shadow-md active:scale-95">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        New Tenant
    </a>
@endsection

@php
    $pendingCount  = \App\Models\TenantRegistration::where('status','pending')->count();
    $approvedCount = \App\Models\TenantRegistration::where('status','approved')->count();
    $rejectedCount = \App\Models\TenantRegistration::where('status','rejected')->count();
    $basicCount    = \App\Models\TenantRegistration::where('plan','basic')->count();
    $standardCount = \App\Models\TenantRegistration::where('plan','standard')->count();
    $premiumCount  = \App\Models\TenantRegistration::where('plan','premium')->count();
    $totalRegs     = \App\Models\TenantRegistration::count();
@endphp

@section('content')

{{-- ── GREETING ── --}}
<div class="mb-7">
    <p class="text-sm font-medium text-[#9DB2BF] tracking-wide mb-0.5">
        {{ now()->format('l, F j, Y') }}
    </p>
    <h1 class="text-2xl font-semibold text-[#27374D]">
        Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
        <span class="text-[#526D82]">{{ explode(' ', auth()->user()->name)[0] }}</span> 👋
    </h1>
</div>

{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

    {{-- Total Tenants --}}
    <div class="bg-white rounded-xl border border-[#DDE6ED] p-5 group hover:border-[#9DB2BF] hover:shadow-sm transition-all duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-9 h-9 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-[#27374D] transition-colors duration-200">
                <svg class="w-4 h-4 text-[#526D82] group-hover:text-[#DDE6ED] transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-[#9DB2BF] bg-[#DDE6ED]/60 px-2 py-0.5 rounded-full">institutions</span>
        </div>
        <div class="text-3xl font-bold text-[#27374D] mb-1">{{ $totalTenants }}</div>
        <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider">Total tenants</div>
    </div>

    {{-- Pending Approvals --}}
    <div class="bg-white rounded-xl border {{ $pendingCount > 0 ? 'border-amber-200' : 'border-[#DDE6ED]' }} p-5 group hover:border-[#9DB2BF] hover:shadow-sm transition-all duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-9 h-9 rounded-lg {{ $pendingCount > 0 ? 'bg-amber-50' : 'bg-[#DDE6ED]' }} flex items-center justify-center group-hover:bg-amber-100 transition-colors duration-200">
                <svg class="w-4 h-4 {{ $pendingCount > 0 ? 'text-amber-500' : 'text-[#526D82]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            @if($pendingCount > 0)
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full animate-pulse">needs review</span>
            @else
                <span class="text-xs font-medium text-[#9DB2BF] bg-[#DDE6ED]/60 px-2 py-0.5 rounded-full">queue</span>
            @endif
        </div>
        <div class="text-3xl font-bold {{ $pendingCount > 0 ? 'text-amber-500' : 'text-[#27374D]' }} mb-1">{{ $pendingCount }}</div>
        <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider">Pending approvals</div>
    </div>

    {{-- Active Domains --}}
    <div class="bg-white rounded-xl border border-[#DDE6ED] p-5 group hover:border-[#9DB2BF] hover:shadow-sm transition-all duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-9 h-9 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-[#526D82] transition-colors duration-200">
                <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-[#9DB2BF] bg-[#DDE6ED]/60 px-2 py-0.5 rounded-full">active</span>
        </div>
        <div class="text-3xl font-bold text-[#27374D] mb-1">{{ $recentTenants->count() }}</div>
        <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider">Active domains</div>
    </div>

    {{-- Approved Registrations --}}
    <div class="bg-white rounded-xl border border-[#DDE6ED] p-5 group hover:border-[#9DB2BF] hover:shadow-sm transition-all duration-200">
        <div class="flex items-center justify-between mb-4">
            <div class="w-9 h-9 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-emerald-500 transition-colors duration-200">
                <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span class="text-xs font-medium text-[#9DB2BF] bg-[#DDE6ED]/60 px-2 py-0.5 rounded-full">approved</span>
        </div>
        <div class="text-3xl font-bold text-[#27374D] mb-1">{{ $approvedCount }}</div>
        <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider">Approved registrations</div>
    </div>

</div>

{{-- ── PLAN BREAKDOWN STRIP ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">

    <div class="bg-white border border-[#DDE6ED] rounded-lg px-4 py-3 flex items-center justify-between hover:border-[#9DB2BF] transition-colors">
        <div>
            <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider mb-0.5">Basic</div>
            <div class="text-base font-semibold text-[#27374D]">{{ $basicCount }} tenants</div>
        </div>
        <span class="w-7 h-7 rounded-full bg-[#DDE6ED] text-[#526D82] text-xs font-semibold flex items-center justify-center">B</span>
    </div>

    <div class="bg-white border border-[#DDE6ED] rounded-lg px-4 py-3 flex items-center justify-between hover:border-[#9DB2BF] transition-colors">
        <div>
            <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider mb-0.5">Standard</div>
            <div class="text-base font-semibold text-[#27374D]">{{ $standardCount }} tenants</div>
        </div>
        <span class="w-7 h-7 rounded-full bg-[#526D82] text-[#DDE6ED] text-xs font-semibold flex items-center justify-center">S</span>
    </div>

    <div class="bg-white border border-[#DDE6ED] rounded-lg px-4 py-3 flex items-center justify-between hover:border-[#9DB2BF] transition-colors">
        <div>
            <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider mb-0.5">Premium</div>
            <div class="text-base font-semibold text-[#27374D]">{{ $premiumCount }} tenants</div>
        </div>
        <span class="w-7 h-7 rounded-full bg-[#27374D] text-[#DDE6ED] text-xs font-semibold flex items-center justify-center">P</span>
    </div>

    <div class="bg-white border border-[#DDE6ED] rounded-lg px-4 py-3 flex items-center justify-between hover:border-[#9DB2BF] transition-colors">
        <div>
            <div class="text-xs text-[#9DB2BF] font-medium uppercase tracking-wider mb-0.5">Rejected</div>
            <div class="text-base font-semibold text-[#27374D]">{{ $rejectedCount }} total</div>
        </div>
        <span class="w-7 h-7 rounded-full bg-red-100 text-red-500 text-xs font-semibold flex items-center justify-center">✕</span>
    </div>

</div>

{{-- ── BOTTOM GRID ── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ── RECENT TENANTS TABLE (spans 2 cols) ── --}}
    <div class="xl:col-span-2 bg-white border border-[#DDE6ED] rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#DDE6ED]">
            <div>
                <h2 class="text-sm font-semibold text-[#27374D]">Recent tenants</h2>
                <p class="text-xs text-[#9DB2BF] mt-0.5">Latest provisioned environments</p>
            </div>
            <a href="{{ route('super_admin.tenants.index') }}"
               class="text-xs font-medium text-[#526D82] hover:text-[#27374D] transition-colors flex items-center gap-1">
                View all
                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[#DDE6ED]/30">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-[#526D82] uppercase tracking-wider">Tenant</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-[#526D82] uppercase tracking-wider">Domain</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-[#526D82] uppercase tracking-wider">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-[#526D82] uppercase tracking-wider">Registered</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#DDE6ED]">
                    @forelse($recentTenants as $tenant)
                    <tr class="hover:bg-[#DDE6ED]/20 transition-colors group">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#27374D] text-[#DDE6ED] flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($tenant->id, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-mono text-xs font-medium text-[#27374D]">{{ $tenant->id }}</div>
                                    @if($tenant->name ?? false)
                                        <div class="text-xs text-[#9DB2BF]">{{ $tenant->name }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @foreach($tenant->domains as $domain)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#DDE6ED]/60 text-[#526D82] font-mono text-xs border border-[#9DB2BF]/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                    {{ $domain->domain }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-[#9DB2BF]">{{ $tenant->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-10 h-10 rounded-full bg-[#DDE6ED] flex items-center justify-center mb-1">
                                    <svg class="w-5 h-5 text-[#9DB2BF]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/>
                                    </svg>
                                </div>
                                <p class="text-sm text-[#9DB2BF]">No tenants yet.</p>
                                <a href="{{ route('super_admin.tenants.create') }}" class="text-xs font-medium text-[#526D82] hover:text-[#27374D] transition-colors">
                                    Create your first tenant →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="flex flex-col gap-4">

        {{-- QUICK ACTIONS --}}
        <div class="bg-white border border-[#DDE6ED] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[#DDE6ED]">
                <h2 class="text-sm font-semibold text-[#27374D]">Quick actions</h2>
            </div>
            <div class="p-3 grid grid-cols-2 gap-2">

                <a href="{{ route('super_admin.tenants.create') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-[#DDE6ED] hover:border-[#9DB2BF] hover:bg-[#DDE6ED]/20 transition-all group text-center">
                    <div class="w-8 h-8 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-[#526D82] transition-colors">
                        <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-[#526D82]">New tenant</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-[#DDE6ED] hover:border-[#9DB2BF] hover:bg-[#DDE6ED]/20 transition-all group text-center">
                    <div class="w-8 h-8 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-[#526D82] transition-colors">
                        <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-[#526D82]">All tenants</span>
                </a>

                <a href="{{ route('super_admin.approvals.pending') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-[#DDE6ED] hover:border-[#9DB2BF] hover:bg-[#DDE6ED]/20 transition-all group text-center relative">
                    <div class="w-8 h-8 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-amber-400 transition-colors relative">
                        <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        @if($pendingCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 bg-amber-400 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $pendingCount }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-medium text-[#526D82]">Approvals</span>
                </a>

                <a href="{{ route('super_admin.tenants.index') }}"
                   class="flex flex-col items-center gap-2 p-3 rounded-lg border border-[#DDE6ED] hover:border-[#9DB2BF] hover:bg-[#DDE6ED]/20 transition-all group text-center">
                    <div class="w-8 h-8 rounded-lg bg-[#DDE6ED] flex items-center justify-center group-hover:bg-[#526D82] transition-colors">
                        <svg class="w-4 h-4 text-[#526D82] group-hover:text-white transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-[#526D82]">Domains</span>
                </a>

            </div>
        </div>

        {{-- REGISTRATION STATUS --}}
        <div class="bg-white border border-[#DDE6ED] rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-[#DDE6ED]">
                <h2 class="text-sm font-semibold text-[#27374D]">Registration status</h2>
            </div>

            {{-- Progress bar visual --}}
            @if($totalRegs > 0)
            <div class="px-5 pt-4 pb-2">
                <div class="flex rounded-full overflow-hidden h-2 bg-[#DDE6ED]">
                    @if($approvedCount > 0)
                        <div class="bg-emerald-400 transition-all" style="width:{{ round(($approvedCount / $totalRegs) * 100) }}%"></div>
                    @endif
                    @if($pendingCount > 0)
                        <div class="bg-amber-400 transition-all" style="width:{{ round(($pendingCount / $totalRegs) * 100) }}%"></div>
                    @endif
                    @if($rejectedCount > 0)
                        <div class="bg-red-400 transition-all" style="width:{{ round(($rejectedCount / $totalRegs) * 100) }}%"></div>
                    @endif
                </div>
                <div class="flex gap-3 mt-2 text-xs text-[#9DB2BF]">
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block"></span> Approved</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block"></span> Pending</span>
                    <span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block"></span> Rejected</span>
                </div>
            </div>
            @endif

            <div class="divide-y divide-[#DDE6ED]">
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-[#9DB2BF] font-medium">Total registrations</span>
                    <span class="text-sm font-semibold text-[#27374D]">{{ $totalRegs }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-[#9DB2BF] font-medium">Pending review</span>
                    <span class="text-sm font-semibold text-amber-500">{{ $pendingCount }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-[#9DB2BF] font-medium">Approved</span>
                    <span class="text-sm font-semibold text-emerald-600">{{ $approvedCount }}</span>
                </div>
                <div class="flex items-center justify-between px-5 py-3">
                    <span class="text-xs text-[#9DB2BF] font-medium">Rejected</span>
                    <span class="text-sm font-semibold text-red-500">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>

        {{-- RECENT REGISTRATIONS FEED --}}
        <div class="bg-white border border-[#DDE6ED] rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#DDE6ED]">
                <h2 class="text-sm font-semibold text-[#27374D]">Recent registrations</h2>
                <a href="{{ route('super_admin.approvals.pending') }}"
                   class="text-xs font-medium text-[#526D82] hover:text-[#27374D] transition-colors flex items-center gap-1">
                    View all
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="divide-y divide-[#DDE6ED]">
                @forelse(\App\Models\TenantRegistration::latest()->take(5)->get() as $reg)
                <div class="flex items-start gap-3 px-5 py-3.5 hover:bg-[#DDE6ED]/20 transition-colors">
                    {{-- Status dot --}}
                    <div class="mt-1.5 flex-shrink-0">
                        @if($reg->status === 'approved')
                            <span class="w-2 h-2 rounded-full bg-emerald-400 block"></span>
                        @elseif($reg->status === 'rejected')
                            <span class="w-2 h-2 rounded-full bg-red-400 block"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-amber-400 block"></span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-[#27374D] leading-snug">
                            <span class="font-semibold">{{ $reg->company_name }}</span>
                            submitted a
                            <span class="font-mono text-[#526D82] bg-[#DDE6ED]/60 px-1 py-0.5 rounded text-[11px]">{{ $reg->plan }}</span>
                            plan request.
                        </p>
                        <p class="text-[11px] text-[#9DB2BF] mt-0.5">{{ $reg->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <p class="text-xs text-[#9DB2BF]">No registrations yet.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>{{-- end right column --}}

</div>{{-- end bottom grid --}}

@endsection