<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleHasPermission;

class RoleHasPermissionController extends Controller
{
    public function addRoleHasPermission(Request $request)
    {
        $role = Role::where('name', $request->role)->first();

        foreach ($request->permission as $permission) {
            $role->givePermissionTo($permission);
        }

        return response()->json(['message' => 'Permissions added to role successfully'], 200);
    }

    public function getRoleHasPermission(Request $request)
    {
        $role = Role::where('name', $request->role)->first();
        $permissions = $role->permissions;
        return response()->json(['permissions' => $permissions], 200);
    }

    public function removeRoleHasPermission(Request $request)
    {
        $role = Role::where('name', $request->role)->first();
        $role->revokePermissionTo($request->permission);
        return response()->json(['message' => 'Permission removed from role successfully'], 200);
    }
}
