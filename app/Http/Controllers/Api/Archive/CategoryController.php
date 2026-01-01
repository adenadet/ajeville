<?php

namespace App\Http\Controllers\Api\Archive;

use App\Http\Controllers\Controller;
use App\Http\Traits\Archive\CategoryTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use CategoryTrait;
    public function destroy(string $id)
    {
        $category = $this->archive_category_delete($id);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 200);
    }
    
    public function index()
    {
        return response()->json([
            'categories' => $this->archive_category_get_all('active', null, true, true, null),
        ]);
    }

    public function show(string $id)
    {
        $category = $this->archive_category_get_by('unique_id', $id, true);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }

    public function store(Request $request)
    {
        $category = $this->archive_category_create($request);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $category = $this->archive_category_update( $request, $id);
        
        return response()->json([
            'category' => $category,
        ], is_string($category) ? 500 : 201);
    }
}
