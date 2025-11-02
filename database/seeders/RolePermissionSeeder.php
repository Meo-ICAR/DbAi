<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\Models\Permission as PermissionModel;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Clear existing permissions and roles to avoid duplicates
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        
        // Create permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'edit permissions',
            'view companies',
            'create companies',
            'edit companies',
            'delete companies',
        ];

        // Create permissions
        $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModels[] = PermissionModel::firstOrCreate([
                'name' => $permission, 
                'guard_name' => 'web'
            ]);
        }

        // Create admin role and assign all permissions
        $adminRole = RoleModel::firstOrCreate([
            'name' => 'admin', 
            'guard_name' => 'web'
        ]);
        
        $adminPermissions = collect($permissionModels)->pluck('name')->toArray();
        $adminRole->syncPermissions($adminPermissions);

        // Create user role with limited permissions
        $userRole = RoleModel::firstOrCreate([
            'name' => 'user', 
            'guard_name' => 'web'
        ]);
        
        $userRole->syncPermissions([
            'view users',
            'view companies',
        ]);

        // Assign admin role to the first user (usually the one you created during installation)
        $user = \App\Models\User::first();
        if ($user) {
            $user->assignRole('admin');
        }
    }
}
