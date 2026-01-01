<?php

namespace App\Http\Traits\Operations;

use App\Http\Traits\General\LogTrait;

use App\Models\Operations\Branch;
use App\Models\Operations\BranchModule;
use App\Models\HRMS\Employee;
use App\Models\Finance\PriceList;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

trait BranchTrait{
    use LogTrait;

    public function operation_branch_create($request){
        DB::beginTransaction();
        try{
            if ($request->input('price_list_id') == 0){
                $price_list = PriceList::create([
                    'name' => $request->input('price_list_name'),
                    'type_id' => 0,
                    'created_by' => auth('api')->id(),
                    'updated_by' => auth('api')->id(),
                ]);
            }

            $branch = Branch::create([
                'name'      =>  $request->input('name'),
                'address'   =>  $request->input('description') ?? '',
                'phone'     =>  $request->input('phone') ?? null,
                'pm_id'     =>  $request->input('pm_id'),
                'cinc_id'   =>  $request->input('cinc_id'),
                'hon_id'    =>  $request->input('hon_id'),
                'status'    =>  $request->input('status'),
                'price_list_id' =>  $request->input('price_list_id') != 0 ? $request->input('price_list_id') : $price_list->id,
            ]);

            /*foreach ($request->input('modules') as $module){
                BranchModule::create([
                    'branch_id' => $branch->id,
                    'module_id' => $module,
                ]);
            }*/
    
            if (!empty($request->input('modules'))){
                foreach ($request->input('modules') as $module){
                    BranchModule::create([
                        'branch_id' => $branch->id,
                        'module_id' => $module,
                    ]);
                }
            }

            if (!empty($request->input('pm_id'))){
                $pm = Employee::find($request->input('pm_id'));
                $practice_manager = User::find($pm->user_id);
                $practice_manager_role = Role::where('name', '=', 'Practice Manager')->first();
                $practice_manager->assignRole($practice_manager_role);
            }

            if (!empty($request->input('cinc_id'))){
            
                $cc = Employee::find($request->input('cinc_id'));
                $chief_consultant = User::find($cc->user_id);
                $chief_consultant_role = Role::where('name', '=', 'Chief Consultant')->first();
                $chief_consultant->assignRole($chief_consultant_role);   
            }

            if (!empty($request->input('pm_id'))){    
                $hn = Employee::find($request->input('hon_id'));
                $head_nurse = User::find($hn->user_id);
                $head_nurse_role = Role::where('name', '=', 'Head Nurse')->first();
                $head_nurse->assignRole($head_nurse_role);
            }
        
            DB::commit();
            $this->log_user_activity('Operation Branch Create', true, $branch->id);
            return $branch;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Branch Create', false, null);
            return $e->getMessage();
        } 
    }

    public function operation_branch_delete($id){
        DB::beginTransaction();
        try{
            $branch = Branch::where('id', '=', $id)->with(['modules'])->firstOrFail();
            $branch->modules()->detach();
            $branch->delete();
            
            DB::commit();
            $this->log_user_activity('Operation Branch Delete', true, $branch->id);
            return $branch;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Branch Delete', false, null);
        } 
    }

    public function operation_branch_get_all($detailed, $paginated, $page){
        $query = Branch::where('status', '=', 1)->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['chief_consultant.user', 'head_nurse.user', 'practice_manager.user', 'modules', 'price_list']) : $query->select('id', 'name');
        $branches = $paginated ? $query->paginate(10) : $query->get(); 
        
        return $branches;
    }

    public function operation_branch_get_branch_by_id($id, $detailed){
        $query = Branch::where('id', '=', $id);
        
        $branch = $detailed ? $query->with(['chief_consultant.user', 'head_nurse.user', 'modules', 'practice_manager.user', 'price_list'])->first(): $query->first();

        return $branch;
    }

    public function operation_branch_get_branch_price_lists($branch_id){
        $query = BranchPlanPriceList::where('branch_id', '=', $branch_id)
        ->select('price_list_id')->distinct()->with('price_list');
        
        return $query->first();
    }

    public function operation_branch_update_branch($request, $id){
        if ($request->input('price_list_id') == 0){
            $price_list = PriceList::create([
                'name' => $request->input('price_list_name'),
                'type_id' => 0,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
        }
     
        $branch = Branch::find($id);

        $branch->name       = $request->input('name');
        $branch->address    = $request->input('address') ?? '';
        $branch->pm_id      = $request->input('pm_id');
        $branch->cinc_id    = $request->input('cinc_id');
        $branch->hon_id     = $request->input('hon_id');
        $branch->status     = $request->input('status');
        $branch->price_list_id = $request->input('price_list_id') != 0 ? $request->input('price_list_id') : $price_list->id;
        
        $branch->save();

        $existing_modules = BranchModule::where('branch_id', '=', $branch->id)->pluck('module_id')->toArray();

        //Update Branch Modules
        foreach ($request->input('modules') as $module){
            //Check if the module is previously included in list of Modules
            if (!in_array($module, $existing_modules)){
                BranchModule::create([
                    'branch_id' => $branch->id,
                    'module_id' => $module,
                ]);
            }
        }
        // Delete the removed modules
        foreach ($existing_modules as $del_module){
            if (!in_array($del_module, $request->input('modules'))){
                BranchModule::where('branch_id', '=', $branch->id)->where('module_id', '=', $del_module)->delete();
            }
        }
        
        $pm = Employee::find($request->input('pm_id'));
        $practice_manager = User::find($pm->user_id);
        $practice_manager_role = Role::where('name', '=', 'Practice Manager')->first();
        $practice_manager->assignRole($practice_manager_role);

        $cc = Employee::find($request->input('cinc_id'));
        $chief_consultant = User::find($cc->user_id);
        $chief_consultant_role = Role::where('name', '=', 'Chief Consultant')->first();
        $chief_consultant->assignRole($chief_consultant_role);

        $hn = Employee::find($request->input('hon_id'));
        $head_nurse = User::find($hn->user_id);
        $head_nurse_role = Role::where('name', '=', 'Head Nurse')->first();
        $head_nurse->assignRole($head_nurse_role);

        return $branch;
    }

    /*
    --------------------------------------------------------------
    Branch Account Functions
    --------------------------------------------------------------
    */
    
    public function operation_branch_account_create($data){}

    public function operation_branch_account_deactivate($data){}

    public function operation_branch_account_get_all($data){}

    public function operation_branch_account_get_by($type, $id, $detailed){}

    public function operation_branch_account_update($data, $id){}

/*
    --------------------------------------------------------------
    Branch Account Functions
    --------------------------------------------------------------
    */
    
    public function operation_branch_price_list_create($data){
        DB::beginTransaction();

        try{
            $query = BranchPlanPriceList::create([
                'branch_id' => $data['branch_id'], 
                'plan_id' => $data['plan_id'] ?? null, 
                'price_list_id' => $data['price_list_id'], 
                'status' => $data['status'] ?? 1, 
                'created_by' => Auth::id() ?? auth('api')->id(), 
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Operation Branch Plan Price List Create', $query->id, true);
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Branch Plan Price List Create', null, false);
            return $e->getMessage();
        }
    }

    public function operation_branch_price_list_deactivate($id){
        DB::beginTransaction();

        try{
            $query = BranchPlanPriceList::where('id', '=', $id)->first();

            $query->status = $query->status == 1 ? 0 : 1; 
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Operation Branch Plan Price List Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Branch Plan Price List Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function operation_branch_price_list_get_all($type, $specific, $detailed, $paginated, $page){
        $query = BranchPlanPriceList::query();

        switch($type){
            case 'active':
                $query->where('status', '=', 1);
            break;
            case 'branch':
                //echo $specific;
                $query->where('branch_id', '=', $specific ?? request()->cookie('current_branch'))->where('status', '=', 1);
            break; 
            case 'inactive':
                $query->where('status', '=', 0);
            break;
            case 'plan':
                $query->where('plan_id', '=', $specific)->where('status', '=', 1);
            break;
        }

        $query = $detailed ? $query->with(['branch', 'plan', 'price_list']) : $query->with('price_list');
        $query = $paginated ? $query->paginate(20, ['*'], $page) : $query->get();

        return $query;
    }

    public function operation_branch_price_list_get_by($type, $id, $detailed){
        $query = BranchPlanPriceList::where('id', '=', $id)->first();

        $query = $detailed ? $query->with(['branch', 'plan', 'price_list']) : $query->with('price_list'); 

        return $query;
    }

    public function operation_branch_price_list_update($data, $id){
        DB::beginTransaction();

        try{
            $query = BranchPlanPriceList::where('id', '=', $id)->first();

            $query->branch_id = $data['branch_id']; 
            $query->plan_id = $data['plan_id'] ?? null; 
            $query->price_list_id = $data['price_list_id']; 
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Operation Branch Plan Price List Update', $id, true);
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Operation Branch Plan Price List Update', $id, false);
            return $e->getMessage();
        }
    }


}