<?php

namespace App\Services\Finance;

use App\Models\CRM\Customer;
use App\Models\Sales\Order;
use App\Models\Finance\Income;
use App\Models\Inventory\StoreItem;
use App\Models\Procurement\PaymentTerm;
use App\Models\Procurement\Vendor;
use App\Models\Sales\OrderApproval;
use App\Models\Sales\OrderItem;
use App\Services\Inventory\IssuanceService;
use App\Services\Finance\MainService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderIncomeService
{
    public function approveOrder($order, $data){
        DB::beginTransaction();
        try{   
            
            $order_items = $data['order']['order_items'] ?? $order->order_items;
            
            //This function simply confirms the order it does not create an Income or Transaction record    
            if ($data['decision'] == 'confirm'){
                foreach($order_items as $order_item){
                    $item = OrderItem::find($order_item['id']);

                    $item->approved_quantity = $order_item['approved_quantity'] ?? $order_item->quantity;
                    $item->total_quantity = ($item->package_quantity ?? 1) * ($order_item['approved_quantity'] ?? $item->quantity);
                    $item->total_price = $item->unit_price * ($order_item['approved_quantity'] ?? $item->quantity) * ($item->package_quantity ?? 1);
                    $item->status =  2;
                    $item->quantity = $order_item['approved_quantity'] ?? $item->quantity;
                    $item->updated_by = auth('api')->id() ?? Auth::id();

                    $item->save();
                }

                $income = $this->generateIncomeFromOrderId($order->id);
                if (is_string($income)){
                    return $income;
                }

                $inventory_service = new IssuanceService();
                foreach ($order->order_items as $order_item){
                    $store_item = StoreItem::where('item_id', '=', $order_item->item_id)->where('store_id', '=', $order->store_id)->first();
                    $fulfilled_orders = $inventory_service->fulfillOrderItem($order_item, $store_item, 'sold');
                    //echo "Fulfilled Orders: ".count($fulfilled_orders)."\n";

                    if (is_string($fulfilled_orders)){
                        return $fulfilled_orders;
                    }
                }
            }

            OrderApproval::create([
                'so_id' => $order->id,
                'decision' => $data['decision'],
                'remark' => $data['remarks'] ?? null,
                'approved_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $order->status = ($data['decision'] == 'confirm') ? Order::StatusApproved : Order::StatusCancelled;
            $order->updated_by = auth('api')->id() ?? Auth::id();
            $order->save();

            DB::commit();
            return $order;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function attachIncomeToOrder(Income $income, Order $order)
    {
        // Example logic
        $order->total_income += $income->amount;
        $order->save();
    }

    public function createIncome($data){
        /*
        -------------------------------------------------------------------------------------------
        Every Income must have two legs
        1. Create an Income Record 
        2. Debit the customer/vendor balance of this amount
        3. Create a Main Transaction Record to keep it balanced (this is a debit transaction)
        
        If any of them fails the whole process should be reversed
        -------------------------------------------------------------------------------------------
        */
 
        DB::beginTransaction();
        try{
            $main_service = new MainService();
            $income = Income::create([
                'unique_id' => $main_service->finance_setting_generate_unique_id('income'),
                'amount' => $data['amount'] ?? 1.00,
                'date' => $data['date'] ?? date('Y-m-d'),
                'due_date' => $data['due_date'] ?? date('Y-m-d'),
                'incomeable_type' => $data['incomeable_type'] ?? null,
                'incomeable_id' => $data['incomeable_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                'classification_id' => $data['classification_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'staff_id' => $data['staff_id'] ?? null,
                'status' => $data['status'] ?? Income::StatusConfirmed,
                'vendor_id' => $data['vendor_id'] ?? null,
                'description' => $data['description'] ?? null,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            if (is_string($income) ){
                DB::rollback();
                return $income;
            }

            $main_transaction = new MainService();
            $transaction = $main_transaction->createTransaction([
                'date' => $income->date,
                'payment_due_date' => $income->due_date,
                'customer_id' => $income->customer_id,
                'vendor_id' => $income->vendor_id,
                'staff_id' => $income->staff_id,
                'trans_type' => 'debit',
                'transactionable_type' => 'App\Models\Finance\Income',
                'transactionable_id' => $income->id,
                'amount' => $income->amount,
            ]);

            if (is_string($transaction) ){
                DB::rollback();
                return $transaction;
            }

            if (!empty($data['customer_id'])){
                $customer = Customer::findOrFail($data['customer_id']);
                $customer->balance += $data['amount'];
                $customer->save();
            }

            if (!empty($data['vendor_id'])){
                $vendor = Vendor::findOrFail($data['vendor_id']);
                $vendor->balance -= $data['amount'];
                $vendor->save();
            }

            DB::commit();
            return $income;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }


    public function generateIncomeFromOrderId(String $id)
    {
        try{
            $order = Order::where('unique_id', '=', $id)->orWhere('id', '=', $id)->with(['customer', 'order_items.item', 'order_items.package', 'creator', 'deleter', 'payment_term', 'store.branch'])->firstOrFail();

            $payment_term = PaymentTerm::findOrFail($order->payment_term_id);
            $payment_due_date = new DateTime($order->delivery_date);
            $payment_due_date->modify('+'.$payment_term->days.' days');
        
            $income = $this->createIncome([
                'amount' => $order->grandTotal(),
                'branch_id' => $order->branch_id ?? $order->store->branch_id,
                'classification_id' => null,
                'customer_id' => $order->customer_id,
                'date' => $order->date,
                'description' => 'Income from Sales Order '.$order->unique_id,
                'due_date' => $payment_due_date->format('Y-m-d'),
                'incomeable_type' => 'App\Models\Sales\Order',
                'incomeable_id' => $order->id,
            ]);
            
            return $income;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}
