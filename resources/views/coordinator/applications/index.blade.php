@extends('layouts.coordinator-app')
@section('title', 'Applications')
@section('page-title', 'Applications')
@section('content')

{{-- Stat tabs --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
    @foreach([
        ['label'=>'Total',    'key'=>'total',    'color'=>'var(--muted2)',  'bg'=>'var(--surface2)'],
        ['label'=>'Pending',  'key'=>'pending',  'color'=>'var(--gold)',   'bg'=>'var(--gold-dim)'],
        ['label'=>'Approved', 'key'=>'approved', 'color'=>'var(--teal)',   'bg'=>'var(--teal-dim)'],
        ['label'=>'Rejected', 'key'=>'rejected', 'color'=>'var(--coral)',  'bg'=>'var(--coral-dim)'],
    ] as $s)
    <a href="{{ route('coordinator.applications.index', ['status' => $s['key'] === 'total' ? '' : $s['key']]) }}"
       style="padding:16px 18px;border-radius:10px;border:1px solid var(--border2);background:var(--surface);
              text-decoration:none;display:block;
              {{ request('status') === $s['key'] || ($s['key']==='total' && !request('status')) ? 'border-color:'.$s['color'].';background:'.$s['bg'].';' : '' }}">
        <div style="font-size:11px;font-weight:600;color:var(--muted);letter-spacing:.5px;margin-bottom:6px;">
            {{ strtoupper($s['label']) }}
        </div>
        <div style="font-size:26px;font-weight:800;color:{{ $s['color'] }};line-height:1;">
            {{ $counts[$s['key']] }}
        </div>
    </a>
    @endforeach
</div>

{{-- Search + filter bar --}}
<div class="card" style="padding:16px;margin-bottom:16px;">
    <form method="GET" action="{{ route('coordinator.applications.index') }}"
          style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search student name…"
               style="flex:1;min-width:200px;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);
                      background:var(--surface2);color:var(--text);font-size:13px;outline:none;font-family:inherit;"
               onfocus="this.style.borderColor='var(--gold)'" onblur="this.style.borderColor='var(--border2)'">

        <select name="status"
                style="padding:8px 12px;border-radius:8px;border:1px solid var(--border2);background:var(--surface2);
                       color:var(--text);font-size:13px;outline:none;font-family:inherit;cursor:pointer;">
            <option value="">All statuses</option>
            <option value="pending"  {{ request('status')==='pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit"
                style="padding:8px 18px;background:var(--gold);color:var(--bg);border:none;border-radius:8px;
                       font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
            Filter
        </button>

        @if(request('search') || request('status'))
        <a href="{{ route('coordinator.applications.index') }}"
           style="padding:8px 14px;border:1px solid var(--border2);border-radius:8px;font-size:13px;
                  color:var(--muted);text-decoration:none;">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Required Hours</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @forelse($applications as $app)
            <tr>
                {{-- Student --}}
                <td>
                    <div style="font-weight:600;font-size:13px;color:var(--text);">
                        {{ $app->student->name ?? '—' }}
                    </div>
                    <div style="font-size:11.5px;color:var(--muted);">
                        {{ $app->student->email ?? '' }}
                    </div>
                </td>

                {{-- Company --}}
                <td>
                    <div style="font-size:13px;color:var(--text);">
                        {{ $app->company->name ?? $app->company_name ?? '—' }}
                    </div>
                    <div style="font-size:11.5px;color:var(--muted);">
                        {{ $app->company->address ?? '' }}
                    </div>
                </td>

                {{-- Hours --}}
                <td style="font-size:13px;color:var(--text);font-weight:600;">
                    {{ $app->required_hours ?? '—' }} hrs
                </td>

                {{-- Date --}}
                <td style="font-size:12px;color:var(--muted);white-space:nowrap;">
                    {{ $app->created_at->format('M d, Y') }}
                </td>

                {{-- Status badge --}}
                <td>
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;
                        background:var(--{{ $app->status === 'approved' ? 'teal' : ($app->status === 'rejected' ? 'coral' : 'gold') }}-dim);
                        color:var(--{{ $app->status === 'approved' ? 'teal' : ($app->status === 'rejected' ? 'coral' : 'gold') }});">
                        {{ ucfirst($app->status) }}
                    </span>
                </td>

                {{-- Actions --}}
                <td>
                    <a href="{{ route('coordinator.applications.show', $app->id) }}"
                           style="font-size:12px;color:var(--teal);text-decoration:none;border:1px solid var(--teal);
                                  padding:4px 12px;border-radius:6px;font-weight:600;white-space:nowrap;">
                            Review
                        </a>
                </td>
            </tr>

            <!-- {{-- Approve modal --}}
            @if($app->status === 'pending')
            <tr id="approve-{{ $app->id }}" style="display:none;">
                <td colspan="6" style="padding:0;border:none;">
                    <div style="background:var(--teal-dim);border:1px solid rgba(45,212,191,0.25);border-radius:8px;
                                padding:16px 20px;margin:4px 0;">
                        <div style="font-size:13px;font-weight:600;color:var(--teal);margin-bottom:10px;">
                            ✓ Approve — {{ $app->student->name }}
                        </div>
                        <form method="POST" action="{{ route('coordinator.applications.approve', $app->id) }}"
                              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                            @csrf
                            <div style="flex:1;min-width:240px;">
                                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.5px;">
                                    REMARKS <span style="font-weight:400;">(optional)</span>
                                </label>
                                <input type="text" name="remarks" placeholder="Add a note…"
                                       style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid var(--border2);
                                              background:var(--surface);color:var(--text);font-size:13px;outline:none;
                                              font-family:inherit;box-sizing:border-box;">
                            </div>
                            <button type="submit"
                                    style="padding:8px 20px;background:var(--teal);color:var(--bg);border:none;
                                           border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                                Confirm Approve
                            </button>
                            <button type="button" onclick="closeModal('approve-{{ $app->id }}')"
                                    style="padding:8px 14px;background:none;border:1px solid var(--border2);color:var(--muted);
                                           border-radius:8px;font-size:13px;cursor:pointer;font-family:inherit;">
                                Cancel
                            </button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- Reject modal --}}
            <tr id="reject-{{ $app->id }}" style="display:none;">
                <td colspan="6" style="padding:0;border:none;">
                    <div style="background:var(--coral-dim);border:1px solid rgba(248,113,113,0.25);border-radius:8px;
                                padding:16px 20px;margin:4px 0;">
                        <div style="font-size:13px;font-weight:600;color:var(--coral);margin-bottom:10px;">
                            ✕ Reject — {{ $app->student->name }}
                        </div>
                        <form method="POST" action="{{ route('coordinator.applications.reject', $app->id) }}"
                              style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;">
                            @csrf
                            <div style="flex:1;min-width:240px;">
                                <label style="display:block;font-size:11px;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.5px;">
                                    REASON FOR REJECTION <span style="color:var(--coral);">*</span>
                                </label>
                                <input type="text" name="remarks" placeholder="State the reason…" required
                                       style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid rgba(248,113,113,0.4);
                                              background:var(--surface);color:var(--text);font-size:13px;outline:none;
                                              font-family:inherit;box-sizing:border-box;">
                            </div>
                            <button type="submit"
                                    style="padding:8px 20px;background:var(--coral);color:var(--bg);border:none;
                                           border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;">
                                Confirm Reject
                            </button>
                            <button type="button" onclick="closeModal('reject-{{ $app->id }}')"
                                    style="padding:8px 14px;background:none;border:1px solid var(--border2);color:var(--muted);
                                           border-radius:8px;font-size:13px;cursor:pointer;font-family:inherit;">
                                Cancel
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endif -->

            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:50px;color:var(--muted);">
                    <div style="font-size:28px;margin-bottom:10px;">📋</div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;">No applications found</div>
                    <div style="font-size:12px;">Try adjusting your filters.</div>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="padding:16px 20px;border-top:1px solid var(--border2);">
        {{ $applications->links() }}
    </div>
</div>

<script>
    function openModal(id) {
        // Close any other open modals first
        document.querySelectorAll('tr[id^="approve-"], tr[id^="reject-"]').forEach(el => {
            el.style.display = 'none';
        });
        document.getElementById(id).style.display = 'table-row';
        document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>

@endsection