{{-- resources/views/student/hours/create.blade.php --}}
@extends('layouts.student-app')

@section('title', 'Log Hours')

@section('content')
<div class="page-header">
    <h1>Log Daily Hours</h1>
    <p class="text-muted">Enter your time-in and time-out for each session today.</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('student.hours.store') }}">
    @csrf

    {{-- Date --}}
    <div class="card mb-4">
        <div class="card-body">
            <label class="form-label fw-semibold">Date</label>
            <input
                type="date"
                name="date"
                class="form-control @error('date') is-invalid @enderror"
                value="{{ old('date', today()->toDateString()) }}"
                max="{{ today()->toDateString() }}"
                required
            >
            @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Morning Session --}}
    <div class="card mb-4 session-card" id="card-morning">
        <div class="card-header d-flex align-items-center gap-3">
            <div class="session-icon morning-icon">AM</div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Morning Session</div>
                <div class="text-muted small">Typical: 8:00 AM – 12:00 PM</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input
                    class="form-check-input session-toggle"
                    type="checkbox"
                    name="log_morning"
                    id="toggle-morning"
                    value="1"
                    data-target="morning-fields"
                    {{ old('log_morning', !in_array('morning', $todayLogs)) ? 'checked' : '' }}
                    {{ in_array('morning', $todayLogs) ? 'disabled' : '' }}
                >
                <label class="form-check-label" for="toggle-morning">
                    {{ in_array('morning', $todayLogs) ? 'Already logged' : 'Log this session' }}
                </label>
            </div>
        </div>

        @if (in_array('morning', $todayLogs))
            <div class="card-body text-muted">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                Morning session already submitted for today.
            </div>
        @else
            <div class="card-body" id="morning-fields">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Time In</label>
                        <input
                            type="time"
                            name="am_time_in"
                            class="form-control @error('am_time_in') is-invalid @enderror"
                            value="{{ old('am_time_in', '08:00') }}"
                        >
                        @error('am_time_in')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Time Out</label>
                        <input
                            type="time"
                            name="am_time_out"
                            class="form-control @error('am_time_out') is-invalid @enderror"
                            value="{{ old('am_time_out', '12:00') }}"
                        >
                        @error('am_time_out')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Activities / Description <span class="text-muted">(optional)</span></label>
                        <textarea
                            name="am_description"
                            class="form-control"
                            rows="2"
                            placeholder="What did you work on this morning?"
                        >{{ old('am_description') }}</textarea>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Afternoon Session --}}
    <div class="card mb-4 session-card" id="card-afternoon">
        <div class="card-header d-flex align-items-center gap-3">
            <div class="session-icon afternoon-icon">PM</div>
            <div class="flex-grow-1">
                <div class="fw-semibold">Afternoon Session</div>
                <div class="text-muted small">Typical: 1:00 PM – 5:00 PM</div>
            </div>
            <div class="form-check form-switch mb-0">
                <input
                    class="form-check-input session-toggle"
                    type="checkbox"
                    name="log_afternoon"
                    id="toggle-afternoon"
                    value="1"
                    data-target="afternoon-fields"
                    {{ old('log_afternoon', !in_array('afternoon', $todayLogs)) ? 'checked' : '' }}
                    {{ in_array('afternoon', $todayLogs) ? 'disabled' : '' }}
                >
                <label class="form-check-label" for="toggle-afternoon">
                    {{ in_array('afternoon', $todayLogs) ? 'Already logged' : 'Log this session' }}
                </label>
            </div>
        </div>

        @if (in_array('afternoon', $todayLogs))
            <div class="card-body text-muted">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                Afternoon session already submitted for today.
            </div>
        @else
            <div class="card-body" id="afternoon-fields">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label">Time In</label>
                        <input
                            type="time"
                            name="pm_time_in"
                            class="form-control @error('pm_time_in') is-invalid @enderror"
                            value="{{ old('pm_time_in', '13:00') }}"
                        >
                        @error('pm_time_in')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Time Out</label>
                        <input
                            type="time"
                            name="pm_out"
                            class="form-control @error('pm_time_out') is-invalid @enderror"
                            value="{{ old('pm_time_out', '17:00') }}"
                        >
                        @error('pm_time_out')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Activities / Description <span class="text-muted">(optional)</span></label>
                        <textarea
                            name="pm_description"
                            class="form-control"
                            rows="2"
                            placeholder="What did you work on this afternoon?"
                        >{{ old('pm_description') }}</textarea>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Submit Hour Log</button>
        <a href="{{ route('student.hours.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>

<style>
.session-card .card-header {
    background: var(--bs-body-bg);
    border-bottom: 1px solid var(--bs-border-color);
    padding: 1rem 1.25rem;
}
.session-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}
.morning-icon   { background: #FEF3C7; color: #92400E; }
.afternoon-icon { background: #DBEAFE; color: #1E40AF; }
</style>

<script>
document.querySelectorAll('.session-toggle').forEach(function (toggle) {
    var targetId = toggle.dataset.target;
    var fields   = document.getElementById(targetId);

    function sync() {
        if (fields) fields.style.display = toggle.checked ? '' : 'none';
    }

    sync();
    toggle.addEventListener('change', sync);
});
</script>
@endsection