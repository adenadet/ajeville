<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use AdmissionTrait;
    public function destroy(string $id)
    {
        $category = $this->admission_category_deactivate($id);

        return response()->json([
            'category' => $category
        ], is_string($category) ? 500 : 200);
    }

    public function index()
    {
        $categories = $this->admission_category_get_all($_GET['type'], $_GET,  true, true);

        return response()->json([
            'categories' => $categories
        ]);
    }

    public function show(string $id)
    {
        $category = $this->admission_category_get_by(null, $id, true);

        return response()->json([
            'category' => $category
        ], is_string($category) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $category = $this->admission_category_create($request);

        return response()->json([
            'category' => $category
        ], is_string($category) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $category = $this->admission_category_update($request, $id);

        return response()->json([
            'category' => $category
        ], is_string($category) ? 500 : 200);
    }
}