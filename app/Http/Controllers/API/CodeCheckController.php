<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CodeCheckRequest;
use Illuminate\Http\Request;
use App\Models\ResetCodePassword;


class CodeCheckController extends Controller
{
    public function __invoke(CodeCheckRequest $request)
    {
        $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

        if ($passwordReset->isExpire()) {
            return $this->jsonResponse(['success' => false], trans('passwords.code_is_expire'), 422);
        }

        return $this->jsonResponse(['success' => true, 'code' => $passwordReset->code], trans('passwords.code_is_valid'), 200);
    }
}
