<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\Hrms\LeaveAllowanceTrait;
use App\Models\Hrms\AttendanceSummary;
use App\Models\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\PublicHoliday;
use App\Models\Hrms\LeaveRequest;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\OrganizationHierarchy;

use App\Mail\AdminApplyLeaveMail;
use App\Mail\ApplyLeaveMail;
use App\Mail\Leave\ConfirmMail;
use App\Mail\Leave\RejectMail;
use App\Mail\Leave\RequestMail;
use App\Mail\Leave\SupervisorConfirmMail;
use App\Mail\Leave\SupervisorInfoMail;
use App\Mail\LeaveStatusMail;

use App\Notifications\Leave\Created;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Session;

trait LeaveTrait{
    use FileManagerTrait, LeaveAllowanceTrait, LogTrait;

    public function hrms_leave_employee_assigned_leave_types($employee_id){
        $query = EmployeeLeaveType::where('employee_id', '=', $employee_id)
            ->with(['employee.user', 'leave_type'])
            ->get();

        return $query;
    }
    public function hrms_leave_employee_assign_leave_types($employee_id, $leave_types){
        DB::beginTransaction();

        try{
            foreach($leave_types as $leave_type){
                $employee_leave_type = EmployeeLeaveType::create([
                    'employee_id' => $employee_id,
                    'leave_type_id' => $leave_type,
                    'days_used' => 0,
                    'pending_days' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $this->log_user_activity('leave_request_confirm', $leave_types, false);
            }
        } 
        catch(Exception $e){
            $this->log_user_activity('leave_request_confirm', $leave_types, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            $employee_leave_types = $this->hrms_leave_types_get_my_current_leave_types($employee_id, true, true);
            return $employee_leave_types;
        }
        else{
            DB::rollBack();
        } 
    }

    public function hrms_leave_request_confirm_leave($data, $id){
        DB::beginTransaction();
        try{
            $leave_request = LeaveRequest::where('id', '=', $id)->first();
            //print($leave_request->status);
            if ($data['action'] == 'confirm'){
                //Update the leave request to confirmed 
                $leave_request->status = 3;
                $leave_request->approval_remark = $data['remark'];
                $leave_request->approved_by = auth('api')->id();
                $leave_request->approved_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->hrms_leave_request_number_of_days($leave_request);
                
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days -= $days;
                $employee_leave_type->days_used += $days;
                $employee_leave_type->save();

                $employee = Employee::where('id', '=', $leave_request->employee_id)->with(['user'])->first();
                //send mail to line manager 
                $line_manager = Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->with(['user'])->first();
                if ($employee){
                    if (!(is_null($employee->email))){
                        $mailed = Mail::to($employee->email)->send(new ConfirmMail($leave_request, $employee->user, $line_manager->user, $days, $data['message']));
                    }
                
                    $supervisor = Employee::where('employee_id', '=', $employee->supervisor_id)->with(['user'])->first();
                    if ($supervisor){
                        if (!(is_null($supervisor->email))){
                            $mailed = Mail::to($supervisor->email)->send(new SupervisorConfirmMail($leave_request, $employee, $supervisor->user, $line_manager->user));
                        }
                    }
                }
                //send mail to supervisor
                
                if ($leave_request->leave_allowance) {$this->hrms_leave_allowance_create_request($employee->id, $leave_request->id);}
                $this->log_user_activity('leave_request_confirm', $id, true);
            }
            
            else if ($data['action'] == 'reject'){
                $leave_request->status = 10;
                $leave_request->approval_remark = $data['remark'];
                $leave_request->approved_by = auth('api')->id();
                $leave_request->approved_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->hrms_leave_request_number_of_days($leave_request);
                
                //Update Employee Leave Type to ensure the dates are aligned 
                $employee_leave_type = EmployeeLeaveType::where('user_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days -= $days;
                $employee_leave_type->save();

                $employee = Employee::find($leave_request->employee_id);
                //send mail to line manager 
                $line_manager = Employee::where('user_id', '=', auth('api')->id() ?? Auth::id())->with(['user'])->first();
                if ($employee){
                    if (!(is_null($employee->email))){
                        $mailed = Mail::to($employee->email)->send(new RejectMail($leave_request, $employee->user, $line_manager->user, $days, $data['message']));
                    }
                }

                $this->log_user_activity('leave_request_reject', $id, true);
            }
        
            DB::commit();
            return $leave_request;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('leave_request_confirm', $id, false);
            return $e->getMessage();
        }    
    }
    public function hrms_leave_request_create_leave($data){
        DB::beginTransaction();
        try{
            if (isset($data['employee_id'])){
                $employee = Employee::find($data['employee_id']);
            }
            else{
                $employee = Employee::where('user_id', '=', (Auth::id() ?? auth('api')->id()))->first();
            }

            if (isset($data['leave_type_id'])){
                $user_leave_type = EmployeeLeaveType::find($data['leave_type_id']);
            }
            else{
                $user_leave_type = EmployeeLeaveType::where('employee_id', '=', $data['employee_id'])->where('leave_type_id', '=', $data['leave_id'])->first();
            }

            //upload leave attachment
            $leave_attachment = !(is_null($data['leave_attachment'])) ? $this->file_upload($data['pdf'], null, 'uploads/leave_requests', $employee->user_id): null;
            
            //create a new leave request
            $leave_request = LeaveRequest::create([
                'employee_id' => $employee->id,
                'user_leave_type_id' => $user_leave_type->id, 
                'leave_type_id' => $user_leave_type->leave_type_id,
                'from_date' => $data['from_date'], 
                'to_date' => $data['to_date'], 
                'leave_allowance' => $data['leave_allowance'] ? 1 : 0,
                'reason' => $data['reason'], 
                'remarks' => $data['remarks'], 
                'status' => $data['status'] ?? 0, 
                'is_half_day' => $data['is_half_day'] ?? 0, 
                'leave_attachment' => $leave_attachment ?? NULL, 
                'created_by' => auth('api')->id(), 
                'updated_by' => auth('api')->id(), 
            ]);

            //if creation successful, 
            if ($leave_request){
                //Create log activity
                $this->log_user_activity('leave_request_new', $leave_request->id, true);
                $days = $this->hrms_leave_request_number_of_days($leave_request);
                
                $data = Array();
                $data['days'] = $days;
                //send mail to line manager 
                $line_manager = Employee::where('employee_id', '=', $employee->reports_to)->with(['user'])->first();
                if ($line_manager){
                    //$line_manager->user->notify(new Created($data, $employee, $leave_request, $line_manager, null));
                    if (!(is_null($line_manager->email))){
                        $mailed = Mail::to($line_manager->email)->send(new RequestMail($leave_request, $employee, $line_manager->user));
                    }
                }
                //send mail to supervisor
                $supervisor = Employee::where('employee_id', '=', $employee->supervisor_id)->with(['user'])->first();
                if (isset($supervisor)){
                    if (!(is_null($supervisor->email))){
                        $mailed = Mail::to($supervisor->email)->send(new SupervisorInfoMail($leave_request, $employee, $supervisor->user, $line_manager->user));
                    }
                }
                
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days += $days;
                
                $employee_leave_type->save();

                $complete = true;

                $this->log_user_activity('leave_request_new', $leave_request->id, true);
        
            }
            else{
                $this->log_user_activity('leave_request_new', $leave_request->id, false);
                $complete = false;
            }
        }
        catch(Exception $e){
            $this->log_user_activity('leave_request_new', null, false);
            $complete = false;
            return $e->getMessage();
        }
        if ($complete){
            DB::commit();
            return $leave_request;
        }
        else{
            DB::rollBack();
        }        
    }
    public function hrms_leave_request_delete_leave($id){
        DB::beginTransaction();

        try{
            $leave_request = LeaveRequest::where('id', '=', $id)->where('status', '=', 1)->first();
            if ($leave_request){
                $leave_request->status = 2;
                $leave_request->deleted_by = auth('api')->id();
                $leave_request->deleted_at = date('Y-m-d H:i:s');
                $leave_request->save();

                $days = $this->hrms_leave_request_number_of_days($leave_request);
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days += $days;
                $employee_leave_type->save();
            
                $this->log_user_activity('leave_request_delete', $id, true);
                $complete = true;
            }
            else{
                $this->log_user_activity('leave_request_delete', $id, false);
                $complete = false;    
            }
        }
        catch (Exception $e){
            $this->log_user_activity('leave_request_delete', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_request;
        }
        else{
            DB::rollBack();
        }     
    }
    public function hrms_leave_request_get_all($status, $specific,  $detailed, $paginated, $page){
        $query = LeaveRequest::query();
        switch ($status){
            case 'active':
                $query = $query->whereDate('from_date', '>=', date('Y-m-d'))->whereDate('to_date', '<=', date('Y-m-d'))->where('status', '=', 3);
                break;
            case 'all':
                $query = $query->orderBy('from_date', 'DESC');
                break;
            case 'cancelled':
                $query = $query->where('status', '=', 2);
                break;
            case 'completed':
                $query = $query->whereDate('to_date', '>', date('Y-m-d'))->where('status', '=', 3);
                break;
            case 'leave_type':
                $query = $query->where('leave_type_id', '=', $specific);
                break;
            case 'mine':
                $employee = Employee::where('user_id', '=', Auth::id() ?? auth('api')->id())->first();
                $query = $query->where('employee_id', '=', $employee->id)->orderBy('from_date', 'DESC');
                break; 
            case 'team':
                $employee = Employee::where('user_id', '=', auth('api')->id())->first();
                $team_members = Employee::where('reports_to', '=', $employee->employee_id)->orWhere('supervisor_id', '=', $employee->employee_id)->pluck('id');
                $query = $query->whereIn('employee_id', $team_members);
                if ($specific != 'all'){$query = $query->where('status', '=', $specific);}
                $query = $query->orderBy('status', 'ASC');
                break;
            case 'pending':
                $query = $query->whereDate('from_date', '<=', date('Y-m-d'))->where('status', '=', 1);
                break;
            case 'rejected':
                $query = $query->where('status', '=', 4);
                break;
            default:
                $query = null;
        }
        
        /*
        if(is_null($query)){
            return [];
        }
        else{
            $quest = $detailed ? $query->with(['employee.user', 'leave_type', 'approver']) : $query;
            $leaves = $paginated ? $quest->latest()->paginate(50) : $quest->latest()->get();
            
            return $leaves;
        }*/
        $quest = $detailed ? $query->with(['employee.user', 'leave_type', 'approver']) : $query;
        $leaves = $paginated ? $quest->latest()->paginate(50) : $quest->latest()->get();
        
        return $leaves;
    }

    public function hrms_leave_request_number_of_days($leave_request){
        $leave_type = LeaveType::find($leave_request->leave_type_id);
        $start_date = new DateTime($leave_request->start_date);
        $end_date = new DateTime($leave_request->to_date);
        $days = 0;
        if ($leave_type->leave_category == 'Working'){
            if (!defined('SATURDAY')) define('SATURDAY', 6);
            if (!defined('SUNDAY')) define('SUNDAY', 0);

            // Array of all public festivities
            $publicHolidays = PublicHoliday::whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->pluck('date')->toArray();

            $yearStart = date('Y', strtotime($leave_request->start_date));
            $yearEnd   = date('Y', strtotime($leave_request->end_date));

            for ($i = $yearStart; $i <= $yearEnd; $i++) {
                $easter = date('Y-m-d', easter_date($i));
                list($y, $m, $g) = explode("-", $easter);
                $monday = mktime(0,0,0, date($m), date($g)+1, date($y));
                $easterMondays[] = $monday;
            }

            $start = strtotime($leave_request->start_date);
            $end   = strtotime($leave_request->end_date);
            $workdays = 0;
            for ($i = $start; $i <= $end; $i = strtotime("+1 day", $i)) {
                $day = date("w", $i);  // 0=sun, 1=mon, ..., 6=sat
                $mmgg = date('m-d', $i);
                if ($day != SUNDAY &&
                !in_array($mmgg, $publicHolidays) &&
                !in_array($i, $easterMondays) &&
                !($day == SATURDAY)) {
                    $workdays++;
                }
            }

            $days = intval($workdays);
        }
        else if ($leave_type->leave_category == 'Calendar'){
            $interval = $start_date->diff($end_date);
            $days = $leave_request->is_half_day ? (($interval->format('%a') + 1) / 2):($interval->format('%a') + 1);
        }
        else{
            $interval = $start_date->diff($end_date);
            $days = $leave_request->is_half_day ? (($interval->format('%a') + 1) / 2):($interval->format('%a') + 1);    
        }
 
        return $days;
    }

    public function hrms_leave_request_reject_leave($id){
        DB::beginTransaction();
        try{
            $leave_request = LeaveRequest::where('id', '=', $id)->where('status', '=', 1)->first();
            if ($leave_request){
                $leave_request->status = 2;
                $leave_request->save();

                $days = $this->hrms_leave_request_number_of_days($leave_request);
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days += $days;
                $employee_leave_type->save();
            
                $this->log_user_activity('leave_request_reject', $id, true);
                $complete = true;
            }
            else{
                $this->log_user_activity('leave_request_reject', $id, false);
                $complete = false;    
            }
        }
        catch(Exception $e){
            $this->log_user_activity('leave_request_reject', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_request;
        }
        else{
            DB::rollBack();
        }    
    }

    public function hrms_leave_request_show_leave($id, $viewer){
        /*switch ($viewer){
            case 'staff':
            break;
            case 'team':
                $query = 
            break;
        }*/
        return $leave_request = LeaveRequest::where('id', '=', $id)->with(['approver.user', 'employee.user', 'leave_type'])->first();
    }

    public function hrms_leave_request_update_leave($data, $id){
        DB::beginTransaction();
        try{
            $old_request = $leave_request = LeaveRequest::where('id', '=', $id)->first();
            if ($leave_request){
                $leave_request->leave_type_id = $data['leave_type_id'];
                $leave_request->from_date = $data['from_date']; 
                $leave_request->to_date = $data['to_date'];
                $leave_request->applied_on = $data['applied_on']; 
                $leave_request->reason = $data['reason']; 
                $leave_request->remarks = $data['remarks']; 
                $leave_request->status = $data['status'] ?? 1; 
                $leave_request->is_half_day = $data['is_half_day']; 
                $leave_request->is_notify = 0;
                $leave_request->leave_attachment = $leave_attachment ?? NULL; 
                $leave_request->updated_by = auth('api')->id();
                
                $leave_request->save();
                $employee = Employee::where('employee_id', '=', $leave_request->employee_id)->with('user')->first();
                $days = $this->hrms_leave_request_number_of_days($leave_request);
                $old_days = $this->hrms_leave_request_number_of_days($old_request);
                $employee_leave_type = EmployeeLeaveType::where('employee_id', '=', $leave_request->employee_id)->where('leave_type_id', '=', $leave_request->leave_type_id)->first();
                $employee_leave_type->pending_days += ($days - $old_days);
                $employee_leave_type->save();
            
                //send mail to line manager 
                $supervisor = Employee::where('id', '=', $employee->supervisor_id)->with(['user'])->first();
                $line_manager = $supervisor->user;
                //try to send notification to line manager not a breaking issue
                $mailed = null;
                if (!(is_null($line_manager->email))){$mailed = Mail::to($line_manager->email)->send(new RequestMail($leave_request, $employee, $line_manager));}
                $leave_request->is_notify = $mailed ? 1: 0; 
                $leave_request->save();

                $this->log_user_activity('leave_request_update', $id, true);
                $complete = true;
            }
            else{
                $this->log_user_activity('leave_request_update', $id, false);
                $complete = false;    
            }
        }
        catch(Exception $e){
            $this->log_user_activity('leave_request_reject', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_request;
        }
        else{
            DB::rollBack();
        }
    }

    public function hrms_leave_types_create_type($request){
        DB::beginTransaction();
        try{
            $leave_type = LeaveType::create([
                'name' => $request->input('name'),
                'leave_category' => $request->input('leave_category'),
                'no_of_days' => $request->input('no_of_days'),
                'status' => 1,
                'start_date' => $request->input('start_date') ?? date('Y-m-d'),
                'end_date' => $request->input('end_date'),
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('leave_type_create', $leave_type->id, true); 
            return $leave_type;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('leave_type_create', null, false);
            return $e->getMessage();
        }
    }
    
    public function hrms_leave_type_get_all($types, $specific, $detailed, $paginated, $page = 1){
        switch ($types){
            case 'all':
                $query = LeaveType::orderBy('start_date', 'DESC');
            break;
            case 'active':
                $query = LeaveType::whereDate('start_date', '<=', ($specific ?? date('Y-m-d')))->whereDate('end_date', '>=', ($specific ?? date('Y-m-d')))->orderBy('start_date', 'DESC');
            break;
            case 'allocate':
                $query = LeaveType::where('status', '=', 1)->whereDate('end_date', '>=', ($specific ?? date('Y-m-d')))->orderBy('start_date', 'DESC');
            break;
        }

        
        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name', 'no_of_days');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_leave_type_get_by_id($id, $detailed){
        $query = LeaveType::where('id', '=', $id)->with(['creator', 'deleter', 'updater'])->first();
        return $query;
    }

    public function hrms_leave_type_get_assigned_by_id($id, $detailed = true){
        $query = EmployeeLeaveType::where('leave_type_id', '=', $id)->with(['employee.user', 'leave_type'])->paginate(52);
        return $query;
    }

    public function hrms_leave_types_update_type($request, $id){
        DB::beginTransaction();
        try{
            $leave_type = LeaveType::where('id', '=', $id)->first();
            $leave_type->name = $request->input('name');
            $leave_type->no_of_days = $request->input('no_of_days');
            $leave_type->status = $request->input('status');
            $leave_type->start_date = $request->input('start_date') ?? date('Y-m-d');
            $leave_type->end_date = $request->input('end_date');
            $leave_type->updated_by = auth('api')->id();
            
            $leave_type->save();

            if ($leave_type){$this->log_user_activity('leave_type_create', $leave_type->id, true); $complete = true;}
            else{
                $this->log_user_activity('leave_type_create', null, false);
                $complete = false;
            }
        }
        catch(Exception $e){
            $this->log_user_activity('leave_type_update', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_type;
        }
        else{
            DB::rollBack();
        }  
    }

    public function hrms_leave_types_delete_by_id($id){
        DB::beginTransaction();

        try{
            $leave_type = LeaveType::where('id', '=', $id)->first();

            if($leave_type){ 
                $leave_type->deleted_by = auth('api')->id();
                $leave_type->deleted_at = date('Y-m-d H:i:s');
                $leave_type->save();

                $this->log_user_activity('leave_type_delete', $id, true);
            }
            else{
                $this->log_user_activity('leave_type_delete', $id, false);
                $complete = false;    
            }
        }
        catch(Exception $e){
            $this->log_user_activity('leave_type_delete', $id, false);
            $complete = false;
        }
        if ($complete){
            DB::commit();
            return $leave_type;
        }
        else{
            DB::rollBack();
        }  

    }

    public function hrms_leave_types_get_all($paginated, $detailed){
        $query = $detailed ? LeaveType::orderBy('name')->with('creator') : LeaveType::orderBy('name', 'ASC');
        $leave_types = $paginated ? $query->paginated(20): $query->get();
        return $leave_types;
    }

    public function hrms_leave_types_get_my_current_leave_types($employee_id, $paginated, $detailed){
        $active_leaves =  LeaveType::whereDate('start_date', '<=', date('Y-m-d'))->pluck('id');
        if(is_null($employee_id)){
            $employee = Employee::where('user_id', '=', Auth::id() ?? auth('api')->id())->first();
            $query = EmployeeLeaveType::where('employee_id', '=', $employee->id);
        }
        else{
            $query = EmployeeLeaveType::where('employee_id', '=', $employee_id);
        }
        
        $query = $detailed ? $query->select('id', 'days_used', 'balance', 'pending_days', 'leave_type_id')->with(['leave_type']) : $query;
        $employee_leave_types = $paginated ? $query->paginate(20) :$query->get();
        return $employee_leave_types;
    }
}