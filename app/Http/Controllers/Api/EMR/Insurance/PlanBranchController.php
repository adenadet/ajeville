<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Operations\PriceListTrait;

use App\Models\Finance\PriceList;
use App\Models\Insurance\Plan;
use App\Models\Insurance\PlanBranch;
use App\Models\Operations\Branch;
use Illuminate\Http\Request;

class PlanBranchController extends Controller
{
    use BranchTrait, InsuranceTrait, PriceListTrait;
    
    public function destroy($id)
    {
        //
    }

    public function index()
    {
        //
    }

    public function initials(){
        return response()->json([
            'branches' => $this->operation_branch_get_all(true, false, null),
            'price_lists' => $this->operation_price_list_get_all('active', null, false, false, null),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {
        
        $plan_branch = $this->insurance_provider_plan_branch_create($request);
        
        return response()->json([
            'plan' => $this->insurance_provider_plan_get_by_id($plan_branch->plan_id), 
            'plan_branches' => $this->insurance_provider_plan_branch_get_all('plan', $plan_branch->plan_id, true, false, null), 
        ]);
    }

    public function update(Request $request, $id)
    {
        $plan_branch = $this->insurance_provider_plan_branch_update($request, $id);
        
        return response()->json([
            'plan' => $this->insurance_provider_plan_get_by_id($plan_branch->plan_id),
            'plan_branches' => $this->insurance_provider_plan_branch_get_all('plan', $plan_branch->plan_id, true, false, null), 
        ]);
    }
}
