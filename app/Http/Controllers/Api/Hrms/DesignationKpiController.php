<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\DesignationTrait;
use Illuminate\Http\Request;

class DesignationKpiController extends Controller
{
    use DesignationTrait;
    
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        $kpis = $this->hrms_designation_kpi_get_all($_GET['designation'] ?? null, $_GET['type'], $_GET['query'] ?? null, true, true);

        return response()->json([
            'kpis' => $kpis
        ]);
    }

    public function initials()
    {
        return response()->json([
            'designations' => $this->hrms_designation_get_all('active', null, false, false),
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        $kpi = $this->hrms_designation_kpi_create($request);

        return response()->json([
            'kpi' => $kpi
        ], is_string($kpi) ? 500 : 201);
    }
    
    public function update(Request $request, string $id)
    {
        $kpi = $this->hrms_designation_kpi_update($request, $id);

        return response()->json([
            'kpi' => $kpi
        ], is_string($kpi) ? 500 : 200);
    }

}
