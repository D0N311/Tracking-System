<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UserHasRoleController extends Controller
{
    // public function assignRole(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {
    //         $role = Role::where('name', $request->role)->first();

    //         if ($role->name == 'superadmin') {
    //             $existingSuperadmin = DB::table('user_has_role')->where('role_id', 1)->first();

    //             if ($existingSuperadmin) {
    //                 return response()->json(['message' => 'Cannot add SuperAdmin again.'], 400);
    //             }
    //         }

    //         $user = User::where('email', $request->email)->first();
    //         $user->assignRole($role);

    //         DB::commit();

    //         return response()->json(['message' => 'Role assigned to user successfully'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollback();

    //         // return error message here
    //         return response()->json(['message' => 'Error occurred while assigning role: ' . $e->getMessage()], 500);
    //     }
    // }

    public function assignRole(Request $request)
    {
        DB::beginTransaction();

        try {
            $role = Role::where('name', $request->role)->first();

            if ($role->id == 1) {
                $existingSuperadmin = DB::table('user_has_role')->where('role_id', 1)->first();

                if ($existingSuperadmin) {
                    return response()->json(['message' => 'Cannot assign SuperAdmin again.'], 400);
                }
            }

            $user = User::where('email', $request->email)->first();
            // Check if the user already has a role
            if ($user->roles()->count() > 0) {
                return response()->json(['message' => 'User cannot have multiple role.'], 400);
            }
            $user->assignRole($role);

            DB::commit();

            return response()->json(['message' => 'Role assigned to user successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();

            // return error message here
            return response()->json(['message' => 'Error occurred while assigning role: ' . $e->getMessage()], 500);
        }
    }

    public function getRole(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $roles = $user->getRoleNames();
        return response()->json(['roles' => $roles], 200);
    }

    public function removeRole(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $role = Role::where('name', $request->role)->first();

        if ($user && $role) {
            // Detach the role from the user
            $user->roles()->detach($role->id);

            // Detach the permissions associated with the role
            $role->permissions()->detach();

            // Delete the role from the database
            $role->delete();

            return response()->json(['message' => 'Role removed from user and deleted successfully'], 200);
        }

        return response()->json(['message' => 'User or role not found'], 404);
    }
}
