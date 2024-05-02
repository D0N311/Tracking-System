<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use App\Mail\VerificationMail;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $success['token'] = $user->createToken('MyApp');
            $success['name'] = $user->name;
        
            if (User::count() == 1) {
                $role = Role::where('name', 'SuperAdmin')->first();
                $user->roles()->attach($role->id);
                $user->activated_at = now();
                Mail::to($request->email)->send(new VerificationMail($user));
                $user->save();
            } else {
                Mail::to($request->email)->send(new VerificationMail($user));
            }
            DB::commit();
            return $this->sendResponse($success, 'User registered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('User registration failed.', $e->getMessage(), 500);
        }
      
    
        return $this->sendResponse($success, 'User registered successfully.');
    }

    public function login(Request $request): JsonResponse
    {

        $credentials = ['email' => $request->email, 'password' => $request->password];
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('Email does not exist.', ['error' => 'Email does not exist']);
        }
        if (!$user->email_verified_at) {
            return $this->sendError('Email is not verified yet.', ['error' => 'Your email is not verified yet.']);
        }
         if (!$user->activated_at) {
            return $this->sendError('User is not activated.', ['error' => 'User is not activated. Please wait for your the admin to activate your account.']);
        }
        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError('Wrong password.', ['error' => 'Wrong password']);
        }
        if (Auth::attempt($credentials)) {
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;
            $success['role'] = $user->roles->first()->name;
            return $this->sendResponse($success, 'User logged in successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function checkUser(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->roles->first()->name,
                'verified' => $user->email_verified_at ? true : false,
                'company_id' => $user->company_id,
                'company_name' => $user->company->company_name,
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->token()->revoke();
        return response()->json([], 200);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            if (!Hash::check($request->old_password, $user->password)) {
                return $this->sendError('Wrong password.', ['error' => 'Wrong password']);
            }
            if (Hash::check($request->new_password, $user->password)) {
                return $this->sendError('New password cannot be the same as old password.', ['error' => 'New password cannot be the same as old password']);
            }
            $user->password = bcrypt($request->new_password);
            $user->save();
            DB::commit();     
            return $this->sendResponse($user, 'Password reset successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Password reset failed.', $e->getMessage(), 500);
        }
    }

   
}
