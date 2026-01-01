<?php 

namespace App\Http\Traits\Hrms;

use App\Http\Traits\General\LogTrait;
use App\Models\Hrms\Assessment;
use App\Models\Hrms\AssessmentPeriod;
use App\Models\Hrms\Designation;
use App\Models\Hrms\DesignationKpi;
use App\Models\Hrms\Employee;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Mail;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

trait DesignationTrait{
    use LogTrait;

    public function hrms_designation_create($data){
        DB::beginTransaction();
        try {
            $designation = Designation::create([
                'sub_department_id' => $data['sub_department_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            
            DB::commit();
            return $designation;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_designation_deactivate($id){
        DB::beginTransaction();
        try {
            $designation = Designation::findOrFail($id);
            
            if ($designation->status == 1){
                $designation->status = 0;
                $designation->deleted_by = auth('api')->id();
                $designation->deleted_at = date('Y-m-d H:i:s');
            } 
            else {
                $designation->status = 1;
                $designation->deleted_by = null;
                $designation->deleted_at = null;
            }
            $designation->save();
            
            DB::commit();
            return $designation;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_designation_get_all($type, $specific, $detailed, $paginated){
        $query = Designation::query();
        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'department':
                $query = $query->where('department_id', '=', $specific);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break;
            case 'search':
                $query = $query->where('name', 'LIKE', "%$specific%");
            break;
        }

        $query = $detailed ? $query->with(['department', 'employees.user', 'employees.department', 'unit']) : $query->select('id', 'name', 'department_id', 'status')->with(['department']);
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(40) : $query->get();
        
        return $query;
    }

    public function hrms_designation_get_by($type, $id, $detailed){
        $query = Designation::where('id', '=', $id);

        $query = $detailed ? $query->with(['department', 'employees.user', 'employees.department', 'kpis', 'unit']) : $query->select('id', 'name', 'department_id', 'status')->with(['department']);

        return $query->first();
    }

    public function hrms_designation_update($data, $id){
        DB::beginTransaction();
        try {
            $designation = Designation::findOrFail($id);
            
            $designation->sub_department_id = $data['sub_department_id'] ?? $designation->sub_department_id;
            $designation->name = $data['name'] ?? $designation->name;
            $designation->description = $data['description']  ?? $designation->description;
            $designation->department_id = $data['department_id']  ?? $designation->department_id;
            $designation->status = $data['status'] ?? $designation->status;
            $designation->updated_by = auth('api')->id();
            
            $designation->save();
            
            DB::commit();
            return $designation;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_designation_kpi_create($data){
        DB::beginTransaction();
        try {
            $kpi = DesignationKpi::create([
                'designation_id' => $data['designation_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'max_score' => $data['max_score'] ?? 0,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            
            DB::commit();
            return $kpi;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_designation_kpi_get_all($designation_id, $type, $specific, $detailed, $paginated){
        $query = DesignationKpi::query();
        if ($designation_id){
            $query = $query->where('designation_id', '=', $designation_id);
        }
        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break;
            case 'search':
                $query = $query->where('title', 'LIKE', "%$specific%")->withTrashed();
            break;
        }
        $query = $detailed ? $query->with(['designation']) : $query->select('id', 'designation_id', 'title', 'max_score', 'status')->with(['designation']);
        $query = $query->orderBy('title', 'ASC');
        $query = $paginated ? $query->paginate(40) : $query->get();

        return $query;
    }

    public function hrms_designation_kpi_deactivate($id){
        DB::beginTransaction();
        try {
            $kpi = DesignationKpi::findOrFail($id);

            if ($kpi->status == 1){
                $kpi->status = 0;
                $kpi->deleted_by = auth('api')->id() ?? Auth::id();
                $kpi->deleted_at = date('Y-m-d H:i:s');
            } 
            else {
                $kpi->status = 1;
                $kpi->deleted_by = null;
                $kpi->deleted_at = null;
            }
            
            $kpi->save();
            
            DB::commit();
            return $kpi;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function hrms_designation_kpi_update($data, $id){
        DB::beginTransaction();
        try {
            $kpi = DesignationKpi::findOrFail($id);

            $kpi->designation_id = $data['designation_id'] ?? $kpi->designation_id;
            $kpi->title = $data['title'] ?? $kpi->title;
            $kpi->description = $data['description'] ?? $kpi->description;
            $kpi->max_score = $data['max_score']  ?? $kpi->max_score;
            $kpi->status = $data['status'] ?? $kpi->status;
            $kpi->updated_by = auth('api')->id() ?? Auth::id();
            
            $kpi->save();
            
            DB::commit();
            return $kpi;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}