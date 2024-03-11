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

class SuperAdminController extends Controller
{
    public function checkUserRole($userId, $roleId)
    {
        $userRole = DB::table('user_has_role')->where('user_id', $userId)->first();
        return $userRole && $userRole->role_id == $roleId;
    }
    public function addCompany(Request $request): JsonResponse
    {
        // Check if the authenticated user is a SuperAdmin
        if (!$this->checkUserRole(auth()->user()->id, 1)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string',
            'description' => 'required|string',
            'location' => 'required',
            'admin_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->whereNotNull('activated_at')->where('role', '!=', 'SuperAdmin');
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {
            $company = Company::create($input);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Company created successfully', 'data' => $company], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function activateAdmin(Request $request)
    {
        // Check if the authenticated user is a SuperAdmin
        if (!$this->checkUserRole(auth()->user()->id, 1)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:users,id',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {
            $admin = User::find($input['admin_id']);
            $admin->activated_at = now();
            $admin->role = $input['role'];
            $admin->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin activated successfully', 'data' => $admin], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    // public function setAdmin(Request $request)
    // {
    //     // Check if the authenticated user is a SuperAdmin
    //     if (!$this->checkUserRole(auth()->user()->id, 1)) {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'company_id' => 'required|exists:company_db,id',
    //         'admin_id' => 'required|exists:users,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
    //     }

    //     $input = $request->all();

    //     DB::beginTransaction();

    //     try {
    //         $admin = User::find($input['admin_id']);
    //         $company = Company::find($input['company_id']);

    //         if ($company) {
    //             if ($company->admin_id) {
    //                 return response()->json(['success' => false, 'message' => 'Company already has an admin'], 400);
    //             }

    //             $company->admin_id = $input['admin_id'];
    //             $admin->company_id = $input['company_id'];
    //             $admin->save();
    //             $company->save();
    //         }

    //         DB::commit();
    //         return response()->json(['success' => true, 'message' => 'Admin activated successfully', 'data' => $admin, $company], 200);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
    //     }
    // }

    public function deactivateAdmin(Request $request)
    {
        // Check if the authenticated user is a SuperAdmin
        if (!$this->checkUserRole(auth()->user()->id, 1)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {
            $admin = User::find($input['admin_id']);
            $admin->activated_at = null;
            $admin->role = 'Admin';
            $admin->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin deactivated successfully', 'data' => $admin], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function removeAdmin(Request $request)
    {
        if (!$this->checkUserRole(auth()->user()->id, 1)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:company_db,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation Error', 'data' => $validator->errors()], 400);
        }

        $input = $request->all();

        DB::beginTransaction();

        try {
            $company = Company::find($input['company_id']);

            if ($company) {
                if (!$company->admin_id) {
                    return response()->json(['success' => false, 'message' => 'Company does not have an admin'], 400);
                }

                $admin = User::find($company->admin_id);
                $company->admin_id = null;
                $admin->company_id = null;
                $admin->save();
                $company->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin removed successfully', 'data' => $admin, $company], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }
}
