<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'urlogo' => 'nullable|url|max:255',
            'url_attivazione' => 'nullable|url|max:255',
            'email_admin' => 'required|email|unique:companies,email_admin',
            'db_secrete' => 'required|string|min:8',
            'db_connection' => 'required|string|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'required|string|min:8',
        ]);

        // Generate a random secret if not provided
        if (empty($validated['db_secrete'])) {
            $validated['db_secrete'] = Str::random(32);
        }

        // Encrypt the database password
        $validated['db_password'] = encrypt($validated['db_password']);

        $company = Company::create($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        return view('admin.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        // Decrypt the password for editing
        $company->db_password = decrypt($company->db_password);
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'urlogo' => 'nullable|url|max:255',
            'url_attivazione' => 'nullable|url|max:255',
            'email_admin' => [
                'required',
                'email',
                Rule::unique('companies', 'email_admin')->ignore($company->id)
            ],
            'db_secrete' => 'required|string|min:8',
            'db_connection' => 'required|string|in:mysql,pgsql,sqlsrv',
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'required|string|min:8',
        ]);

        // Encrypt the database password if it was changed
        if ($validated['db_password'] !== $company->db_password) {
            $validated['db_password'] = encrypt($validated['db_password']);
        }

        $company->update($validated);

        return redirect()->route('admin.companies.show', $company)
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return redirect()->route('admin.companies.index')
            ->with('success', 'Company deleted successfully.');
    }
}
