<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    // public function login(Request $request): JsonResponse
    // {
    //     $credentials = ['email' => $request->email, 'password' => $request->password];

    //     // Check if the email exists
    //     $user = User::where('email', $request->email)->first();
    //     if (!$user) {
    //         return $this->sendError('Email does not exist.', ['error' => 'Email does not exist']);
    //     }

    //     // Check if the user is verified
    //     if (!$user->email_verified_at) {
    //         return $this->sendError('Email is not verified yet.', ['error' => 'Your email is not verified yet.']);
    //     }

    //     // Check if the password is correct
    //     if (!Hash::check($request->password, $user->password)) {
    //         return $this->sendError('Wrong password.', ['error' => 'Wrong password']);
    //     }

    //     // If the email exists and the password is correct, attempt to authenticate
    //     if (Auth::attempt($credentials)) {
    //         $success['token'] = $user->createToken('MyApp')->accessToken;
    //         $success['name'] = $user->name;

    //         return $this->sendResponse($success, 'User logged in successfully.');
    //     } else {
    //         return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
    //     }
    // }

    public function login(Request $request): JsonResponse
    {
        $credentials = ['email' => $request->email, 'password' => $request->password];
        // Check if the email exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError('Email does not exist.', ['error' => 'Email does not exist']);
        }
        // Check if the user is activated
        if (!$user->activated_at) {
            return $this->sendError('User is not activated.', ['error' => 'User is not activated. Please wait for your the admin to activate your account.']);
        }
        // Check if the user is verified
        if (!$user->email_verified_at) {
            return $this->sendError('Email is not verified yet.', ['error' => 'Your email is not verified yet.']);
        }
        // Check if the password is correct
        if (!Hash::check($request->password, $user->password)) {
            return $this->sendError('Wrong password.', ['error' => 'Wrong password']);
        }
        // If the email exists and the password is correct, attempt to authenticate
        if (Auth::attempt($credentials)) {
            $success['token'] = $user->createToken('MyApp')->accessToken;
            $success['name'] = $user->name;
            // $success['role'] = $user->roles->first()->name; // Pass the user's role
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
            ], 200);
        } else {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->token()) {
                $user->token()->revoke();
                return response()->json([], 200);
            }
            return response()->json([], 200);
        }
        return response()->json([], 200);
    }
}
