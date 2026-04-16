<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\{OjtApplication, Company};
use Illuminate\Http\Request;
use App\Models\TenantNotification;
use App\Mail\ApplicationApproved;
use App\Mail\ApplicationRejected;
use Illuminate\Support\Facades\Mail;


class CoordinatorApplicationController extends Controller
{
    public function index(Request $request)
    {
        $query = OjtApplication::with(['student', 'company', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $applications = $query->paginate(15)->withQueryString();

        $counts = [
            'total'        => OjtApplication::count(),
            'pending'      => OjtApplication::where('status', 'pending')->count(),
            'under_review' => OjtApplication::where('status', 'under_review')->count(),
            'approved'     => OjtApplication::where('status', 'approved')->count(),
            'rejected'     => OjtApplication::where('status', 'rejected')->count(),
        ];

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('coordinator.applications.index', compact('applications', 'counts', 'companies'));
    }

    public function show(OjtApplication $application)
    {
        $application->load(['student', 'company', 'reviewer']);
        return view('coordinator.applications.show', compact('application'));
    }

    public function approve(Request $request, OjtApplication $application)
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status'      => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        TenantNotification::notify(
            title:      'Application Approved',
            message:    "Your OJT application for {$application->company->name} has been approved.",
            type:       'success',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        Mail::to($application->student->email)->send(new ApplicationApproved($application));

        return back()->with('success', $application->student->name . ' has been approved.');
    }

    public function reject(Request $request, OjtApplication $application)
    {
        $request->validate([
            'remarks' => ['required', 'string', 'max:1000'],
        ]);

        $application->update([
            'status'      => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'remarks'     => $request->remarks,
        ]);

        TenantNotification::notify(
            title:      'Application Rejected',
            message:    "Your OJT application for {$application->company->name} was rejected. Remarks: {$request->remarks}",
            type:       'warning',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        Mail::to($application->student->email)->send(new ApplicationRejected($application));

        return back()->with('success', $application->student->name . ' has been rejected.');
    }

    public function sendToReview(Request $request, OjtApplication $application)
    {
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $application->update([
            'status'      => 'under_review',
            'remarks'     => $validated['review_notes'] ?? $application->remarks,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        TenantNotification::notify(
            title:      'Document Review Required',
            message:    "Your OJT application for {$application->company->name} is under document review.",
            type:       'info',
            targetRole: 'student_intern',
            userId:     $application->student_id
        );

        return redirect()
            ->route('coordinator.applications.show', $application)
            ->with('success', 'Application sent for document review.');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'ids'     => ['required', 'array'],
            'ids.*'   => ['integer', 'exists:ojt_applications,id'],
            'action'  => ['required', 'in:approve,reject'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        // Reject bulk action requires a remark
        if ($request->action === 'reject' && empty($request->remarks)) {
            return back()->withErrors(['remarks' => 'A reason is required when bulk rejecting applications.']);
        }

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        $applications = OjtApplication::whereIn('id', $request->ids)->get();
        $count = 0;

        foreach ($applications as $application) {
            if (!in_array($application->status, ['pending', 'under_review'])) continue;

            $application->update([
                'status'      => $status,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'remarks'     => $request->remarks,
            ]);

            TenantNotification::notify(
                title:      $request->action === 'approve' ? 'Application Approved' : 'Application Rejected',
                message:    $request->action === 'approve'
                    ? "Your OJT application for {$application->company->name} has been approved."
                    : "Your OJT application for {$application->company->name} was rejected."
                        . ($request->remarks ? " Remarks: {$request->remarks}" : ''),
                type:       $request->action === 'approve' ? 'success' : 'warning',
                targetRole: 'student_intern',
                userId:     $application->student_id
            );

            $mailClass = $request->action === 'approve'
                ? new ApplicationApproved($application)
                : new ApplicationRejected($application);

            try {
                Mail::to($application->student->email)->send($mailClass);
            } catch (\Throwable $e) {
                \Log::error("Coordinator bulk mail failed for app {$application->id}: " . $e->getMessage());
            }

            $count++;
        }

        $label = $request->action === 'approve' ? 'approved' : 'rejected';
        return back()->with('success', "{$count} application(s) {$label} successfully.");
    }
}