<?php 

namespace App\Services\EMR;

use App\Http\Traits\General\LogTrait;
use App\Models\Insurance\ContactPerson;
use App\Models\Insurance\Plan;
use App\Models\Insurance\PlanBranch;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use App\Models\EMR\Insurance\RequestItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InsuranceProviderService
{
    use LogTrait;

    public function insurance_provider_create($data){
        $provider = Provider::create([
            'name' => $data['name'],
            'hmo_type_id' => $data['hmo_type_id'],
            'website' => $data['website'] ?? NULL,
            'portal' => $data['portal'] ?? NULL,
            'phone' => $data['phone'] ?? NULL,
            'description' => $data['description'] ?? NULL,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
            'status' => $data['status'] ?? 1,
        ]);
    }

    public function insurance_provider_deactivate($id){
        DB::beginTransaction();
        try{
            $query = Provider::find($id);
            
            $query->status = 0;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            $this->log_user_activity('Insurance Provider Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Insurance Provider Deactivate', false, $id);
            return $e;
        }
    }   
}