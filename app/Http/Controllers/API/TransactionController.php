<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddTransactionRequest;
use App\Models\Company;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
   
    public function AddTransaction(AddTransactionRequest $request){
        DB::beginTransaction();
        $configuration = \Uploadcare\Configuration::create(config('app.uploadcare_public'), config('app.uploadcare_secret'));
        $uploader = (new \Uploadcare\Api($configuration))->uploader();
        $transactionID = 'TXN' . time() . rand(1000, 9999);
        $transactionFrom = Company::where('company_name', $request->ship_from)->first()->id;
        $registerBy = auth()->user()->id;
        
        try {
            $image = empty($request->image) ? '' : $uploader->fromPath($request->image, 'image/jpeg');
            $user = User::where('email', $request->ship_to)->first();
            $transaction = new Transaction();
            $transaction->transaction_id = $transactionID;
            $transaction->description = $request->description;
            $transaction->transaction_from = $transactionFrom;
            $transaction->registered_by = $registerBy;
            $transaction->courier_name = $request->courier_name;
            $transaction->transaction_status = 1;
            $transaction->ship_from = $request->ship_from;
            $transaction->ship_to = $user->id;
            $transaction->image_link = empty($image) ? 'none' : 'https://ucarecdn.com/' . $image->getUuid() . '/-/preview/500x500/-/quality/smart/-/format/auto/';
            $transaction->save();
            foreach ($request->transaction_item as $item) {
                $transactionItem = new TransactionItem();
                $transactionItem->transaction_id = $transaction->id;
                $transactionItem->item_id = $item['id'];
                $transactionItem->quantity = 99;
                $transactionItem->status_id = 1;
                $transactionItem->save();
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction added successfully',
                'data' => $transaction
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to add transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function transactionIdex(){
    //     $transactions = Transaction::with('ship_to', 'register_by')->paginate(10);
    //     foreach ($transactions as $transaction) {
    //         $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
    //         $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->get();
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $transactions
    //     ], 200);
    // }
  
    public function transactionIdex(){
        $transactions = Transaction::with(['ship_to', 'registered_by'])->paginate(10);
        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->get();
        }
        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

    

    // public function transactionIdex(){
    //     $transactions = Transaction::paginate(10);
    //     foreach ($transactions as $transaction) {
    //         $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
    //         $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->get();
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $transactions
    //     ], 200);
    // }
}
