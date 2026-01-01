<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Procurement\VendorTrait;
use Illuminate\Http\Request;

class VendorCategoryController extends Controller
{
    use VendorTrait;
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'categories' => $this->procurement_vendor_category_get_all('all', null, true, true, $GET['page'] ?? 1),       
        ]);
    }

    
    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        $category = $this->procurement_vendor_category_create($request);
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }
    public function update(Request $request, string $id)
    {
        $category = $this->procurement_vendor_category_update($request, $id);
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }
}
