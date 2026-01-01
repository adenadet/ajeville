<?php
namespace App\Http\Traits\Equipments;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Equipments\Asset;
use App\Models\Equipments\AssetType;
use App\Models\Equipments\AssignmentRegister;
use App\Models\Finance\Account;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Sales\Order;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait AssetTrait {
    use FileManagerTrait, LogTrait;

    private function equipment_asset_generate_uuid($type){
        switch($type) {
            case 'asset':
                $uuid = 'ASSET-'.strtoupper(uniqid());
                break;
            case 'asset_type':
                $uuid = 'ASSET_TYPE-'.strtoupper(uniqid());
                break;
            default:
                $uuid = strtoupper(uniqid());
        }

        return $uuid;
    }
    /*
    |--------------------------------------------------------------------------
    Basic CRUD for Assets
    ---------------------------------------------------------------------------|
    */
    public function equipment_asset_assign($data, $id){
        DB::beginTransaction();

        try{
            //Create a new Asset
            $asset = Asset::where('uuid', '=', $id)->firstOrFail();

            $asset->update([
                'assigned_to_user_id' => $data['assigned_to_user_id'] ?? $asset->assigned_to_user_id,
                'location_id' => $data['location_id'] ?? $asset->location_id,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            
            // Check if the asset is already assigned to a user or location
            $previousAssignment = AssignmentRegister::where('asset_id', '=', $id)->where('status', '=', 1)->first();
            if($previousAssignment){
                // If the asset is already assigned, update the previous assignment
                $previousAssignment->status = 0; // Mark as inactive
                $previousAssignment->end_date = date('Y-m-d');
                $previousAssignment->updated_by = auth('api')->id() ?? Auth::id();
                $previousAssignment->save();
            }

            $query = AssignmentRegister::create([
                'asset_id' => $id,
                'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
                'location_id' => $data['location_id'] ?? null,
                'start_date' => date('Y-m-d'),
                'status' => 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('Asset Assigned', $id, true);

            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Assigned', $id, false);

            return $e->getMessage();
        }
    }

    public function equipment_asset_create($data){
        DB::beginTransaction();

        try {
            //Create a new Asset
            $query = Asset::create([
                'name' => $data['name'],
                'acquisition_date' => $data['acquisition_date'] ?? date('Y-m-d'),
                'assigned_to_user_id' => $data['assigned_to_user_id'] ?? null,
                'depreciation_rate' => $data['depreciation_rate'] ?? 25,
                'description' => $data['description'],
                'purchase_value' => $data['purchase_value'] ?? 0,
                'serial_number' => $data['serial_number'],
                'status' => $data['status'] ?? 1,
                'type_id' => $data['type_id'],
                'uuid' => $this->equipment_asset_generate_uuid('asset'),
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('Asset Created', $query->id, true);
        
            if (isset($data['assigned_to_user_id']) || isset($data['location_id'])) {
                $this->equipment_asset_assign($data, $query->id);
            }
        
            DB::commit();
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function equipment_asset_deactivate($id){
        DB::beginTransaction();

        try {
            //Create a new Asset
            $query = Asset::where('uuid', '=', $id)->orWhere('id', '=', $id)->firstOrFail();
            
            if ($query->status == Asset::STATUS_ACTIVE) {
                
                $query->update([
                    'status' => Asset::STATUS_INACTIVE,
                    'deleted_by'=> auth('api')->id() ?? Auth::id(),
                    'deleted_at'=> date('Y-m-d H:i:s'),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }
            else{
                $query->update([
                    'status' => Asset::STATUS_ACTIVE,
                    'deleted_by'=> null,
                    'deleted_at'=> null,
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }
            $this->log_user_activity('Asset Deactivated', $id, true);
            //Log the transaction
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function equipment_asset_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Asset::query();
        switch ($type) {
            case 'active':
                $query = $query->whereIn('status', [1, 2, 3, 4]);
            break;
            case 'assigned':
                $query = $query->whereNotNull('assigned_to_user_id')->orWhereNotNull('location_id');
            break;
            case 'location':
                $query = $query->where('location_id', '=', $specific['location_id']);
            break;
            case 'status':
                $query = $query->where('status', '=', $specific['status']);
            break;
        }

        if (is_array($specific)) {
            if (isset($specific['query']) && !empty($specific['query'])){
                $query = $query->where('name', 'LIKE', '%'.$specific['query'].'%');
            }
            if (isset($specific['type']) && !empty($specific['type'])){
                switch($specific['type']){
                    case 'all':
                        $query = $query->withTrashed();
                        break;
                    case 'active':
                        $query = $query->where('status','=', Asset::STATUS_ACTIVE);
                        break;
                    case 'inactive':
                        $query = $query->where('status','=', Asset::STATUS_INACTIVE)->withTrashed();
                        break;
                } 
            }
        }

        $query = $detailed ? $query->with(['assignedUser', 'category', 'location', 'creater', 'updater']) : $query->select('id', 'uuid', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function equipment_asset_get_valuation_summary($date){
        $dates = (isset($date) && is_null($date)) ? Carbon::parse($date) : now();
        $totalPurchaseValue = 0;
        $totalDepreciation = 0;
        $assets = Asset::all();
        foreach ($assets as $asset) {
            $yearsUsed = $asset->acquisition_date
                ? $dates->diffInYears(Carbon::parse($asset->acquisition_date))
                : 0;

            $annualDep = ($asset->purchase_value * $asset->depreciation_rate) / 100;
            $depreciation = min($asset->purchase_value, $annualDep * $yearsUsed);

            $totalPurchaseValue += $asset->purchase_value;
            $totalDepreciation += $depreciation;
        }

        $netFixedAssets = $totalPurchaseValue - $totalDepreciation;
        return ['total_net' =>$netFixedAssets, 'total_purchase_value' => $totalPurchaseValue, 'total_depreciation' => $totalDepreciation];
    }

    public function equipment_asset_return($id) {
        DB::beginTransaction();

        try {
            //Find asset
            $query = Asset::where('uuid', '=', $id)->first();
            if (!$query) {return 'Asset not found.';}

            $query->update([
                'assigned_to_user_id' => null,
                'location_id' => null,
                'status' => 1,
            ]);

            $previousAssignment = AssignmentRegister::where('asset_id', $id)->where('status', '=', 1)->first();
            if ($previousAssignment) {
                $previousAssignment->status = 0; // Mark as inactive
                $previousAssignment->end_date = date('Y-m-d');
                $previousAssignment->updated_by = auth('api')->id() ?? Auth::id();
                $previousAssignment->save();
            }

            $this->log_user_activity('Asset Returned', $id, true);
            DB::commit();
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Returned', $id, false);
            return $e->getMessage();
        }
    }
    public function equipment_asset_update($data, $id){
        DB::beginTransaction();

        try {
            //Create a new Asset
            $query = Asset::where('id', '=', $id)->orWhere('uuid', '=', $id)->firstOrFail();
            
            $query->name = $data['name'];
            $query->acquisition_date = $data['acquisition_date'] ?? $query->acquisition_date;
            $query->assigned_to_user_id = $data['assigned_to_user_id'] ?? $query->assigned_to_user_id;
            $query->depreciation_rate = $data['depreciation_rate'] ?? $query->depreciation_rate;
            $query->description = $data['description'] ?? $query->description;
            $query->location_id = $data['location_id'] ?? $query->location_id;
            $query->purchase_value = $data['purchase_value'] ?? $query->purchase_value;
            $query->serial_number = $data['serial_number'] ?? $query->serial_number;
            $query->type_id = $data['type_id'] ?? $query->type_id;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            echo $query->purchase_value;
            echo $data['purchase_value'];

            $query->save();
            
            if ((isset($data['assigned_to_user_id']) && $data['assigned_to_user_id'] != $query->assigned_to_user_id) || 
                isset($data['location_id']) && $data['location_id'] != $query->location_id) {
                $this->equipment_asset_assign($data, $id);
            }

            //Log the transaction
            $this->log_user_activity('Asset Updated', $id, true);
            DB::commit();
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Updated', $id, false);
            return $e->getMessage();
        }
    }

    /*
    |--------------------------------------------------------------------------
    Basic CRUD for Asset Types
    ---------------------------------------------------------------------------|
    */
    public function equipment_asset_type_create($data){
        DB::beginTransaction();

        try {
            //Create a new Asset Type
            $query = AssetType::create([
                'name' => $data['name'],
                'uuid' => $this->equipment_asset_generate_uuid('asset_type'),
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('Asset Type Created', $query->id, true);
            DB::commit();
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Type Created', null, false);
            return $e->getMessage();
        }
    }

    public function equipment_asset_type_deactivate($id){
        DB::beginTransaction();

        try {
            $type = AssetType::where('uuid', '=', $id)->first();
            if (!$type) {
                return 'Asset Type not found.';
            }
    
            $type->status = 4; // Assuming 4 means deactivated
            $type->updated_by = auth('api')->id() ?? Auth::id();
            $type->save();
    
            $this->log_user_activity('Asset Type Deactivated', $id, true);
            DB::commit();
            return $type;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Type Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function equipment_asset_type_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = AssetType::where('id', '=', $id);
                break;
            case 'uuid':
                $query = AssetType::where('uuid', '=', $id);
                break;
        }

        $query = $detailed ? $query->with(['creater', 'updater']) : $query->select( 'uuid', 'name');
        return $query;
    }

    public function equipment_asset_type_get_all($type, $specific, $detailed, $paginated, $page){
        $query = AssetType::query();
        switch($type){
            case 'all':
                $query = $query->where('status', '!=', 6);
                break;
            case 'active':
                $query = $query->whereIn('status', [1, 2, 3, 4]);
            break;
            case 'search':
                $query = $query->whereIn('status', [1, 2, 3, 4]);
                break;
            case 'status':
                $query = $query->where('status', '=', $specific);
                break;
            default:
                $query = $query->with(['creater', 'updater'])->get();
        }

        $query = $detailed ? $query->with(['creater', 'updater']) : $query->select( 'id', 'uuid', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function equipment_asset_type_update($data, $id){
        DB::beginTransaction();

        try {
            //Create a new Asset Type
            $query = AssetType::where('uuid', '=', $id)->first();

            if (!$query) {DB::rollback();
                $this->log_user_activity('Asset Type Updated', $id, false);
                return 'Asset Type not found.';
            }
            
            $query->name = $data['name'];
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            $this->log_user_activity('Asset Type Updated', $id, true);
            DB::commit();
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Asset Type Updated', $id, false);
            return response()->json(['error' => 'An error occurred while creating the asset type.'], 500);
        }
    }
}