<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use CustomerTrait;
    public function index()
    {
        return response()->json([
            'customers' => (isset($_GET['search']) && $_GET['search'] != '') ? $this->crm_customer_get_all('search', $_GET['search'], true, true, $GET['page'] ?? 1) : $this->crm_customer_get_all($_GET['type'] ?? 'active', null, true, true, $GET['page'] ?? 1),       
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->crm_customer_category_get_all('active', null, false, false, null),  
            'customers' =>  $this->crm_customer_get_all('active', null, false, false, null),   
        ]);
    }

    public function store(Request $request)
    {
        $customer = $this->crm_customer_create($request);
        return response()->json([
            'customer' => $customer,       
        ], is_string($customer) ? 500 : 201);
    }

    public function show(string $id)
    {
        $customer = $this->crm_customer_get_single('uuid', $id, true);
        
        return response()->json([
            'customer' => $customer,
        ], is_string($customer) ? 404 : 200);
    }

    public function update(Request $request, string $id)
    {
        $customer = $this->crm_customer_update($request, $id);
        return response()->json([
            'customer' => $customer,       
        ], is_string($customer) ? 501 : 200);
    }

    public function destroy(string $id)
    {
        $customer = $this->crm_customer_delete($id);
        return response()->json([
            'customer' => $customer,       
        ], is_string($customer) ? 500 : 200);
    }
}
