<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
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

    public function searchInput(SearchRequest $request)
    {
        $search = $request->search;
        $user = User::whereNull('activated_at')
            ->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            })
            ->first();

        return response()->json(['success' => true, 'message' => 'Search result retrieved successfully', 'data' => $user], 200);
    }
}
