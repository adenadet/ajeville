<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\EMR\Settings\LaboratoryBottle;
use Illuminate\Http\Request;

use App\Models\EMR\Settings\LaboratoryService;
use App\Models\Inventory\Item;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json([
            'services' => Item::where('service_id', '=', 7)->orderBy('name', 'ASC')->with(['bottle', 'creator', 'deleter', 'template', 'updater'])->latest()->paginate(30),
        ]);
    }

    public function store(Request $request)
    {
        Item::create([
            'name' => $request->input('name'),
            'service_id' => 7,
            'category_id' => $request->input('category_id'),
            'status' => 1,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'services' => Item::where('service_id', '=', 7)->orderBy('name', 'ASC')->with(['bottle', 'creator', 'deleter', 'template', 'updater'])->latest()->paginate(30),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function reactivate(Request $request, $id)
    {
        $item = Item::where('id', '=', $id)->first();

        $item->status = $item->status == 0 ? 1 : 0;
        $item->updated_by = auth('api')->id();

        $item->save();

        return response()->json([
            'services' => Item::where('service_id', '=', 7)->orderBy('name', 'ASC')->with(['bottle', 'creator', 'deleter', 'template', 'updater'])->latest()->paginate(30),
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Item::where('id', '=', $id)->first();

        $service->name = $request->input('name');
        $service->category_id = $request->input('category_id');
        $service->bottle_type_id = $request->input('bottle_type_id');
        $service->result_template_id = $request->input('result_template_id');
        $service->updated_by = auth('api')->id();

        $service->save();


        return response()->json([
            'services' => Item::where('service_id', '=', 7)->orderBy('name', 'ASC')->with(['bottle', 'creator', 'deleter', 'template', 'updater'])->latest()->paginate(30),
        ]);
    }

    public function destroy($id)
    {
        $service = Item::where('id', '=', $id)->first();

        $service->deleted_by = auth('api')->id();
        $service->deleted_at = date('Y-m-d H:i:s');

        $service->save();


        return response()->json([
            'services' => Item::where('service_id', '=', 7)->orderBy('name', 'ASC')->with(['bottle', 'creator', 'deleter', 'template', 'updater'])->latest()->paginate(30),
        ]);
    }
}
