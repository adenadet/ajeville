<?php

namespace App\Http\Controllers\Api\EMR\Anesthesia;

use App\Http\Controllers\Controller;
use App\Http\Traits\Coop\SettingsTrait;
use App\Http\Traits\EMR\AnesthesiaTrait;
use App\Http\Traits\Operations\DrugTrait;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    use AnesthesiaTrait, DrugTrait, SettingsTrait;
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        //
    }

    public function initials()
    {
        return response()->json([
            'drugs' => $this->operation_drug_get_all(false, false, null),
            'forms' => $this->emr_settings_drug_form_get_all('active', null, false, false),
            'routes' => $this->emr_settings_drug_route_get_all('active', null, false, false),
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
