<?php

namespace App\Http\Controllers;
use App\Models\Finance\{
    Transaction
};

use App\Models\Inventory\{
    Item,
    Store,
    StoreItem,
    StoreItemBatch,
    TransferOrder,
    TransferOrderItem,    
};

use App\Models\Sales\{
    DeliveryNote,
    Invoice,
    Order as SalesOrder,
    OrderItem as SalesOrderItem,
};

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller handling CRUD for Items and CSV import
 */
class ItemController extends Controller
{
    /** List items with optional filters */
    public function index(Request $request)
    {
        $query = Item::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        return $query->paginate($request->get('per_page', 20));
    }

    /** Create a new item */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'type_id'           => 'required|integer',
            'classification_id' => 'nullable|integer',
            'category_id'       => 'nullable|integer',
            'unique_id'         => 'nullable|string|unique:items,unique_id',
            'specific_id'       => 'nullable|string',
            'image'             => 'nullable|image',
            'barcode'           => 'nullable|string|unique:items,barcode',
            'description'       => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }
        $validated['created_by'] = $request->user()->id;

        return Item::create($validated);
    }

    /** Show single item */
    public function show(Item $item)
    {
        return $item;
    }

    /** Update item */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'type_id'     => 'sometimes|required|integer',
            'image'       => 'nullable|image',
            'barcode'     => "nullable|string|unique:items,barcode,{$item->id}",
            'description' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            // remove old image if exists
            if ($item->image) Storage::disk('public')->delete($item->image);
            $validated['image'] = $request->file('image')->store('items', 'public');
        }
        $validated['updated_by'] = $request->user()->id;

        $item->update($validated);
        return $item->refresh();
    }

    /** Soft delete item */
    public function destroy(Item $item, Request $request)
    {
        $item->update(['deleted_by' => $request->user()->id]);
        $item->delete();
        return response()->noContent();
    }

    /**
     * Import items from a CSV file with headers matching column names.
     * Required headers: name,type_id,classification_id,category_id,barcode,description
     */
    public function importCsv(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt']);

        $path   = $request->file('file')->getRealPath();
        $rows   = array_map('str_getcsv', file($path));
        $header = array_map('trim', array_shift($rows));

        DB::transaction(function () use ($rows, $header, $request) {
            foreach ($rows as $row) {
                $data = array_combine($header, $row);
                $data['created_by'] = $request->user()->id;
                $data['unique_id']  = $data['unique_id'] ?? Str::uuid();
                Item::updateOrCreate([
                    'barcode' => $data['barcode'] ?? null
                ], $data);
            }
        });

        return response()->json(['message' => 'Import successful']);
    }
}

/**
 * CRUD for Stores, plus ability to update store‑item settings.
 */
class StoreController extends Controller
{
    public function index() { return Store::paginate(); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'branch_id'     => 'required|integer',
            'department_id' => 'nullable|integer'
        ]);
        $validated['created_by'] = $request->user()->id;
        return Store::create($validated);
    }

    public function show(Store $store) { return $store; }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string'
        ]);
        $validated['updated_by'] = $request->user()->id;
        $store->update($validated);
        return $store->refresh();
    }

    public function destroy(Store $store, Request $request)
    {
        $store->update(['deleted_by' => $request->user()->id]);
        $store->delete();
        return response()->noContent();
    }

    /** Update StoreItem settings in bulk */
    public function updateItemSettings(Request $request, Store $store)
    {
        $request->validate([
            'items'                           => 'required|array',
            'items.*.item_id'                 => 'required|integer',
            'items.*.reorder_level'           => 'required|numeric|min:0',
            'items.*.maximum_level'           => 'required|numeric|min:0',
            'items.*.expiry_notification'     => 'nullable|integer|min:0'
        ]);

        DB::transaction(function () use ($store, $request) {
            foreach ($request->items as $row) {
                StoreItem::updateOrCreate(
                    [ 'store_id' => $store->id, 'item_id' => $row['item_id'] ],
                    [
                        'reorder_level'       => $row['reorder_level'],
                        'maximum_level'       => $row['maximum_level'],
                        'expiry_notification' => $row['expiry_notification'] ?? 0,
                        'updated_by'          => $request->user()->id,
                    ]
                );
            }
        });

        return response()->json(['message' => 'Settings updated']);
    }
}

/**
 * CRUD for Transfer Orders with nested items.
 */
class TransferOrderController extends Controller
{
    public function index() { return TransferOrder::with('items')->paginate(); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'description'         => 'nullable|string',
            'requesting_store_id' => 'required|integer',
            'issuing_store_id'    => 'required|integer|different:requesting_store_id',
            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'required|integer',
            'items.*.requested_quantity' => 'required|numeric|min:1'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $order = TransferOrder::create([
                'name'                => $validated['name'],
                'description'         => $validated['description'] ?? null,
                'unique_id'           => Str::uuid(),
                'requesting_store_id' => $validated['requesting_store_id'],
                'issuing_store_id'    => $validated['issuing_store_id'],
                'status'              => 'pending',
                'created_by'          => $request->user()->id
            ]);

            foreach ($validated['items'] as $row) {
                $order->items()->create([
                    'item_id'            => $row['item_id'],
                    'requested_quantity' => $row['requested_quantity']
                ]);
            }
            return $order->load('items');
        });
    }

    public function show(TransferOrder $transferOrder) { return $transferOrder->load('items'); }

    public function update(Request $request, TransferOrder $transferOrder)
    {
        $this->authorize('update', $transferOrder); // Policy for status checks
        // Only allow modification while pending
        if ($transferOrder->status !== 'pending') {
            abort(422, 'Approved/processed orders cannot be updated');
        }
        // Re‑use store() validation but items optional
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'items'               => 'sometimes|array|min:1',
            'items.*.id'          => 'sometimes|integer|exists:transfer_order_items,id',
            'items.*.item_id'     => 'required_with:items|integer',
            'items.*.requested_quantity' => 'required_with:items|numeric|min:1'
        ]);

        return DB::transaction(function () use ($validated, $transferOrder, $request) {
            $transferOrder->update(array_intersect_key($validated, array_flip(['name','description'])) + ['updated_by' => $request->user()->id]);

            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $row) {
                    if (isset($row['id'])) {
                        $transferOrder->items()->where('id', $row['id'])->update($row);
                    } else {
                        $transferOrder->items()->create($row);
                    }
                }
            }
            return $transferOrder->load('items');
        });
    }

    public function destroy(TransferOrder $transferOrder, Request $request)
    {
        $this->authorize('delete', $transferOrder);
        $transferOrder->update(['deleted_by' => $request->user()->id]);
        $transferOrder->delete();
        return response()->noContent();
    }
}

/**
 * CRUD for Sales Orders; confirmation converts to invoice / delivery note / transaction.
 */
class SalesOrderController extends Controller
{
    public function index() { return SalesOrder::with('items')->paginate(); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id'         => 'required|integer',
            'customer_id'      => 'required|integer',
            'type_id'          => 'required|integer',
            'payment_term_id'  => 'required|integer',
            'delivery_date'    => 'required|date',
            'date'             => 'required|date',
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_cost'=> 'required|numeric|min:0'
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $order = SalesOrder::create([
                'unique_id'      => Str::uuid(),
                'store_id'       => $validated['store_id'],
                'customer_id'    => $validated['customer_id'],
                'type_id'        => $validated['type_id'],
                'payment_term_id'=> $validated['payment_term_id'],
                'delivery_date'  => $validated['delivery_date'],
                'date'           => $validated['date'],
                'status'         => 'draft',
                'created_by'     => $request->user()->id,
            ]);

            foreach ($validated['items'] as $row) {
                $order->items()->create([
                    'item_id'   => $row['item_id'],
                    'quantity'  => $row['quantity'],
                    'unit_cost' => $row['unit_cost'],
                    'total'     => $row['quantity'] * $row['unit_cost']
                ]);
            }
            return $order->load('items');
        });
    }

    public function show(SalesOrder $salesOrder) { return $salesOrder->load('items'); }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            abort(422, 'Only draft orders can be updated');
        }
        // Similar validation as store but items optional
        // ... (for brevity)
    }

    /** Confirm order and convert to invoice & delivery note */
    public function confirm(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status !== 'draft') {
            abort(422, 'Order already confirmed');
        }

        return DB::transaction(function () use ($salesOrder, $request) {
            // Reduce stock from StoreItemBatch (FIFO)
            foreach ($salesOrder->items as $item) {
                $remaining = $item->quantity;
                $batches = StoreItemBatch::where('store_id', $salesOrder->store_id)
                    ->where('item_id', $item->item_id)
                    ->where('balance', '>', 0)
                    ->orderBy('created_at')
                    ->lockForUpdate()
                    ->get();

                foreach ($batches as $batch) {
                    $take = min($remaining, $batch->balance);
                    $batch->decrement('balance', $take);
                    $batch->increment('sold', $take);
                    $remaining -= $take;
                    if ($remaining <= 0) break;
                }

                if ($remaining > 0) {
                    abort(422, "Insufficient stock for item {$item->item_id}");
                }
            }

            // Create invoice & delivery note
            $invoice = Invoice::create([
                'sales_order_id' => $salesOrder->id,
                'customer_id'    => $salesOrder->customer_id,
                'date'           => now(),
                'created_by'     => $request->user()->id
            ]);
            $deliveryNote = DeliveryNote::create([
                'sales_order_id' => $salesOrder->id,
                'store_id'       => $salesOrder->store_id,
                'date'           => now(),
                'created_by'     => $request->user()->id
            ]);

            // Create transactions per item
            foreach ($salesOrder->items as $item) {
                Transaction::create([
                    'date'             => now(),
                    'customer_id'      => $salesOrder->customer_id,
                    'item_id'          => $item->item_id,
                    'item_name'        => $item->item->name,
                    'item_qty'         => $item->quantity,
                    'item_unit_cost'   => $item->unit_cost,
                    'item_total'       => $item->total,
                    'status'           => 'completed',
                    'service_status'   => 'fulfilled',
                    'created_by'       => $request->user()->id
                ]);
            }

            $salesOrder->update(['status' => 'confirmed', 'updated_by' => $request->user()->id]);

            return $salesOrder->load('items');
        });
    }

    public function destroy(SalesOrder $salesOrder, Request $request)
    {
        if ($salesOrder->status !== 'draft') {
            abort(422, 'Only draft orders can be deleted');
        }
        $salesOrder->update(['deleted_by' => $request->user()->id]);
        $salesOrder->delete();
        return response()->noContent();
    }
}

/**
 * Reporting controller for stock levels.
 */
class StockReportController extends Controller
{
    public function currentLevels(Request $request)
    {
        $query = StoreItemBatch::select(
            'store_id',
            'item_id',
            DB::raw('SUM(balance) as stock_balance')
        )
        ->groupBy('store_id', 'item_id')
        ->with(['item:id,name', 'store:id,name']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->item_id);
        }
        return $query->get();
    }
}

/**
 * Detailed movement for an item or batch
 */
class ItemMovementController extends Controller
{
    public function movements(Request $request, $itemId)
    {
        // Collect movements from different sources
        $batches = StoreItemBatch::where('item_id', $itemId)
            ->with('store:id,name')
            ->get(['id','store_id','received','transferred','issued','sold','balance','created_at']);

        $transactions = Transaction::where('item_id', $itemId)
            ->get(['id','date','item_qty','item_unit_cost','item_total','description']);

        return [
            'batches'      => $batches,
            'transactions' => $transactions
        ];
    }
}
