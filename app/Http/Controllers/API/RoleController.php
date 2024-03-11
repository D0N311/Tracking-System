<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function addRole(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:role_db,name'
        ]);

        $role = new Role;
        $role->name = $request->input('name');
        $role->save();

        return response()->json($role, 201);
    }

    public function getRole()
    {
        $role = Role::all();
        return response()->json($role, 200);
    }
}
