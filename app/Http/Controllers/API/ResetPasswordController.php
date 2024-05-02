<?php

namespace App\Http\Controllers\API;

use App\Models\ResetCodePassword;
// use Illuminate\Foundation\Auth\User;
use App\Models\User;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends ApiController
{
    /**
     * Change the password (Setp 3)
     *
     * @param  mixed $request
     * @return void
     */
    public function __invoke(ResetPasswordRequest $request)
    {
        DB::beginTransaction();
        try{
            $passwordReset = ResetCodePassword::firstWhere('code', $request->code);

            if ($passwordReset->isExpire()) {
                return $this->jsonResponse(['success' => false], 'The provided code has expired.', 422);
            }

            $user = User::firstWhere('email', $passwordReset->email);

            if (Hash::check($request->password, $user->password)) {
                return $this->jsonResponse(['success' => false], 'Use new password.', 422);
            }

            $user->password = bcrypt($request->password);
            $user->save();
            $passwordReset->delete();
            DB::commit();
            return $this->jsonResponse(['success' => true], 'Password has been successfully reset.', 200);

        }catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['success' => false], 'An error occurred while resetting the password: ' . $e->getMessage(), 500);
        }

    }
}
