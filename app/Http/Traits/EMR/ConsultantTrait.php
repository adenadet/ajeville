<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\DialysisTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\PhysiotheraphyTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Appointment\Appointment;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\Specialty;
use App\Models\EMR\Consultation\SpecialtyDoctor;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ConsultantTrait{
    use LogTrait;

    /*
    ------------------------------------------------------------------
    Consultant Functions
    ------------------------------------------------------------------
    */

    public function emr_consultant_create($data){
        //Create a new consultant        
    }

    public function emr_consultant_deactivate($id){
        //Deactivate a Consultant 
    }

    public function emr_consultant_get_all($type, $specific, $detailed, $paginated){
        //Get all consultants that meet requirements
    }

    public function emr_consultant_get_by($type, $id, $detailed){
        //Get one single consultant
    }

    public function emr_consultant_update($data, $id){
        //Update a Consultant's details
    }

    /*
    ------------------------------------------------------------------
    Consultant Specialty Functions
    ------------------------------------------------------------------
    */

    public function emr_consultant_specialty_create($data){
        //Create a new consultant_specialty        
    }

    public function emr_consultant_specialty_deactivate($id){
        //Deactivate a Consultant 
    }

    public function emr_consultant_specialty_get_all($type, $specific, $detailed, $paginated){
        //Get all consultant_specialtys that meet requirements
    }

    public function emr_consultant_specialty_get_by($type, $id, $detailed){
        //Get one single consultant_specialty
    }

    public function emr_consultant_specialty_update($data, $id){
        //Update a Consultant's details
    }

    /*
    ------------------------------------------------------------------
    Specialty Functions
    ------------------------------------------------------------------
    */

    public function emr_specialty_create($data){
        //Create a new specialty 
        DB::beginTransaction();

        try{
            $query = Specialty::where( 'name', '=', $data['name'])->withTrashed()->first();

            if($query){
                $query->deleted_at = null;
                $query->save(); 
            }
            else{
                $query = Specialty::create([
                    'name' => $data['name'],
                ]);
            }
            
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }       
    }

    public function emr_specialty_deactivate($id){
        //Deactivate a Consultant
        DB::beginTransaction();

        try{
            $query = Specialty::where('id', '=', $id);

            if (is_null($query->deleted_at)){
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_at = null;  
            }
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        } 
    }

    public function emr_specialty_get_all($type, $specific, $detailed, $paginated){
        //Get all specialtys that meet requirements
        $query = Specialty::query();
        switch($type){

        }

        $query = $detailed ? $query->select('id', 'name')->with(['doctors.user']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_specialty_get_by($type, $id, $detailed){
        //Get one single specialty
        try{
            $query = Specialty::where('id', '=', $id);
            $query = $detailed ? $query->select('id', 'name')->with(['doctors']) : $query->select('id', 'name');
            return $query->firstOrFail();    
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_specialty_update($data, $id){
        //Update a Consultant's details
        DB::beginTransaction();

        try{
            $query = Specialty::findOrFail( $id);

            $query->name = $data['name'];
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