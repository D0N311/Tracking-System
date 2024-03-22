<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserActivateRequest;
use App\Http\Requests\UserDeactivateRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function addUserToCompany(Request $request)
    {
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

    public function activateUser(UserActivateRequest $request)
    {
        $input = $request->all();
        $user_id = $input['user_id'];

        try {
            DB::transaction(function () use ($user_id, $request) {
                $role = Role::where('name', 'user')->first();
                $user = User::find($user_id);
                if ($user) {
                    $user->activated_at = now();
                    $user->company_id = $request->user()->company_id;
                    $user->save();
                    $user->roles()->attach($role);
                }
            });

            return response()->json(['success' => true, 'message' => 'User activated and role set to user successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function userIndex(Request $request)
    {
        $admin = $request->user();
        $query = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })
            ->where('company_id', $admin->company_id);

        $total = $query->count();
        $users = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'User list',
            'total' => $total,
            'data' => $users
        ], 200);
    }

    //deacticate and remove the user to the company
    public function deactivateUser(UserDeactivateRequest $request)
    {
        $input = $request->all();
        try {
            DB::transaction(function () use ($input) {
                $user = User::whereHas('roles', function ($query) {
                    $query->where('name', 'user');
                })->find($input['user_id']);

                if (!$user) {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }

                $user->activated_at = null;
                $user->company_id = null;
                $user->roles()->detach();
                $user->save();
            });

            return response()->json(['success' => true, 'message' => 'User deactivated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }
}
