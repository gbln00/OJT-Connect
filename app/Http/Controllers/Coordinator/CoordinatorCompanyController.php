<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CoordinatorCompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $companies = $query->latest()->paginate(15)->withQueryString();
        $total     = Company::count();
        $active    = Company::where('is_active', true)->count();

        return view('coordinator.companies.index', compact('companies', 'total', 'active'));
    }

    public function create()
    {
        return view('coordinator.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'address'        => ['nullable', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
            'industry'       => ['nullable', 'string', 'max:100'],
        ]);

        Company::create($request->validated() + ['is_active' => true]);

        return redirect()->route('coordinator.companies.index')
            ->with('success', 'Company added successfully.');
    }

    public function edit(Company $company)
    {
        return view('coordinator.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'address'        => ['nullable', 'string', 'max:500'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email'  => ['nullable', 'email', 'max:255'],
            'contact_phone'  => ['nullable', 'string', 'max:50'],
            'industry'       => ['nullable', 'string', 'max:100'],
        ]);

        $company->update($request->validated());

        return redirect()->route('coordinator.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function toggleActive(Company $company)
    {
        $company->update(['is_active' => !$company->is_active]);
        $status = $company->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Company {$status} successfully.");
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('coordinator.companies.index')
            ->with('success', 'Company removed successfully.');
    }
}