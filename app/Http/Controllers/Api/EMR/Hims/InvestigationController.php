<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EMR\Visit;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;
use App\Models\EMR\LaboratoryRequest;
use App\Models\EMR\RadiologyRequest;
use App\Models\EMR\Patient;

class InvestigationController extends Controller
{
    public function index()
    {
        
    }

    public function initials($id)
    {
        $visit = Visit ::where('unique_id', '=', $id)->with(['patient', 'transactions'])->first();
        if ($visit !== null){
            return response()->json([
                'laboratory_services' => Item::select('id', 'name', 'category_id', 'service_id')->where('service_id', '=', 7)->with('category')->get(),
                'radiology_services' => Item::select('id', 'name', 'category_id', 'service_id')->where('service_id', '=', 2)->with('category')->get(),
                'visit' => $visit,
            ]);
        }
        else{
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid Visit',
            ]);
        }
    }

    public function store(Request $request)
    {
        $visit = Visit ::where('id', '=', $request->input('visit_id'))->first();
        foreach ($request->input('investigations') as $investigation){
            $transaction = Transaction::create([
                'visit_id' => $visit->id,
                'date' => $request->input('date') ?? date('Y-m-d') ,
                'patient_id' => $visit->patient_id,
                'item_id'   => $investigation['id'],
                'service_type_id' => $investigation['service_id'],
                'item_qty'  => $investigation['quantity'],
                'item_name' => $investigation['name'],
                'item_unit_cost' => $item_price ?? 400000,
                'item_total' => ($item_price ?? 400000) * $investigation['quantity'],
                'status' => 0,
                'paid_by' => 1,
                'care_id' => NULL,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            if($transaction->service_type_id == 7){
                LaboratoryRequest::create([
                    'visit_id' => $visit->id,
                    'date' => $transaction->date,
                    'patient_id' => $visit->patient_id,
                    'transaction_id' => $transaction->id,
                    'item_id'   => $investigation['id'],
                    'category_id' => $investigation['category_id'],
                    'status' => 0,
                    'description' => $investigation['description'],
                    'created_by' => auth('api')->id(),
                    'updated_by' => auth('api')->id(),
                ]);
            }

            else if ($transaction->service_type_id == 2){
                RadiologyRequest::create([
                    'visit_id' => $visit->id,
                    'date' => $transaction->date,
                    'patient_id' => $visit->patient_id,
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
