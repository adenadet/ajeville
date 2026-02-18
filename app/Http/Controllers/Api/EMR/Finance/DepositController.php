<?php

namespace App\Http\Controllers\Api\EMR\Finance;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\Finance\BranchBank;
use App\Models\Finance\PaymentMode;
use App\Models\Finance\Deposit;
use App\Models\Finance\Payment;
use App\Models\EMR\VisitTransaction;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {   
        return response()->json([
            'deposits' => Deposit::where('status', '=', 1)->orderBy('date', 'DESC')->get(),
            'modes' => PaymentMode::where('status', '=', 1)->orderBy('name', 'ASC')->get(),
            'banks' => BranchBank::where('status', '=', 1)->orderBy('bank_name', 'ASC')->get(),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'modes' => PaymentMode::where('status', '=', 1)->orderBy('name', 'ASC')->get(),
            'banks' => BranchBank::where('status', '=', 1)->orderBy('bank_name', 'ASC')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_id'            => 'required|numeric',
            'bank_id'               => 'required|numeric',
            'mode_id'               => 'required|numeric',
            'amount'                => 'required|numeric', 
        ]);
        $patient = Patient::where('id', '=', $request->input('patient_id'))->first();

        $deposit = Deposit::create([
            'date'          => $request->input('date') ?? date('Y-m-d'),
            'patient_id'    => $request->input('patient_id'),
            'mode_id'       => $request->input('mode_id'),
            'bank_id'       => $request->input('bank_id'),
            'amount'        => $request->input('amount'),
            'collected_by'  => $request->input('collected_by') ?? auth('api')->id(),
            'collected_at'  => $request->input('collected_at') ?? date('Y-m-d H:i:s'),
            'created_by'    => auth('api')->id(),
            'updated_by'    => auth('api')->id(),
        ]);

        $patient->balance = $patient->balance + $deposit->amount;
        $patient->save();
        
        foreach ($request->input('transactions') as $transaction){
            //Add the new payment
            $payment = Payment::create([
                'date'              => date('Y-m-d'),
                'transaction_id'    => $transaction['id'],
                'source'            => 1,
                'amount'            => $transaction['amount'],
                'status'            => 1,
                'created_by'        => auth('api')->id(),
                'updated_by'        => auth('api')->id(),
            ]);

            $patient->balance = $patient->balance - $payment->amount;
            $patient->save();

            //Get a list of all confirmed payments
            $payments = Payment::where('transaction_id', '=', $transaction['id'])->where('status', '=', 1)->get();
            $total_payments = $payments->sum('amount');

            //Check if total payments is equal to the transaction total amount
            $transaction = VisitTransaction::where('id', '=', $transaction['id'])->first();
            if ($transaction->item_total == $total_payments){
                $transaction->status = 1;
                $transaction->updated_by = auth('api')->id();
                $transaction->save();
            }
        }
    }

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
