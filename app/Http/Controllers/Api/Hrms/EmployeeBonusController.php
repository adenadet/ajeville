<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\SalaryTrait;
use Illuminate\Http\Request;

class EmployeeBonusController extends Controller
{
    use EmployeeTrait, SalaryTrait;

    public function destroy(string $id)
    {
        $bonus = $this->hrms_salary_employee_bonus_deactivate($id);

        return response()->json([
            'bonus' => $bonus,
        ], is_string($bonus) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'bonuses' => $this->hrms_salary_employee_bonus_get_all($_GET['type'] ?? 'all', $_GET, true, true)
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
        $bonus = $this->hrms_salary_employee_bonus_get_by(null, $id, true);
        return response()->json([
            'bonuses' => $bonus
        ], is_string($bonus) ? 404 : 200);
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

        $bonus = $this->hrms_salary_employee_bonus_create($request);
        return response()->json([
            'bonuses' => $bonus
        ], is_string($bonus) ? 500 : 201);

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

        $bonus = $this->hrms_salary_employee_bonus_update($request, $id);
        return response()->json([
            'bonuses' => $bonus
        ], is_string($bonus) ? 500 : 200);
    }
}
