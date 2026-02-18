<?php
namespace App\Http\Controllers\Api\EMR\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\Finance\DepositTrait;
use App\Http\Traits\TransactionTrait;
use App\Models\EMR\Patient\Patient;
use App\Models\Finance\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    use DepositTrait, VisitTransactionTrait;

    public function destroy($id)
    {
        $deposit = $this->emr_visit_payment_deactivate($id);

        return response()->json([
            'deposit' => $deposit,
        ], is_string($deposit) ? 404 : 201);
    }


    public function index()
    {
        $deposits = $this->emr_visit_payment_get_all(null, null, true, true);
        return response()->json([
            'deposit' => $deposits,
        ], is_string($deposits) ? 500 : 200);
    }

    public function show($id)
    {
        $deposit = $this->emr_visit_payment_get_by(null, $id, true);
    
        return response()->json([
            'deposit' => $deposit,
        ], is_string($deposit) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $deposit = $this->emr_visit_payment_create($request);
    
        return response()->json([
            'deposit' => $deposit,
        ], is_string($deposit) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $deposit = $this->emr_visit_payment_update($request, $id);
    
        return response()->json([
            'deposit' => $deposit,
        ], is_string($deposit) ? 404 : 200);
    }
}
