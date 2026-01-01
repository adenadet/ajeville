<?php
namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollRun;
use App\Services\SalaryCalculator;
use Illuminate\Http\Request;

class PayrollRunController extends Controller
{
    public function run(Request $r, SalaryCalculator $calc)
    {
        $period = $r->input('period');
        $employees = \App\Models\Employee::with(['salaryStructure','bonuses','deductions'])->get();

        $run = PayrollRun::create(['period' => $period, 'created_by' => $r->user()->id ?? null]);

        foreach($employees as $emp){
            $res = $calc->computeForEmployee($emp);
            $run->items()->create([
                'employee_id' => $emp->id,
                'gross_pay' => $res['gross'],
                'total_allowances' => $res['breakdown']['bonuses'] ? array_sum(array_column($res['breakdown']['bonuses'], 'amount')) : 0,
                'total_deductions' => $res['totalDeductions'] ?? $res['totalDeductions'],
                'net_pay' => $res['net'],
                'breakdown' => $res['breakdown'],
            ]);
        }

        $run->status = 'finalized';
        $run->save();

        return $run->load('items');
    }

    public function show($id){
        return PayrollRun::with('items')->findOrFail($id);
    }
}
