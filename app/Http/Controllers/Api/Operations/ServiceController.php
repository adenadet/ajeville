<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultantTrait;
use App\Http\Traits\EMR\DrugTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\Operations\ServiceTrait;
use App\Http\Traits\Inventory\ItemTrait;
use Illuminate\Http\Request;

use App\Models\Operations\Branch;
use App\Models\Finance\PriceList;
use App\Models\HRMS\Employee;
use App\Models\Operations\BranchModule;
use App\Models\Operations\Module;
use App\Models\User;

class ServiceController extends Controller
{
    use ItemTrait, ConsultantTrait, ServiceTrait;

    public function destroy($id)
    {
        $branch = $this->operation_service_delete($id);

        return response()->json([
            'admission_services'    => $this->operation_service_get_all('admission', true, true, $_GET['page'] ?? 1),
            'lab_services'          => $this->operation_service_get_all('admission', true, true, $_GET['page'] ?? 1),
            'rad_services'          => $this->operation_service_get_all('admission', true, true, $_GET['page'] ?? 1),
            'services'              => $this->operation_service_get_all('admission', true, true, $_GET['page'] ?? 1), 
        ]); 
    }
    public function index()
    {
        return response()->json([
            'items'    => $this->inventory_item_get_all('emr_services', null, true, true, null),
            'services'    => $this->operation_service_get_all('all', $_GET, true, true)
        ]);        
    }

    public function initials()
    {
        return response()->json([
            //'drugs'             => $this->emr_drugs_get_all('drug_types', false, false, $_GET['page'] ?? 1),
            //'drug_forms'        => $this->emr_drugs_get_all('drug_forms', false, false, $_GET['page'] ?? 1),
            //'lab_bottle_types'  => $this->laboratory_bottles_get_all('all_active', false, false, $_GET['page'] ?? 1),
            'service_types'     => $this->operation_service_type_get_all('active', null,  false, true),           
            'specialties'       => $this->emr_specialty_get_all('active', null, true, false),
        ]);        
    }

    public function lists($id)
    {
        return response()->json([
            'services'  => $this->inventory_item_get_all_items_by_service($id, null, true, true,  $_GET['page'] ?? 1, 50),  
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'service'  => $this->inventory_item_get_item_by_id($id),  
        ]);
    }

    public function store(Request $request)
    {
        $service = $this->operation_service_create($request);

        return response()->json([
            'service'    => $service
        ], is_string($service) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $branch = $this->operation_branch_update_branch($request, $id);
        return response()->json([
            'branches'    => Branch::with(['chief_consultant.user', 'head_nurse.user', 'practice_manager.user', 'modules', 'price_list'])->orderBy('name', 'ASC')->paginate(10),       
        ]);
    }

}