<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\PreemployeeTrait;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    use PreemployeeTrait;
    public function destroy(string $id)
    {
        $bonus = $this->hrms_salary_employee_bonus_deactivate($id);

        
    }

    public function index()
    {
        //
    }
    public function show(string $id)
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
