<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\Finance\TransactionTrait;
use App\Models\EMR\LaboratoryRequest;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;
use Illuminate\Http\Request;

class LaboratoryController extends Controller
{
    use TransactionTrait;

    public function index()
    {
        //return response()->json(['drugs' => $drugs->with('specific_drugs')->limit(10)->get(),]);
    }
    
    public function initials()
    {
        return response()->json([
            'services' => Item::where('service_id', '=', 7)->select('id', 'name')->get(),
        ]);
    }

    public function search()
    {
        //return response()->json(['drugs' => $drugs->with('specific_drugs')->limit(10)->get(),]);
    }


    public function show($id)
    {
        return response()->json([
            'request' => LaboratoryRequest::where('id', '=', $id)->with(['patient.user', 'branch', 'creator', 'item', 'reporter', 'secondary_reporter', 'approver', 'collector'])->first(),
        ]);
    }

    public function store(Request $request)
    { 
        foreach ($request->input('investigations') as $investigation){
            $transaction = $this->createTransaction($investigation['service_id'], $investigation['patient_id'], $investigation['quantity'], false, $investigation['visit_id']);

            LaboratoryRequest::create([
                'visit_id' => $request->input('visit_id'),
                'date' => $transaction->date,
                'patient_id' => $request->input('patient_id'),
                'consultation_id' => $request->input('consultation_id') ?? NULL,
                'transaction_id' => $transaction->id,
                'item_id'   => $investigation['id'],
                'category_id' => $investigation['category_id'],
                'status' => 0,
                'description' => $investigation['description'],
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $laboratory_request = LaboratoryRequest::where('id', '=', $id)->first();
        
        if ($laboratory_request->status == 1){
            return response()->json([
                'message' => 'This has already been processed, cannot delete it',
                'icon' => 'error',
            ]);
        }
        $laboratory_request->deleted_at = date('Y-m-d H:i:s');
        $laboratory_request->deleted_by = auth('api')->id();

        $laboratory_request->save();

        return response()->json([
            'message' => 'This has already been processed, cannot delete it',
            'icon' => 'error',
        ]);
    

    }
}
