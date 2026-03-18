<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OjtApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Block if already has pending or approved application
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

        $request->validate([
            'company_id'     => 'required|exists:companies,id',
            'program'        => 'required|string|max:100',
            'school_year'    => 'required|string|max:20',
            'semester'       => 'required|string|max:50',
            'required_hours' => 'required|integer|min:1',
            'document'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        // Handle file upload
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

        return redirect()->route('student.application.show', $application->id)
            ->with('success', 'Application submitted! Waiting for coordinator review.');
    }

    public function show(OjtApplication $application)
    {
        // Student can only view their own
        if ($application->student_id !== Auth::id()) {
            abort(403);
        }

        return view('student.application.show', compact('application'));
    }
}