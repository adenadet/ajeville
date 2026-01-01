<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Models\Hrms\Assessment;
use App\Models\Hrms\AssessmentAnswer;
use App\Models\Hrms\AssessmentHrItem;
use App\Models\Hrms\AssessmentPeriod;
use App\Models\Hrms\Designation;
use App\Models\Hrms\Employee;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Session;

trait AssessmentTrait{
    use LogTrait;
    public function hrms_assessment_create($data){
        DB::beginTransaction();
        try {
            $employee = Employee::findOrFail($data['employee_id']);
            $assessment = Assessment::create([
                'assessment_period_id' => $data['assessment_period_id'],
                'employee_id' => $data['employee_id'],
                'line_manager_id' => $employee->line_manager_id,
                'status' => $data['status'] ?? Assessment::StatusDraft,
                'total_score' => $data['total_score'] ?? 0,
                'max_score' => $data['max_score'] ?? 0,
                'summary' => $data['summary'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            
            DB::commit();
            return $assessment;
        } 
        catch (Exception $e) {
            DB::rollback();
            //$this->log_error('hrms_assessment_create', $e->getMessage());
            return $e->getMessage();
        }
    }

    public function hrms_assessment_deactivate($id){
        DB::beginTransaction();
        try {
            $assessment = Assessment::findOrFail($id);
            
            if($assessment->status == 1){
                $assessment->status = 1;
                $assessment->updated_by = auth('api')->id() ?? Auth::id();
                $assessment->deleted_by = auth('api')->id() ?? Auth::id();
                $assessment->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $assessment->status = 0;
                $assessment->updated_by = auth('api')->id() ?? Auth::id();
                $assessment->deleted_by = null;
                $assessment->deleted_at = null;
            }
            $assessment->save();
            
            DB::commit();
            return $assessment;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_assessment_get_all($type, $specific, $sort_by, $order, $detailed, $paginated){
        $query = Assessment::query();
        $active_periods = AssessmentPeriod::where('status', 1)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->whereDate('start_date', '<=', date('Y-m-d'))
            ->pluck('id')->toArray();
        switch($type) {
            case 'completed':
                $query = $query->where('status', Assessment::StatusCompleted);
            break;
            case 'in_progress':
                $query = $query->where('status', Assessment::StatusInProgress)->whereIn('assessment_period_id', $active_periods);
            break;
            case 'employee':
                $query->where('employee_id', $specific);
            break;
            case 'line_manager':
                $query->where('line_manager_id', $specific);
            break;
            case 'pending':
                $query = $query->where('status', Assessment::StatusDraft)->whereIn('assessment_period_id', $active_periods);
            break;
            case 'period':
                $query->where('assessment_period_id', $specific);
            break;
        }
        $query = $detailed ? $query->with(['answers', 'comments', 'employee.user', 'line_manager.user', 'period']) : $query->with(['period', 'employee']);
        $query->orderBy($sort_by ?? 'id', $order ?? 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get(); 

        return $query;
    }

    public function hrms_assessment_get_by($type, $id, $detailed){
        try{
            $query = Assessment::findOrFail($id);

            $query = $detailed ? $query->with(['answers', 'comments', 'employee.user', 'line_manager.user', 'period']) : $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_assessment_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Assessment::findOrFail($id);

            $query->assessment_period_id = $data['assessment_period_id'] ?? $query->assessment_period_id;
            $query->employee_id = $data['employee_id'] ?? $query->employee_id;
            $query->line_manager_id = $data['employee_id'] ?? $query->line_manager_id;
            $query->status = $data['status'] ?? $query->status;
            $query->total_score = $data['total_score'] ?? $query->total_score;
            $query->max_score = $data['max_score'] ?? $query->max_score;
            $query->summary = $data['summary'] ?? $query->summary;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    /**
     * 
     * Assessment HR items Functions
     * 
     * **/

    public function hrms_assessment_answer_create($data){
        DB::beginTransaction();

        try{
            $query = AssessmentAnswer::create([
                'assessment_id' => $data['assessment_id'], 
                'item_type' => $data['item_type'], 
                'item_id' => $data['item_id'], 
                'employee_score' => $data['employee_score'] ?? null,
                'line_manager_score' => $data['line_manager_score'] ?? null,
                'line_manager_comment' => $data['line_manager_comment'] ?? null,
                'status' =>  $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(), 
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    
    public function hrms_assessment_answer_deactivate($id){
        DB::beginTransaction();

        try{
            $query = AssessmentAnswer::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = now();
            }
            else{
                $query->status = 0;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();

            return $e->getMessage();
        }
    }
    public function hrms_assessment_answer_get_all($type, $specific, $detailed, $paginated){
        $query = AssessmentAnswer::query();
        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', 1)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->whereDate('start_date', '<=', date('Y-m-d'));
            break;
            case 'inactive':
                $query = $query->where('status', 0)->orwhereDate('end_date', '<', date('Y-m-d'))->orwhereDate('start_date', '>', date('Y-m-d'));
            break;
        }

        $query = $detailed ? $query->with(['assessments']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_assessment_answer_get_by($type, $id, $detailed){
        try{
            $query = AssessmentAnswer::findOrFail($id);
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_assessment_answer_update($data, $id){
        DB::beginTransaction();

        try{
            $query = AssessmentAnswer::findOrFail($id);
           
            $query->assessment_id = $data['assessment_id'] ?? $query->assessment_id; 
            $query->item_type = $data['item_type'] ?? $query->item_type; 
            $query->item_id = $data['item_id'] ?? $query->item_id; 
            $query->employee_score = $data['employee_score'] ?? $query->employee_score;
            $query->line_manager_score = $data['line_manager_score']  ?? $query->line_manager_score;
            $query->line_manager_comment = $data['line_manager_comment']  ?? $query->line_manager_comment;
            $query->status =  $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 
     * Assessment HR items Functions
     * 
     * **/

    public function hrms_assessment_hr_item_create($data){
        DB::beginTransaction();

        try{
            $query = AssessmentHrItem::create([
                'title' => $data['title'], 
                'description' => $data['description'], 
                'max_score' => $data['max_score'],
                'status' => $data['status'] ?? 1, 
                'created_by' => auth('api')->id() ?? Auth::id(), 
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();

            return $e->getMessage();
        }
    }
    
    public function hrms_assessment_hr_item_deactivate($id){
        DB::beginTransaction();

        try{
            $query = AssessmentHrItem::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = now();
            }
            else{
                $query->status = 0;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();

            return $e->getMessage();
        }
    }
    public function hrms_assessment_hr_item_get_all($type, $specific, $detailed, $paginated){
        $query = AssessmentHrItem::query();
        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', 1);
            break;
            case 'inactive':
                $query = $query->where('status', 0);
            break;
        }

        if ($specific !== null){
            $query->where('title', 'LIKE', "%$specific%");
        }

        $query = $detailed ? $query : $query->select('id', 'title');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_assessment_hr_item_get_by($type, $id, $detailed){
        try{
            $query = AssessmentHrItem::findOrFail($id);

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_assessment_hr_item_update($data, $id){
        DB::beginTransaction();

        try{
            $query = AssessmentHrItem::findOrFail($id);

            $query->status = $data['status'] ?? $query->status;
            $query->title = $data['title'] ?? $query->title; 
            $query->description = $data['description'] ?? $query->description;
            $query->max_score = $data['max_score'] ?? $query->max_score;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    /**
     * 
     * Assessment Period Functions
     * 
     * **/
    public function hrms_assessment_period_create($data){
        DB::beginTransaction();

        try{
            $query = AssessmentPeriod::create([
                'name' => $data['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'] ?? 1,
                'notes' => $data['notes'],
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    
    public function hrms_assessment_period_deactivate($id){
        DB::beginTransaction();

        try{
            $query = AssessmentPeriod::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = now();
            }
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            
            DB::commit();
            return $query;

        }
        catch(Exception $e){
            DB::rollback();

            return $e->getMessage();
        }
    }
    public function hrms_assessment_period_get_all($type, $specific, $detailed, $paginated){
        $query = AssessmentPeriod::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', 1)
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->whereDate('start_date', '<=', date('Y-m-d'));
            break;
            case 'inactive':
                $query = $query->where('status', 0)->orwhereDate('end_date', '<', date('Y-m-d'))->orwhereDate('start_date', '>', date('Y-m-d'));
            break;
        }

        if($specific){
            if (isset($specific['query']) && $specific['query'] != ''){}
            if (isset($specific['start_date']) && $specific['start_date'] != ''){}
            if (isset($specific['end_date']) && $specific['end_date'] != ''){}
        }

        $query = $detailed ? $query->with(['assessments']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_assessment_period_get_by($type, $id, $detailed){
        try{
            $query = AssessmentPeriod::where('id', '=', $id);

            $query = $detailed ? $query->with(['assessments']) : $query->select('id', 'name');

            return $query->first();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_assessment_period_update($data, $id){
        DB::beginTransaction();

        try{
            $query = AssessmentPeriod::findOrFail($id);

            $query->name = $data['name'] ?? $query->name;
            $query->start_date = $data['start_date'] ?? $query->start_date;
            $query->end_date = $data['end_date'] ?? $query->end_date;
            $query->status = $data['status'] ?? $query->status;
            $query->notes = $data['notes'] ?? $query->notes;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}