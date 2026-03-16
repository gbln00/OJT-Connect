<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers          = User::count();
        $totalStudents       = User::where('role', 'student_intern')->count();
        $pendingApplications = 0;
        $totalCompanies      = Company::count();
        $activeCompanies     = Company::where('is_active', true)->count();
        $activeUsers         = User::where('is_active', true)->count();
        $inactiveUsers       = User::where('is_active', false)->count();
        $pendingCount        = 0;

        $roleBreakdown = [
            'admin'       => User::where('role', 'admin')->count(),
            'coordinator' => User::where('role', 'ojt_coordinator')->count(),
            'supervisor'  => User::where('role', 'company_supervisor')->count(),
            'student'     => User::where('role', 'student_intern')->count(),
        ];

        $recentUsers    = User::latest()->take(6)->get();
        $recentActivity = [];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStudents',
            'pendingApplications',
            'totalCompanies',
            'activeCompanies',
            'activeUsers',
            'inactiveUsers',
            'pendingCount',
            'roleBreakdown',
            'recentUsers',
            'recentActivity'
        ));
    }

    // ── SETTINGS ─────────────────────────────────────────────────────────────

    public function settings()
    {
        return view('admin.profile.settings');
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        auth()->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}