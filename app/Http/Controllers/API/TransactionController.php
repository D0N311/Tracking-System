<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddTransactionRequest;
use App\Http\Requests\ApprovalRequest;
use App\Models\Company;
use App\Models\Items;
use App\Models\TransactionItem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    private $configuration;
    private $uploader;

    public function __construct()
    {
        $this->configuration = \Uploadcare\Configuration::create(config('app.uploadcare_public'), config('app.uploadcare_secret'));
        $this->uploader = (new \Uploadcare\Api($this->configuration))->uploader();
    }
   
    public function AddTransaction(AddTransactionRequest $request){
        DB::beginTransaction();
        
        $transactionID = 'TXN' . time() . rand(1000, 9999);
        $transactionFrom = Company::where('company_name', $request->ship_from)->first()->id;
        $registerBy = auth()->user()->id;
        
        try {
            $image = empty($request->image) ? '' : $this->uploader->fromPath($request->image, 'image/jpeg');
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
        }
       
         catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to add transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function transactionIdex(){
        $companyId = auth()->user()->company_id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('transaction_from', $companyId)
            ->where('transaction_status', 1)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

       public function inProgressIndex(){
        $companyId = auth()->user()->company_id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('transaction_from', $companyId)
            ->where('transaction_status', 2)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

    public function cancelIndex(){
        $companyId = auth()->user()->company_id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('transaction_from', $companyId)
            ->where('transaction_status', 4)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

    
    public function deliverIndex(){
        $companyId = auth()->user()->company_id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('transaction_from', $companyId)
            ->where('transaction_status', 3)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }




    public function approveTransaction(ApprovalRequest $request){
        $user = auth()->user();
        DB::beginTransaction();
        try{
        if (!$user->getRoleNames()->contains('Admin')) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Only admins can approve transactions'
            ], 403);
        }   
     
        $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();
        if ($transaction->transaction_status != 1 && $transaction->transaction_status != 4) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Transaction already approved'
            ], 400);
        }
        $transaction->transaction_status = 2;
        $transaction->approved_at = now();
        $transaction->save();
        $transactionItems = TransactionItem::where('transaction_id', $transaction->id)->get();

        foreach($transactionItems as $item){
            Log::info('Processing transaction item: ' . $item->id);
            $item->status_id = 2;
            $item->save();
        }
        DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'Transaction approved successfully',
            'data' => $transaction
        ], 200);
    }catch(\Exception $e){
        DB::rollback();
        return response()->json([
            'status' => 'failed',
            'message' =>  $e->getMessage()
        ], 500);
    }
    }


    public function cancelTransaction(ApprovalRequest $request){
        $user = auth()->user();
        DB::beginTransaction();
        try{
            if (!$user->getRoleNames()->contains('Admin')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Only admins can cancel transactions'
                ], 403);
            }
         
            $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();
            if ($transaction->transaction_status != 1 && $transaction->transaction_status != 2) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaction cannot be cancelled'
                ], 400);
            }
            $transaction->transaction_status = 4;
            $transaction->cancelled_at = now();
            $transaction->save();
            $transactionItems = TransactionItem::where('transaction_id', $transaction->id)->get();
    
            foreach($transactionItems as $item){
                Log::info('Processing transaction item: ' . $item->id);
                $item->status_id = 4;
                $item->save();
                Log::info('Updated status_id for transaction item: ' . $item->id);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction cancelled successfully',
                'data' => $transaction
            ], 200);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' =>  $e->getMessage()
            ], 500);
        }
    }

    public function HistoryIndex(){
        $companyId = auth()->user()->company_id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('transaction_from', $companyId)
            ->where('transaction_status', '<>', 1)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

    
    
    public function recieveTransaction (ApprovalRequest $request) {
        

        $user = auth()->user();
        $companyId = $user->company_id;
        DB::beginTransaction();
        try{
            $image = empty($request->image) ? '' : $this->uploader->fromPath($request->image, 'image/jpeg');
            if (!$user->getRoleNames()->contains('User')) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'You cant recieve transactions'
                ], 403);
            }
         
            $transaction = Transaction::where('transaction_id', $request->transaction_id)->first();
            if ($transaction->transaction_status != 2) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Transaction already recieved'
                ], 400);
            }
            $transaction->transaction_status = 3;
            $transaction->recieved_at = now();
            $transaction->r_description = $request->r_description;
            $transaction->r_image_link = empty($image) ? 'none' : 'https://ucarecdn.com/' . $image->getUuid() . '/-/preview/500x500/-/quality/smart/-/format/auto/';
            $transaction->save();
            $transactionItems = TransactionItem::where('transaction_id', $transaction->id)->get();

            foreach($transactionItems as $item){
                $item->status_id = 3;
                $item->save();
                $relatedItem = Items::find($item->item_id);
                if ($relatedItem) {
                    $relatedItem->owned_by = $transaction->ship_to;
                    $relatedItem->under_company = $companyId;
                    $relatedItem->save();
                }

            }
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Transaction recieved successfully',
                'data' => $transaction
            ], 200);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json([
                'status' => 'failed',
                'message' =>  $e->getMessage()
            ], 500);
        }
    }

    public function userToRecieveIndex (){
        $userId = auth()->user()->id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('ship_to', $userId)
            ->where('transaction_status', 2)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

    public function transRecievedIndex (){
        $userId = auth()->user()->id;

        $transactions = Transaction::with(['ship_to', 'registered_by', 'transaction_status'])
            ->where('ship_to', $userId)
            ->where('transaction_status', 3)
            ->paginate(10);

        foreach ($transactions as $transaction) {
            $transaction->items_count = TransactionItem::where('transaction_id', $transaction->id)->count();
            $transaction->items = TransactionItem::where('transaction_id', $transaction->id)->with('item')->get();
        }

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ], 200);
    }

}
