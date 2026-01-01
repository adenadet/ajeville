<?php
namespace App\Http\Traits\General;

use App\Models\Area;
use App\Models\Country;
use App\Models\Department;
use App\Models\State;
use Exception;
use Illuminate\Support\Facades\DB;

trait SettingsTrait{

    /*
    --------------------------------------------------------------------------------
    Basic Department Functions
    --------------------------------------------------------------------------------
    */
    public function general_generate_random_id($length=10){}
    public function general_settings_area_create($data){}

    public function general_settings_area_delete($id){}

    public function general_settings_area_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Area::withTrashed();
            break;
            case 'active':
                $query = Area::withTrashed();
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed  ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();
        return $query;
    }

    public function general_settings_area_get_by($type, $specific, $detailed){

    }

    public function general_settings_area_update($data, $id){

    }

    /*
    --------------------------------------------------------------------------------
    Basic Country Functions
    --------------------------------------------------------------------------------
    */
    public function general_settings_country_create($data){}

    public function general_settings_country_delete($id){}

    public function general_settings_country_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Country::withTrashed();
            break;
            case 'active':
                $query = Country::withTrashed();
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed  ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();
        return $query;
    }

    public function general_settings_country_get_by($type, $specific, $detailed){

    }

    public function general_settings_country_update($data, $id){

    }

    /*
    --------------------------------------------------------------------------------
    Basic Department Functions
    --------------------------------------------------------------------------------
    */
    public function general_settings_department_create($data){}

    public function general_settings_department_delete($id){}

    public function general_settings_department_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Department::withTrashed();
            break;
            case 'active':
                $query = Department::where('status', '=', 1);
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with('hod', 'staffs') : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();
        return $query;
    }

    public function general_settings_department_get_by($type, $specific, $detailed){}

    public function general_settings_department_update($data, $id){

    }

    /*
    --------------------------------------------------------------------------------
    Basic State Functions
    --------------------------------------------------------------------------------
    */
    public function general_settings_state_create($data){
        DB::beginTransaction();

        try{
            $state = State::create([
                'name' => $data['name'],
                'country_id' => $data['country_id'],
            ]);

            DB::commit();
        }
        catch(Exception $e){
            DB::rollBack();
        }
    }

    public function general_settings_state_delete($id){

    }

    public function general_settings_state_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = State::withTrashed();
            break;
            case 'active':
                $query = State::withTrashed();
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['areas']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();
        return $query;
    }

    public function general_settings_state_get_by($type, $specific, $detailed){}

    public function general_settings_state_update($data, $id){

    }
}