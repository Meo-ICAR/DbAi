<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.form', [
            'title' => 'Tambah Role Baru',
            'action' => route('roles.store'),
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::create(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }
    
    /**
     * Show the form for editing role permissions.
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $role->load('permissions');
        
        return view('admin.roles.permissions', compact('role', 'permissions'));
    }
    
    /**
     * Sync permissions for the specified role.
     */
    public function syncPermissions(Request $request, Role $role)
    {
        try {
            DB::beginTransaction();
            
            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);
            
            DB::commit();
            
            return redirect()
                ->route('admin.roles.permissions', $role)
                ->with('success', 'Permessi aggiornati con successo!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Errore durante l\'aggiornamento dei permessi: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $role->load('permissions');
        
        return view('admin.roles.form', [
            'title' => 'Edit Role: ' . $role->name,
            'action' => route('roles.update', $role),
            'method' => 'PUT',
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->update(['name' => $validated['name']]);
        
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of admin role
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete admin role');
        }

        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
    
    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);
        
        $role->syncPermissions($validated['permissions'] ?? []);
        
        return redirect()->route('roles.index')
            ->with('success', 'Role permissions updated successfully');
    }
}
