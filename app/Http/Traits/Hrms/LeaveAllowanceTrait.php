<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\General\FileManagerTrait;

use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveAllowance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;

trait LeaveAllowanceTrait{
    use FileManagerTrait, LogTrait;

    public function hrms_leave_allowance_approve_request($data, $id){
        $leave_allowance = LeaveAllowance::find($id);

        $leave_allowance->status = LeaveAllowance::StatusApproved;
        $leave_allowance->amount = $data['amount'];
        $leave_allowance->approved_by = Auth::id() ?? auth('api')->id(); 
        $leave_allowance->approved_at = date('Y-m-d H:i:s'); 
        $leave_allowance->approval_remark = $data['approval_remark'];

        $leave_allowance->save();

        return $leave_allowance;
    }
    
    public function hrms_leave_allowance_create($employee_id, $leave_request_id){
        $leave_allowance = LeaveAllowance::create([
            'employee_id' => $employee_id,
            'request_id' => $leave_request_id,
            'status' => LeaveAllowance::StatusPending,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $leave_allowance;
    }

    public function hrms_leave_allowance_delete_request($id){
        $leave_allowance = LeaveAllowance::find($id);

        $leave_allowance->deleted_by = Auth::id() ?? auth('api')->id(); 
        $leave_allowance->deleted_at = date('Y-m-d H:i:s'); 
    
        $leave_allowance->save();

        return $leave_allowance;
    }

    public function hrms_leave_allowance_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'all':
                $query = LeaveAllowance::all();
            break;
            case 'mine':
                $employee = Employee::where('user_id', '=', (Auth::id() ?? auth('api')->id()))->first();
                $query = LeaveAllowance::where('employee_id', '=', $employee->id);
            break;
            case 'status':
                $query = LeaveAllowance::where('status', '=', $specific);
            break; 
        }

        $query = $detailed ? $query->with(['employee.user']) : $query;
        $query = $paginated ? $query->paginate(50) : $query->get(); 

        return $query;
    }

    public function hrms_leave_allowance_get_by_id($id){
        $leave_allowance = LeaveAllowance::where('id', '=', $id)->with(['approver', 'employee', 'updater'])->first();
        return $leave_allowance;
    }

    public function hrms_leave_allowance_process_request($data, $id){
        DB::beginTransaction();

        try{
            $leave_allowance = LeaveAllowance::find($id);

            $leave_allowance->status = LeaveAllowance::StatusProcessed;
            $leave_allowance->amount = $data['amount'];
            $leave_allowance->approved_by = Auth::id() ?? auth('api')->id(); 
            $leave_allowance->approved_at = date('Y-m-d H:i:s'); 
            $leave_allowance->approval_remark = $data['approval_remark'];

            $leave_allowance->save();


            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to process leave allowance'], 500);
        }
        
        return $leave_allowance;
    }
}