<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\OrganizationHierarchy;
//use App\Traits\MetaTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Mail;
use App\Mail\AdminApplyLeaveMail;
use App\Mail\ApplyLeaveMail;
use App\Mail\LeaveStatusMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

trait EmployeeTrait{

    use LogTrait, UserTrait;
    public function hrms_applicant_create($request){}

    public function hrms_applicant_upgrade_to_staff($user_id){}

    public function hrms_employee_confirm_employee_request($request, $id){}

    public function hrms_employee_create_employee($data){
        $user = $data['from_user'] == 'new' ? $this-> ums_user_create($data['user']) : User::find($data['user_id']);
        $user->assignRole('Staff');
        
        $number = Employee::max('employee_id');
        $number++;
        
        $query = Employee::create([
            'user_id' => $user->id,
            'employee_id' => $number,
            'office_shift_id' => NULL,
            'reports_to' => $data['reports_to'],
            'supervisor_id' => $data['supervisor_id'],
            'username' => $data['username'] ?? $user->unique_id ?? 'USR-'.$number,
            'email' => $data['email'],
            'department_id' => $data['department_id'],
            'sub_department_id' => NULL,
            'designation_id' => $data['designation_id'],
            'date_of_joining' => $data['date_of_joining'] ?? date('Y-m-d'),
            'date_of_leaving' => $data['date_of_leaving'] ?? NULL,
            'employment_status' => $data['employment_status'] ?? 1,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        return $query;
    }

    public function hrms_employee_get_all($type, $specific, $detailed, $paginated, $page){
        $users = User::pluck('id');
        $query = Employee::query();
        switch($type){
            case 'active':
                $query = $query->whereIn('user_id', $users)->where('employment_status', '=', 1)->has('user')->orderBy('username', 'ASC');
            break;
            case 'all':
                $query = $query->whereIn('user_id', $users)->orderBy('username', 'ASC');
            break;
            case 'my_employee':
            break;
            case 'new':
                $query = $query->whereDate('date_of_joining', '>=', date('Y-m-d', strtotime("-3 Months")))->whereNull('date_of_leaving');
            break;
            case 'pending_employee':
            break;
            default:
                $query = Employee::orderBy('username', 'ASC');
            break;
        }

        $query = $query->orderBy('username', 'ASC');

        if ($specific == 'leave'){
            $query = $query->select('id', 'user_id', 'supervisor_id', 'reports_to', 'department_id', 'username')->with(['leave_types.leave_type', 'department', 'user.department']);
        }
        else{
            $query = $detailed ? $query->with(['leave_types.leave_type', 'department', 'designation', 'supervisor.user', 'line_manager.user', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state']) : $query->select('id', 'employee_id', 'username', 'user_id')->with('user');
        }
        $query = $paginated ? $query->paginate(52) : $query->get();
        
        return $query;
    }

    public function hrms_employee_get_all_active_users($type, $specific, $paginated, $page = 1){
        $employee = Employee::where('employment_status', '=', 1)->pluck('user_id');
        switch($type){
            case 'all':
                $query = User::whereIn('id', $employee)->orderBy('last_name', 'ASC');
                break;
            case 'department':
                $employee =  (!is_null($specific)) ? $employee->where('department_id', '=', $specific) : $employee;
                $query =  User::whereIn('id', $employee)->orderBy('last_name', 'ASC');
            break;    
        }
        return $query = $paginated ? $query->paginate(50) : $query->get();
    }

    public function hrms_employee_get_single($getter, $specific, $detailed){
        switch ($getter){
            case 'id':
                $query = Employee::where('id', '=', $specific);
            break;
            case 'user_id':
                $query = Employee::where('user_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['department', 'designation', 'leave_types.leave_type', 'line_manager.user', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state']) : $query->select('id', 'user_id')->with('user');
        
        return $query->first();
    }

    public function hrms_employee_get_multiple($getter, $specific, $detailed){
        switch ($getter){
            case 'id':
                $query = Employee::where('id', '=', $specific);
            break;
            case 'user_id':
                $query = Employee::where('user_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['department', 'designation', 'leave_types.leave_type', 'line_manager.user', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state']) : $query->select('id', 'user_id')->with('user');
        
        return $query->first();
    }

    public function hrms_employee_get_by_id($id, $viewer){
        switch($viewer){
            case 'admin':
                $query = Employee::where('id', '=', $id)->with(['department', 'designation', 'leave_types.leave_type', 'line_manager.user', 'supervisor.user', 'user.area', 'user.state'])->first();
            break;
            default:
                $query = Employee::where('id', '=', $id)->with(['department', 'designation', 'leave_types.leave_type', 'line_manager.user', 'supervisor.user', 'user.area', 'user.state'])->first();
            break;
        }

        return $query;
    }

    public function hrms_employee_get_by_status($status, $detailed, $paginated, $page){
        $users = User::pluck('id');
        $query = Employee::whereIn('user_id', $users)->where('employment_status', '=', $status)->orderBy('username', 'ASC');
        

        $query = $detailed ? $query->with(['department', 'designation', 'supervisor.user', 'leave_types', 'line_manager.user', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state']) : $query->select('id', 'user_id')->with('user');
        
        $query = $paginated ? $query->paginate(52) : $query->get();
        
        return $query;
    }

    public function hrms_employee_search_by_query($search, $detailed, $paginated, $page){
        $users = User::where('first_name', 'LIKE', "%$search%")
            ->orWhere('middle_name', 'LIKE', "%$search%")
            ->orWhere('last_name', 'LIKE', "%$search%")
            ->orWhere('email', 'LIKE', "%$search%")
            ->pluck('id');
        
        $query = Employee::whereIn('user_id', $users)->orWhere('username', 'LIKE', "%$search%")->orderBy('username', 'ASC');

        $query = $detailed ? $query->with(['department', 'designation', 'supervisor.user', 'leave_types', 'line_manager.user', 'user.area', 'user.branch', 'user.department', 'user.roles', 'user.state']) : $query->select('id', 'user_id')->with('user');
        
        $query = $paginated ? $query->paginate(52) : $query->get();
        
        return $query;
        
    }
    
    public function hrms_employee_get_my_team_members_employee_requests($team_members){}

    public function hrms_employee_reject_employee_request($request, $id){}

    public function hrms_employee_types_create($request){}

    public function hrms_employee_types_update($request, $id){}

    public function hrms_employee_types_delete_by_id($request){}

    public function hrms_employee_types_get_all(){}

    public function hrms_employee_types_get_my_current_employee_types($user_id){}

    public function hrms_resignation_create($request){}
    
    public function hrms_resignation_confirm($request){}

    public function hrms_resignation_reject($request){}

    public function hrms_employee_update($data, $id){
        DB::beginTransaction();

        try{
            $employee = Employee::find($id);

            $employee->date_of_joining  = $data['date_of_joining'] ?? $$employee->date_of_joining;
            $employee->date_of_leaving  = $data['date_of_leaving'] ?? $employee->date_of_leaving;
            $employee->department_id    = $data['department_id'] ?? $employee->department_id;
            $employee->designation_id   = $data['designation_id'] ?? $employee->designation;
            $employee->email            = $data['email'] ?? $employee->email;
            $employee->office_shift_id  = $data['office_shift_id'] ?? $employee->office_shift_id;
            $employee->reports_to       = $data['reports_to'] ?? $employee->reports_to;
            $employee->sub_department_id= $data['sub_department_id'] ?? $employee->sub_department_id;
            $employee->supervisor_id    = $data['supervisor_id'] ?? $employee->supervisor_id;
            $employee->user_id          = $data['user_id'] ?? $employee->user_id;
            $employee->username         = $data['username'] ?? $employee->username;
            
            $employee->save();

            DB::commit();
            return $employee;    
        }

        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
         
    }
}