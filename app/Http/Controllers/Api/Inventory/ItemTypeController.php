<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\SettingsTrait;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    use SettingsTrait;

    public function destroy($id)
    {
        return response()->json([
            'item_type' => $this->inventory_settings_item_type_deactivate($id),
        ]);
    }    
    
    public function index()
    {
        return response()->json([
            'item_types' => $this->inventory_settings_item_type_get_all('all', null, true, true, $GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'item_type' => $this->inventory_settings_item_type_create($request),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'item_type' => $this->inventory_settings_item_type_get_by('id', $id, true),
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'item_type' => $this->inventory_settings_item_type_update($request, $id),
        ]);
    }
}
