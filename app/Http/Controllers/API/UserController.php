<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Js;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function getAllUsers(): JsonResponse
    {
        $users = User::all();

        return response()->json(['success' => true, 'message' => 'User List', 'data' => $users]);
    }

    public function getUser($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        return response()->json(['success' => true, 'message' => 'User details', 'data' => $user]);
    }
}
