<?php

namespace App\Imports\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Models\Hrms\Employee;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class LeaveRequestImport implements  ToModel, WithHeadingRow
{
    use EmployeeTrait, LeaveTrait, LogTrait;
    public function model(array $row)
    {
        $employee_id = Employee::where('username', '=', $row['staff_id'])->first()->pluck('id');
        $row['employee_id'] = $employee_id;
        $leave_type = $this->hrms_leave_request_create_leave($row);
        
        $this->log_user_activity('New Employee Created', $leave_type->id, true);
        return $leave_type;
    }
}
