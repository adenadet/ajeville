<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\SalaryTrait;
use Illuminate\Http\Request;

class EmployeeDeductionController extends Controller
{
    use EmployeeTrait, SalaryTrait;

    public function destroy(string $id)
    {
        $deduction = $this->hrms_salary_employee_deduction_deactivate($id);

        return response()->json([
            'deduction' => $deduction,
        ], is_string($deduction) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'deductions' => $this->hrms_salary_employee_deduction_get_all($_GET['type'] ?? 'all', $_GET, true, true)
        ]);
    }

    public function initials()
    {
        return response()->json([
            'employees' => $this->hrms_employee_get_all('active', null, false, false, null),
        ]);
    }
    public function show(string $id)
    {
        $deduction = $this->hrms_salary_employee_deduction_get_by(null, $id, true);
        return response()->json([
            'deduction' => $deduction
        ], is_string($deduction) ? 404 : 200);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'employee_id' => 'required',
            'name' => 'required',
            'amount' => 'required|numeric',
            'description' => 'sometimes|nullable|string',
            'month' => 'required',
        ]);

        $deduction = $this->hrms_salary_employee_deduction_create($request);
        return response()->json([
            'deduction' => $deduction
        ], is_string($deduction) ? 500 : 201);

    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'employee_id' => 'required',
            'name' => 'required',
            'amount' => 'required|numeric',
            'description' => 'sometimes|nullable|string',
            'month' => 'required',
        ]);

        $deduction = $this->hrms_salary_employee_deduction_update($request, $id);
        return response()->json([
            'deduction' => $deduction
        ], is_string($deduction) ? 500 : 200);
    }
}
