<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateAdminRequest;
use App\Http\Requests\AddCompanyRequest;
use App\Http\Requests\DeactivateAdminRequest;
use App\Http\Requests\RemoveAdminRequest;
use App\Http\Requests\SetAdminRequest;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function addCompany(AddCompanyRequest $request): JsonResponse
    {
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

    public function companyIndex()
    {
        $companies = DB::table('company_db')
            ->leftJoin('users as admin', 'company_db.admin_id', '=', 'admin.id')
            ->select('company_db.id', 'company_db.company_name', 'admin.name as admin_name', 'company_db.location', 'company_db.description', 'company_db.created_at')
            ->selectSub(function ($query) {
                $query->from('users')
                    ->join('user_has_role', 'users.id', '=', 'user_has_role.user_id')
                    ->whereRaw('users.company_id = company_db.id')
                    ->where('user_has_role.role_id', 3) // Assuming role_id 3 is for 'User'
                    ->selectRaw('count(*)');
            }, 'user_count')
            ->orderBy('company_db.created_at', 'desc')
            ->paginate(10);

        return response()->json(['success' => true, 'message' => 'Companies retrieved successfully', 'data' => $companies], 200);
    }

    public function activateAdmin(ActivateAdminRequest $request)
    {
        $input = $request->all();
        DB::beginTransaction();

        try {
            // Find the user by ID or email
            $admin = User::where('id', $input['admin'])
                ->orWhere('email', $input['admin'])
                ->first();

            if (!$admin) {
                return response()->json(['success' => false, 'message' => 'Admin not found'], 404);
            }

            if ($admin->activated_at) {
                return response()->json(['success' => false, 'message' => 'Admin is already activated'], 400);
            }

            $admin->activated_at = now();
            $admin->save();

            $role = Role::where('name', 'Admin')->first();

            if ($role) {
                $admin->roles()->attach($role->id);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin activated successfully', 'data' => $admin], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }


    public function deactivateAdmin(DeactivateAdminRequest $request)
    {
        $input = $request->all();
        DB::beginTransaction();

        try {
            $admin = User::find($input['admin_id']);
            $company = Company::where('admin_id', $input['admin_id'])->first();

            if ($company) {
                $company->admin_id = null;
                $company->save();
            }

            $admin->activated_at = null;
            $admin->company_id = null;
            $admin->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin deactivated successfully', 'data' => $admin], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function removeAdmin(RemoveAdminRequest $request)
    {
        $input = $request->all();
        DB::beginTransaction();

        try {
            $company = Company::find($input['company_id']);

            $admin = User::findOrFail($company->admin_id);
            $company->update(['admin_id' => null]);
            $admin->update(['company_id' => null]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin removed successfully', 'data' => [$admin, $company]], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function adminIndex()
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'Admin');
        })
            ->with(['company' => function ($query) {
                $query->select('id', 'company_name');
            }])
            ->orderBy('id', 'desc')
            ->paginate(10, ['id', 'name', 'email', 'activated_at', 'company_id']);

        return response()->json(['success' => true, 'message' => 'All admins retrieved successfully', 'data' => $admins], 200);
    }

    public function setAdmin(SetAdminRequest $request)
    {
        $input = $request->all();
        $user = User::find($input['admin_id']);
        $company = Company::find($input['company_id']);
        DB::beginTransaction();

        if (!$user->roles()->where('name', 'Admin')->exists()) {
            return response()->json(['success' => false, 'message' => 'User is not an admin'], 400);
        }

        if ($user->company_id) {
            return response()->json(['success' => false, 'message' => 'Admin already belongs to a company'], 400);
        }

        if ($company->admin_id) {
            return response()->json(['success' => false, 'message' => 'Company already has an admin'], 400);
        }

        try {
            $user->company_id = $company->id;
            $company->admin_id = $user->id;
            $company->save();
            $user->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Admin set successfully', 'data' => [$user, $company]], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Database Error', 'data' => $e->getMessage()], 500);
        }
    }

    public function noRoleIndex()
    {
        $users = User::whereDoesntHave('roles')
            ->orderBy('id', 'desc')
            ->paginate(10, ['id', 'name', 'email', 'activated_at', 'company_id']);

        $noRoleCount = User::whereDoesntHave('roles')->count();

        return response()->json([
            'success' => true,
            'count' => $noRoleCount,
            'message' => 'Users with no role retrieved successfully',
            'data' => $users,

        ], 200);
    }
}
