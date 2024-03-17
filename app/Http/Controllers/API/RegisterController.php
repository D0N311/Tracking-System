<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;

class RegisterController extends Controller
{
    //
    public function register(RegisterRequest $request): JsonResponse
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp');
        $success['name'] = $user->name;

        Mail::to($request->email)->send(new VerificationMail($user));
        return $this->sendResponse($success, 'User registered successfully.');
    }
}
