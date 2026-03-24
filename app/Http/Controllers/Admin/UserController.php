<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total'       => User::count(),
            'admin'       => User::where('role', 'admin')->count(),
            'coordinator' => User::where('role', 'ojt_coordinator')->count(),
            'supervisor'  => User::where('role', 'company_supervisor')->count(),
            'student'     => User::where('role', 'student_intern')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts'));
    }

    public function create()
    {
        // Pass active companies so the supervisor role can pick one
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $isSupervisor = $request->role === 'company_supervisor';

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'min:8', 'confirmed'],
            'role'       => ['required', Rule::in([
                'ojt_coordinator', 'company_supervisor', 'student_intern',
            ])],
            // company_id is required only for supervisors
            'company_id' => $isSupervisor
                ? ['required', 'exists:companies,id']
                : ['nullable'],
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'company_id' => $isSupervisor ? $request->company_id : null,
            'is_active'  => true,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User account created successfully.');
    }

    public function edit(User $user)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $isSupervisor = $request->role === 'company_supervisor';

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'       => ['required', Rule::in([
                'admin', 'ojt_coordinator', 'company_supervisor', 'student_intern',
            ])],
            'password'   => ['nullable', 'min:8', 'confirmed'],
            'company_id' => $isSupervisor
                ? ['required', 'exists:companies,id']
                : ['nullable'],
        ]);

        $data = [
            'name'       => $request->name,
            'email'      => $request->email,
            'role'       => $request->role,
            'company_id' => $isSupervisor ? $request->company_id : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User account {$status} successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }
}