<?php
namespace App\Services\UMS;
use App\Models\User;
use Spatie\Permission\Models\Role;


class UserRoleService{

    public function assign_role($user_id, $role_name){
        $user = User::find($user_id);
        $role = Role::where('name', '=', $role_name)->first();
        if ($user->hasRole($role->name)){
            return 'User already has this role';
        }
        else{
            $user->assignRole($role->name);
            return 'Role assigned successfully';
        }
    }

    public function remove_role($user_id, $role_name){
        $user = User::find($user_id);
        $role = Role::where('name', '=', $role_name)->first();
        if ($user->hasRole($role->name)){
            $user->removeRole($role->name);
            return 'Role removed successfully';
        }
        else{
            return 'User does not have this role';
        }
    }
}