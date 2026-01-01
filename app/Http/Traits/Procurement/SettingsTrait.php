<?php
namespace App\Http\Traits\Procurement;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Inventory\PurchaseOrder;
use App\Models\Procurement\PackageType;
use App\Models\Procurement\PaymentTerm;
use App\Models\Procurement\Vendor;
use App\Models\Procurement\VendorCategory;
use App\Models\Procurement\VendorContactPerson;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


trait SettingsTrait {
    use FileManagerTrait, LogTrait;

    /*
    ---------------------------------------------------------------------------
    Procurement Settings Basic Package Types Functions
    
    ---------------------------------------------------------------------------
    */
    public function procurement_settings_package_type_create($data){
        DB::beginTransaction();

        try{
            $payment_term = PackageType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Procurement Package Type Create', $payment_term->id, true); 
            DB::commit();
            return $payment_term;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Package Type Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_settings_package_type_delete($id){
        DB::beginTransaction();

        try{
            $query = PackageType::find($id);
            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();
            $this->log_user_activity('Procurement Package Type Delete', $id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Package Type Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_settings_package_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = PackageType::where('status', '=', 1);
                break;
            case 'inactive':
                $query = PackageType::where('status', '!=', 0);
                break;
            case 'search':
                $query = PackageType::where('name', 'like', '%'.$specific.'%');
                break;
            case 'status':
                $query = PackageType::where('status', '=', $specific);
                break;
            default:
                $query = PackageType::where('status', 1);
            break;
        }

        $query = $detailed ? $query->with('creator', 'updater', 'deleter') : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function procurement_settings_package_type_get_by($type, $specific, $detailed){
        $query = PackageType::where($type, '=', $specific);
        $query = $detailed ? $query->with('creator', 'updater', 'deleter') : $query->select('id', 'name');
        $query = $query->first();

        return $query;
    }

    public function procurement_settings_package_type_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = PackageType::find($id);

            $query->name = $data['name'];
            $query->description = $data['description'] ?? null;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Package Type Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ---------------------------------------------------------------------------
    Procurement Settings 
    1. Payment Terms
    
    ---------------------------------------------------------------------------
    */
    public function procurement_settings_payment_term_create($data){
        DB::beginTransaction();

        try{
            $payment_term = PaymentTerm::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Procurement Payment Term Create', $payment_term->id, true); 
            DB::commit();
            return $payment_term;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Payment Term Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_settings_payment_term_delete($id){
        DB::beginTransaction();

        try{
            $query = PaymentTerm::find($id);
            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();
            $this->log_user_activity('Procurement Payment Term Delete', $id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Payment Term Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_settings_payment_term_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = PaymentTerm::where('status', '=', 1);
                break;
            case 'inactive':
                $query = PaymentTerm::where('status', '!=', 0);
                break;
            case 'search':
                $query = PaymentTerm::where('name', 'like', '%'.$specific.'%');
                break;
            case 'status':
                $query = PaymentTerm::where('status', '=', $specific);
                break;
            default:
                $query = PaymentTerm::where('status', 1);
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with('creator', 'updater', 'deleter') : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;
    }

    public function procurement_settings_payment_term_get_by($type, $specific, $detailed){
        $query = PaymentTerm::where($type, '=', $specific);
        $query = $detailed ? $query->with('creator', 'updater', 'deleter') : $query->select('id', 'name');
        $query = $query->first();

        return $query;
    }

    public function procurement_settings_payment_term_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = PaymentTerm::find($id);

            $query->name = $data['name'];
            $query->description = $data['description'] ?? null;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Payment Term Update', $id, false);
            return $e->getMessage();
        }
    }
}