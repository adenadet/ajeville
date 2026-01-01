<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;
use App\Models\EMR\LaboratoryRequest;
use App\Models\EMR\RadiologyRequest;
use App\Models\EMR\Patient;
use App\Models\EMR\Visit;

class RadiologyController extends Controller
{
    public function initials()
    {
        return response()->json([
            'services' => Item::where('service_id', '=', 2)->select('id', 'name')->get(),
        ]);
    }

    public function search()
    {
        //return response()->json(['drugs' => $drugs->with('specific_drugs')->limit(10)->get(),]);
    }


    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {
        
        foreach ($request->input('investigations') as $investigation){
            $transaction = Transaction::create([
                'visit_id' => $request->input('visit_id'),        
                'date' => $request->input('date') ?? date('Y-m-d') ,
                'patient_id' => $request->input('patient_id'),
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

            RadiologyRequest::create([
                'visit_id' => $request->input('visit_id'),
                'date' => $transaction->date,
                'patient_id' => $request->input('patient_id'),
                'consultation_id' => $request->input('consultation_id'),
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $radiology_request = RadiologyRequest::where('id', '=', $id)->first();
        
        if ($radiology_request->status == 1){
            return response()->json([
                'message' => 'This has already been processed, cannot delete it',
                'icon' => 'error',
            ]);
        }
        $radiology_request->deleted_at = date('Y-m-d H:i:s');
        $radiology_request->deleted_by = auth('api')->id();

        $radiology_request->save();

        return response()->json([
            'message' => 'This has already been processed, cannot delete it',
            'icon' => 'error',
        ]);
    }
}
