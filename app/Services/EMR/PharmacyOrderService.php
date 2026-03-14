<?php 

namespace App\Services\Pharmacy;

use App\Models\EMR\Pharmacy\Prescription;
use App\Services\Sales\OrderService;
use Exception;
use Illuminate\Support\Facades\DB;

class PharmacyOrderService
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createSalesOrderFromPrescription(Prescription $prescription)
    {
        return DB::transaction(function () use ($prescription) {

            
            if ($prescription->status !== Prescription::StatusConfirmed) {
                throw new Exception("Prescription must be confirmed before generating order.");
            }

            // Prevent duplicate order creation
            if ($prescription->order_id) {
                throw new Exception("Sales order already generated for this prescription.");
            }

            $itemsPayload = [];

            foreach ($prescription->items as $item) {

                // Skip external prescriptions
                if ($item->is_external) {
                    continue;
                }

                if (!$item->specific_drug_id) {
                    throw new Exception("Specific drug must be selected before generating order.");
                }

                if ($item->quantity_prescribed <= 0) {
                    continue;
                }

                $itemsPayload[] = [
                    'item_id'  => $item->specific_drug_id,
                    'quantity' => $item->quantity_prescribed,
                    'price'    => $item->unit_price ?? 0,
                ];
            }

            if (empty($itemsPayload)) {throw new Exception("No valid prescription items available for order generation.");}
            $storeId = $this->resolveStoreForPrescription($prescription);
            $order = $this->orderService->create([
                'customer_id' => $prescription->patient_id,
                'store_id' => $storeId,
                'type' => 'sales',
                'referenceable_type' => Prescription::class,
                'referenceable_id' => $prescription->id,
                'items' => $itemsPayload,
            ]);

            $prescription->update(['order_id' => $order->id, 'status'   => 'ordered',]);
            return $order->load('items');
        });
    }

    protected function resolveStoreForPrescription(Prescription $prescription)
    {
        if ($prescription->visit && $prescription->visit->is_admitted) {return config('inventory.main_pharmacy_store_id');}

        return config('inventory.main_pharmacy_store_id');
    }
}
