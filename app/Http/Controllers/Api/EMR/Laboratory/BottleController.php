<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Models\EMR\Settings\LaboratoryBottle;
use Illuminate\Http\Request;

use App\Http\Traits\EMR\LaboratoryTrait;

class BottleController extends Controller
{
    use LaboratoryTrait;

    public function index()
    {
        return response()->json([
            'bottles' => $this->laboratory_bottles_get_all('all_active', true, true, $_GET['page']),
        ]);
    }

    public function store(Request $request)
    {
        $bottle = $this->laboratory_bottles_create($request);
        return response()->json([
            'bottles' => $this->laboratory_bottles_get_all('all_active', true, true, $_GET['page']),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $bottle = LaboratoryBottle::where('id', '=', $id)->first();

        $bottle->name = $request->input('name');
        $bottle->colour = $request->input('colour');
        $bottle->size = $request->input('size');
        $bottle->updated_by = auth('api')->id();
        
        $bottle->save();

        return response()->json([
            'bottles' => LaboratoryBottle::orderBy('name', 'ASC')->with(['creator', 'deleter', 'updater'])->paginate(30),
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
