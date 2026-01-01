<?php
namespace App\Http\Traits\Ums;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Mail\Ums\CompleteRegistrationMail;
use App\Mail\Ums\OtpMail;
use App\Mail\Ums\PasswordResetConfirmMail;
use App\Models\Ums\Company;
use App\Models\Ums\CompanyShareholder;
use App\Models\Ums\UserOTP;
use App\Models\User;

use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

trait CompanyTrait{
    use FileManagerTrait, LogTrait;

    private function company_uuid_generate($type){
        $uuid = Str::uuid();
        switch ($type){
            case 'company':
                $query = Company::where('uuid', '=', $uuid)->first();
                if($query){
                    return $this->company_uuid_generate($type);
                }
                else{return $uuid;}
            break;
            case 'shareholder':
                $query = CompanyShareholder::where('uuid', '=', $uuid)->first();
                if($query){
                    return $this->company_uuid_generate($type);
                }
                else{return $uuid;}
            break;
        }
    }
    public function ums_company_approve($data, $id){
        DB::beginTransaction();
        try{
            $query = Company::where('uuid', '=', $id)->firstOrFail();
            $query->status = $data['status'] ?? 0;
            $query->status_by = Auth::id() ?? auth('api')->id();
            $query->status_description = $data['status_description'] ?? null;
            $query->status_at = date('Y-m-d H:i:s');
            $query->save();
            DB::commit();
            $this->log_user_activity('User Company Confirmed', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Confirmed', $id, false);
            return $e->getMessage();
        }
    }

    public function ums_company_confirm_item($item, $action, $id){
        DB::beginTransaction();
        try{
            $query = Company::where('uuid', '=', $id)->firstOrFail();
            if ($item == 'cac_certificate'){
                $query->cac_certificate_confirmed = $action;
            }
            elseif ($item == 'address'){
                $query->address_confirmed = $action;
            }
            elseif ($item == 'mermart_form'){
                $query->memart_confirmed = $action;
            }
            else{
                return null;
            }
            
            $query->save();
            DB::commit();
            $this->log_user_activity('User Company Item Confirmed', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Item Confirmed', $id, false);
            return $e->getMessage();
        }
    }
    
    public function ums_company_create($data){
        DB::beginTransaction();
        try{
            $query = Company::create([
                'uuid' => $this->company_uuid_generate('company'),
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'registration_type' => $data['registration_type'],
                'cac_number' => $data['cac_number'],
                'cac_certificate' => $this->file_upload($data['files']['cac_certificate'], $data['files']['cac_certificate_type'], 'uploads/companies', 'CAC', true),//$this->file_upload_to_location($data['cac_certificate'], 'file', 'img/company/cac/', null),
                'address' => $data['address'],
                'proof_of_address' => $this->file_upload($data['files']['proof_of_address'], $data['files']['proof_of_address_type'], 'uploads/companies', 'POA', true),
                'mermart_form' => $this->file_upload($data['files']['memart_form'], $data['files']['memart_form_type'], 'uploads/companies', 'MEM', true),
                'public_key' => $data['public_key'],
                'private_key' => $data['private_key'],
                'status' => 0,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            foreach ($data['shareholders'] as $shareholder) {
                $shareholder['company_id'] = $query->uuid;
                $this->ums_company_shareholder_create($shareholder, $shareholder['uuid'] ?? null);
            }
            DB::commit();
            $this->log_user_activity('User Company Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Created', null, false);
            return $e->getMessage();
        }
    }

    public function ums_company_delete_by($type, $id){
        DB::beginTransaction();
        try{
            $query = Company::where($type, '=', $id)->firstOrFail();
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->status = 10; //i.e. deleted
            $query->save();
            DB::commit();
            $this->log_user_activity('User Company Deleted', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Deleted', null, false);
            return $e->getMessage();
        }
    }

    public function ums_company_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Company::where('status', '=', 0);
            break;
            case 'status':
                $query = Company::where('status', '=', $specific);
            break;
            case 'user':
                $query = Company::where('user_id', '=', $specific)->where('status', '=', 0);
            break;
            case 'uuid':
                $query = Company::where('uuid', '=', $specific)->where('status', '=', 0);
            break;
            default:
                return null;
        }
        $query = $detailed ? $query->with(['owner', 'shareholders']) : $query;
        $query = $query->orderBy('name', 'asc');
        $query = $paginated ? $query->paginate(32) : $query->get();
        
        return $query;
        
    }

    public function ums_company_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = Company::where('id', '=', $id)->where('status', '=', 0);
            break;
            case 'uuid':
                $query = Company::where('uuid', '=', $id)->where('status', '=', 0);
            break;
        }
        $query = $detailed ? $query->with(['owner', 'shareholders']) : $query;
        return $query->first();
    }


    public function ums_company_update($data, $id){
        DB::beginTransaction();
        try{
            $query = Company::where('uuid', '=', $id)->firstOrFail();
            $query->update($data);
            
            if(isset($data['cac_certificate'])){
                $query->cac_certificate = $this->file_upload_to_location($data['cac_certificate'], 'file', 'img/company/cac/', null);
            }
            if(isset($data['proof_of_address'])){
                $query->proof_of_address = $this->file_upload_to_location($data['proof_of_address'], 'file', 'img/company/address/', null);
            }
            if(isset($data['mermart_form'])){
                $query->mermart_form = $this->file_upload_to_location($data['mermart_form'], 'file', 'img/company/mermart/', null);
            }

            if (isset($data['sharehoders']) && is_array($data['sharehoders'])) {
                foreach ($data['sharehoders'] as $shareholder) {
                    $query->shareholders()->updateOrCreate(
                        ['uuid' => $shareholder['uuid']],
                        [
                            'name' => $shareholder['name'],
                            'company_id' => $id,
                            'bvn' => $shareholder['bvn'],
                            'id_card_type' => $shareholder['id_card_type'],
                            'id_card' => $shareholder['id_card'],
                            'status' => 0,
                        ]
                    );
                }
            }

            DB::commit();
            $this->log_user_activity('User Company Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Created', null, false);
            return $e->getMessage();
        }
    }

    
    public function ums_company_shareholder_create($data, $uuid = null){
        DB::beginTransaction();
        try{
            if ($uuid === null){
                $uuid = $this->company_uuid_generate('shareholder');
            }
            $query = CompanyShareholder::updateOrCreate(
                ['uuid' => $uuid],
                [
                    'name' => $data['name'],
                    'company_id' => $data['company_id'],
                    'bvn' => $data['bvn'],
                    'id_card_type' => $data['id_card_type'],
                    'id_card' => $this->file_upload_to_location($data['id_card'], 'file', 'img/company/address/', null) ,
                    'status' => 0,
                ]
            );
            
            DB::commit();
            $this->log_user_activity('User Company Shareholder Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Company Shareholder Created', null, false);
            return $e->getMessage();
        }
    }
}