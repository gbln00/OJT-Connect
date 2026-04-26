@extends($layout . '.app')

@section('title', 'New Support Ticket')
@section('page-title', 'New Support Ticket')

@section('content')

<div class="fade-up" style="max-width:700px;margin:0 auto;display:flex;flex-direction:column;gap:12px;">



    {{-- Back link --}}
    @php
        $indexRoute = match(true) {
            request()->routeIs('admin.*')       => 'admin.support.index',
            request()->routeIs('coordinator.*') => 'coordinator.support.index',
            request()->routeIs('supervisor.*')  => 'supervisor.support.index',
            default                             => 'student.support.index',
        };
        $storeRoute = match(true) {
            request()->routeIs('admin.*')       => 'admin.support.store',
            request()->routeIs('coordinator.*') => 'coordinator.support.store',
            request()->routeIs('supervisor.*')  => 'supervisor.support.store',
            default                             => 'student.support.store',
        };
    @endphp

    {{-- Header --}}
    <div style="margin-bottom:24px;">
        <div style="font-family:'DM Mono',monospace;font-size:10px;letter-spacing:0.18em;
                    text-transform:uppercase;color:var(--muted);margin-bottom:6px;">
            // New Ticket
        </div>
        <h1 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:900;color:var(--text);">
            Submit a <span style="color:var(--crimson);font-style:italic;">Support Request</span>
        </h1>
        <p style="font-size:13px;color:var(--muted);margin-top:6px;">
            Describe your issue or feedback in as much detail as possible.
            We typically respond within 1–2 business days.
        </p>
        
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Ticket Details</span>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">
                <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
                Back
            </a>
        </div>

        

        <form method="POST" action="{{ route($storeRoute) }}" style="padding:24px;">
            @csrf

            {{-- Validation errors --}}
            @if($errors->any())
            <div style="background:rgba(140,14,3,0.07);border:1px solid rgba(140,14,3,0.25);
                        padding:12px 16px;margin-bottom:20px;">
                <ul style="margin:0;padding:0 0 0 16px;font-size:13px;color:var(--crimson);">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Subject --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}"
                       class="form-input @error('subject') error @enderror"
                       placeholder="Brief summary of your issue or request"
                       maxlength="255" required>
                @error('subject')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type + Priority (2-column) --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="">— Select type —</option>
                        @foreach([
                            'bug'             => '🐛 Bug Report',
                            'feature_request' => '✨ Feature Request',
                            'general_inquiry' => '💬 General Inquiry',
                            'billing'         => '💳 Billing',
                            'account'         => '👤 Account',
                            'other'           => '📋 Other',
                        ] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="form-label">Priority *</label>
                    <select name="priority" class="form-select" required>
                        <option value="low"    {{ old('priority', 'normal') === 'low'    ? 'selected' : '' }}>Low — Can wait</option>
                        <option value="normal" {{ old('priority', 'normal') === 'normal' ? 'selected' : '' }}>Normal — Needs attention</option>
                        <option value="high"   {{ old('priority', 'normal') === 'high'   ? 'selected' : '' }}>High — Affecting work</option>
                        <option value="urgent" {{ old('priority', 'normal') === 'urgent' ? 'selected' : '' }}>Urgent — System down</option>
                    </select>
                    @error('priority')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Module (optional) --}}
            <div style="margin-bottom:18px;">
                <label class="form-label">Related Module <span style="color:var(--muted);font-weight:400;">(optional)</span></label>
                <select name="module" class="form-select">
                    <option value="">— Not specific to a module —</option>
                    @foreach([
                        'applications'   => 'OJT Applications',
                        'hour_logs'      => 'Hour Logs',
                        'weekly_reports' => 'Weekly Reports',
                        'evaluations'    => 'Evaluations',
                        'users'          => 'User Management',
                        'companies'      => 'Companies',
                        'notifications'  => 'Notifications',
                        'exports'        => 'Exports / Reports',
                        'customization'  => 'Customization',
                        'qr_clock_in'    => 'QR Clock-In',
                        'login'          => 'Login / Authentication',
                        'other'          => 'Other',
                    ] as $val => $label)
                        <option value="{{ $val }}" {{ old('module') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Message --}}
            <div style="margin-bottom:24px;">
                <label class="form-label">Message *</label>
                <textarea name="message" rows="8" class="form-textarea"
                          placeholder="Describe your issue in detail. Include steps to reproduce (for bugs), expected behavior, and any error messages you saw."
                          required minlength="20" maxlength="5000">{{ old('message') }}</textarea>
                <p class="form-hint">Minimum 20 characters. The more detail you provide, the faster we can help.</p>
                @error('message')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22,2 15,22 11,13 2,9"/>
                    </svg>
                    Submit Ticket
                </button>
                <a href="{{ route($indexRoute) }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Tips card --}}
    <div class="card fade-up fade-up-2" style="margin-top:16px;">
        <div class="card-header">
            <span class="card-title">💡 Tips for a faster response</span>
        </div>
        <div style="padding:16px 20px;">
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;">
                @foreach([
                    ['For bugs', 'Include the exact steps to reproduce the issue and what you expected to happen.'],
                    ['Screenshots', 'If you can capture a screenshot of the error, attach it in the follow-up reply.'],
                    ['Feature requests', 'Describe the problem you\'re trying to solve, not just the solution you want.'],
                    ['Priority', 'Mark "Urgent" only if the system is completely unusable — it helps us triage faster.'],
                ] as [$title, $desc])
                <li style="display:flex;gap:10px;font-size:13px;">
                    <span style="color:var(--crimson);font-weight:600;flex-shrink:0;min-width:120px;">{{ $title }}</span>
                    <span style="color:var(--muted);">{{ $desc }}</span>
                </li>
                @endforeach
            </ul>
        </div>
    </div>

</div>

@endsection