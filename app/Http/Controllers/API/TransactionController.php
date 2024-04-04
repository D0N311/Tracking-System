<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddTransactionRequest;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    public function addTransaction(AddTransactionRequest $request)
    {
        info($request->all());
        $configuration = \Uploadcare\Configuration::create(config('app.uploadcare_public'), config('app.uploadcare_secret'));
        $uploader = (new \Uploadcare\Api($configuration))->uploader();
        $transactionID = 'TXN' . time() . rand(1000, 9999);
        $transaction_from_company = User::where('id', auth()->id())->first()->company_id;
        $userID = auth()->id();

        DB::beginTransaction();
        try{    
            $image = empty($request->image) ? '' : $uploader->fromPath($request->image, 'image/jpeg');

            $transaction = new Transaction();
            $transaction->description = $request->description;
            $transaction->transaction_from = $transaction_from_company;
            $transaction->transaction_id = $transactionID;
            $transaction->image_link = empty($image) ? 'none' : 'https://ucarecdn.com/' . $image->getUuid() . '/-/preview/500x500/-/quality/smart/-/format/auto/';
            $transaction->courier_name = $request->courier_name;
            $transaction->ship_from = $request->ship_from;
            $transaction->ship_to = $request->ship_to;
            $transaction->registered_by = $userID;
            $transaction->transaction_status = 'pending';
            $transaction->save();

            foreach ($request->transaction_item as $item) {
                $transactionItem = new TransactionItem();
                $transactionItem->transaction_id = $transaction->id;
                $transactionItem->item_id = $item['item_id'];
                $transactionItem->quantity = $item['quantity'];
                $transactionItem->status_id = 1;
                $transactionItem->save();
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction added successfully',
                'data' => $transactionID
            ], 201);

           
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => 'Failed to add transaction',
                'error' => $e->getMessage()
            ], 500);
        }
}
}
