<?php
namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    public function index(){ return SalaryStructure::with('components')->get(); }
    public function store(Request $r){
        $s = SalaryStructure::create($r->only(['name','description','active']));
        if($r->has('components')){
            $s->components()->createMany($r->input('components'));
        }
        return $s->load('components');
    }
    public function show(SalaryStructure $salaryStructure){ return $salaryStructure->load('components'); }
    public function update(Request $r, SalaryStructure $salaryStructure){
        $salaryStructure->update($r->only(['name','description','active']));
        // TODO: update components robustly
        return $salaryStructure->load('components');
    }
    public function destroy(SalaryStructure $salaryStructure){ $salaryStructure->delete(); return response('',204); }
}
