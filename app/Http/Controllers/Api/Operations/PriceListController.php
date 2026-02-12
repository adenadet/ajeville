<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\FileTrait;
use App\Http\Traits\Operations\PriceListTrait;
use App\Http\Traits\Operations\ServiceTrait;
use App\Imports\Finance\PriceListImport;
use App\Models\Operations\Branch;
use App\Models\EMR\Service;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Insurance\Plan;
use App\Models\Insurance\ProviderType;
use App\Models\Inventory\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class PriceListController extends Controller
{
    use FileTrait, PriceListTrait, ServiceTrait;
    public function destroy($id)
    {
        return response()->json([
            'price_list' => $this->operation_price_list_delete($id),
            'price_lists' => $this->operation_price_list_get_all('all', null, true, true, $_GET['page']??1),             
        ]);
    }
    
    public function import(Request $request, $id)
    {
        $upload = $this->file_upload_by_type($request['uploaded_file'], 'xlsx', 'upload/price_lists', $id);
        $price_list_items = Excel::import(new PricelistImport($id), $upload);

        /*return response()->json([
            'branches' => Branch::orderBy('name', 'ASC')->get(),
            'plans' => Plan::orderBy('name', 'ASC')->with('provider')->get(),
            'provider_types' =>  ProviderType::select('id', 'name')->orderBy('name', 'ASC')->get(),           
        ]);*/
    }

    public function index()
    {
        return response()->json([
            'price_lists' => $this->operation_price_list_get_all('all', null, true, true, $_GET['page']??1),             
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => Branch::orderBy('name', 'ASC')->get(),
            'plans' => Plan::orderBy('name', 'ASC')->with('provider')->get(),
            'provider_types' =>  ProviderType::select('id', 'name')->orderBy('name', 'ASC')->get(),           
        ]);
    }

    
    public function search(Request $request, $id)
    {
        $items = Item::select(DB::raw('`inventory_items`.`id` as `item_id`'),
            DB::raw('`inventory_items`.`name`'), 
            DB::raw('`inventory_items`.`service_type_id`'), 
            DB::raw('`emr_service_types`.`name` as `service_name`'), 
            DB::raw('`emr_service_types`.`price` as fallback_price'), 
            DB::raw('`finance_price_list_items`.`plan_id`'),
            DB::raw('`finance_price_list_items`.`price`'),
            DB::raw('`finance_price_list_items`.`covered`'),
            DB::raw('`finance_price_list_items`.`requires_code`'),
        );
        
        $items = $items->with(['category', 'service'])
            ->leftJoin('finance_price_list_items', function($join) use ($id){
                $join->on('inventory_items.id', '=', 'finance_price_list_items.item_id');
                $join->on('finance_price_list_items.item_id','=', DB::raw($id));
            })    
            ->orderBy('name', 'ASC')
            ->get();
        
        return response()->json([
            'price_list' => $this->operation_price_list_get_price_list_by_id($id, true),
            'price_list_items' => $items,
        ]);
    }
    
    public function show($id)
    {
        return response()->json([
            'categories' => [],
            'services' => $this->operation_service_type_get_all('all', null, false, true),
            'price_list' => $this->operation_price_list_get_price_list_by_id($id, true),
            'price_list_items' => $this->operation_price_list_item_get_by_price_list_id($id, null, true),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'price_list' => $this->operation_price_list_create_new($request),
            'price_lists' => $this->operation_price_list_get_all('all', null, true, true, $_GET['page'] ?? 1),
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $price_list = $this->operation_price_list_update_price_list($request, $id);
        return response()->json([
            'price_lists' => $this->operation_price_list_get_all('all', null, true, true, $_GET['page'] ?? 1),          
        ]);
    }

}
