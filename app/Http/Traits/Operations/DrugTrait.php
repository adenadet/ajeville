<?php

namespace App\Http\Traits\Operations;
//use App\Http\Traits\UMS\LogTrait;

use App\Models\EMR\Drugs\Drug;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DrugTrait{
    //use LogTrait;
    public function operation_drug_create($data){
        DB::beginTransaction();
        try{
            $drug = Drug::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'ham' => $data['ham'] ?? false,
                'status' => $data['status'] ?? false,
                'interactions' => $data['interactions'] ?? NULL,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Create', true, null);
            DB::commit();
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Create', false, null);
        }
        
    }

    public function operation_drug_deactivate($id){
        DB::beginTransaction();
        try{
            $drug = Drug::find($id);

            $drug->deleted_by = Auth::id() ?? auth('api')->id();
            $drug->updated_by = Auth::id() ?? auth('api')->id();
            
            $drug->save();

            DB::commit();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Deactivate', true, null);
        
            return $drug;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Deactivate', false, null);
        }
        
    }

    public function operation_drug_get_all($paginated, $detailed, $page){
        $drugs = $detailed ? Drug::orderBy('name')->with(['creator', 'specific_drugs']) : Drug::orderBy('name')->select('id', 'name'); 
        $drugs = $paginated ? $drugs->paginate(100) : $drugs->get();

        return $drugs;          
    }

    public function operation_drug_get_by_id($id){
        $drug = Drug::where('id', '=', $id)->with(['creator', 'items', 'updater'])->first();
        return $drug;          
    }

    public function operation_drug_update($data, $id){
        DB::beginTransaction();
        try{
            $drug = Drug::where('id', '=', $id)->first();
            
            $drug->name = $data['name'];
            $drug->description = $data['description'];
            $drug->interactions = $data['interactions'];
            $drug->updated_by = auth('api')->id();

            $drug->save();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Update', true, null);
            DB::commit();
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Operation Drug Update', false, null);
        }
    }
}
