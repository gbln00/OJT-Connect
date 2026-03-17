<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompanyRequest;
use App\Http\Requests\Admin\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
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

        return view('admin.companies.index', compact('companies', 'total', 'active'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        Company::create($request->validated() + ['is_active' => true]);

        return redirect()->route('admin.companies.index')
                         ->with('success', 'Company added successfully.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());

        return redirect()->route('admin.companies.index')
                         ->with('success', 'Company updated successfully.');
    }

    public function toggleActive(Company $company)
    {
        $company->update(['is_active' => ! $company->is_active]);
        $status = $company->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Company {$status} successfully.");
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('admin.companies.index')
                         ->with('success', 'Company removed successfully.');
    }
}