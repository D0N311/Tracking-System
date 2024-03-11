<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function checkUserRole($userId, $roleId)
    {
        $userRole = DB::table('user_has_role')->where('user_id', $userId)->first();
        return $userRole && $userRole->role_id == $roleId;
    }

    public function addUserToCompany(Request $request)
    {
        // Check if the authenticated user is a Admin
        if (!$this->checkUserRole(auth()->user()->id, 2)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:company_db,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        try {
            $user = User::find($input['user_id']);
            $user->company_id = $input['company_id'];
            $user->save();
            return response()->json(['success' => true, 'message' => 'User updated successfully', 'data' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function activateUser(Request $request)
    {
        // Check if the authenticated user is a Admin
        if (!$this->checkUserRole(auth()->user()->id, 2)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        try {
            $user = User::find($input['user_id']);
            $user->activated_at = now();
            $user->save();
            return response()->json(['success' => true, 'message' => 'User activated successfully', 'data' => $user], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }
}
