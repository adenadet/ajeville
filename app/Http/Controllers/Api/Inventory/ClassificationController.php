<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\SettingsTrait;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    use SettingsTrait;

    public function destroy($id)
    {
        return response()->json([
            'classification' => $this->inventory_settings_classification_deactivate($id),
        ]);
    }    
    
    public function index()
    {
        return response()->json([
            'classifications' => $this->inventory_settings_classification_get_all('all', null, true, true, $GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'classification' => $this->inventory_settings_classification_create($request),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'classification' => $this->inventory_settings_classification_get_by('id', $id, true),
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'classification' => $this->inventory_settings_classification_update($request, $id),
        ]);
    }
}
