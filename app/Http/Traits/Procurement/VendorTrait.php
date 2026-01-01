<?php
namespace App\Http\Traits\Procurement;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Procurement\Vendor;
use App\Models\Procurement\VendorAccount;
use App\Models\Procurement\VendorCategory;
use App\Models\Procurement\VendorContactPerson;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait VendorTrait {
    use FileManagerTrait, LogTrait;

    public function procurement_vendor_create($data){
        DB::beginTransaction();

        try{
            $query = Vendor::create([
                'name' => $data['name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'category_id' => $data['category_id'],
                'description' => $data['description'],
                'tin' => $data['tin'] ?? null,
                'vatable' => $data['vatable'] ?? 1,
                'website' => $data['website'] ?? null,
                'withholding_tax' => $data['withholding_tax'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Procurement Vendor Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_delete($id){
        DB::beginTransaction();

        try{
            $query = Vendor::find($id);

            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s'); 
            $query->save();
            
            $this->log_user_activity('Procurement Vendor Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Delete', null, false);
            return $e->getMessage();
        }
    }
    public function procurement_vendor_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Vendor::withTrashed();
            break;
            case 'active':
                $query = Vendor::where('status', '=', 1);
            break;
            case 'inactive':
                $query = Vendor::where('status', '!=', 1)->withTrashed();
            break;
            case 'search':
                $query = Vendor::where('name', 'LIKE', "%$specific%")->withTrashed();
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->select('id', 'name', 'phone', 'email', 'address', 'category_id')->with(['category']) : $query->select('id', 'name', 'category_id');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function procurement_vendor_get_single($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = Vendor::where('id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->select('id', 'name', 'phone', 'email', 'address', 'category_id', 'description', 'tin', 'vatable', 'website', 'withholding_tax')->with(['category']) : $query->select('id', 'name');
        
        return $query->first();
    }

    public function procurement_vendor_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Vendor::find($id);

            $query->name            = $data['name'];
            $query->address         = $data['address'] ?? null;
            $query->phone           = $data['phone'] ?? null;
            $query->email           = $data['email'] ?? null;
            $query->category_id     = $data['category_id'] ?? null;
            $query->description     = $data['description'] ?? null;
            $query->tin             = $data['tin'] ?? null;
            $query->vatable         = $data['vatable'] ?? 1;
            $query->website         = $data['website'] ?? null;
            $query->withholding_tax = $data['withholding_tax'] ?? null;
            $query->status          = $data['status'] ?? 1;
            $query->updated_by      = auth('api')->id() ?? Auth::id();

            $query->save();
            
            $this->log_user_activity('Procurement Vendor Update', $query->id, true); 

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Update', null, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------------------------------
    Vendor Account 
    ----------------------------------------------------------------------------
    */
        public function procurement_vendor_account_create($data){
        DB::beginTransaction();

        try{
            $query = VendorAccount::create([
                'vendor_id' => $data['vendor_id'],
                'bank_id' => $data['bank_id'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'status' => $data['status'] ?? 1,
            ]);
            $this->log_user_activity('Procurement Vendor Account Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Account Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_account_delete($id){
        DB::beginTransaction();

        try{
            $query = VendorAccount::findOrFail($id);
            
            $query->status = 0;
            $query->deleted_at = date('Y-m-d H:i:s');
            
            $query->save();
            $this->log_user_activity('Procurement Vendor Account Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Account Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_account_get_all($type, $specific, $detailed, $paginated, $page){
        $query = VendorAccount::query();
        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '!=', 1)->withTrashed();
            break;
        }

        if($specific !== null && is_array($specific)){
            if(!empty($specific['vendor'])){
                $query = $query->where('vendor_id', '=', $specific['vendor_id']);
            }
        }
        
        $query = $detailed ? $query->select('id', 'vendor_id', 'bank_id', 'account_name', 'account_number', 'status')->with(['bank', 'vendor']) : $query->select('id', 'bank_id', 'account_number')->with(['bank', 'vendor']);
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;

    }

    public function procurement_vendor_account_get_by($type, $id, $detailed){
        try{
            $query = VendorAccount::where('id', '=', $id);
                
            $query = $detailed ? $query->select('id', 'vendor_id', 'bank_id', 'account_name', 'account_number', 'status')->with(['bank', 'vendor']) : $query->select('id', 'bank_id', 'account_number')->with(['bank']) ;
            
            return $query->get();
        }
        catch(Exception $e){
            return $e->getMessage();
        }

    }
    
    public function procurement_vendor_account_update($data, $id){
        DB::beginTransaction();

        try{
            $query = VendorAccount::findOrFail($id);
            
            $query->vendor_id = $data['vendor_id'];
            $query->bank_id = $data['bank_id'] ?? $query->bank_id;
            $query->account_name = $data['account_name'] ?? $query->account_name;
            $query->account_number = $data['account_number'] ?? $query->account_number;
            $query->status = $data['status'] ?? 1;

            
            $query->save();
            $this->log_user_activity('Procurement Vendor Account Update', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Account Update', null, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------------------------------------------
    Vendor Category Traits
    ----------------------------------------------------------------------------------------
    */
    public function procurement_vendor_category_create($data){
        DB::beginTransaction();

        try{
            $query = VendorCategory::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_user_activity('Procurement Vendor Category Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Category Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_category_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = VendorCategory::withTrashed();
            break;
            case 'active':
                $query = VendorCategory::where('status', '=', 1);
            break;
            case 'inactive':
                $query = VendorCategory::where('status', '!=', 1)->withTrashed();
            break;
        }

        $query = $detailed ? $query->select('id', 'name', 'description', 'status') : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function procurement_vendor_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = VendorCategory::find($id);
            
            $query->name = $data['name'];
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
            $this->log_user_activity('Procurement Vendor Category Update', $id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Category Update', $id, false);
            return $e->getMessage();
        } 
    }
    /*
    ----------------------------------------------------------------------------
    Vendor Contact 
    ----------------------------------------------------------------------------
    */
    public function procurement_vendor_contact_create($data){
        DB::beginTransaction();

        try{
            $query = VendorContactPerson::create([
                'vendor_id' => $data['vendor_id'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'title' => $data['title'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'alt_phone' => $data['alt_phone'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_user_activity('Procurement Vendor Contact Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Contact Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_contact_delete($id){
        DB::beginTransaction();

        try{
            $query = VendorContactPerson::find($id);
            
            $query->status = 0;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->updated_at = date('Y-m-d H:i:s');
            
            $query->save();
            $this->log_user_activity('Procurement Vendor Contact Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Contact Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_vendor_contact_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = VendorContactPerson::withTrashed();
            break;
            case 'active':
                $query = VendorContactPerson::where('status', '=', 1);
            break;
            case 'inactive':
                $query = VendorContactPerson::where('status', '!=', 1)->withTrashed();
            break;
            case 'search':
                $query = VendorContactPerson::where('first_name', 'LIKE', "%$specific%")->orWhere('last_name', 'LIKE', "%$specific%")->withTrashed();
            break;
        }

        $query = $detailed ? $query->select('id', 'vendor_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status')->with(['vendor']) : $query->select('id', 'first_name', 'last_name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;

    }

    public function procurement_vendor_contact_get_by($type, $specific, $detailed){
        switch($type){
            case 'vendor':
                $query = VendorContactPerson::where('vendor_id', '=', $specific);
            break;
        }

        $query = $query->select('id', 'vendor_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status')->with(['vendor']);
        
        return $query->get();
    }
    
    public function procurement_vendor_contact_update($data, $id){
        DB::beginTransaction();

        try{
            $query = VendorContactPerson::find($id);
            
            $query->vendor_id = $data['vendor_id'];
            $query->first_name = $data['first_name'];
            $query->last_name = $data['last_name'];
            $query->title = $data['title'];
            $query->email = $data['email'];
            $query->phone = $data['phone'];
            $query->alt_phone = $data['alt_phone'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();
            $this->log_user_activity('Procurement Vendor Contact Update', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Vendor Contact Update', null, false);
            return $e->getMessage();
        }
    }
}