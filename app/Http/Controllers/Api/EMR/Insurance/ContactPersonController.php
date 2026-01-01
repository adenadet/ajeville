<?php

namespace App\Http\Controllers\Api\EMR\Insurance;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Models\Insurance\ContactPerson;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use Illuminate\Http\Request;

class ContactPersonController extends Controller
{
    use InsuranceTrait;
    public function index()
    {
        return response()->json([
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),       
        ]);
    }

    public function initials()
    {
        return response()->json([
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),   
        ]);
    }

    public function provider($id)
    {
        return response()->json([
            'contacts' => $this->insurance_provider_contact_get_all('provider', $id, false, true, $_GET['page'] ?? 1),
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),       
        ]);
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'provider_id' => 'required|numeric',
            'name' => 'required',
            'status' => 'required|numeric',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);

        $contact = $this->insurance_provider_contact_create($data);

        return response()->json([
            'contacts' => $this->insurance_provider_contact_get_all('provider', $contact->provider_id, false, true, $_GET['page'] ?? 1),
            'providers' => $this->insurance_provider_get_all('active', null, true, true, $_GET['page'] ?? 1),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, false, false, $_GET['page'] ?? 1),      
        ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
