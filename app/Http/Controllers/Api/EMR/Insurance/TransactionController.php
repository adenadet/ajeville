<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadesRequest;

use App\Models\Finance\Transaction;
use App\Models\EMR\Visit;

class TransactionController extends Controller
{
    use InsuranceTrait;
    public function index()
    {
        $query = FacadesRequest::get('q');
    
        $transactions = $this->insurance_transaction_get_all($query, null, true, true, $_GET['page'] ?? 1); 

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
