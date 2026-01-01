<?php

namespace App\Http\Traits\Operations;

use App\Http\Traits\General\LogTrait;
use App\Models\Operations\Module;
use App\Models\Ums\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ModuleTrait{
    use LogTrait;

    public function operation_module_assign($type, $data, $id){
        switch($type){
            case 'role':
                $role = Role::with(['modules'])->findOrFail($id);
                $module = Module::where('unique_id', '=', $data['module_id'])->orWhere('id', '=', $data['module_id'])->firstOrFail();
                if($role->modules->contains($module)){}
                $role->modules()->attach($module->id);
                return $role;
            case 'user':
                $user = User::with(['modules', 'roles.modules'])->findOrFail($id);
                $module = Module::where('unique_id', '=', $data['module_id'])->orWhere('id', '=', $data['module_id'])->firstOrFail();
                if($user->modules->contains($module)){}
                $user->modules()->attach($module->id);
                return $user;
        }
    }

    public function operation_module_assigned_modules($type, $id){
        switch($type){
            case 'role':
                $role = \App\Models\Ums\Role::with(['modules'])->findOrFail($id);
                $allModules = $role->modules;
            case 'user':
                $user = User::with(['modules', 'roles.modules'])->findOrFail($id);
                $directModules = $user->modules;
                $roleModules = $user->roles->flatMap(function ($role) {
                    return $role->modules;
                });
                $allModules = $directModules->merge($roleModules)->unique('id')->values();
            break;
        }
        $groupedModules = Module::whereNotNull('group_name')
            ->orderBy('group_name')
            ->orderBy('name')
            ->get()
            ->groupBy('group_name');

        $ungroupedModules = Module::whereNull('group_name')
            ->orderBy('name')
            ->get();
        return $allModules;
    }

    public function operation_module_create($data){
        DB::beginTransaction();

        try{
            $data['created_by'] = auth('api')->id() ?? Auth::id();
            $data['updated_by'] = auth('api')->id() ?? Auth::id();
            $module = Module::create($data);
            DB::commit();
            $this->log_user_activity('Module created', $module->id, true);
            return $module;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Module created', null, false);
            return $e->getMessage();
        }
    }

    public function operation_module_delete($id){
        DB::beginTransaction();

        try{
            $module = Module::where('unique_id', '=', $id)->orWhere('id', '=', $id)->withTrashed()->firstOrFail();
            if(!$module){
                return response()->json(['message' => 'Module not found'], 404);
            }
            $module->deleted_by = auth('api')->id() ?? Auth::id();
            $module->save();

            DB::commit();
            $this->log_user_activity('Module deleted', $id, true);
            return $module;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Module deleted', $id, false);
            return $e->getMessage();
        }
    }

    public function operation_module_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Module::withTrashed();
            break;
            case 'active':
                $query = Module::where('status', '=', 1);
            break;
        }

        $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['roles', 'users']) : $query;
        $query = $paginated ? $query->paginate($page ?? 10) : $query->get();
        
        return $query;
    }

    public function operation_module_get_by_id($id){
        $module = Module::where('unique_id', '=', $id)->orWhere('id', '=', $id)->withTrashed()->with(['creator', 'updater'])->firstOrFail();

        if(!$module){
            return response()->json(['message' => 'Module not found'], 404);
        }

        return $module;
    }

    public function operation_module_update($data, $id){
        DB::beginTransaction();
        try{
            $module = Module::where('unique_id', '=', $id)->orWhere('id', '=', $id)->withTrashed()->firstOrFail();
            $data['updated_by'] = auth('api')->id() ?? Auth::id();
            $module->update($data);
            DB::rollBack();
            $this->log_user_activity('Module updated', $id, false);
            return $module;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Module updated', $id, false);
            return $e->getMessage();
        }
    }

}