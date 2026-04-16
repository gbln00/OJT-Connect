@extends('layouts.superadmin-app')
@section('title', 'System Versions')
@section('page-title', 'Version Control')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <div>
        <h2 style="font-family:'Playfair Display',serif;">Changelog Versions</h2>
        <p style="color:var(--muted);font-size:13px;">Manage and publish system version changelogs</p>
    </div>
    <a href="{{ route('super_admin.versions.create') }}" class="btn btn-primary">
        + New Version
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr>
                <th>Version</th><th>Type</th><th>Label</th>
                <th>Status</th><th>Published</th><th>Actions</th>
            </tr></thead>
            <tbody>
                @forelse($versions as $v)
                <tr>
                    <td><strong>v{{ $v->version }}</strong></td>
                    <td>
                        <span class="status-pill {{ $v->typeColor() }}">
                            {{ ucfirst($v->type) }}
                        </span>
                    </td>
                    <td>{{ $v->label ?? '—' }}</td>
                    <td>
                        @if($v->is_published)
                            <span class="status-pill teal">Published</span>
                        @else
                            <span class="status-pill steel">Draft</span>
                        @endif
                    </td>
                    <td>{{ $v->published_at?->format('M d, Y') ?? '—' }}</td>
                    <td style="display:flex;gap:6px;">
                        <a href="{{ route('super_admin.versions.edit', $v) }}"
                           class="btn btn-ghost btn-sm">Edit</a>
                        @if(!$v->is_published)
                        <form method="POST"
                              action="{{ route('super_admin.versions.publish', $v) }}">
                            @csrf
                            <button class="btn btn-approve btn-sm">Publish</button>
                        </form>
                        @endif
                        <form method="POST"
                              action="{{ route('super_admin.versions.destroy', $v) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this version?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px;">
                    No versions yet. Create the first one.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $versions->links() }}
    </div>
</div>
@endsection