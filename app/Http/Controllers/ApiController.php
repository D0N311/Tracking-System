<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
class ApiController extends Controller
{
    /**
     * return json response global
     *
     * @param  mixed $data
     * @param  mixed $messages
     * @param  mixed $code
     * @return void
     */
    public function jsonResponse($data, $message, $status): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    public function codeHTTP(): array
    {
        return ['200', '201', '202'];
    }
}
