<?php 
namespace App\Services\Sales;

use App\Models\Sales\Order;
use App\Models\Sales\OrderItem;
use App\Services\Sales\OrderValidationService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected OrderValidationService $validator;


    public function __construct(OrderValidationService $validator)
    {
        $this->validator = $validator;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data['customer_id'] ?? null,
                'store_id'    => $data['store_id'],
                'type'        => $data['type'], // sales | procurement | transfer
                'status'      => 'draft',
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id'   => $data['reference_id'] ?? null,
                'created_by'  => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'] ?? 0,
                    'quantity_fulfilled' => 0,
                    'status' => 'pending',
                ]);
            }

            return $order->load('items');
        });
    }

    public function update(array $data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $order = Order::lockForUpdate()->findOrFail($id);
            $this->validator->ensureOrderIsEditable($order);
            $order->update($data);

            return $order;
        });
    }

    public function approve($id)
    {
        return DB::transaction(function () use ($id) {
            $order = Order::lockForUpdate()->findOrFail($id);
            $this->validator->ensureOrderIsEditable($order);
            $order->update(['status' => 'approved']);
            return $order;
        });
    }

    public function cancel($id)
    {
        return DB::transaction(function () use ($id) {
            $order = Order::lockForUpdate()->findOrFail($id);
            $this->validator->ensureOrderCanBeCancelled($order);
            $order->update(['status' => 'cancelled']);
            return $order;
        });
    }
}
