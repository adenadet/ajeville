<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use App\Models\Loans\ConfirmationMatrix;
use Illuminate\Http\Request;

use App\Models\Loans\Requirement;
use App\Models\Loans\Type;
use App\Models\Loans\TypeRequirement;

class TypeController extends Controller
{
    public function index()
    {
        return response()->json([
            'loan_types' => Type::with('requirements')->paginate(30),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'matrices' => ConfirmationMatrix::orderby('name', 'ASC')->get(),
            'requirements' => Requirement::orderBy('name', 'ASC')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'requirements' => 'required|array',
            'name' => 'required',
            'category_id' => 'required|numeric',
            'percentage' => 'required|numeric',
            'min_duration' => 'nullable|numeric',
            'max_duration' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $type = Type::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'interest_type'=> $request->input('interest_type') ?? 'Flat',
            'percentage' => $request->input('percentage') ?? 0.6,
            'min_duration' => $request->input('min_duration'),
            'max_duration' => $request->input('max_duration'),
            'start_date' => $request->input('state_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status') ?? 1,
            'matrix_id' => $request->input('matrix_id') ?? 1,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        $type_requirement = TypeRequirement::where('loan_type_id', '=', $type->id)->delete();

        foreach ($request->input('requirements') as $requirement){
            TypeRequirement::create([
                'loan_type_id' => $type->id,
                'requirement_id' => $requirement,
            ]);
        }
        return response()->json([
            'loan_types' => Type::orderBy('name', 'ASC')->with('requirements')->paginate(30),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'loan_type' => Type::where('id', '=', $id)->with('requirements')->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'requirements' => 'required|array',
            'name' => 'required',
            'category_id' => 'required|numeric',
            'percentage' => 'required|numeric',
            'min_duration' => 'nullable|numeric',
            'max_duration' => 'nullable|numeric',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $type = Type::find($id);

        
        $type->name = $request->input('name');
        $type->category_id = $request->input('category_id');
        $type->percentage = $request->input('percentage');
        $type->min_duration = $request->input('min_duration');
        $type->max_duration = $request->input('max_duration');
        $type->start_date = $request->input('state_date');
        $type->end_date = $request->input('end_date');
        $type->status = $request->input('status');
        $type->updated_by = auth('api')->id();

        $type->save();
        
        if (TypeRequirement::where('loan_type_id', '=', $type->id)) {
            TypeRequirement::where('loan_type_id', '=', $type->id)->delete();
        }

        foreach ($request->input('requirements') as $requirement){
            TypeRequirement::create([
                'loan_type_id' => $type->id,
                'requirement_id' => $requirement,
            ]);
        }
     
        return response()->json([
            'loan_types' => Type::orderBy('name', 'ASC')->with('requirements')->paginate(30),
        ]);
    }

    public function destroy($id)
    {
        $type = Type::where('id', '=', $id)->first();

        $type->deleted_by = auth('api')->id();
        $type->deleted_at = date('Y-m-d H:i:s');
        $type->status = 2;

        $type->save();

        return response()->json([
            'loan_types' => Type::orderBy('name', 'ASC')->with('requirements')->paginate(30),
        ]);
    }
}
