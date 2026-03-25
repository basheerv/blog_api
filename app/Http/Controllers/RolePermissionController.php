<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
class RolePermissionController extends Controller
{
    public function index()
    {
        $permission = Permission::pluck('name');
        return response()->json($permission, 200);
    }

    public function show($id)
    {
        $role = Role::with('permissions:id,name')->findOrFail($id);
        return response()->json($role, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
          'roleName' => 'required|string|unique:roles,name',
          'permissions' => 'required|array',
          'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $request->roleName, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        if (!empty($request->permissions)) {
         $role->syncPermissions($request->permissions);
        }

        return response()->json([
            'message' => 'Role created and permissions assigned successfully',
            'role' => $role
        ], 201);
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'roleName' => 'required|string|unique:roles,name,'.$id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::findOrFail($id);
        $role->name = $request->roleName;
        $role->save();
        $role->syncPermissions($request->permissions);

        return response()->json(['message' => 'Permissions updated successfully'], 200);
    }

    public function syncRoles(Request $request)
    {
        $permissions = [
            'users.create',
            'users.edit',
            'users.delete',
            'users.view',
            'view.dashboard',
            'roles.manage',
            'roles.view',
            'permissions.manage',
            'permissions.view'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm,
                'guard_name' => 'web'
            ]);
        }

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $merchantRole = Role::firstOrCreate([
            'name' => 'merchant',
            'guard_name' => 'web'
        ]);

        $userRole = Role::firstOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        $adminRole->syncPermissions($permissions);

        $merchantRole->syncPermissions([
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'view.dashboard'
        ]);

        $userRole->syncPermissions([
            'users.view',
            'view.dashboard'
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'message' => 'Roles and permissions synced successfully'
        ]);
    }

    public function userPermissions($id)
    {
        $user = User::with('permissions')->findOrFail($id);

        $permissions = Permission::all()->map(function ($permission) use ($user) {
            return [
                'name' => $permission->name,
                'active' => $user->hasPermissionTo($permission->name)
            ];
        });

        return response()->json($permissions);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully'], 200);
    }

    public function roleList()
    {
        $roles = Role::with('permissions:id,name')->get();
        return response()->json($roles, 200);
    }
}
