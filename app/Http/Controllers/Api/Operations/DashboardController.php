<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Operations\DepartmentTrait;
use App\Http\Traits\Operations\DrugTrait;
use App\Http\Traits\Operations\PriceListTrait;

class DashboardController extends Controller
{
    use BranchTrait, DepartmentTrait, DrugTrait, PriceListTrait;
    public function index()
    {
        return response()->json([
            'branches' => $this->operation_branch_get_all(false, false, $_GET['page'] ?? 1),
            'departments' => $this->operation_department_get_all_departments(false, false, $_GET['page'] ?? 1), 
            'drugs' => $this->operation_drug_get_all(false, false, $_GET['page'] ?? 1),
            'price_lists' => $this->operation_price_list_get_all('all', null, false, false, $_GET['page'] ?? 1)         
        ]);
    }

    public function store(Request $request)
    {
        //
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
