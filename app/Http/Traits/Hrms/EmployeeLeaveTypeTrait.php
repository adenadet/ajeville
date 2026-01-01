<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;

use App\Models\Hrms\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;

use Mail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait EmployeeLeaveTypeTrait{
    use LogTrait;
    public function employee_leave_type_create($data){
        DB::beginTransaction();
        try{
            //;
            foreach ($data['leave_types'] as $leave_type_id){
                $leave_type = LeaveType::findOrFail($leave_type_id);
                //Check if that leave_type is not already assigned to user
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $data['employee_id'])->where('leave_type_id', '=', $leave_type_id)->count();
                if ($employee_leave_type == 0){
                    $query = EmployeeLeaveType::create([
                        'employee_id' => $data['employee_id'],
                        'leave_type_id' => $leave_type_id,
                        'balance' => $leave_type->no_of_days,
                        'days_used' => 0,
                        'pending_days' => 0,
                        'created_by' => Auth::id() ?? auth('api')->id(),
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);
                    $this->log_user_activity('employee_leave_type_create', $query->id, true);
                }
            }
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
        return $query ?? null; 
    }

    public function employee_leave_type_delete($id){
        DB::beginTransaction();
        try{
            $query = EmployeeLeaveType::find($id);
            
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');

            $query->save();

            $this->log_user_activity('employee_leave_type_delete', $query->id, true);
            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('employee_leave_type_delete', $id, false);
            return $e->getMessage();
        }
        return $query; 
    }
    
    public function employee_leave_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $active_leave_types = LeaveType::whereDate('start_date', '>=', date('Y-m-d'))->whereDate('end_date', '<', date('Y-m-d'))->pluck('id');
                $query = EmployeeLeaveType::whereIn('leave_type_id', $active_leave_types);
            break;
            case 'employee_id':
                $query = EmployeeLeaveType::where('employee_id', '=', $specific);
            break;
            case 'employee_unique_id':
                $employee = Employee::where('username', '=', $specific);
                $query = EmployeeLeaveType::where('employee_id', '=', $employee->id);
            break;    
            case 'leave_type_id':
                $query = EmployeeLeaveType::where('employee_id', '=', $specific);
            break;
            case 'user_id':
                $employee = Employee::where('user_id', '=', $specific);
                $query = EmployeeLeaveType::where('employee_id', '=', $employee->id);
            break;
        }

        $query = $detailed ? $query->with(['employee', 'leave_type', 'creator', 'updater']) : $query->select('id', 'balanced', 'days_used');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function employee_leave_type_update($data, $id){
        $query = EmployeeLeaveType::find($id);
        
        $query->employee_id = $data['employee_id'];
        $query->leave_type_id = $data['leave_type_id'];
        $query->balance = $data['balance'];
        $query->days_used = $data['no_of_days'];
        $query->pending_days = $data['pending'];
        $query->updated_by = Auth::id() ?? auth('api')->id();
        

        return $query; 
    }
}