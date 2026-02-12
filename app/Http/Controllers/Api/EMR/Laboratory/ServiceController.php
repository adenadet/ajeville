<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\SettingsTrait;
use App\Models\EMR\Settings\LaboratoryBottle;
use Illuminate\Http\Request;

use App\Models\EMR\Settings\LaboratoryService;
use App\Models\Inventory\Item;

class ServiceController extends Controller
{
    use LaboratoryTrait, SettingsTrait;
    public function index()
    {
        return response()->json([
            'services' => $this->emr_laboratory_service_get_all($_GET['status'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'bottle_types' => $this->emr_laboratory_bottles_get_all('active', null, false, false),
            'categories' => $this->emr_laboratory_category_get_all($_GET['status'] ?? 'active', $_GET, false, false),
            'result_templates' => $this->emr_laboratory_result_template_get_all('active', null, false, false),
            'specimen_types' => $this->emr_laboratory_specimen_type_get_all('active', null, false, false),

        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'bottle_type_id'=> 'required|numeric',
            'category_id'=> 'required|numeric',
            'description' => 'sometimes',
            'name'=> 'required',
            'result_template_id'=> 'required|numeric',
            'specimen_type_id'=> 'required|numeric',
        ]);

        $service = $this->emr_laboratory_service_create($request);

        return response()->json([
            'service' => $service,
        ], is_string($service) ? 500 : 201);
    }

    public function show($id)
    {
        $service = $this->emr_laboratory_service_get_by(null, $id, true);

        return response()->json([
            'service' => $service,
        ], is_string($service) ? 404 : 200);
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
        $this->validate($request, [
            'bottle_type_id'=> 'required|numeric',
            'category_id'=> 'required|numeric',
            'description' => 'sometimes',
            'name'=> 'required',
            'result_template_id'=> 'required|numeric',
            'specimen_type_id'=> 'required|numeric',
        ]);

        $service = $this->emr_laboratory_service_update($request, $id);

        return response()->json([
            'service' => $service,
        ], is_string($service) ? 500 : 201);


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
