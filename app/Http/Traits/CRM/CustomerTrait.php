<?php
namespace App\Http\Traits\CRM;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\CRM\Customer;
use App\Models\CRM\CustomerCategory;
use App\Models\CRM\CustomerContactPerson;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


trait CustomerTrait {
    use FileManagerTrait, LogTrait;
    
    protected function generate_unique_id($type){
        //return uniqid($type . '_');
        $code = Str::uuid()->toString();
        switch($type){
            case 'contact':
                $prefix = 'CNT';   
                $query = CustomerContactPerson::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->generate_unique_id('contact');
                }else{
                    return $code;
                }
            case 'customer':
                $prefix = 'CST';
                $query = Customer::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->generate_unique_id('contact');
                }else{
                    return $code;
                }   
        }
    }

    /*
    ----------------------------------------------------------------------------------------
    Customer Traits
    ----------------------------------------------------------------------------------------
    */
    
    public function crm_customer_create($data){
        DB::beginTransaction();

        try{
            $query = Customer::create([
                'uuid' => $this->generate_unique_id('customer'),
                'name' => $data['name'],
                'address' => $data['address'],
                'balance' => $data['balance'] ?? 0.00,
                'phone' => $data['phone'],
                'email' => $data['email'],
                'category_id' => $data['category_id'],
                'delivery_address' => $data['delivery_address'] ?? NULL,
                'description' => $data['description'] ?? NULL,
                'tin' => $data['tin'] ?? NULL,
                'vatable' => $data['vatable'] ?? 0,
                'website' => $data['website'] ?? NULL,
                'withholding_tax' => $data['withholding_tax'] ?? NULL,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('CMS Customer Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Create', null, false);
            return $e->getMessage();
        }
    }

    public function crm_customer_delete($id){
        DB::beginTransaction();

        try{
            $query = Customer::where('id', '=', $id)->orWhere('uuid', '=', $id)->firstOrFail();

            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s'); 
            $query->save();
            
            $this->log_user_activity('CMS Customer Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Delete', null, false);
            return $e->getMessage();
        }
    }
    public function crm_customer_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Customer::withTrashed();
            break;
            case 'active':
                $query = Customer::where('status', '=', 1);
            break;
            case 'inactive':
                $query = Customer::where('status', '!=', 1)->withTrashed();
            break;
            case 'quick_search':
                $query = Customer::where('name', 'LIKE', "%$specific%");
            break;
            case 'search':
                $query = Customer::where('name', 'LIKE', "%$specific%")->withTrashed();
            break;
            case 'this_month':
                $query = Customer::whereDate('created_at', '>=', date('Y-m-').'01');
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['category']) : $query->select('id', 'name', 'category_id', 'uuid');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function crm_customer_get_by($type, $specific, $detailed){
        try{
            $query = Customer::query();
            switch($type){
                case 'id':
                    $query = $query->where('id', '=', $specific);
                break;
                default:
                    $query = $query->where('id', '=', $specific)->orWhere('uuid', '=', $specific);
            }

            $query = $detailed ? $query->select('id', 'name', 'phone', 'email', 'address', 'category_id', 'description', 'tin', 'vatable', 'website', 'withholding_tax')->with(['category']) : $query->select('id', 'name', 'balance');
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function crm_customer_search($query, $detailed, $paginated, $page){
        $query = Customer::where('name', 'LIKE', '%$query%');

        $query = $detailed ? $query->select('id', 'name', 'phone', 'email', 'address', 'category_id')->with(['category']) : $query->select('id', 'name', 'category_id');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function crm_customer_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Customer::find($id);

            $query->name            = $data['name'] ?? $query->name;
            $query->address         = $data['address'] ?? $query->address;
            $query->phone           = $data['phone'] ?? $query->phone;
            $query->email           = $data['email'] ?? $query->email;
            $query->category_id     = $data['category_id'] ?? $query->category_id;
            $query->description     = $data['description'] ?? $query->description;
            $query->delivery_address = $data['delivery_address'] ?? $query->delivery_address;
            $query->tin             = $data['tin'] ?? $query->tin;
            $query->vatable         = $data['vatable']  ?? $query->vatable;
            $query->website         = $data['website']  ?? $query->website;
            $query->withholding_tax = $data['withholding_tax'] ?? $query->withholding_tax;
            $query->status          = $data['status'] ?? $query->status;
            $query->updated_by      = auth('api')->id() ?? Auth::id();

            $query->save();
            
            $this->log_user_activity('CMS Customer Update', $query->id, true); 

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Update', null, false);
            return $e->getMessage();
        }
    }

    public function crm_customer_update_balance($customer_id, $amount){
        try{
            $customer = Customer::findOrFail($customer_id);
            $customer->balance += $amount;
            $customer->updated_by = auth('api')->id();
            $customer->save();
            
            $this->log_user_activity('CMS Customer Balance Updated', ['customer_id' => $customer->id, 'amount' => $amount], true);

            return $customer;

        } 
        catch (Exception $e) {
            $this->log_user_activity('CMS Customer Balance Updated', ['customer_id' => $customer->id, 'amount' => $amount], true);
            return $e->getMessage();
        }
    }


    /*
    ----------------------------------------------------------------------------------------
    Customer Category Traits
    ----------------------------------------------------------------------------------------
    */
    public function crm_customer_category_create($data){
        DB::beginTransaction();

        try{
            $query = CustomerCategory::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_user_activity('CMS Customer Category Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Category Create', null, false);
            return $e->getMessage();
        }
    }

    public function crm_customer_category_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = CustomerCategory::withTrashed();
            break;
            case 'active':
                $query = CustomerCategory::where('status', '=', 1);
            break;
            case 'inactive':
                $query = CustomerCategory::where('status', '!=', 1)->withTrashed();
            break;
        }

        $query = $detailed ? $query->select('id', 'name', 'description', 'status') : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function crm_customer_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = CustomerCategory::find($id);
            
            $query->name = $data['name'];
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
            $this->log_user_activity('CMS Customer Category Update', $id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Category Update', $id, false);
            return $e->getMessage();
        } 
    }
    /*
    ----------------------------------------------------------------------------
    Customer Contact 
    ----------------------------------------------------------------------------
    */
    public function crm_customer_contact_create($data){
        DB::beginTransaction();

        try{
            $query = CustomerContactPerson::create([
                'customer_id' => $data['customer_id'],
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
            $this->log_user_activity('CMS Customer Contact Create', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Contact Create', null, false);
            return $e->getMessage();
        }
    }

    public function crm_customer_contact_delete($id){
        DB::beginTransaction();

        try{
            $query = CustomerContactPerson::find($id);
            
            $query->status = 0;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->updated_at = date('Y-m-d H:i:s');
            
            $query->save();
            $this->log_user_activity('CMS Customer Contact Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Contact Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function crm_customer_contact_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = CustomerContactPerson::withTrashed();
            break;
            case 'active':
                $query = CustomerContactPerson::where('status', '=', 1);
            break;
            case 'customer':
                $query = CustomerContactPerson::where('customer_id', '=', $specific);
            break;
            case 'inactive':
                $query = CustomerContactPerson::where('status', '!=', 1)->withTrashed();
            break;
            case 'search':
                $query = CustomerContactPerson::where('first_name', 'LIKE', "%$specific%")->orWhere('last_name', 'LIKE', "%$specific%")->withTrashed();
            break;
        }

        $query = $detailed ? $query->select('id', 'customer_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status')->with(['customer']) : $query->select('id',  'first_name', 'last_name');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;

    }

    public function crm_customer_contact_get_by($type, $specific, $detailed){
        switch($type){
            case 'vendor':
                $query = CustomerContactPerson::where('customer_id', '=', $specific);
            break;
        }

        $query = $query->select('id', 'customer_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status')->with(['vendor']);
        
        return $query->get();
    }
    
    public function crm_customer_contact_update($data, $id){
        DB::beginTransaction();

        try{
            $query = CustomerContactPerson::find($id);
            
            $query->customer_id = $data['customer_id'];
            $query->first_name = $data['first_name'];
            $query->last_name = $data['last_name'];
            $query->title = $data['title'];
            $query->email = $data['email'];
            $query->phone = $data['phone'];
            $query->alt_phone = $data['alt_phone'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();
            $this->log_user_activity('CMS Customer Contact Update', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('CMS Customer Contact Update', null, false);
            return $e->getMessage();
        }
    }
}