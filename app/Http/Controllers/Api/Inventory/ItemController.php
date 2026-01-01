<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\SettingsTrait;
use App\Imports\Inventory\ItemImport;
use App\Models\EMR\Settings\ServiceType;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Inventory\Classification;
use App\Models\Inventory\Item;

use App\Models\Inventory\TransferOrderItem;
use App\Models\Operations\Branch;
use App\Models\Procurement\Batch;
use App\Models\Procurement\PackageType;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use App\Models\Procurement\ReceiveNote;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;

class ItemController extends Controller
{
    use ItemTrait, PharmacyTrait, SettingsTrait;
    
    public function bulk_update(Request $request)
    {
        $result = [
            'created' => 0,
            'updated' => 0,
            'unprocessed' => 0,
            'error_log' => [],
        ];

        foreach($request['items'] as $item){
            $query = Item::find($item['id']);
            $quest = $query ? $this->inventory_item_update($item, $item['id']) : $this->inventory_item_create($item);

            if($query && !is_string($quest)){
                $result['updated']++;
            }
            else if(!$query && !is_string($quest)){
                $result['created']++;
            }
            else{
                $result['unprocessed']++;
                array_push($result['error_log'], $quest);
            }

        }

        return response()->json($result);
    }

    public function import(Request $request)
    {
        $dent = explode("base64,", $request->input('file'));
        $decodedData = base64_decode($dent[1], true);
        if ($decodedData === false) {
            return response()->json([
                'result' => null,
                'message' => 'The provided string is not valid Base64.',
            ], 500);
        }
        
        $fileSignature = substr($decodedData, 0, 4);
        $validCsvSignature = chr(0xEF) . chr(0xBB) . chr(0xBF); // Optional BOM for UTF-8 CSV
        $validXlsxSignature = chr(0x50) . chr(0x4B) . chr(0x03) . chr(0x04); // XLSX files (PKZIP format)

        if ($fileSignature === $validCsvSignature || strpos($decodedData, ',') !== false) {
            $fileType = "xlsx";
        } 
        elseif ($fileSignature === $validXlsxSignature) {
            $fileType = "xlsx";
        }
        else {
            return response()->json([
                'result' => null,
                'message' => "The Base64 string does not represent a valid CSV or Excel file."
            ]);
        }

        $fileName = 'uploaded_file_'.time().'.'. $fileType;
        $tempPath = public_path('uploads/files/' . $fileName);
        file_put_contents($tempPath, $decodedData);

        try {
            $query = Excel::import(new ItemImport, $tempPath);
            @unlink($tempPath);
            return response()->json([
                'result' => $query,
                'message' => 'The file was imported successfully',
            ]);
        }

        catch(Exception $e){
            @unlink($tempPath);
            return response()->json(['error' => 'Failed to process the file', 'details' => $e->getMessage()], 500);
        }
    }
    
    public function index()
    {
        if (isset($_GET['detailed'])){
            $paginated = $_GET['detailed'] == 'yes' ? true : false;
        }
        if (isset($_GET['paginated'])){
            $paginated = $_GET['paginated'] == 'yes' ? true : false;
        }
        return response()->json([
            'items' => $this->inventory_item_get_all($_GET['type'] ?? 'all', $_GET['query'] ?? null, $detailed ?? true, $paginated ?? true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'brands' => $this->inventory_settings_brand_get_all('active', null, false, false, null),
            'categories' => $this->inventory_item_category_get_all('active', null, false, false, null),
            'classifications' => Classification::select('id', 'name')->get(),
            'drugs' => $this->emr_pharmacy_drug_get_all('active', null, false, false, null),
            'package_types' => PackageType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'types' => ServiceType::where('queueable', '=', 1)->select('id', 'name')->get(),
        ]);
    }

    public function quick_search()
    {
        $filter = $_GET['q'];

        if (!empty($_GET['so_id']) && ($_GET['so_id'] != 0)){
            $so_id = $_GET['so_id'];
            $query = DB::table('inventory_items')
            ->join('sales_order_items', function ($join) use ($so_id) {
                $join->on('inventory_items.id', '=', 'sales_order_items.item_id')
                ->where('sales_order_items.so_id', '=', $so_id);
            })
            ->where(function ($q) use ($filter) {
                $q->where('inventory_items.name', 'like', "%{$filter}%")
                ->orWhere('inventory_items.unique_id', 'like', "%{$filter}%")
                ->orWhere('inventory_items.description', 'LIKE', "%{$filter}%");
            })
            ->whereNull('inventory_items.deleted_at') // Soft delete handling
            ->select('inventory_items.id', 'inventory_items.unique_id', 'inventory_items.name', 'sales_order_items.unit_price')->get();
        } 
        else {
            $branch = Branch::find(request()->cookie('current_branch') ?? auth('api')->user()->branch_id);
            $plan_id = isset($_GET['plan_id']) && !empty($_GET['plan_id']) ? $_GET['plan_id'] : $branch->price_list_id;
            $query = DB::table('inventory_items')
            ->join('finance_price_list_items', function ($join) use ($plan_id) {
                $join->on('inventory_items.id', '=', 'finance_price_list_items.item_id')
                ->where('finance_price_list_items.price_list_id', '=', $plan_id);
            })
            ->where(function ($q) use ($filter) {
                $q->where('inventory_items.name', 'like', "%{$filter}%")
                ->orWhere('inventory_items.unique_id', 'like', "%{$filter}%")
                ->orWhere('inventory_items.description', 'LIKE', "%{$filter}%");
            })
            ->whereNull('inventory_items.deleted_at') // Soft delete handling
            ->select('inventory_items.id', 'inventory_items.unique_id', 'inventory_items.name', 'finance_price_list_items.price')->get();
        }
        
        return $query->count() > 0 ? response()->json(['items' => $query,]) : response()->json(['items' => [],]);
        
    }

    public function report(Request $request){
        $items = $this->inventory_item_get_all($request->input('source'), $request->input('query') ?? null, true, false, null);

        //return($store_items);
        switch ($request->input('type')){
            case 'csv':
                $filename = 'all_items_list_' . now()->format('Ymd_His') . '.csv';
                // Define CSV headers
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"$filename\"",
                ];

                // Open output stream
                $callback = function () use ($items) {
                    $file = fopen('php://output', 'w');

                    // Column headings
                    fputcsv($file, [
                        'Item Name',
                        'Classification',
                        'Category',
                        'Brand',
                        'Last Landing Cost',
                        'Average Landing Cost',
                        'Status',
                    ]);

                    foreach ($items as $item) {
                        
                        fputcsv($file, [
                            $item->name,
                            $item->classification !== null ? $item->classification->name : 'Not Assigned',
                            $item->category != null ? $item->category->name : 'Not Assigned',
                            $item->brand != null ? $item->brand->name : 'Unbranded',
                            $item->last_landing_cost,
                            $item->current_cost_price,
                            $item->status,
                        ]);
                    }

                    fclose($file);
                };
                
                return Response::stream($callback, 200, $headers);
            //break;
        }
    }

    public function search_request(Request $request){
        if (isset($_GET['detailed'])){
            $paginated = $_GET['detailed'] == 'yes' ? true : false;
        }
        if (isset($_GET['paginated'])){
            $paginated = $_GET['paginated'] == 'yes' ? true : false;
        }

        $items = $this->inventory_item_get_all('search_request', $request, $detailed ?? true, $paginated ?? true, $_GET['page'] ?? 1);

        return response()->json([
            'items' =>  $this->inventory_item_get_all('search_request', $request, $detailed ?? true, $paginated ?? true, $_GET['page'] ?? 1),
        ]);
        
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'type_id' => 'nullable|numeric',
            'quantity' => 'sometimes|numeric',
            'minimum_level' => 'sometimes|numeric',
            'current_cost_price' => 'sometimes|numeric',
        ]);

        $item = $this->inventory_item_create($request);
        return response()->json([
            'item' =>  $item,
        ], is_string($item) ? 500 : 201);
    }

    
    public function search(Request $request)
    {
        $this->validate($request, [
            'name' => 'sometimes',
            'item_type_id' => 'nullable|numeric',
            'category_id' => 'nullable|numeric',
        ]);
        
        $query = Item::orderBy('name', 'ASC');

        if (!is_null($request->input('name')) || $request->input('name') != ''){
            $query = $query->where('name', 'LIKE', "%{$request->input('name')}%");
        }
        if (!is_null($request->input('item_type_id')) || $request->input('item_type_id') != ''){
            $query = $query->where('item_type_id', '=', $request->input('item_type_id'));
        }
        if (!is_null($request->input('brand_id')) || $request->input('brand_id') != ''){
            $query = $query->where('brand_id', '=', $request->input('brand_id'));
        }
        if (!is_null($request->input('category_id')) || $request->input('category_id') != ''){
            $query = $query->where('category_id', '=', $request->input('category_id'));
        }
        if (!is_null($request->input('classification_id')) || $request->input('classification_id') != ''){
            $query = $query->where('classification_id', '=', $request->input('classification_id'));
        }
        
        $query = $query->get();

        return response()->json([
            'items' => $query,
        ]);
    }

    public function show($id)
    {
        $purchase_orders = PurchaseOrderItem::where('item_id', '=', $id)->with(['purchase_order'])->latest()->limit(10);
        $transfer_orders = TransferOrderItem::where('item_id', '=', $id)->with(['transfer_order'])->latest()->limit(10);
        $locations = StoreItemBatch::groupBy('store_id', 'item_id')
                    ->select(DB::raw("SUM(`balance`) as `balance`"), 'item_id', 'store_id')->with('store')
                    ->where('item_id', '=', $id)->get();

        return response()->json([
            'item' => $this->inventory_item_get_by('id', $id, true),
            'purchase_orders' => $purchase_orders,
            'transfer_orders' => $transfer_orders,
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'type_id' => 'required',
            'quantity' => 'sometimes|numeric',
            'minimum_level' => 'sometimes|numeric',
            'current_cost_price' => 'sometimes|numeric',
        ]);

        $item = $this->inventory_item_update($request, $id);
        return response()->json([
            'item' => $item,
        ], is_string($item) ? 500 : 200);
    }

    public function destroy($id)
    {
        $item = $this->inventory_item_delete($id);
        return response()->json([
            'item' => $item
        ], is_string($item) ? 500 : 200);
    }
}
