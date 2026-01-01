<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Procurement\VendorTrait;
use App\Models\Finance\Bank;
use Illuminate\Http\Request;

class VendorAccountController extends Controller
{
    use VendorTrait;
    public function index()
    {
        return response()->json([
            'accounts' => $this->procurement_vendor_account_get_all($_GET['status'] ?? 'active', $_GET, true, true, $GET['page'] ?? 1),  
        ]);
    }

    public function initials()
    {
        return response()->json([
            'banks' => Bank::select('id', 'name')->get(),
            'vendors' => $this->procurement_vendor_get_all('active', null, false, false, null),
        ]);
    }

    public function show(string $id)
    {
        $account = $this->procurement_vendor_account_get_by(null, $id, true);  
        
        return response()->json([
            //'contact' => $this->procurement_vendor_contact_get_by($request),
        ], is_string($account) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $account = $this->procurement_vendor_account_create($request);
        return response()->json([
            'account' => $account,
        ]);
    }

    
    public function update(Request $request, string $id)
    {
        return response()->json([
            'account' => $this->procurement_vendor_account_update($request, $id),
        ]);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'account' => $this->procurement_vendor_account_delete($id),
        ]);
    }
}
