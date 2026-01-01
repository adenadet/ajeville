<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Procurement\VendorTrait;
use Illuminate\Http\Request;

class VendorContactController extends Controller
{
    use VendorTrait;
    public function index()
    {
        return response()->json([
            'vendors' => (isset($_GET['vendor_id']) && $_GET['vendor_id'] != '') ? $this->procurement_vendor_get_all('search', $_GET['search'], true, true, $GET['page'] ?? 1) : $this->procurement_vendor_get_all($_GET['type'] ?? 'active', null, true, true, $GET['page'] ?? 1),       
        ]);
    }

    public function initials()
    {
        return response()->json([
            'vendors' => $this->procurement_vendor_get_all('all', null, false, false, null),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            //'contact' => $this->procurement_vendor_contact_get_by($request),
        ]);
    }

    public function vendor(string $id)
    {
        return response()->json([
            'contacts' => $this->procurement_vendor_contact_get_by('vendor',$id, true),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'contact' => $this->procurement_vendor_contact_create($request),
        ]);
    }

    
    public function update(Request $request, string $id)
    {
        return response()->json([
            'contact' => $this->procurement_vendor_contact_update($request, $id),
        ]);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'contact' => $this->procurement_vendor_contact_delete($id),
        ]);
    }
}
