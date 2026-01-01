<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Models\Insurance\ContactPerson;
use App\Models\Insurance\Plan;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    use InsuranceTrait;
    public function index()
    {
        return response()->json([
            'providers' => $this->insurance_provider_get_all($_GET['q'] ?? 'active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),    
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'provider' => $this->insurance_provider_create($request),
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'provider' => $this->insurance_provider_get_by_id($id),
            'plans' => Plan::where('provider_id', '=', $id)->get(),
            'contacts' => ContactPerson::where('provider_id', '=', $id)->get(),   
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page']?? 1), 
        ]);
    }

    public function update(Request $request, $id)
    {

        return response()->json([
            'provider' => $this->insurance_provider_update($request, $id),
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page']?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'provider' => $this->insurance_provider_deactivate($id),
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }
}
