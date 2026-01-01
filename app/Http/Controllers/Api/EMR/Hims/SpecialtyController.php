<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Models\EMR\Specialty;
use App\Models\EMR\SpecialtyDoctor;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        return response()->json([
            'specialties' => Specialty::orderBy('name')->paginate(20),
        ]);
    }

    public function store(Request $request)
    {
        $specialty = Specialty::create([
            'name' => $request->input('name'),
        ]);

        foreach ($request->input('doctors') as $doctor){
            SpecialtyDoctor::create([
                'specialty_id' => $specialty->id,
                'doctor_id' => $doctor->id,
            ]);
        }

        return response()->json([
            'specialties' => Specialty::orderBy('name')->paginate(20),
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
