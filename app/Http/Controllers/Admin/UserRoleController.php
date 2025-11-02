<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UserRoleController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        
        return view('admin.users.roles.index', compact('users', 'roles'));
        $user->setConnection('dbai');
        
        $query = $user->with('roles');
        
        // Search by name or email
        if (request()->has('search') && !empty(request('search'))) {
            $search = '%' . request('search') . '%';
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search);
            });
        }
        
        // Filter by role
        if (request()->has('role') && !empty(request('role'))) {
            $roleId = request('role');
            $query->whereHas('roles', function($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }
        
        $users = $query->orderBy('name')->paginate(15);
    
        // Load roles with their permissions and count of users using Spatie's methods
        $roles = Role::with('permissions')
            ->withCount('users')
            ->get()
            ->map(function ($role) {
                return (object) [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description ?? 'No description',
                    'users_count' => $role->users_count,
                    'permissions' => $role->permissions,
                    'is_system' => in_array($role->name, ['admin', 'user']) // Mark system roles
                ];
            });
    
        return view('admin.users.roles.index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified user's roles in storage.
     */
    public function update(Request $request, User $user)
    {
        // Ensure we're using the dbai connection
        $user->setConnection('dbai');
        
        // Temporarily set the default connection for validation
        $connection = config('database.default');
        config(['database.default' => 'dbai']);
        
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        // Restore the default connection
        config(['database.default' => $connection]);

        DB::connection('dbai')->transaction(function () use ($user, $request) {
            $user->roles()->sync($request->input('roles', []));
        });

        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Ruoli aggiornati con successo!');
    }
    
    /**
     * Show the form for bulk role assignment.
     */
    public function bulkAssign()
    {
        // Ensure we're using the dbai connection
        $user = new User();
        $user->setConnection('dbai');
        
        $role = new Role();
        $role->setConnection('dbai');
        
        $users = $user->orderBy('name')->get();
        $roles = $role->all();
        
        return view('admin.users.roles.bulk-assign', compact('users', 'roles'));
    }
    
    /**
     * Process bulk role assignments.
     */
    public function processBulkAssign(Request $request)
    {
        $validated = $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $users = User::whereIn('id', $validated['users'])->get();
        
        foreach ($users as $user) {
            $user->sync($validated['roles']);
        }
        $user->setConnection('dbai');
        
        $users = $user->whereIn('id', $request->users)->get();
        $roleIds = $request->roles;
        $action = $request->action;
        
        DB::connection('dbai')->transaction(function () use ($users, $roleIds, $action) {
            foreach ($users as $user) {
                $user->setConnection('dbai');
                if ($action === 'assign') {
                    $user->roles()->syncWithoutDetaching($roleIds);
                } else {
                    $user->roles()->detach($roleIds);
                }
            }
        });
        
        return redirect()->route('admin.users.roles.index')
            ->with('success', 'Ruoli aggiornati con successo per gli utenti selezionati!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
