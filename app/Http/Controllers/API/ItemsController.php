<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Items;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ItemsRequest;
use App\Models\User;

class ItemsController extends Controller
{
    // public function addItems(ItemsRequest $request)
    // {
    //     $user = User::where('email', $request->email)->first();
    //     // Check if the user has a role of 'user'
    //     if (!$user->hasRole('user')) {
    //         return response()->json(['message' => 'Only users can add items'], 403);
    //     }
    //     $validator = Validator::make($request->all(), []);
    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()], 400);
    //     }
    //     DB::beginTransaction();
    //     try {
    //         $item = new Items();
    //         $item->item_name = $request->input('item_name');
    //         $item->item_type = $request->input('item_type');
    //         $item->stocks = $request->input('stocks');
    //         $item->model_number = $request->input('model_number');
    //         $item->image_link = $request->input('image_link');
    //         $item->under_company_id = $request->input('under_company_id');
    //         $item->save();

    //         DB::commit();

    //         return response()->json(['message' => 'Item added successfully'], 200);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json(['message' => 'Failed to add item'], 500);
    //     }
    // }
    public function addItems(ItemsRequest $request)
    {
        // $user = User::where('email', $request->email)->first();

        // // Check if the user has a role of 'user'
        // if (!$user->hasRole('user')) {
        //     return response()->json(['message' => 'Only users can add items'], 403);
        // }

        DB::beginTransaction();
        try {
            $item = Items::create($request->validated());
            $item->item_name = $request->input('item_name');
            $item->item_type = $request->input('item_type');
            $item->stocks = $request->input('stocks');
            $item->model_number = $request->input('model_number');
            $item->image_link = $request->input('image_link');
            $item->under_company_id = $request->input('under_company_id');
            $item->save();

            DB::commit();

            return response()->json(['message' => 'Item added successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to add item'], 500);
        }
    }

    public function confirmItem(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if the user has a role of 'admin'
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Only admin can confirm items'], 403);
        }

        DB::beginTransaction();
        try {
            $item = Items::find($request->input('item_id'));
            $item->status = 'confirmed';
            $item->save();

            DB::commit();

            return response()->json(['message' => 'Item confirmed successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to confirm item'], 500);
        }
    }

    public function setOwner(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        // Check if the user has a role of 'admin'
        if (!$user->hasRole('admin')) {
            return response()->json(['message' => 'Only admin can set owner'], 403);
        }

        DB::beginTransaction();
        try {
            $item = Items::find($request->input('item_id'));
            $item->owner_id = $request->input('owner_id');
            $item->save();

            DB::commit();

            return response()->json(['message' => 'Owner set successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to set owner'], 500);
        }
    }
}
