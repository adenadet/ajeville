<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Inventory\TransferOrderTrait;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    use ItemTrait, PatientTrait, StoreTrait, TransactionTrait, TransferOrderTrait, VisitTrait;

    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        if(isset($_GET['query'])){
            $transfer_order = $this->inventory_sales_order_get_all('search', ['query' => $_GET['query'], 'status' => $_GET['status'], 't' => $_GET['t'] ?? 'out',],
            true, true, $_GET['page'] ?? 1);
        }
        else if ($_GET['status'] != 'all'){
            $transfer_order = $this->inventory_sales_order_get_all('status', ['status' => $_GET['status'], 't' => $_GET['t'] ?? 'in',], true, true, $_GET['page'] ?? 1);    
        }
        else{
            $transfer_order = $this->inventory_sales_order_get_all($_GET['t'] ?? 'in', [], true, true, $_GET['page'] ?? 1);
        }
        return response()->json([
            'transfer_orders' => $transfer_order,
        ]);

    }

    public function initials()
    {
        return response()->json([
            'items' => $this->inventory_item_get_all('consumable', '', false, false, null),
            'my_stores' => $this->inventory_store_user_get('my_stores', null, false, false, null),
            'patients' => $this->emr_patient_get_all('all', null, false, false, null),
            'visits' => $this->emr_visit_get_all('branch_active', null, false, false, null),
        ]);        
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'description' => 'sometimes', 
            'issuing_store_id' => 'required|numeric', 
            'patient_id' => 'required|numeric',
            'items' => 'required|array', 
        ]);

        $transfer_order = $this->inventory_sales_order_create($request);
        return response()->json([
            'transfer_order' => $transfer_order,
        ], status: is_string($transfer_order) ? 500: 200);
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
