<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\UMS\LogTrait;

class AuthCodeController extends Controller
{
    use LogTrait, TransactionTrait;
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        foreach ($request->input('transactions') as $trans){
            $transaction = Transaction::where('id', $trans->id)->withSum('payments', 'amount')->first();
            $transaction->item_unit_price = $request['item_unit_price'];
            $transaction->item_total = $request['item_unit_price'] * $transaction->item_qty;
            $transaction->updated_by = Auth::id() ?? auth('api')->id();
            $transaction->paid_by = 3;
            $transaction->save();


            Payment::create([
                'transaction_id'    => $transaction->id(),
                'source'            => 2,
                'plan_id'           => $request->input('plan_id') ?? $transaction->care_id,
                'amount'            => $transaction->amount(),
                'auth_code'         => $request->input('auth_code'),
                'auth_channel'      => $request->input('request_method'),
                'auth_personnel'    => $request->input('contact_person'),
                'auth_description'  => $request->input('auth_description'),
                'status'            => 1,
                'created_by'        => auth('api')->id(),
                'created_at'        => auth('api')->id(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
