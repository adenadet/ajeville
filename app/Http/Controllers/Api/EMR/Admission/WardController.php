<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use App\Http\Traits\Operations\BranchTrait;
use Illuminate\Http\Request;

class WardController extends Controller
{
    use AdmissionTrait, BranchTrait;

    public function destroy(string $id)
    {
        $ward = $this->admission_ward_deactivate($id);

        return response()->json([
            'ward' => $ward
        ], is_string($ward) ? 500 : 200);
    }

    public function index()
    {
        $wards = $this->admission_ward_get_all($_GET['type'] ?? 'active', $_GET, true, true);

        return response()->json([
            'wards' => $wards
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => $this->operation_branch_get_all('active', false, false),
        ]);
    }

    public function show(string $id)
    {
        $ward = $this->admission_ward_get_by(null, $id, true);

        return response()->json([
            'rooms' => $ward->rooms,    
            'ward' => $ward,
        ], is_string($ward) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $ward = $this->admission_ward_create($request);

        return response()->json([
            'ward' => $ward
        ], is_string($ward) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $ward = $this->admission_ward_update($request, $id);

        return response()->json([
            'ward' => $ward
        ], is_string($ward) ? 500 : 200);
    }
}
