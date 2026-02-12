<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Laboratory\Analyte;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Laboratory\Bottle;
use App\Models\EMR\Laboratory\Category;
use App\Models\EMR\Laboratory\ReferenceRange;
use App\Models\EMR\Laboratory\ResultTemplate;
use App\Models\EMR\Laboratory\ResultTemplateAnalyte;
use App\Models\EMR\Laboratory\ResultTemplateVersion;
use App\Models\EMR\Laboratory\Service;
use App\Models\EMR\Laboratory\Specimen;
use App\Models\EMR\Laboratory\SpecimenType;
use App\Models\EMR\Service as EMRService;
use App\Models\EMR\VisitTransaction;
use App\Models\Inventory\Item;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LaboratoryTrait{
    use LogTrait, ItemTrait, VisitTransactionTrait;

    /*
    ---------------------------------------------------------
    Laboratory Analytes
    ---------------------------------------------------------
    */
    public function emr_laboratory_analyte_create($data){
        DB::beginTransaction();
        
        try{ 
            $query = Analyte::create([
                'name'          => $data['name'],
                'default_unit'  => $data['default_unit'], 
                'input_type'    => $data['input_type'],
                'options'       => $data['options'] ?? null,
                'description'   => $data['description'] ?? null,
                'status'        => $data['status'] ?? 1,
            ]);

            $this->log_user_activity('EMR Laboratory Analyte Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Analyte Create', null, false);
            return $e->getMessage();
        }  
    }

    public function emr_laboratory_analyte_deactivate($id){
        DB::beginTransaction();
        
        try{ 
            $query = Analyte::find($id);
            if(is_null($query->deleted_at)){
                $query::update(['deleted_at'   => now(),]);
            }
            else{
                $query::update([
                    'deleted_at'   => null,
                ]);
            }

            $this->log_user_activity('EMR Laboratory Analyte Deactivate', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Analyte Deactivate', $id, false);
            return $e->getMessage();
        }  
    }
    
    public function emr_laboratory_analyte_get_all($type='all', $specific, $detailed, $paginated){
        $query = Analyte::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
        }
        
        $query = $detailed ? $query->with(['reference_ranges']) : $query->select('id', 'name',);
        $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_laboratory_analyte_get_by($id, $detailed){
        try{
            $query = Analyte::where('id', '=', $id);
            $query = $detailed ? $query->with(['reference_ranges']) : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_analyte_update($data, $id){
        DB::beginTransaction();
        
        try{ 
            $query = Analyte::findOrFail($id);
            
            $query->name          = $data['name'] ?? $query->name;
            $query->default_unit  = $data['default_unit'] ?? $query->default_unit; 
            $query->input_type    = $data['input_type'] ?? $query->input_type;
            $query->options       = $data['options'] ?? $query->options;
            $query->description   = $data['description'] ?? $query->description;
            $query->save();

            $this->log_user_activity('EMR Laboratory Analyte Update', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Analyte Update', $id, false);
            return $e->getMessage();
        }  
    }

    /*
    ---------------------------------------------------------
    Laboratory Bottles
    ---------------------------------------------------------
    */
    
    public function emr_laboratory_bottles_create($data){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::create([
                'name'          => $data['name'],
                'additive'      => $data['additive'] ?? '',
                'description'   => $data['description'] ?? '',
                'status'        => $data['status'] ?? 1,
                'colour'       => $data['colour'] ?? '',
                'size'         => $data['size'] ?? '',
                'created_by'   => auth('api')->id() ?? Auth::id(),
                'updated_by'   => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('EMR Laboratory Bottle Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Create', null, false);
            return $e->getMessage();
        }  
    }

    public function emr_laboratory_bottles_deactivate($id){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::find($id);
            if($query->status == 0){
                    $query::update([
                    'status'       => 0,
                    'updated_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_at'   => now(),
                ]);
            }
            else{
                $query::update([
                    'status'       => 1,
                    'updated_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_by'   => null,
                    'deleted_at'   => null,
                ]);
            }
            $this->log_user_activity('EMR Laboratory Bottle Deactivate', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Deactivate', $id, false);
            return $e->getMessage();
        }  
    }
    
    public function emr_laboratory_bottles_get_all($type='all', $specific, $detailed, $paginated){
        $query = Bottle::query();

        switch($type){
            case 'active':
                $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query->where('status', '=', 0)->withTrashed();
            break;
            default:
                $query->withTrashed();
            break;
        }
        
        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name', 'colour');
        $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_laboratory_bottles_get_by($id, $detailed){
        try{
            $query = Bottle::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_bottles_update($data, $id){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::findOrFail($id);
            
            $query->additive = $data['additive'] ?? $query->additive;
            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? 1;
            $query->colour = $data['colour'] ?? $query->colour;
            $query->size = $data['size'] ?? $query->size;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            $this->log_user_activity('EMR Laboratory Bottle Update', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Update', $id, false);
            return $e->getMessage();
        }  
    }

    /*
    ----------------------------------------------------------
    Laboratory Category
    ----------------------------------------------------------
    */
    public function emr_laboratory_category_create($data){
        DB::beginTransaction();
        
        try{ 
            $query = Category::create([
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'status'        => $data['status'] ?? 1,
            ]);

            $this->log_user_activity('EMR Laboratory Category Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Category Create', null, false);
            return $e->getMessage();
        }  
    }

    public function emr_laboratory_category_deactivate($id){
        DB::beginTransaction();
        
        try{ 
            $query = Category::find($id);
            if($query->status == 0){
                    $query::update([
                    'status'       => 0,
                    'deleted_at'   => now(),
                ]);
            }
            else{
                $query::update([
                    'status'       => 1,
                    'deleted_at'   => null,
                ]);
            }
            $this->log_user_activity('EMR Laboratory Category Deactivate', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Category Deactivate', $id, false);
            return $e->getMessage();
        }  
    }
    
    public function emr_laboratory_category_get_all($type='all', $specific, $detailed, $paginated){
        $query = Category::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
            case 'active':
                $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query->where('status', '=', 0)->withTrashed();
            break;
        }
        
        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_laboratory_category_get_by($id, $detailed){
        try{
            $query = Category::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_category_update($data, $id){
        DB::beginTransaction();
        
        try{ 
            $query = Category::find($id);
            $query::update([
                'name'          => $data['name'] ?? $query->name,
                'description'   => $data['description'] ?? $query->description,
                'status'        => $data['status'] ?? 1,
            ]);

            $this->log_user_activity('EMR Laboratory Category Update', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Category Update', $id, false);
            return $e->getMessage();
        }  
    }


    /*
    ---------------------------------------------------------
    EMR Laboratory Request Functions
    ---------------------------------------------------------
    */

    public function emr_laboratory_request_create($patient_id, $item_id, $visit_id =null, $consultation_id = null, $date = null, $special = 0){
        DB::beginTransaction();
        
        try{ 
            $transaction = $this->emr_visit_transaction_create($item_id, $patient_id, 1, false, $visit_id);

            if(is_string($transaction)){
                $this->log_user_activity('EMR Laboratory Request Create', true, null);
                return $transaction." Can not create transaction"; 
            }  

            $query = LaboratoryRequest::create([
                // 'result', 'sample_by', 'sample_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'outsourced_type', 'outsourced_to_id', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'outsource_result_file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
                'date' => $date ?? date('Y-m-d'),
                'visit_id' => $visit_id,
                'branch_id' => $visit_id ?? request()->cookie('current_branch'),
                'consultation_id' => $consultation_id,
                'request_type_id' => $data['request_type_id'] ?? null,
                'patient_id' => $patient_id,
                'transaction_id' => $transaction->id,
                'quantity' => 1,
                'item_id' => $item_id,
                'status' => $transaction->status == 1 ? 1 : 0,
                'special' => $special,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('EMR Laboratory Request Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Request Create', null, false);
            return $e->getMessage();
        }  
    }

    public function emr_laboratory_request_get_all($type, $specific, $detailed, $paginated){
        $query = LaboratoryRequest::query();
        
        switch($type){
            case 'awaiting':
                $query = $query->where('branch_id', '=', request()->cookie('current_branch'))->where('status', '=', LaboratoryRequest::StatusSampleCollected);
            break;    
            case 'completed':
                $query = $query->where('status', '=', LaboratoryRequest::StatusConfirmed)->where('branch_id', '=', request()->cookie('current_branch'));
            break;
            case 'insurance':
                $transactions = VisitTransaction::whereIn('paid_by', [1, 3])->pluck('id');    
                $query = $query->where('branch_id', '=', request()->cookie('current_branch'))->whereIn('transaction_id', $transactions)->where('status', '=', LaboratoryRequest::StatusBooked);
            break;
            case 'reffered_in':
                $query = $query->where('status', '=', LaboratoryRequest::StatusReferredOut)->where('outsourced_to_id', '=', request()->cookie('current_branch'))->where('outsourced_type', '=', 0);
            break;
            case 'reffered_out':
                $query = $query->where('branch_id', '=', request()->cookie('current_branch'))->where('status', '=', LaboratoryRequest::StatusReferredOut)->where('outsourced_to_id', '=', request()->cookie('current_branch'))->where('outsourced_type', '=', 1);
            break;
            case 'uncollected':
                $query = $query->where('branch_id', '=', request()->cookie('current_branch'))->where('status', '=', LaboratoryRequest::StatusConfirmed);
            break;
            case 'unpaid':
                $query = $query->where('branch_id', '=', request()->cookie('current_branch'))->where('status', '=', LaboratoryRequest::StatusBooked);
            break;
        }


    }

    public function emr_laboratory_request_get_by($type, $id, $detailed){
        try{
            $query = LaboratoryRequest::where('id', '=', $id)->orWhere('unique_id', '=', $id);

            $query = $detailed ? $query->with(['item', 'service.service', 'patient.user', 'transaction.payments']) : $query->select('id', 'unique_id')->with(['transaction.payments']);
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    
    public function emr_laboratory_request_update($data, $id){
        DB::beginTransaction();
        
    }

    /*
    -----------------------------------------------------------------------
    Laboratory Result Templates
    -----------------------------------------------------------------------
    */

    public function emr_laboratory_result_template_create($data){
        DB::beginTransaction();

        try{
            $template = ResultTemplate::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'layout' => $data['layout'],
                'is_panel' => $data['is_panel'] ?? false,
                'created_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $version = ResultTemplateVersion::create([
                'result_template_id' => $template->id,
                'version' => 1,
                'is_current' => true,
                'created_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['analytes'] as $index => $analyte) {

                $templateAnalyte = ResultTemplateAnalyte::create([
                    'result_template_id' => $template->id,
                    'template_version_id' => $version->id,
                    'analyte_id' => $analyte['analyte_id'] ?? null,
                    'analyte_name' => $analyte['name'],
                    'unit' => $analyte['unit'] ?? null,
                    'input_type' => $analyte['input_type'] ?? 'number',
                    'display_order' => $index,
                    'options' => $analyte['options'] ?? null
                ]);
            }
            
            DB::commit();
            $this->log_user_activity('Laboratory Result Template Create', $template->id, true);
            return $template->load('currentVersion');
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Laboratory Result Template Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_result_template_deactivate($id){
        DB::beginTransaction();

        try{
            $query = ResultTemplate::findOrFail($id);
            
            if($query->status == 1){
                $query->status = 0;
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = 1;
                $query->deleted_at = null;    
            }

            $query->save();

            DB::commit();
            $this->log_user_activity('Laboratory Result Template Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Laboratory Result Template Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_result_template_get_all($type, $specific, $detailed, $paginated){
        $query = ResultTemplate::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('stauts', '=', 0)->withTrashed();
            break;
            default:
                $query = $query->withTrashed();
            break;
        }

        if(is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $query = $query->where('name', 'LIKE', "%$search%");
            }
        }
        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_laboratory_result_template_get_by($type, $id, $detailed){
        try{
            $query = ResultTemplate::where('id', '=', $id);
            $query = $detailed ? $query : $query->select('id', 'name');

            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_result_template_update($data, $id){
        DB::beginTransaction();

        try{

            $template = ResultTemplate::findOrFail($id);

            // Mark old version as not current
            ResultTemplateVersion::where('result_template_id', $template->id)->where('is_current', true)->update(['is_current' => false]);

            //Create New Template Version
            $newVersionNumber = ResultTemplateVersion::where('result_template_id', $template->id)->max('version') + 1;

            $version = ResultTemplateVersion::create([
                'result_template_id' => $template->id,
                'version' => $newVersionNumber,
                'change_note' => $data->change_note,
                'is_current' => true,
                'created_by' => auth()->id()
            ]);

            $template->update([
                'name' => $data->name,
                'description' => $data->description,
                'layout' => $data->layout,
                'updated_by' => auth()->id()
            ]);

            foreach ($data->analytes as $index => $analyte) {
                $templateAnalyte = ResultTemplateAnalyte::create([
                    'result_template_id' => $template->id,
                    'template_version_id' => $version->id,
                    'analyte_id' => $analyte['analyte_id'] ?? null,
                    'analyte_name' => $analyte['name'],
                    'unit' => $analyte['unit'] ?? null,
                    'input_type' => $analyte['input_type'],
                    'display_order' => $index,
                    'options' => $analyte['options'] ?? null
                ]);

                /*foreach ($analyte['reference_ranges'] ?? [] as $range) {
                    ReferenceRange::create([
                        'template_analyte_id' => $templateAnalyte->id,
                        'gender' => $range['gender'] ?? 'any',
                        'age_min' => $range['age_min'] ?? null,
                        'age_max' => $range['age_max'] ?? null,
                        'value_min' => $range['value_min'] ?? null,
                        'value_max' => $range['value_max'] ?? null,
                        'textual_range' => $range['textual_range'] ?? null
                    ]);
                }*/
            }

            //return response()->json();

            DB::commit();
            $this->log_user_activity('Laboratory Result Template Update', $id, true);
            return $template->load('currentVersion');
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Laboratory Result Template Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------
    EMR Laboratory Reference Range Functions
    -----------------------------------------------------------
    */

    public function emr_laboratory_reference_range_create($data){
        DB::beginTransaction();
        try{
            $query = ReferenceRange::create([
                'analyte_id' => $data['analyte_id'],
                'gender' => $data['gender'] ?? null,
                'age_min' => $data['age_min'] ?? 0,
                'age_max' => $data['age_max'] ?? 120,
                'low_value' => $data['low_value'] ?? null,
                'normal_value' => $data['normal_value'] ?? null,
                'high_value' => $data['high_value'] ?? null,
                'critical_low' => $data['critical_low'] ?? null,
                'critical_high' => $data['critical_high'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('EMR Laboratory Reference Range Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Reference Range Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_reference_range_deactivate($id){
        DB::beginTransaction();

        try{
            $query = ReferenceRange::where('id', '=', $id)->firstOrFail();

            if(is_null($query->deleted_at)){
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_at = null;                
            }

            $query->updated_at = date('Y-m-d H:i:s');
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Laboratory Reference Range Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Reference Range Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_reference_range_get_all($type, $specific, $detailed, $paginated){
        $query = ReferenceRange::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['id'])){
                $query = $query->where('analyte_id', '=', $specific['id']);
            }
        }

        $query = $detailed ? $query->with(['analyte']) : $query;
        $query = $paginated? $query->paginate(20) : $query->get();
        
        return $query;
    }

    public function emr_laboratory_reference_range_get_by($type, $id, $detailed){
        try{
            $query = ReferenceRange::where('id', '=', $id);
            $query = $detailed ? $query->with(['analyte']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_reference_range_update($data, $id){
        DB::beginTransaction();
        try{
            $query = ReferenceRange::where('id', '=', $id)->firstOrFail();

            $query->analyte_id = $data['analyte_id'] ?? $query->analyte_id;
            $query->gender = $data['gender'] ?? $query->gender;
            $query->age_min = $data['age_min'] ?? $query->age_min;
            $query->age_max = $data['age_max'] ?? $query->age_max;
            $query->low_value = $data['low_value'] ?? $query->low_value;
            $query->normal_value = $data['normal_value'] ?? $query->normal_value;
            $query->high_value = $data['high_value'] ?? $query->high_value;
            $query->critical_low = $data['critical_low']  ?? $query->critical_low;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Laboratory Reference Range Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Reference Range Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------
    Laboratory Services
    -----------------------------------------------------------------------
    */
    public function emr_laboratory_service_create($data){
        DB::beginTransaction();

        try{
            $emr_service = EMRService::create([
                'item_id'           => null,
                'service_type_id'   => $data['type_id'] ?? 6,
                'reference_id'      => null,
                'description'       => $data['description'],
                'status'            => EMRService::StatusActive ?? 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $service = Service::create([
                'service_id'        => $emr_service->id,
                'category_id'       => $data['category_id'],
                'bottle_type_id'    => $data['bottle_type_id'],
                'specimen_type_id'  => $data['specimen_type_id'],
                'result_template_id'=> $data['result_template_id'],
                'status'            => 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $item = Item::create([
                'name'                  => $data['name'],
                'type_id'               => $data['type_id'] ?? 6,
                'unique_id'             => $this->inventory_generate_unique_id('item'),
                'service_id'            => $emr_service->id,
                'last_landing_cost'     => $data['landing_cost'] ?? 0.00,
                'average_landing_cost'  => $data['landing_cost'] ?? 0.00,
                'description'           => $data['description'] ?? null,
                'status'                => 1,
                'created_by'            => Auth::id() ?? auth('api')->id(),
                'updated_by'            => Auth::id() ?? auth('api')->id(),
            ]);

            $emr_service->reference_id = $service->id;
            $emr_service->item_id = $item->id;

            $emr_service->save();

            $this->log_user_activity('EMR Laboratory Service Create', $service->id, true);
            DB::commit();
            return $emr_service;

        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Service Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_service_get_all($type, $specific, $detailed, $paginated){
        $query = Service::query();

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $item_list = Item::where('name', 'LIKE', "%$search%")->pluck('id');
                $service_lists = EMRService::whereIn('item_id', $item_list)->pluck('id');
                $query = $query->whereIn('service_id', $service_lists);
            }
        }

        $query = $detailed ? $query->with(['bottle_type', 'category', 'creator', 'deleter', 'result_template', 'service.item', 'specimen_type', 'updater']) : $query->with(['service.item']);
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_laboratory_service_get_by($type, $id, $detailed){
        try{
            $query = Service::where('id', '=', $id);
            $query = $detailed ? $query->with(['bottle_type', 'category', 'creator', 'deleter', 'reference_ranges', 'result_template', 'service.item', 'specimen_type', 'updater']) : $query->with(['service.item']);
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_service_update($data, $id){
        DB::beginTransaction();

        try{
            $service = Service::findOrFail($id);
            $service->service_id = $data['service_id'] ?? $service->service_id;
            $service->category_id = $data['category_id'] ?? $service->category_id;
            $service->bottle_type_id = $data['bottle_type_id'] ?? $service->bottle_type_id;
            $service->specimen_type_id = $data['specimen_type_id'] ?? $service->specimen_type_id;
            $service->result_template_id = $data['result_template_id'] ?? $service->result_template_id;
            $service->status = $data['status'] ?? 1;
            $service->updated_by = Auth::id() ?? auth('api')->id();
        
            $service->save();
            
            //Check if there is an existing EMR Service for this Laboratory Service
            if (is_null($service->service_id)){
                $emr_service = EMRService::create([
                    'item_id'           => null,
                    'service_type_id'   => $data['type_id'] ?? 6,
                    'reference_id'      => $service->id,
                    'description'       => $data['description'],
                    'status'            => EMRService::StatusActive ?? 1,
                    'created_by'        => Auth::id() ?? auth('api')->id(),
                    'updated_by'        => Auth::id() ?? auth('api')->id(),
                ]);

                $service->service_id = $emr_service->id;
                $service->save();
            }
            else{
                $emr_service = EMRService::findOrFail($service->service_id);
                
                $emr_service->service_type_id = $data['type_id'] ?? 6;
                $emr_service->reference_id = $id;
                $emr_service->description = $data['description'];
                $emr_service->status = EMRService::StatusActive;
                $emr_service->updated_by =  Auth::id() ?? auth('api')->id();
                $emr_service->save();
            }

            //Check if there is an existing item for this EMR Service
            if (is_null($emr_service->item_id)){
                $item = Item::create([
                    'name'                  => $data['name'],
                    'type_id'               => $data['type_id'] ?? 6,
                    'unique_id'             => $this->inventory_generate_unique_id('item'),
                    'service_id'            => $emr_service->id,
                    'last_landing_cost'     => $data['landing_cost'] ?? 0.00,
                    'average_landing_cost'  => $data['landing_cost'] ?? 0.00,
                    'description'           => $data['description'] ?? null,
                    'status'                => 1,
                    'created_by'            => Auth::id() ?? auth('api')->id(),
                    'updated_by'            => Auth::id() ?? auth('api')->id(),
                ]);

                $emr_service->item_id = $item->id;
                $emr_service->save();
            }
            else{
                $item = Item::findOrFail($emr_service->item_id);
                
                $item->name = $data['name'];
                $item->type_id = $data['type_id'] ?? 6;
                $item->service_id = $data['type_id'];
                $item->last_landing_cost = $data['landing_cost'] ?? $item->last_landing_cost;
                $item->average_landing_cost = $data['landing_cost'] ?? $item->last_landing_cost;
                $item->description = $data['description'] ?? $item->description;
                $item->status = 1;
                $item->updated_by = Auth::id() ?? auth('api')->id();
                $item->deleted_by = null;
                $item->deleted_at = null;

                $item->save();
            }

            $this->log_user_activity('EMR Laboratory Service Update', $id, true);
            DB::commit();
            return $emr_service;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Service Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------
    Laboratory Specimen Types
    -----------------------------------------------------------------------
    */
    public function emr_laboratory_specimen_create($data){
        DB::beginTransaction();

        try{
            $query = Specimen::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Specimen::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_get_all($type, $specific, $detailed, $paginated){
        $query = Specimen::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
            break;
            default:
                $query = $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $query = $query->where('name', 'LIKE', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();

        return $query;
    }

    public function emr_laboratory_specimen_get_by($type, $id, $detailed){
        try{
            $query = Specimen::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
            return $query->firstORFail();    
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Specimen::findOrFail($id);

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------
    Laboratory Specimen Types
    -----------------------------------------------------------------------
    */
    public function emr_laboratory_specimen_type_create($data){
        DB::beginTransaction();

        try{
            $query = SpecimenType::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = SpecimenType::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_type_get_all($type, $specific, $detailed, $paginated){
        $query = SpecimenType::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
            break;
            default:
                $query = $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $query = $query->where('name', 'LIKE', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(30) : $query->get();

        return $query;
    }

    public function emr_laboratory_specimen_type_get_by($type, $id, $detailed){
        try{
            $query = SpecimenType::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
            return $query->firstORFail();    
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_specimen_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = SpecimenType::findOrFail($id);

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Laboratory Specimen Type Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Laboratory Specimen Type Update', $id, false);
            return $e->getMessage();
        }
    }
}