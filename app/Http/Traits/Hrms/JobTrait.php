<?php 

namespace App\Http\Traits\Hrms;
use App\Mail\AdminApplyLeaveMail;
use App\Mail\ApplyLeaveMail;
use App\Mail\LeaveStatusMail;

use App\Http\Traits\LogTrait;
use App\Http\Traits\UMS\UserTrait;

use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\Job;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\OrganizationHierarchy;

//use App\Traits\MetaTrait;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Session;

trait JobTrait{
    public function hrms_job_offer_approve($data, $id){

    }

    public function hrms_job_offer_create($data){
        DB::beginTransaction();

        try{

        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('HRMS Job Offers Bank Delete', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_job_offer_get_all($type, $specific, $detailed, $paginated){

    }

    public function hrms_job_offer_get_by($type, $id, $detailed){

    }
    
    public function hrms_job_offer_update($data, $id){

    }

    public function hrms_job_posting_approval($data,$id){

    }

    public function hrms_job_posting_create($data){

    }

    public function hrms_job_posting_deactivate($id){

    }

    public function hrms_job_posting_get_all($data){

    }

    public function hrms_job_posting_get_by($id, $detailed){

    }

    public function hrms_job_posting_update($data, $id){

    }

    public function hrms_job_requisition_approval($data,$id){

    }

    public function hrms_job_requisition_create($data){

    }

    public function hrms_job_requisition_deactivate($id){

    }

    public function hrms_job_requisition_get_all($data){

    }

    public function hrms_job_requisition_get_by($id, $detailed){

    }

    public function hrms_job_requisition_update($data, $id){

    }

}