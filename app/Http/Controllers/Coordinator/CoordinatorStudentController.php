<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\OjtApplication;
use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CoordinatorStudentController extends Controller
{
    /**
     * List all active student interns (approved OJT applications).
     * Also shows all student_intern and company_supervisor accounts
     * so the coordinator can manage them from one place.
     */
    public function index(Request $request)
    {
        // --- Active interns (approved applications) ---
        $query = OjtApplication::with(['student', 'company'])
            ->where('status', 'approved');

        if ($request->filled('search')) {
            $query->whereHas('student', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        $students = $query->latest()->paginate(15)->withQueryString();

        return view('coordinator.students.index', compact('students'));
    }

    // ── USER ACCOUNTS ─────────────────────────────────────────────

    /**
     * List all student_intern and company_supervisor accounts.
     */
    public function accounts(Request $request)
    {
        $query = User::whereIn('role', ['student_intern', 'company_supervisor']);

        if ($request->filled('search')) {
            $query->where(fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total'    => User::whereIn('role', ['student_intern', 'company_supervisor'])->count(),
            'student'  => User::where('role', 'student_intern')->count(),
            'supervisor'=> User::where('role', 'company_supervisor')->count(),
        ];

        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('coordinator.students.accounts', compact('users', 'counts', 'companies'));
    }

    /**
     * Show create user form.
     * Coordinator can only create student_intern and company_supervisor accounts.
     */
    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('coordinator.students.create', compact('companies'));
    }

    /**
     * Store a new student or supervisor account.
     */
    public function store(Request $request)
    {
        $isSupervisor = $request->role === 'company_supervisor';
        $isStudent    = $request->role === 'student_intern';

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'password'   => ['required', 'min:8', 'confirmed'],
            'role'       => ['required', Rule::in(['company_supervisor', 'student_intern'])],
            'company_id' => $isSupervisor ? ['required', 'exists:companies,id'] : ['nullable'],
            // Student profile fields
            'student_id'     => $isStudent ? ['required', 'string', 'max:50'] : ['nullable'],
            'course'         => $isStudent ? ['required', 'string', 'max:100'] : ['nullable'],
            'year_level'     => $isStudent ? ['required', 'string', 'max:20'] : ['nullable'],
            'section'        => $isStudent ? ['required', 'string', 'max:50'] : ['nullable'],
            'required_hours' => $isStudent ? ['required', 'integer', 'min:1'] : ['nullable'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'address'        => ['nullable', 'string', 'max:500'],
        ]);

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'company_id' => $isSupervisor ? $request->company_id : null,
            'is_active'  => true,
        ]);

        // Create student profile if role is student_intern
        if ($isStudent) {
            StudentProfile::create([
                'user_id'        => $user->id,
                'student_id'     => $request->student_id,
                'firstname'      => explode(' ', $request->name)[0] ?? $request->name,
                'lastname'       => implode(' ', array_slice(explode(' ', $request->name), 1)) ?: $request->name,
                'course'         => $request->course,
                'year_level'     => $request->year_level,
                'section'        => $request->section,
                'required_hours' => $request->required_hours ?? 486,
                'phone'          => $request->phone,
                'address'        => $request->address,
            ]);
        }

        return redirect()->route('coordinator.accounts.index')
            ->with('success', "Account for {$user->name} created successfully.");
    }

    /**
     * Show edit form for a student or supervisor account.
     */
    public function edit(User $user)
    {
        // Coordinator can only edit students and supervisors
        if (!in_array($user->role, ['student_intern', 'company_supervisor'])) {
            abort(403, 'You can only edit student and supervisor accounts.');
        }

        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('coordinator.students.edit', compact('user', 'companies'));
    }

    /**
     * Update an existing student or supervisor account.
     */
    public function update(Request $request, User $user)
    {
        if (!in_array($user->role, ['student_intern', 'company_supervisor'])) {
            abort(403, 'You can only edit student and supervisor accounts.');
        }

        $isSupervisor = $request->role === 'company_supervisor';

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role'       => ['required', Rule::in(['company_supervisor', 'student_intern'])],
            'password'   => ['nullable', 'min:8', 'confirmed'],
            'company_id' => $isSupervisor ? ['required', 'exists:companies,id'] : ['nullable'],
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

        return redirect()->route('coordinator.accounts.index')
            ->with('success', "Account for {$user->name} updated successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleActive(User $user)
    {
        if (!in_array($user->role, ['student_intern', 'company_supervisor'])) {
            abort(403);
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Account {$status} successfully.");
    }

    /**
     * Delete a student or supervisor account.
     */
    public function destroy(User $user)
    {
        if (!in_array($user->role, ['student_intern', 'company_supervisor'])) {
            abort(403);
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('coordinator.accounts.index')
            ->with('success', "Account for {$name} deleted.");
    }
}