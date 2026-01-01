<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\AccountTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Procurement\SettingsTrait;
use App\Http\Traits\Sales\OrderTrait;
use App\Mail\Sales\OrderMail;
use App\Models\CRM\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    use AccountTrait, BranchTrait, CustomerTrait, OrderTrait,  SettingsTrait, StoreTrait;
    
    public function complete(string $id)
    {
        $order = $this->sales_order_complete($id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 200);
    }

    public function destroy(string $id)
    {
        $order = $this->sales_order_delete($id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 200);
    }

    public function display()
    {
        return response()->json([
            'accounts' => $this-> finance_account_get_all('primary', null, true, false, null),
        ]);
    }

    public function index()
    {
        //echo $_GET['status'] ?? '';
        //echo $_GET['query'] != 0  ? '' : 'Failed';
        $orders = (isset($_GET['status']) && $_GET['status'] != '')
            ? $this->sales_order_get_all('search', [
                'status' => $_GET['status'], 
                'question' => isset($_GET['query']) ? $_GET['query'] : null 
            ], true, true, $_GET['page'] ?? 1)
            : $this->sales_order_get_all($_GET['type'] ?? 'active', null, true, true, $_GET['page'] ?? 1);

        return response()->json(['orders' => $orders,], is_string($orders) ? 500 : 200);
    }

    public function initials()
    {
        $branch_id = request()->cookie('current_branch') ?? auth('api')->user()->branch_id;
        return response()->json([
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'package_types' => $this->procurement_settings_package_type_get_all('active', null, false, false, null),
            'payment_terms' => $this->procurement_settings_payment_term_get_all('active', null, false, false, null),
            'price_lists' => $this->operation_branch_price_list_get_all('branch', $_GET['branch_id'] ?? $branch_id, false, false, null),
            'stores' => $this->inventory_store_user_get('my_stores', null, false, false, null),
        ]);
    }

    public function mail(string $id)
    {
        try{
            // 1) Fetch invoice & customer
            $order = $this->sales_order_get_by('unique_id', $id, true);
            if (is_string($order)) {
                return response()->json(['error' => $order], 404);
            }

            if ($order->customer_id == 0){
                //return response()->json(['error' => 'Quotation has no associated customer.'], 400);
            }
            else{
                $customer = Customer::findOrFail($order->customer_id);
                if (!$customer) {
                    //return response()->json(['error' => 'Customer not found.'], 404);
                }
                else if(!$customer->email) {
                    //return response()->json(['error' => 'Customer email not found.'], 400);
                }
                
                Mail::to($customer->email)->send(new OrderMail($customer, $order));
            }
            
            return response()->json(['message' => 'Quotation emailed successfully.']);
        } 
        catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email: ' . $e->getMessage()], 500);
        }
    }
    
    public function show(string $id)
    {
        $order = $this->sales_order_get_by(null, $id, true);

        return response()->json(['order' => $order,], is_string($order) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|integer',
            'customer_id' => 'nullable|integer',
            'payment_term_id' => 'required|integer',
            'delivery_date' => 'required|date',
        ]);

        $order = $this->sales_order_create($request);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'store_id' => 'required|integer',
            'customer_id' => 'nullable|integer',
            'payment_term_id' => 'required|integer',
            'delivery_date' => 'required|date',
        ]);

        $order = $this->sales_order_update($request, $id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 200);
    }
}