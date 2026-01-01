<?php

namespace App\Http\Controllers\Api\Approvals;

use App\Http\Controllers\Controller;
use App\Models\Approvals\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\Sales\OrderTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
{
    use OrderTrait;
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try{
            switch($request['reference_type']){
                case 'returns':
                    $query = $this->sales_return_confirm($request, $request['reference_id']);
                    $reference_type = 'App\Models\Sales\OrderReturn';
                break;
            }

            $action = [];
            if (!is_string($query)){
                $action = Action::create([
                    'decision' => $request['decision'],            // e.g. approved, rejected, forwarded
                    'description' => $request['description'],
                    'reference_type' => $reference_type,   // polymorphic type (Invoice, PurchaseOrder...)
                    'reference_id' => $request['reference_id'],     // polymorphic id
                    'created_by' => Auth::id() ?? auth('api')->id(), 
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);
            }

            return response()->json([
                'action' => $action,
                'query' => $query,
            ], is_string($query) ? 500 : 201);

        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
