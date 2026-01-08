<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EMR\Drugs\Drug;
use App\Http\Traits\Operations\DrugTrait;
use App\Models\EMR\Drugs\Form;
use App\Models\EMR\Drugs\Route;
use App\Models\EMR\Settings\Frequency;

class DrugController extends Controller
{
    use DrugTrait;

    public function index()
    {
        return response()->json([
            'drugs' => $this->operation_drug_get_all(true, true, $_GET['page'] ?? 1),
        ]);   
    }

    public function initials(){
        return response()->json([
            'drug_forms' => Form::select('name')->orderBy('name', 'ASC')->get(),
            'frequencies' => Frequency::select('id', 'code', 'per_day', 'name')->orderBy('name', 'ASC')->get(),
            'routes' => Route::select('name')->orderBy('name', 'ASC')->get(),
        ]);
    }
   
    public function search()
    {
        if (!empty($_GET['q'])){
            $search = $_GET['q'];
            $drugs = Drug::select('id', 'name')->orderBy('name', 'ASC')->where('name', 'LIKE', "%$search%");
        }
        else{
            $drugs = Drug::select('id', 'name')->orderBy('name', 'ASC');
        }
        
        return response()->json([
            'drugs' => $drugs->with('specific_drugs')->get(),
        ]);
    }

    public function specific_store(Request $request)
    {
        $drug = Drug::create([
            'name'=>$request->input('name'),
            'description' => $request->input('description'),
            'ham' => $request->input('ham') ?? 0,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'drugs' => Drug::select('id', 'name')->orderBy('name', 'ASC')->with('specific_drugs')->paginate(50),
        ]);
    }

    public function store(Request $request)
    {
        $drug = Drug::create([
            'name'=>$request->input('name'),
            'description' => $request->input('description'),
            'ham' => $request->input('ham') ?? 0,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'drugs' => Drug::select('id', 'name')->orderBy('name', 'ASC')->with('specific_drugs')->paginate(50),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'drug' => Drug::where('id', '=', $id)->orderBy('name', 'ASC')->with('specific_drugs')->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $drug = Drug::where('id', '=', $id)->first();

        $drug->name = $request->input('name');
        $drug->description  =  $request->input('description');
        $drug->ham  =  $request->input('ham') ?? 0;
        $drug->updated_by  =  auth('api')->id();

        $drug->save();

        return response()->json([
            'drugs' => Drug::select('id', 'name')->orderBy('name', 'ASC')->with('specific_drugs')->paginate(50),
        ]);
    }

    public function destroy($id)
    {
        $drug = Drug::where('id', '=', $id)->first();

        $drug->deleted_by = auth('api')->id();
        $drug->deleted_at = date('Y-m-d H:i:s');

        $drug->save();

        return response()->json([
            'drugs' => Drug::select('id', 'name')->orderBy('name', 'ASC')->with('specific_drugs')->paginate(50),
        ]);
    }
}
