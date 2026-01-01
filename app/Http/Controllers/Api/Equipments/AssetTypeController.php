<?php

namespace App\Http\Controllers\Api\Equipments;

use App\Http\Controllers\Controller;
use App\Http\Traits\Equipments\AssetTrait;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    use AssetTrait;
    public function index()
    {
        
        $asset_types = $this->equipment_asset_type_get_all('all', null, true, true, $_GET['page'] ?? 1);
        return response()->json([
            'asset_types' => $asset_types
        ], is_string($asset_types) ? 500 : 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|numeric',
        ]);

        $asset_type = $this->equipment_asset_type_create($validated);
        
        return response()->json([
            'asset_type' => $asset_type
        ], is_string($asset_type) ? 500 : 201);
    }

    public function show(string $id)
    {
        $asset_type = $this->equipment_asset_type_get_by('uuid', $id, true);
        return response()->json([
            'asset_type' => $asset_type
        ], is_string($asset_type) ? 500 : 200);

    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|numeric',
        ]);

        $asset_type = $this->equipment_asset_type_update($validated, $id);
        return response()->json([
            'asset_type' => $asset_type
        ], is_string($asset_type) ? 500 : 200);

    }

    public function destroy(string $id)
    {
        $asset_type = $this->equipment_asset_type_get_deactivate($id);
        return response()->json([
            'asset_type' => $asset_type
        ], is_string($asset_type) ? 500 : 200);

    }
}
