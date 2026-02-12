<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\AppointmentTrait;
use App\Http\Traits\EMR\DialysisTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\PhysiotheraphyTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\EMR\Drugs\Form as DrugForm;
use App\Models\EMR\Drugs\Route as DrugRoute;
use App\Models\EMR\Frequency;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient;
use App\Models\EMR\PatientInsurance;
use App\Models\EMR\Settings\Location;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SettingsTrait{
    use FileManagerTrait, LogTrait;

    public function emr_settings_drug_form_create($data){}

    public function emr_settings_drug_form_deactivate($id){}

    public function emr_settings_drug_form_get_all($type, $specific, $detailed, $paginated){
        $query = DrugForm::query();

        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function emr_settings_drug_form_get_by($type, $id, $detailed){}

    public function emr_settings_drug_form_update($data, $id){}

    public function emr_settings_drug_route_create($data){}

    public function emr_settings_drug_route_deactivate($id){}

    public function emr_settings_drug_route_get_all($type, $specific, $detailed, $paginated){
        $query = DrugRoute::query();

        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function emr_settings_drug_route_get_by($type, $id, $detailed){}

    public function emr_settings_drug_route_update($data, $id){}


    public function emr_settings_frequency_create($data){
        DB::beginTransaction();

        try{
            $query = Frequency::create([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);
            $this->$this->log_user_activity('EMR Frequency Create', $query->id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Create', null, false);
            return $e->getMessage();
        }   
    }
    
    public function emr_settings_frequency_delete($id){
        DB::beginTransaction();

        try{
            $query = Frequency::find($id);
            
            $query->deleted_at = date('Y-m-d H:i:s');
            
            $query->save();

            $this->$this->log_user_activity('EMR Frequency Delete', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Delete', $id, false);
            return $e->getMessage();
        }
    }
    
    public function emr_settings_frequency_get_all($type, $specific, $detailed, $paginated, $page){
        $query = $paginated ? Frequency::paginate(20) : Frequency::get();

        return $query; 
    }

    public function emr_settings_frequency_get_by($type, $specific, $detailed){
        $query = Frequency::find($specific);

        return $query;
    }

    public function emr_settings_frequency_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Frequency::find($id);
            
            $query->name = $data['name'];
            $query->description = $data['description'];
            
            $query->save();

            $this->$this->log_user_activity('EMR Frequency Update', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Update', $id, false);
            return $e->getMessage();
        }   
    }
    
    public function emr_settings_location_create($data){
        DB::beginTransaction();

        try{
            $query = Location::create([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);
            $this->$this->log_user_activity('EMR Frequency Create', $query->id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Create', null, false);
            return $e->getMessage();
        }   
    }
    
    public function emr_settings_location_delete($id){
        DB::beginTransaction();

        try{
            $query = Location::find($id);
            
            $query->deleted_at = date('Y-m-d H:i:s');
            
            $query->save();

            $this->$this->log_user_activity('EMR Frequency Delete', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Delete', $id, false);
            return $e->getMessage();
        }
    }
    
    public function emr_settings_location_get_all($type, $specific, $detailed, $paginated){
        $query = Location::query();

        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query; 
    }

    public function emr_settings_location_get_by($type, $specific, $detailed){
        $query = Location::find($specific);

        return $query;
    }

    public function emr_settings_location_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Location::find($id);
            
            $query->name = $data['name'];
            $query->description = $data['description'];
            
            $query->save();

            $this->$this->log_user_activity('EMR Frequency Update', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Frequency Update', $id, false);
            return $e->getMessage();
        }   
    }
}