<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, $id)
    {
        // if (!$request->hasValidSignature()) {
        //     abort(403);
        // }
        $user = User::findOrFail($id);

        $user->update(['email_verified_at' => now()]);

        return view('success');
    }
}
