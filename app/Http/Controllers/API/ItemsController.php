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
    public function store(ItemsRequest $request){
        DB::beginTransaction();

    try {
        info($request->all());
        $configuration = \Uploadcare\Configuration::create(config('app.uploadcare_public'), config('app.uploadcare_secret'));
        $uploader = (new \Uploadcare\Api($configuration))->uploader();
        $user = User::where('email', $request->owned_by)->first();
        $

        $image = empty($request->image) ? '' : $uploader->fromPath($request->image, 'image/jpeg');
        $item = new Items();
        $item->name = $request->item_name;
        $item->item_type = $request->item_type;
        $item->stock = $request->stocks;
        $item->model_number = $request->model_number;
        $item->image_link = empty($image) ? 'none' : 'https://ucarecdn.com/' . $image->getUuid() . '/-/preview/500x500/-/quality/smart/-/format/auto/';
        $item->under_company = $request->under_company;
        $item->owned_by = $user->id;
        $item->save();

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Item added successfully',
            'img' => $image,
            'data' => $item
        ], 201);
    } catch (\Exception $e) {
        DB::rollback();

        return response()->json([
            'message' => 'Failed to add item',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function companyItems (Request $request){
        $companyId = $request->user()->company_id; 
        
        $items = DB::table('items_db')
        ->join('users', 'items_db.owned_by', '=', 'users.id')
        ->join('company_db', 'items_db.under_company', '=', 'company_db.id')
        ->where('items_db.under_company', $companyId)
        ->select('items_db.*', 'users.name as owned_by', 'company_db.company_name as under_company')
        ->paginate(20); 
        
        return response()->json([
            'status' => 'success',
            'data' => $items
        ], 200);
    }

    public function userItems (Request $request){
        $userId = $request->user()->id; 

        $items = DB::table('items_db')
            ->join('users', 'items_db.owned_by', '=', 'users.id')
            ->join('company_db', 'items_db.under_company', '=', 'company_db.id')
            ->where('items_db.owned_by', $userId)
            ->select('items_db.*', 'users.name as owned_by', 'company_db.company_name as under_company')
            ->paginate(20);
            
        return response()->json([
            'status' => 'success',
            'data' => $items
        ], 200);
    }
}
