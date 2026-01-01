<?php
namespace App\Http\Traits\Coop;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Coop\AllBank;
use App\Models\Coop\BankAccount;
use App\Models\Coop\Guarantor;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
trait SettingsTrait {
    use LogTrait, FileManagerTrait;

    public function coop_settings_banks_get_all($paginated, $page){
        $query = AllBank::orderBy('name', 'ASC');
        return $paginated ? $query->paginate($page) : $query->get();
    }
    public function coop_settings_banks_create($data){
        $bank_account = AllBank::create([
            'uuid' => Str::uuid(), 
            'name' => $data['name'], 
            'status' => 1,
        ]);

        return $bank_account;
    }

    public function coop_settings_bank_account_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = BankAccount::where('status', '1')->orderBy('created_at', 'DESC');
            break;
            case 'inactive':
                $query = BankAccount::where('status', '0')->orderBy('created_at', 'DESC');
            break;
            case 'all':
                $query = BankAccount::orderBy('created_at', 'DESC');
            break;
            case 'cooperator':
                $query = BankAccount::where('user_id', '=', $specific)->orderBy('created_at', 'DESC');
            break;
        }
        if($detailed){
            return ($paginated) ? $query->paginate($page) : $query->get();
        }else{
            return ($paginated) ? $query->paginate($page)->makeHidden(['created_by', 'updated_by']) : $query->get()->makeHidden(['created_by', 'updated_by']);
        }
    }
    public function coop_settings_bank_account_create($data){
        $bank_account = new BankAccount();
        $bank_account->bank_uuid = $data['user_id'] ?? auth('api')->id(); 
        $bank_account->account_number = $data['account_number'] ?? null; 
        $bank_account->account_name = $data['account_name'] ?? null; 
        $bank_account->branch = $data['branch'] ?? null; 
        $bank_account->status_date = date('Y-m-d H:i:s');
        $bank_account->status = 1;
        $bank_account->save();
    }
}