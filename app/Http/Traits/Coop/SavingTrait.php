<?php
namespace App\Http\Traits\Coop;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;

use App\Models\Coop\SavingAccount;
use App\Models\Coop\SavingAccountType;
use App\Models\Coop\Cooperator;
use App\Models\Coop\Guarantor;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
trait SavingTrait {

    use FileManagerTrait, LogTrait;

    //Savings Account Basic Functions
    public function coop_saving_account_create($data){
        DB::beginTransaction();
        try {
            $cooperator = Cooperator::where('user_id', '=', auth('api')->id())->first();
            $account = SavingAccount::create([
                'uuid' => Str::uuid(),
                'account_type_id' => $data['account_type_id'],
                'cooperator_id' => $data['cooperator_id'] ?? $cooperator->uuid,
                'balance' => $data['balance'] ?? 0.00,
                'fixed' =>  $data['fixed'] ?? 0.00,
                'unconfirmed' => $data['unconfirmed'] ?? 0.00,
                'name' => $data['name'],
                'target' => $data['target'],
                'status' => 1,
                'status_date' => date('Y-m-d H:i:s'),
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('Coopearive Account Created'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $account->id, true);
            return $account;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Coopearive Account Created'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, null, false);
            return $e->getMessage();
        }
        
    }

    public function coop_saving_account_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = SavingAccount::where('status', '1')->orderBy('created_at', 'DESC');
            break;
            case 'all':
                $query = SavingAccount::orderBy('created_at', 'DESC');
            break;
            case 'cooperator':
                $query = SavingAccount::where('user_id', '=', $specific)->orderBy('created_at', 'DESC');
            break;
            case 'inactive':
                $query = SavingAccount::where('status', '0')->orderBy('created_at', 'DESC');
            break;
            case 'mine':
                $cooperator_id = Cooperator::where('user_id', '=', auth('api')->id())->first()->uuid;
                $query = SavingAccount::where('cooperator_id', '=', $cooperator_id)->orderBy('created_at', 'DESC');
            break;
            default:
                $query = SavingAccount::orderBy('created_at', 'DESC');
            break;
        }

        $query = $detailed ? $query->with(['branch', 'contributions','cooperator.user']) : $query->select('uuid', 'name', 'cooperator');

        $query = $paginated ? $query->paginate(24) : $query->get();

        return $query;
    }

    public function coop_saving_account_get_by($type, $id, $viewer, $detailed){
        switch($type){
            case 'id':
                $query = SavingAccount::where('id', '=', $id);
            break;
            case 'uuid':
                $query = SavingAccount::where('uuid', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['cooperator']) : $query->select('id', 'user_id');
        
        return $query->first();
    }

    public function coop_saving_account_update($data, $id){
        DB::beginTransaction();
        try {
            $cooperator = Cooperator::where('user_id', '=', auth('api')->id())->first();
            $account = SavingAccount::where('uuid', '=', $id)->first();
            if (!$account) {
                $this->log_user_activity('Coopearive Account Updated'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $id, false);
                return new Exception('Account not found');
            }

            $account->uuid = Str::uuid();
            $account->account_type_id = $data['account_type_id'];
            $account->cooperator_id = $data['cooperator_id'] ?? $cooperator->uuid;
            $account->balance = $data['balance'] ?? 0.00;
            $account->fixed =  $data['fixed'] ?? 0.00;
            $account->unconfirmed = $data['unconfirmed'] ?? 0.00;
            $account->name = $data['name'];
            $account->target = $data['target'];
            $account->status = $data['status'] ?? 1;
            $account->status_date = date('Y-m-d H:i:s');
            $account->updated_by = auth('api')->id();
            
            $account->save();
            
            DB::commit();
            $this->log_user_activity('Coopearive Account Updated'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $account->id, true);
            return $account;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Coopearive Account Updated'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, null, false);
            return $e->getMessage();
        }
        
    }

    /*
    -------------------------------------------------------------------
    Savings Account Basic Functions
    -------------------------------------------------------------------
    */

    public function coop_saving_account_type_create($data){
        DB::beginTransaction();
        try {
            $account_type = SavingAccountType::create([
                'uuid' => Str::uuid(),
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'interest_rate' => $data['interest_rate'] ?? 0.00,
                'status' => 1,
                'status_date' => date('Y-m-d H:i:s'),
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('Coopearive Account Type Created'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $account_type->id, true);
            return $account_type;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Coopearive Account Type Created'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, null, false);
            return $e->getMessage();
        }
    }

    public function coop_saving_account_type_deactivte($id){}

    public function coop_saving_account_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = SavingAccountType::where('status', '=', '1')->orderBy('created_at', 'DESC');
            break;
            case 'all':
                $query = SavingAccountType::orderBy('created_at', 'DESC')->withTrashed();
            break;
            case 'inactive':
                $query = SavingAccountType::where('status', '=', '0')->orderBy('created_at', 'DESC');
            break;
            default:
                $query = SavingAccountType::orderBy('created_at', 'DESC');
            break;
        }

        $query = $detailed ? $query->with(['cooperator']) : $query->select('id', 'user_id');

        $query = $paginated ? $query->paginate(52) : $query->get();

        return $query;
    }

    public function coop_saving_account_type_get_by($type, $id, $viewer, $detailed){
        switch($type){
            case 'id':
                $query = SavingAccountType::where('id', '=', $id);
            break;
            case 'uuid':
                $query = SavingAccountType::where('uuid', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['accounts', 'creater', 'updater']) : $query->select('name', 'uuid_id');
        
        return $query->first();
    }

    public function coop_saving_account_type_update($data, $id){
        DB::beginTransaction();
        try {
            $account_type = SavingAccountType::where('uuid', '=', $id)->first();
            if (!$account_type) {
                $this->log_user_activity('Coopearive Account Type Updated', $id, false);
                return new Exception('Account Type not found');
            }

            $account_type->uuid = Str::uuid();
            $account_type->name = $data['name'];
            $account_type->description = $data['description'] ?? null;
            $account_type->interest_rate = $data['interest_rate'] ?? 0.00;
            $account_type->status = $data['status'] ?? 1;
            $account_type->status_date = date('Y-m-d H:i:s');
            $account_type->updated_by = auth('api')->id();
            
            $account_type->save();
            
            DB::commit();
            $this->log_user_activity('Coopearive Account Type Updated', $account_type->id, true);
            return $account_type;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Coopearive Account Type Updated', null, false);
            return $e->getMessage();
        }
    }
}