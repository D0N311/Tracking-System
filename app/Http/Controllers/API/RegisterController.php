<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    //
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
}
