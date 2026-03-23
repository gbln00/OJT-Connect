@extends('layouts.coordinator-app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

{{-- Welcome bar --}}
<div style="margin-bottom:24px;">
    <div style="font-size:18px;font-weight:700;color:var(--text);letter-spacing:-0.3px;">
        Welcome back, {{ auth()->user()->name }} 👋
    </div>
    <div style="font-size:12.5px;color:var(--muted);margin-top:3px;">
        {{ now()->format('l, F d, Y') }} · OJT Coordinator Dashboard
    </div>
</div>

{{-- Stat cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px;">

    {{-- Pending Applications --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--gold-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">PENDING APPLICATIONS</div>
        <div style="font-size:32px;font-weight:800;color:var(--gold);line-height:1;">{{ $pendingApplications }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">awaiting review</div>
        <a href="{{ route('coordinator.applications.index') }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--gold);text-decoration:none;font-weight:600;">
            Review → 
        </a>
    </div>

    {{-- Active Interns --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--teal-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">ACTIVE INTERNS</div>
        <div style="font-size:32px;font-weight:800;color:var(--teal);line-height:1;">{{ $activeInterns }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">currently on OJT</div>
        <a href="{{ route('coordinator.applications.index', ['status' => 'approved']) }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--teal);text-decoration:none;font-weight:600;">
            View all →
        </a>
    </div>

    {{-- Pending Hour Logs --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--blue-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">PENDING HOUR LOGS</div>
        <div style="font-size:32px;font-weight:800;color:var(--blue);line-height:1;">{{ $pendingLogs }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">logs to approve</div>
        <a href="{{ route('coordinator.hours.index') }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--blue);text-decoration:none;font-weight:600;">
            Approve →
        </a>
    </div>

    {{-- Pending Reports --}}
    <div class="card" style="padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;border-radius:50%;background:var(--coral-dim);opacity:.5;"></div>
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:10px;">PENDING REPORTS</div>
        <div style="font-size:32px;font-weight:800;color:var(--coral);line-height:1;">{{ $pendingReports }}</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">weekly reports</div>
        <a href="{{ route('coordinator.reports.index') }}"
           style="display:inline-block;margin-top:12px;font-size:11.5px;color:var(--coral);text-decoration:none;font-weight:600;">
            Review →
        </a>
    </div>

</div>

{{-- Bottom section: recent applications + quick actions --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:16px;align-items:start;">

    {{-- Recent Pending Applications table --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="width:28px;height:28px;border-radius:7px;background:var(--gold-dim);display:flex;align-items:center;justify-content:center;color:var(--gold);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    Recent Pending Applications
                </div>
            </div>
            <a href="{{ route('coordinator.applications.index') }}"
               style="font-size:12px;color:var(--gold);text-decoration:none;font-weight:600;">
                View all
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Company</th>
                        <th>Applied</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentApplications as $app)
                <tr>
                    <td>
                        <div style="font-weight:600;font-size:13px;color:var(--text);">
                            {{ $app->student->name ?? '—' }}
                        </div>
                        <div style="font-size:11.5px;color:var(--muted);">
                            {{ $app->student->email ?? '' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;color:var(--text);">{{ $app->company->name ?? $app->company_name ?? '—' }}</div>
                    </td>
                    <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                        {{ $app->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('coordinator.applications.show', $app->id) }}"
                           style="font-size:12px;color:var(--teal);text-decoration:none;border:1px solid var(--teal);
                                  padding:4px 12px;border-radius:6px;font-weight:600;white-space:nowrap;">
                            Review
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center;padding:40px;color:var(--muted);">
                        🎉 No pending applications right now.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card" style="padding:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
            <div style="width:28px;height:28px;border-radius:7px;background:var(--teal-dim);display:flex;align-items:center;justify-content:center;color:var(--teal);">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="16"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            Quick Actions
        </div>

        <div style="display:flex;flex-direction:column;gap:10px;">

            <a href="{{ route('coordinator.applications.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--gold)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--gold-dim);display:flex;align-items:center;justify-content:center;color:var(--gold);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Applications</div>
                    <div style="font-size:11.5px;color:var(--muted);">Review & approve</div>
                </div>
                @if($pendingApplications > 0)
                <span style="margin-left:auto;padding:2px 8px;background:var(--gold-dim);color:var(--gold);
                             border-radius:20px;font-size:11px;font-weight:700;">
                    {{ $pendingApplications }}
                </span>
                @endif
            </a>

            <a href="{{ route('coordinator.hours.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--blue)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--blue-dim);display:flex;align-items:center;justify-content:center;color:var(--blue);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Hour Logs</div>
                    <div style="font-size:11.5px;color:var(--muted);">Approve submissions</div>
                </div>
                @if($pendingLogs > 0)
                <span style="margin-left:auto;padding:2px 8px;background:var(--blue-dim);color:var(--blue);
                             border-radius:20px;font-size:11px;font-weight:700;">
                    {{ $pendingLogs }}
                </span>
                @endif
            </a>

            <a href="{{ route('coordinator.reports.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--coral)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--coral-dim);display:flex;align-items:center;justify-content:center;color:var(--coral);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Weekly Reports</div>
                    <div style="font-size:11.5px;color:var(--muted);">Read & give feedback</div>
                </div>
                @if($pendingReports > 0)
                <span style="margin-left:auto;padding:2px 8px;background:var(--coral-dim);color:var(--coral);
                             border-radius:20px;font-size:11px;font-weight:700;">
                    {{ $pendingReports }}
                </span>
                @endif
            </a>

            <a href="{{ route('coordinator.students.index') }}"
               style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:8px;
                      background:var(--surface2);border:1px solid var(--border2);text-decoration:none;
                      transition:border-color .15s;"
               onmouseover="this.style.borderColor='var(--teal)'" onmouseout="this.style.borderColor='var(--border2)'">
                <div style="width:32px;height:32px;border-radius:8px;background:var(--teal-dim);display:flex;align-items:center;justify-content:center;color:var(--teal);flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                        <path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">Students</div>
                    <div style="font-size:11.5px;color:var(--muted);">View intern list</div>
                </div>
            </a>

        </div>
    </div>

</div>

@endsection