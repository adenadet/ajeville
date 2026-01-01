<?php 

namespace App\Http\Traits\Hrms;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;

use App\Models\Hrms\AttendanceSummary;
use App\Models\Hrms\Branch;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeBonus;
use App\Models\Hrms\EmployeeDeduction;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\Leave;
use App\Models\Hrms\LeaveType;
use App\Models\Hrms\OrganizationHierarchy;
use App\Models\Hrms\Salary;
use App\Models\Hrms\SalaryConfirmation;
use App\Models\Hrms\SalaryStructure;
//use App\Traits\MetaTrait;
use App\Models\Hrms\SalaryStructureComponent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;

use function PHPUnit\Framework\isEmpty;

trait SalaryTrait{

    use FileManagerTrait, LogTrait;
    public function hrms_salary_confirm($data, $id){
        DB::beginTransaction();

        try{
            $query = Salary::where('id', '=', $id)->first();

            $query->status = Salary::StatusConfirmed;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            $confirmation = SalaryConfirmation::create([
                'salary_id' => $id,
                'remarks' => $data['remarks'] ?? null,
                'confirmed_by' => Auth::id() ?? auth('api')->id(),
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('HRMS Salary Confirm', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Confirm', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_create($data){
        DB::beginTransaction();

        try{
            $query = Salary::updateOrCreate([
                    'employee_id' => $data['employee_id'],
                    'period' => $data['period'],
                ],
        [
                    'net_pay' => $data['net_pay'] ?? 0.00,
                    'status' => $data['status'] ?? Salary::StatusUnconfirmed,
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]
            );

            DB::commit();
            $this->log_user_activity('HRMS Salary Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Create', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_get_all($type, $specific, $detailed, $paginated){
        $query = Salary::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'confirmed':
                $query = $query->where('status', '=', Salary::StatusConfirmed);
            break;
            case 'rejected':
                $query = $query->where('status', '=', Salary::StatusRejected);
            break;
            case 'mine':
                $query = $query->where('employee_id', '=', Auth::id() ?? auth('api')->id())->where('status', '=', Salary::StatusConfirmed);
            break;
            case 'unconfirmed':
                $query = $query->where('status', '=', Salary::StatusUnconfirmed);
            break;
        }

        $query = $detailed ? $query->with(['components', 'creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(50) : $query->get();

        return $query;
    }

    public function hrms_salary_reject($data, $id){
        DB::beginTransaction();

        try{
            $query = Salary::where('id', '=', $id)->first();

            $query->status = Salary::StatusRejected;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Reject', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Reject', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_structure_create($data){
        DB::beginTransaction();

        try{
            $query = SalaryStructure::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? SalaryStructure::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('HRMS Salary Structure Create', $query->id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Structure Create', null, false);
            return $e->getMessage();
        }
    }
    public function hrms_salary_structure_deactivate($id){}
    public function hrms_salary_structure_get_all($type, $specific, $detailed, $paginated){
        $query = SalaryStructure::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', SalaryStructure::StatusActive);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', SalaryStructure::StatusInactive);
            break;
        }

        $query = $detailed ? $query->with(['components', 'creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(50) : $query->get();

        return $query;
    }
    public function hrms_salary_structure_get_by($type, $id, $detailed){
        $query = SalaryStructure::where('id', '=', $id);

        $query = $detailed ? $query->with(['components', 'creator', 'deleter', 'updater']) : $query->select('id', 'name');
        return $query->first();
    }

    public function hrms_salary_structure_update($data, $id){
        DB::beginTransaction();

        try{
            $query = SalaryStructure::where('id', '=', $id)->first();

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Structure Update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Structure Update', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_structure_component_create($data){
        $query = SalaryStructureComponent::create([
            'salary_structure_id' => $data['salary_structure_id'],
            'name' => $data['name'],
            'label' => $data['label'],
            'calculation_type' => $data['calculation_type'] ?? SalaryStructureComponent::CalcTypeFixed,
            'amount' => $data['amount'] ?? 0.00,
            'is_taxable' => $data['is_taxable'] ?? 1,
            'status' => $data['status'] ?? SalaryStructureComponent::StatusActive,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $query;
    }

    public function hrms_salary_structure_component_deactivate($id){
        $query = SalaryStructureComponent::find($id);
        
        $query->update([
            'status' => SalaryStructureComponent::StatusInactive,
            'deleted_by' => Auth::id() ?? auth('api')->id(),
            'deleted_at' => date('Y-m-d H:i:s'),
        ]);

        return $query;
    }

    public function hrms_salary_structure_component_update($data, $id){
        $query = SalaryStructureComponent::find($id);
        
        $query->update([
            'salary_structure_id' => $data['salary_structure_id'],
            'name' => $data['name'],
            'label' => $data['label'],
            'calculation_type' => $data['calculation_type'] ?? SalaryStructureComponent::CalcTypeFixed,
            'amount' => $data['amount'] ?? 0.00,
            'is_taxable' => $data['is_taxable'] ?? 1,
            'status' => $data['status'] ?? SalaryStructureComponent::StatusActive,
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $query;
    }

    public function hrms_salary_types_create($data){}

    public function hrms_salary_types_update($data, $id){}

    public function hrms_salary_types_delete_by_id($data){}

    public function hrms_salary_types_get_all(){}

    public function hrms_salary_types_get_my_current_salary_types($user_id){}

    public function hrms_salary_employee_bonus_create($data){
        DB::beginTransaction();

        try{
            $query = EmployeeBonus::create([
                'employee_id' => $data['employee_id'],
                'name' => $data['name'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'month' => $data['month'],
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),            
            ]);

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Bonus Create', $query->id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Bonus Create', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_bonus_deactivate($id){
        DB::beginTransaction();

        try{
            $query = EmployeeBonus::findOrFail($id);
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            if($query->deleted_by !== null){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_by = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_by = date('Y-m-d H:i:s');
            }

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Bonus Deactivate', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Bonus Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_bonus_get_all($type, $specific, $detailed, $paginated){
        $query = EmployeeBonus::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'employee':
                $query = $query->where('employee_id', '=', $specific['employee_id']);
            break;
            case 'month':
                $query = $query->where('month', '=', $specific['month']);
            break;
        }

        if(!isEmpty($specific['query'])){
            $question = $specific['query'];
            $query = $query->where('name', 'LIKE', "%$question%");
        }

        $query = $detailed ? $query->with(['employee.user']) : $query->select('id', 'name', 'employee_id');
        $query = $query->orderBy('month', 'DESC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_salary_employee_bonus_get_by($type, $id, $detailed){
        try{
            $query = EmployeeBonus::where('id', '=', $id);
            $query = $detailed ? $query->with(['employee.user']) : $query->select('id', 'name', 'employee_id');
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_bonus_update($data, $id){
        DB::beginTransaction();

        try{
            $query = EmployeeBonus::findOrFail($id);
            
            $query->employee_id = $data['employee_id'] ?? $query->employee_id;
            $query->name = $data['name'] ?? $query->name;
            $query->amount = $data['amount'] ?? $query->amount;
            $query->description = $data['description'] ?? $query->description;
            $query->month = $data['month'] ?? $query->month;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Bonus Update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Bonus Update', $id, false);
            return $e->getMessage();
        }
    }

    /**
     * Salary Deductions Here
     */
    public function hrms_salary_employee_deduction_create($data){
        DB::beginTransaction();

        try{
            $query = EmployeeDeduction::create([
                'employee_id' => $data['employee_id'],
                'name' => $data['name'],
                'amount' => $data['amount'],
                'description' => $data['description'] ?? null,
                'month' => $data['month'],
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),            
            ]);

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Deduction Create', $query->id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Deduction Create', null, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_deduction_deactivate($id){
        DB::beginTransaction();

        try{
            $query = EmployeeDeduction::findOrFail($id);
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            if($query->deleted_by !== null){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_by = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_by = date('Y-m-d H:i:s');
            }

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Deduction Deactivate', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Deduction Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_deduction_get_all($type, $specific, $detailed, $paginated){
        $query = EmployeeDeduction::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'employee':
                $query = $query->where('employee_id', '=', $specific['employee_id']);
            break;
            case 'month':
                $query = $query->where('month', '=', $specific['month']);
            break;
        }

        if(!isEmpty($specific['query'])){
            $question = $specific['query'];
            $query = $query->where('name', 'LIKE', "%$question%");
        }

        $query = $detailed ? $query->with(['employee.user']) : $query->select('id', 'name', 'employee_id');
        $query = $query->orderBy('month', 'DESC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function hrms_salary_employee_deduction_get_by($type, $id, $detailed){
        try{
            $query = EmployeeDeduction::where('id', '=', $id);
            $query = $detailed ? $query->with(['employee.user']) : $query->select('id', 'name', 'employee_id');
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function hrms_salary_employee_deduction_update($data, $id){
        DB::beginTransaction();

        try{
            $query = EmployeeDeduction::findOrFail($id);
            
            $query->employee_id = $data['employee_id'] ?? $query->employee_id;
            $query->name = $data['name'] ?? $query->name;
            $query->amount = $data['amount'] ?? $query->amount;
            $query->description = $data['description'] ?? $query->description;
            $query->month = $data['month'] ?? $query->month;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('HRMS Salary Employee Deduction Update', $id, true); 
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('HRMS Salary Employee Deduction Update', $id, false);
            return $e->getMessage();
        }
    }

}