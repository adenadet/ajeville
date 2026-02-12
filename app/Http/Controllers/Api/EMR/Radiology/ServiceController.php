<?php

namespace App\Http\Controllers\Api\EMR\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\EMR\SettingsTrait;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use RadiologyTrait, SettingsTrait;
    public function index()
    {
        //
    }

    public function initials()
    {
        return response()->json([
            'locations' => $this->emr_settings_location_get_all('active', null, false, false),
            'radiology_types' => $this->emr_radiology_investigation_type_get_all('active', null, false, false),
        ]);
    }

    public function store(Request $request)
    {
        //
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