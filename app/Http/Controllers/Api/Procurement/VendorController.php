<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Procurement\VendorTrait;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    use FileManagerTrait, LogTrait, VendorTrait;

    public function index()
    {
        return response()->json([
            'vendors' => (isset($_GET['search']) && $_GET['search'] != '') ? $this->procurement_vendor_get_all('search', $_GET['search'], true, true, $GET['page'] ?? 1) : $this->procurement_vendor_get_all($_GET['type'] ?? 'active', null, true, true, $GET['page'] ?? 1),       
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->procurement_vendor_category_get_all('active', null, false, false, null),  
            'vendors' =>  $this->procurement_vendor_get_all('active', null, false, false, null),   
        ]);
    }

    public function store(Request $request)
    {
        $vendor = $this->procurement_vendor_create($request);
        return response()->json([
            'vendor' => $vendor,       
        ], is_string($vendor) ? 500 : 201);
    }

    public function show(string $id)
    {
        return response()->json([
            'vendor' => $this->procurement_vendor_get_single('id', $id, true),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $vendor = $this->procurement_vendor_update($request, $id);
        return response()->json([
            'vendor' => $vendor,       
        ],is_string($vendor) ? 500 : 200);
    }

    public function destroy(string $id)
    {
        $vendor = $this->procurement_vendor_delete($id);
        return response()->json([
            'vendor' => $vendor,       
        ], is_string($vendor) ? 500 : 200);
    }
}
