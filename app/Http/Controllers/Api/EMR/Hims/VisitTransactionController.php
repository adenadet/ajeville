<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\EMR\VisitTransactionTrait;
use Illuminate\Http\Request;

class VisitTransactionController extends Controller
{
    use VisitTrait, VisitTransactionTrait;
    public function destroy($id)
    {
        $transaction = $this->emr_visit_transaction_deactivate($id);

        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 404 : 200);

    }

    public function index()
    {
        $transactions = $this->emr_visit_transaction_get_all($_GET['type'], $_GET, true, true);
        return response()->json([
            'transactions' => $transactions,
        ], is_string($transactions) ? 500 : 200);
    }

    public function pending($id)
    {
        $transactions = $this->emr_visit_transaction_get_all('unconfirmed', ['patient_id' => $id], true, false);
        
        return response()->json([
            'transactions' => $transactions,
        ], is_string($transactions) ? 500 : 200);

    }

    public function store(Request $request)
    {
        $transactions = $this->emr_visit_transaction_create_multiple($request);
        return response()->json([
            'transactions' => $transactions,
        ], is_string($transactions) ? 500 : 201);
    }

    public function show($id)
    {
        $transaction = $this->emr_visit_transaction_get_by($id, true);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 404 : 200);
    }

    public function update(Request $request, $id)
    {
        $transaction = $this->emr_visit_transaction_update($request, $id);
        return response()->json([
            'transaction' => $transaction,
        ], is_string($transaction) ? 500 : 200);
    }

}
