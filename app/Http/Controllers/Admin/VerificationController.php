<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'student_intern')
            ->with('studentProfile')
            ->latest();

        // Filter by verification status
        $tab = $request->get('tab', 'pending');
        if ($tab === 'pending') {
            $query->where('is_verified', false)->whereNull('rejection_reason');
        } elseif ($tab === 'verified') {
            $query->where('is_verified', true);
        } elseif ($tab === 'rejected') {
            $query->where('is_verified', false)->whereNotNull('rejection_reason');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhereHas('studentProfile', fn($sq) =>
                      $sq->where('student_id', 'like', "%$search%")
                         ->orWhere('course', 'like', "%$search%")
                  );
            });
        }

        $students = $query->paginate(15)->withQueryString();

        $counts = [
            'pending'  => User::where('role', 'student_intern')->where('is_verified', false)->whereNull('rejection_reason')->count(),
            'verified' => User::where('role', 'student_intern')->where('is_verified', true)->count(),
            'rejected' => User::where('role', 'student_intern')->where('is_verified', false)->whereNotNull('rejection_reason')->count(),
        ];

        return view('admin.verification.index', compact('students', 'counts', 'tab'));
    }

    public function show(User $user)
    {
        $user->load(['studentProfile', 'verifier']);
        return view('admin.verification.show', compact('user'));
    }

    public function verify(User $user)
    {
        $user->update([
            'is_verified'       => true,
            'verified_at'       => now(),
            'verified_by'       => auth()->id(),
            'rejection_reason'  => null,
        ]);

        return back()->with('success', "{$user->name} has been verified successfully.");
    }

    public function reject(Request $request, User $user)
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $user->update([
            'is_verified'      => false,
            'verified_at'      => null,
            'verified_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', "{$user->name}'s registration has been rejected.");
    }
}