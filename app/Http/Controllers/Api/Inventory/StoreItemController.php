<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class StoreItemController extends Controller
{
    use ItemTrait, StoreTrait, LogTrait;

    public function batches($store_id, $item_id)
    {
        $batches = $this->inventory_store_items_batches($store_id, $item_id);

        return response()->json(['batches' => $batches,], is_string($batches) ? 500 : 200);
    }

    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        $items = $this->inventory_store_items_get_all('items', $_GET['store_id'],  null, false, false, null);

        return response()->json([
            'store_items' => $items,
        ], (is_string($items) ? 500 : 200));
    }

    public function initials()
    {
        $items = $this->inventory_item_get_all('active', null, false, false, null);
        $stores= $this->inventory_store_get_all('active', null, false, false, null);

        return response()->json([
            'items'     => $items,
            'stores'    => $stores,
        ]);
    }

    public function report(Request $request, $id, $type)
    {
        $store_items = $this->inventory_store_items_get_all('search', $id,  $request, true, false, null);

        //return($store_items);
        switch ($type){
            case 'csv':
                $filename = 'store_item_report_' . now()->format('Ymd_His') . '.csv';
                // Define CSV headers
                $headers = [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => "attachment; filename=\"$filename\"",
                ];

                // Open output stream
                $callback = function () use ($store_items) {
                    $file = fopen('php://output', 'w');

                    // Column headings
                    fputcsv($file, [
                        'Item Name',
                        'Reorder Level',
                        'Maximum Level',
                        'Expiry Notification (in days)',
                        'Balance',
                        'Received',
                        'Sold',
                        'Transferred',
                        'Issued',
                    ]);

                    foreach ($store_items as $store_item) {
                        $item = $store_item->item;

                        fputcsv($file, [
                            $item->name ?? 'Deleted Item',
                            $store_item->reorder_level ?? 'N/A',
                            $store_item->maximum_level ?? 'N/A',
                            $store_item->expiry_notification ?? 'N/A',
                            $store_item->total_balance ?? 0,
                            $store_item->total_received ?? 0,
                            $store_item->total_sold ?? 0,
                            $store_item->total_transferred ?? 0,
                            $store_item->total_issued ?? 0,
                        ]);
                    }

                    fclose($file);
                };
                
                return Response::stream($callback, 200, $headers);
            //break;
            case 'pdf':
                // Generate PDF
                $pdf = Pdf::loadView('reports.inventory.store_items', ['store_items' => $store_items])->setPaper('a4', 'landscape');

                $filename = 'store_item_report_' . now()->format('Ymd_His') . '.pdf';
                return $pdf->download($filename);
        }
    }

    public function reset(string $id)
    {
        $store_items = $this->inventory_store_items_reset($id);

        return response()->json(['store_items' => $store_items,]);
    }

    public function search(Request $request, $id)
    {
        $store_items = $this->inventory_store_items_get_all('search', $id,  $request, true, true, null);

        return response()->json(['store_items' => $store_items,]);
    }

    public function show(string $id)
    {
        $store_items = $this->inventory_store_items_get_all('all', $id,  null, true, true, null);

        return response()->json(['store_items' => $store_items,]);
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $store_items = $this->inventory_store_items_update($request, $id,);

        return response()->json(['store_items' => $store_items,]);
    }    
}
