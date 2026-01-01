<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\LogTrait;
use App\Http\Traits\UMS\UserTrait;

use DB;
use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\Designation;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeExperience;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\EmployeeSalary;
use App\Models\Hrms\EmployeeSocial;
use App\Models\Hrms\EmployeeTraining;
use App\Models\Hrms\Job;
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
use Session;

trait ApplicantTrait{
    public function hrms_applicant_create($request){

    }
    
    public function hrms_applicant_confirm($request, $user_id){
        //$user
    }

    public function hrms_applicant_parse($id){
        //$user
    }

    public function hrms_applicant_reject($request){}

    public function hrms_applicant_experience_create($request){
        $experience = EmployeeExperience::create([
            'user_id' => $request->input('user_id') ?? auth('api')->id(), 
            'institution_name' => $request->input('institution_name'), 
            'institution_location' => $request->input('institution_location'), 
            'institution_job_description' => $request->input('institution_job_description'), 
            'start_date' => $request->input('start_date'), 
            'end_date' => $request->input('end_date') ?? NULL,
        ]);
        return $experience;
    }

    public function hrms_applicant_experience_delete($id){
        $experience = EmployeeExperience::where('id', '=', $id)->first();

        $experience->deleted_at = date('Y-m-d H:i:s');
        $experience->save();
    }

    public function hrms_applicant_experience_get_all_user_experiences($user_id, $paginated, $page){
        $query = EmployeeExperience::where('user_id', '=', $user_id);

        $user_experiences = $paginated ? $query->paginated(50) : $query->get();

        return $user_experiences;
    }

    public function hrms_applicant_experience_get_by_id($id){
        $user_experience = EmployeeExperience::where('id', '=', $id)->with([])->first();

        return $user_experience;
    }

    public function hrms_applicant_experience_update($request, $id){
        $experience = EmployeeExperience::where('id', '=', $id)->first();

        $experience->user_id = $request->input('user_id') ?? auth('api')->id();
        $experience->institution_name = $request->input('institution_name');
        $experience->institution_location = $request->input('institution_location');
        $experience->institution_job_description = $request->input('institution_job_description');
        $experience->start_date = $request->input('start_date');
        $experience->end_date = $request->input('end_date') ?? NULL;

        $experience->save();

        return $experience;
    }
    
}