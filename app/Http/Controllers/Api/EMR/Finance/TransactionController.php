<?php

namespace App\Http\Controllers\Api\EMR\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Visit;
use Illuminate\Http\Request;

use App\Models\EMR\VisitTransaction;

class TransactionController extends Controller
{
    use LogTrait, VisitTransactionTrait;

    public function destroy($id)
    {
        $transaction = VisitTransaction::where('id', '=', $id)->first();

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
    
    public function index()
    {
        return response()->json([
            'transactions' => VisitTransaction::whereNull('verified_by')->with(['service_type', 'visit'])->latest()->paginate(30),
        ]); 
    }

    public function store(Request $request)
    {
        //
    }

    public function patient_pending($id)
    {
        return response()->json([
            'patient' => Patient::where('id', '=', $id)->with(['insurances', 'user', ])->first(),
            'transactions' => VisitTransaction::where('status', '=', '0')->where('patient_id', '=', $id)->with(['creator', 'payments.creator', 'patient.user', 'service_type', 'visit'])->latest()->get(),
        ]);
    }

    public function patient_transactions($id)
    {
        return response()->json([
            'patient' => Patient::where('id', '=', $id)->with(['insurances', 'user', ])->first(),
            'transactions' => VisitTransaction::where('patient_id', '=', $id)->with(['creator', 'payments.creator', 'patient.user', 'service_type', 'visit', ])->latest()->paginate(20),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'transaction' => VisitTransaction::where('id', '=', $id)->with(['creator', 'payments.creator', 'patient.user', 'service_type', 'visit', ])->first(),
        ]);
    }

    public function payment($id){
        $transaction = $this->emr_visit_transaction_payment($id, $_GET['forced']);

        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 200);
    }

    public function update(Request $request, $id)
    {
        
    }
}
