<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Models\Finance\Payment;
use Illuminate\Http\Request;


class ClaimsController extends Controller
{
    use InsuranceTrait;
    
    public function claims(Request $request)
    {
        return response()->json([
            'report' => $this->insurance_claims_get_all($request),
        ]);    
    }
    
    public function index()
    {
        return response()->json([
            'payments' => Payment::where('source', '=', 2)->with(['plan.provider', 'patient.user', ''])->latest()->paginate(50),
        ]);
    }
    
    public function store(Request $request)
    {
        
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
