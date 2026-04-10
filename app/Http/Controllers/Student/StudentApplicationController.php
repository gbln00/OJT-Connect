<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OjtApplication;
use App\Models\Plan;
use App\Models\TenantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentApplicationController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        $existing = $user->activeApplication()->first();
        if ($existing) {
            return redirect()->route('student.application.show', $existing->id)
                ->with('info', 'You already have an active application.');
        }

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('student.application.create', compact('companies', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Double-check no duplicate
        $existing = $user->activeApplication()->first();
        if ($existing) {
            return redirect()->route('student.application.show', $existing->id);
        }

        // ── Plan quota check ─────────────────────────────────────────
        $tenant    = tenancy()->tenant ?? null;
        $planModel = $tenant
            ? Plan::where('name', $tenant->plan ?? 'basic')->first()
            : null;

        $cap = $planModel?->student_cap ?? null;   // null = unlimited (Premium)

        if ($cap !== null) {
            // Count ALL approved applications in this tenant DB
            $currentCount = OjtApplication::whereIn('status', ['pending', 'approved'])->count();

            if ($currentCount >= $cap) {
                return back()->withErrors([
                    'quota' => "Your institution has reached its student limit ({$cap} students) " .
                               "for the " . ($planModel->label ?? 'current') . " plan. " .
                               "Please contact your OJT coordinator or administrator to request a plan upgrade.",
                ])->withInput();
            }
        }
        // ── End quota check ──────────────────────────────────────────

        $request->validate([
            'company_id'     => 'required|exists:companies,id',
            'program'        => 'required|string|max:100',
            'school_year'    => 'required|string|max:20',
            'semester'       => 'required|string|max:50',
            'required_hours' => 'required|integer|min:1',
            'document'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')
                ->store('ojt-documents/' . $user->id, 'public');
        }

        $application = OjtApplication::create([
            'student_id'     => $user->id,
            'company_id'     => $request->company_id,
            'program'        => $request->program,
            'school_year'    => $request->school_year,
            'semester'       => $request->semester,
            'required_hours' => $request->required_hours,
            'document_path'  => $documentPath,
            'status'         => 'pending',
        ]);

        TenantNotification::notify(
            title:      'New OJT Application',
            message:    auth()->user()->name . " submitted an OJT application for {$application->company->name}.",
            type:       'info',
            targetRole: 'ojt_coordinator'
        );

        TenantNotification::notify(
            title:      'New OJT Application',
            message:    auth()->user()->name . " submitted an OJT application for {$application->company->name}.",
            type:       'info',
            targetRole: 'admin'
        );

        return redirect()->route('student.application.show', $application->id)
            ->with('success', 'Application submitted! Waiting for coordinator review.');
    }

    public function show(OjtApplication $application)
    {
        if ($application->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.application.show', compact('application'));
    }

    private function getStorageMb(string $tenantId): float
    {
        try {
            $disk  = Storage::disk('public');
            $total = 0;

            // Check all folders that tenant uploads go into
            foreach (['ojt-documents', 'weekly-reports'] as $folder) {
                if ($disk->exists($folder)) {
                    $files = $disk->allFiles($folder);
                    $total += array_sum(array_map(fn($f) => $disk->size($f), $files));
                }
            }

            return round($total / 1024 / 1024, 2);
        } catch (\Throwable) {
            return 0.0;
        }
    }
}