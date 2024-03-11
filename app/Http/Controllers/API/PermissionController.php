<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function addPermission(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permission_db,name'
        ]);

        $permission = new Permission;
        $permission->name = $request->input('name');
        $permission->save();

        return response()->json($permission, 201);
    }

    public function getPermission()
    {
        $permission = Permission::all();
        return response()->json($permission, 200);
    }
}
