<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Models\EMR\Service;
use App\Models\EMR\ServiceType;
use App\Models\Inventory\Item;
use App\Models\Inventory\Category;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function initials()
    {
        return response()->json([
            'categories' => Category::orderBy('name', 'ASC')->get(),
            'service_types' => ServiceType::select('id', 'name', 'status')->orderBy('name', 'ASC')->with(['creator', 'items', 'categories.items'])->get(),
            'items' => Item::orderBy('name', 'ASC')->with(['service', 'category', 'sub_category'])->get()
        ]);
    }

    public function index()
    {
        return response()->json([
            'services' => Service::select('id', 'name', 'status', 'created_by', 'updated_at')->orderBy('name', 'ASC')->with(['creator'])->paginate(10),
            'categories' => [],
            'items' => Item::orderBy('name', 'ASC')->with(['service', 'category', 'sub_category'])->get()
        ]);
    }

    public function store(Request $request)
    {
        $service = Service::create([
            'name' => $request->input('name'),
            'status' => $request->input('status') ?? 1,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'services' => Service::select('id', 'name', 'status', 'created_by', 'updated_at')->orderBy('name', 'ASC')->with(['creator'])->paginate(10),
            'categories' => [],
            'items' => Item::orderBy('name', 'ASC')->with(['service', 'category', 'sub_category'])->get()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'service' => Service::select('id', 'name', 'status', 'created_by', 'updated_at')->with(['creator'])->where('id', '=', $id)->first(),
            'items' => Item::where('service_id', '=', $id)->orderBy('name', 'ASC')->with(['service', 'category', 'sub_category'])->paginate(30),
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::where('id', '=', $id)->first();

        $service->name = $request->input('name');
        $service->status = $request->input('status') ?? 1;
        $service->updated_by = auth('api')->id();
        
        $service->save();

        return response()->json([
            'services' => Service::select('id', 'name', 'status', 'created_by', 'updated_at')->orderBy('name', 'ASC')->with(['creator'])->paginate(10),
            'categories' => [],
            'items' => Item::orderBy('name', 'ASC')->with(['service', 'category', 'sub_category'])->get()
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
