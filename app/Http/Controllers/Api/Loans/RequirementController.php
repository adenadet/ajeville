<?php

namespace App\Http\Controllers\Api\Loans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Loans\Requirement;

class RequirementController extends Controller
{
    public function index()
    {
        return response()->json([
            'loan_requirements' => Requirement::orderBy('name', 'ASC')->paginate(30),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|string',
            'rate' => 'nullable|numeric',
            'expires' => 'sometimes|boolean',
            'attachment' => 'sometimes|boolean',
        ]);

        $requirement = Requirement::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'rate' => $request->input('rate') ?? NULL,
            'expires' => $request->input('expires') ?? 0,
            'attachment' => $request->input('attachment') ?? 0,
            'created_by' => auth('api')->id(),
            'updated_by' => auth('api')->id(),
        ]);

        return response()->json([
            'loan_requirements' => Requirement::orderBy('name', 'ASC')->paginate(30),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'loan_requirement' => Requirement::where('id', '=', $id)->first(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|string',
            'rate' => 'nullable|numeric',
            'expires' => 'sometimes|boolean',
            'attachment' => 'sometimes|boolean',
        ]);

        $requirement = Requirement::find($id);

        $requirement->name = $request->input('name');
        $requirement->type = $request->input('type');
        $requirement->rate = $request->input('rate');
        $requirement->expires = $request->input('expires') ?? 0;
        $requirement->attachment = $request->input('attachment') ?? 0;
        $requirement->created_by = auth('api')->id();
        $requirement->updated_by = auth('api')->id();
        
        $requirement->save();

        return response()->json([
            'loan_requirements' => Requirement::orderBy('name', 'ASC')->paginate(30),
        ]);
    }

    public function destroy($id)
    {
        $requirement = Requirement::find($id);

        $requirement->deleted_by = auth('api')->id();
        $requirement->deleted_at = date('Y-m-d H:i:s');

        $requirement->save();

        return response()->json([
            'loan_requirements' => Requirement::orderBy('name', 'ASC')->paginate(30),
        ]);
    }
}
