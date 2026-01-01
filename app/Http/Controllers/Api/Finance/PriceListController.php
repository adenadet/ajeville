<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Operations\PriceListTrait;
use Illuminate\Http\Request;

use App\Models\Branch;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Insurance\ProviderType;
use App\Models\Inventory\Item;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
    use ItemTrait, PriceListTrait;
    public function index()
    {
        return response()->json([
            'price_lists' => $this->operation_price_list_get_all($_GET['type'] ?? 'all', $_GET['query'] ?? null, true, true, $_GET['page'] ?? 1),             
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => Branch::orderBy('name', 'ASC')->get(),
            'provider_types' =>  ProviderType::select('id', 'name')->orderBy('name', 'ASC')->get()            
        ]);
    }

    public function plans($id)
    {
        $_GET['price_list_id'] = $id;
        $branch_price_lists = $this->operation_branch_plan_price_list_get_all( 'active', $_GET,  true, true);
        return response()->json([
            'branch_price_lists' => $branch_price_lists,          
        ]);
    }

    public function search(Request $request, $id)
    {
        $price_list_items = $this->operation_price_list_item_search_by_query($request,$id);
        
        return response()->json([
            'price_list' => PriceList::where('id', '=', $id)->with(['branch', 'plan.provider'])->first(),
            'price_list_items' => $price_list_items,
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'price_list' => $this->operation_price_list_get_by('id', $id, true),
            'price_list_items' => $this->operation_price_list_item_get_by_price_list_id($id, null, true),
        ]);
    }

    public function store(Request $request)
    {
        $price_list = $this->operation_price_list_create($request);

        return response()->json([
            'price_list' => $price_list,             
        ], is_string($price_list) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $price_list = $this->operation_price_list_update($request, $id);

        return response()->json([
            'price_list' => $price_list,             
        ], is_string($price_list) ? 500 : 200);
    }

    public function update_item_price(Request $request, $id)
    {
        $price_list = PriceList::where('id', '=', $id)->first();
        $success_count = 0;
        $error_count = 0;
        $error = [];
        foreach ($request->input('items') as $price){
            echo $price['price'];
            if (is_null($price['price'])){

            }
            else{
                $this->operation_price_list_item_update($price, $id);
                if (is_string($price)){
                    array_push($error, $price);
                    $error_count++; 
                }
                else{
                    $success_count++;
                }
            }
        }
        return response()->json([
            'error_count' => $error_count,
            'error' => $error,
            'price_list' => PriceList::where('id', '=', $id)->with(['branch', 'plan.provider'])->first(),
            'price_list_items' => $this->operation_price_list_item_get_by_price_list_id($price_list->id, null, true),
            'success_count' => $success_count,
        ]);
    }

    public function update_items(Request $request, $id)
    {
        
        foreach ($request->input('items') as $item){
            $this->operation_price_list_item_update_by_price_list_id($item, $id);
        }
        return response()->json([
            'price_list' => PriceList::where('id', '=', $id)->with(['branch', 'plan.provider'])->first(),
            'price_list_items' => $this->operation_price_list_item_get_by_price_list_id($id, null, true),
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
