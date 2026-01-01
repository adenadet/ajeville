<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Visit;
use Illuminate\Http\Request;

use App\Models\Finance\Transaction;

class TransactionController extends Controller
{
    use LogTrait, MainTransactionTrait, TransactionTrait;

    public function index()
    {
        return response()->json([
            'transactions' => $this->finance_main_transaction_get_all($_GET['type'] ?? 'all', $_GET['query'] ?? null, true, true),
        ]); 
    }

    public function show($id)
    {
        return response()->json([
            'transaction' => $this->finance_main_transaction_get_by(null, $id, true),
        ]);
    }

    
    public function store(Request $request)
    {
        $transaction = $this->finance_transaction_create($request['item_id'], $request['patient_id'], $request['quantity'], $request['auto_debit = false'], $request['visit_id']);
        if(is_string($transaction)){
            return response()->json([
                'message' => 'Transaction Created Successfully',
                'icon' => 'success',
                'transaction' => $transaction,
            ]);
        }
        else{
            return response()->json([
                'message' => 'Transaction was not created',
                'icon' => 'error',
                'error' => $transaction,
            ], 500);
        }
    }

    public function patient_pending($id)
    {
        return response()->json([
            'patient' => Patient::where('id', '=', $id)->with(['insurances', 'user', ])->first(),
            'transactions' => Transaction::where('status', '=', '0')->where('patient_id', '=', $id)->with(['creator', 'payments.creator', 'patient.user', 'service_type', 'visit'])->latest()->get(),
        ]);
    }

    public function patient_transactions($id)
    {
        return response()->json([
            'patient' => Patient::where('id', '=', $id)->with(['insurances', 'user', ])->first(),
            'transactions' => Transaction::where('patient_id', '=', $id)->with(['creator', 'payments.creator', 'patient.user', 'service_type', 'visit', ])->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $transaction = Transaction::where('id', '=', $id)->first();

        $visit_id = $transaction->visit_id;
        $patient_id = $transaction->patient_id;

        $this->cancelTransaction($id);

        return response()->json([
            'message' => 'Transaction Cancelled Successfully',
            'icon' => 'success',
            'visit' => Visit::where('id', '=', $visit_id)->with(['patient.user', 'visit_type', 'price_list.price_list_items', 'transactions.service_type'])->first(),
            'patient' => Patient::where('id', '=', $patient_id)->with(['user', 'insurances.plan.provider', 'transactions.service_type'])->first(),
        ]);
    }
}
