@extends('layouts.coordinator-app')
@section('title', 'Students')
@section('page-title', 'Students')
@section('content')

<div class="card">
    <div class="card-header">
        <div class="card-title">Active Student Interns</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Program</th>
                    <th>Required Hours</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
            @forelse($students as $app)
            <tr>
                <td>
                    <div style="font-weight:600;color:var(--text);">{{ $app->student->name }}</div>
                    <div style="font-size:11.5px;color:var(--muted);">{{ $app->student->email }}</div>
                </td>
                <td>{{ $app->company->name ?? '—' }}</td>
                <td>{{ $app->program }}</td>
                <td>{{ $app->required_hours }} hrs</td>
                <td>{{ $app->semester }} {{ $app->school_year }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                    No active interns found.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--border2);">
        {{ $students->links() }}
    </div>
</div>

@endsection