<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApproveApplicationRequest;
use App\Http\Requests\Admin\RejectApplicationRequest;
use App\Models\OjtApplication;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\TenantNotification;
use App\Mail\ApplicationApproved;
use App\Mail\ApplicationRejected;
use Illuminate\Support\Facades\Mail;


class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = OjtApplication::with(['student', 'company', 'reviewer'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $applications = $query->paginate(15)->withQueryString();

        $counts = [
            'total'    => OjtApplication::count(),
            'pending'  => OjtApplication::where('status', 'pending')->count(),
            'approved' => OjtApplication::where('status', 'approved')->count(),
            'rejected' => OjtApplication::where('status', 'rejected')->count(),
        ];

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('admin.applications.index', compact('applications', 'counts', 'companies'));
    }

    public function show(OjtApplication $application)
    {
        $application->load(['student', 'company', 'reviewer']);
        return view('admin.applications.show', compact('application'));
    }

    public function approve(ApproveApplicationRequest $request, OjtApplication $application)
    {
        $application->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->validated()['remarks'] ?? null,
            
        ]);

        TenantNotification::notify(
            title:      'Application Approved',
            message:    "Your OJT application for {$application->company->name} has been approved.",
            type:       'success',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        // Send approval email
        Mail::to($application->student->email)->send(new ApplicationApproved($application));

        return back()->with('success', "Application for {$application->student->name} has been approved.");
    }

    

    public function reject(RejectApplicationRequest $request, OjtApplication $application)
    {
        $application->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->validated()['remarks'],
        ]);

        TenantNotification::notify(
            title:      'Application Rejected',
            message:    "Your OJT application for {$application->company->name} was rejected. Remarks: {$application->remarks}",
            type:       'warning',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        // Send rejection email
        Mail::to($application->student->email)->send(new ApplicationRejected($application));


        return back()->with('success', "Application for {$application->student->name} has been rejected.");
    }

    public function destroy(OjtApplication $application)
    {
        if ($application->document_path) {
            Storage::disk('public')->delete($application->document_path);
        }

        $application->delete();

        return redirect()->route('admin.applications.index')
            ->with('success', 'Application deleted successfully.');
    }
}