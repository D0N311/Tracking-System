<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationMail;

class RegisterController extends Controller
{
    //
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);



        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorResponse = [];

            foreach ($errors->all() as $message) {
                $errorResponse[] = $message;
            }

            if ($errors->has('email')) {
                return $this->sendError('The email is already in use.', $errorResponse);
            } elseif ($errors->has('password')) {
                return $this->sendError('The password is required.', $errorResponse);
            } elseif ($errors->has('c_password')) {
                return $this->sendError('The password and confirmation password do not match.', $errorResponse);
            }

            return $this->sendError('Validation Error.', $errorResponse);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp');
        $success['name'] = $user->name;
        // ...

        Mail::to($request->email)->send(new VerificationMail($user));
        return $this->sendResponse($success, 'User registered successfully.');
    }
}
