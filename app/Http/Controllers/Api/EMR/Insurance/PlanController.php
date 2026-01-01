<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use Illuminate\Http\Request;

use App\Models\Insurance\Plan;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;

class PlanController extends Controller
{
    use InsuranceTrait;
    public function index()
    {
        return response()->json([
            'providers' => $this->insurance_provider_get_all('active', null, false, false, $_GET['page'] ?? 1),
            'provider_type' => $this-> insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function initials()
    {
        return response()->json([
            'provider' => $this->insurance_provider_get_by_id($id),
            'provider_type' => $this-> insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function provider($id)
    {
        return response()->json([
            'plans' => $this->insurance_provider_plan_get_all('provider', $id, true, true, $_GET['page'] ?? 1),
            'provider' => $this->insurance_provider_get_by_id($id),
            'provider_type' => $this->insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),          
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|numeric',
            'name' => 'required',
            'status' => 'required|numeric',
            'description' => 'sometimes|nullable',
        ]);

        $plan = $this->insurance_provider_plan_create($request);

        return response()->json([
            'plan' => $plan,
            'plans' => $this->insurance_provider_plan_get_all('provider', $plan->provider_id, true, true, $_GET['page'] ?? 1),
            'provider' => $this->insurance_provider_get_by_id($plan->provider_id),
            'provider_type' => $this-> insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'plan' => $this->insurance_provider_plan_get_by_id($id),
            'plan_branches' => $this->insurance_provider_plan_branch_get_all('plan', $id, true, false, null),
        ]);
    }

    public function update(Request $request, $id)
    {
        $plan = $this->insurance_provider_plan_update($request, $id);
        
        return response()->json([
            'plan' => $plan,
            'plans' => $this->insurance_provider_plan_get_all('provider', $plan->provider_id, true, true, $_GET['page'] ?? 1),
            'provider' => $this->insurance_provider_get_by_id($plan->provider_id),
            'provider_type' => $this-> insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),      
        ]); 
    }

    public function destroy($id)
    {
        $plan = $this->insurance_provider_plan_delete($id);

        return response()->json([
            'plan' => $plan,
            'plans' => $this->insurance_provider_plan_get_all('provider', $plan->provider_id, true, true, $_GET['page'] ?? 1),
            'provider' => $this->insurance_provider_get_by_id($plan->provider_id),
            'provider_type' => $this-> insurance_provider_type_get_all('all', null, false, false, $_GET['page'] ?? 1),      
        ]); 
    }
}
