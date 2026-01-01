<?php

namespace App\Http\Traits\Operations;
use App\Http\Traits\General\LogTrait;
use App\Models\Hrms\Employee;
use App\Models\Operations\Department;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DepartmentTrait{
    use LogTrait;
    public function operation_department_create($request){
        DB::beginTransaction();
        try{
            $department = Department::create([
                'name' => $request->input('name'),
                'description' => $request->input('description') ?? '',
                'hod_id' => $request->input('hod_id'),
                'ext' => $request->input('ext'),
                'email' => $request->input('email'),
            ]);

            $this->log_user_activity('Operation Department Create', true, $department->id);
            DB::commit();
            return $department;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Department Create', false, null);
        }  
    }

    public function operation_department_delete($id){
        DB::beginTransaction();
        try{
            $users = Employee::where('department_id', '=', $id)->get();
            
            if ((count($users) != 0) && (!is_null($users))){
                foreach ($users as $user){
                    $user->department_id = null;
                    $user->save();
                }
            }

            $department = Department::where('id', '=', $id)->first();

            $department->deleted_by = auth('api')->id();
            $department->deleted_at = date('Y-m-d H:i:s');

            $department->save();

            $this->log_user_activity('Operation Department Delete', true, $id);
            DB::commit();
            return $department;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Department Delete', false, $id);
        }
    }

    public function operation_department_get_all_departments($detailed, $paginated, $page){
        $query = Department::orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['employees.user', 'hod.user']) : $query;
        $departments = $paginated ? $query->paginate(10) : $query->get();

        return $departments;
    }

    public function operation_department_get_all_departments_in_branch($branch_id){
        
    }

    public function operation_department_get_department_by_id($id, $detailed){
        $query = Department::where('id', '=', $id);
        $department = $detailed ? $query->with(['employees.user', 'hod.user'])->first() : $query->first();

        return $department;
    }
    
    public function operation_department_update($request, $id){
        DB::beginTransaction();
        try{
            $department = Department::find($id);

            $department->name        = $request->input('name');
            $department->description = $request->input('description') ?? '';
            $department->hod_id      = $request->input('hod_id');
            $department->ext         = $request->input('ext');
            $department->email       = $request->input('email');
    
            $department->save();
        
            $this->log_user_activity('Operation Department Update', true, $id);
            DB::commit();
            return $department;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Department Update', false, $id);
        }
    }   
}