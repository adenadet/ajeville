<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\StoreTrait;
use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;

use App\Models\Inventory\Store;
use App\Models\Inventory\StoreItemBatch;

class StoreController extends Controller
{
    use StoreTrait;
    public function index()
    {
        return response()->json([
            'stores' => $this->inventory_store_get_all($_GET['type'] ?? 'all', null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => Branch::where('status', '=', 1)->orderBy('name', 'ASC')->get(),
            'departments' => Department::where('status', '=', 1)->orderBy('name', 'ASC')->get(),
            'stores' => Store::select('id', 'name', 'branch_id')->where('status', '=', 1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'branch_id' => 'nullable|numeric',
            'department_id' => 'nullable|numeric',
            'status' => 'required|numeric',
            'store_items' => 'sometimes|array',
        ]);

        return response()->json([
            'store' => $this->inventory_store_create_new($request),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'store' => Store::where('id', '=', $id)->with(['department', 'branch'])->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'branch_id' => 'nullable|numeric',
            'department_id' => 'nullable|numeric',
            'status' => 'required|numeric',
        ]);

        return response()->json([
            'store' => $this->inventory_store_update($request, $id),
            'stores' => $this->inventory_store_get_all($_GET['type'] ?? 'all', null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'store' => $this->inventory_store_delete($id),
            'stores' => $this->inventory_store_get_all($_GET['type'] ?? 'all', null, true, true, $_GET['page'] ?? 1),
        ]);
    }
}