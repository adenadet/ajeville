<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\ServiceTrait;
use App\Http\Traits\Inventory\SettingsTrait;
use App\Models\Inventory\Item;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    use ItemTrait, SettingsTrait, ServiceTrait;
    public function index()
    {
        return response()->json([
            'categories' => $this->inventory_settings_category_get_all('all', null, true, true, $GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->inventory_settings_category_get_all('all', null, false, false, $GET['page'] ?? 1),
            'item_types' => $this->inventory_settings_item_type_get_all('active', null, false, false, $GET['page'] ?? 1),
            'classifications' => $this->inventory_settings_classification_get_all('all', null, false, false, null),
        ]);
    }

    public function store(Request $request)
    {
        $category = $this->inventory_settings_category_create($request);
        return response()->json(['category' => $category,], is_string($category) ? 500 : 201);
    }

    public function show($id)
    {
        return response()->json([
            'category' => $this->inventory_settings_category_get_by('id', $id, true),
            'items' => $this->inventory_item_get_all('category', $id, true, true, $_GET['page'] ?? 1)
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'category' => $this->inventory_settings_category_update($request, $id),
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'category' => $this->inventory_settings_category_delete($id),
        ]);
    }
}
