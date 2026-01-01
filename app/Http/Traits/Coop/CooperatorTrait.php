<?php
namespace App\Http\Traits\Coop;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Coop\Cooperator;
use App\Models\Coop\LoanAccount;
use App\Models\Coop\Payout;
use App\Models\Coop\SavingAccount;
use App\Models\Coop\Share;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

trait CooperatorTrait {
    use FileManagerTrait, LogTrait, UserTrait;

    private function coop_cooperator_generate_unique_id($branch_id = null){
        $branch = $branch_id ?? auth('api')->user()->branch_id;
        $unique_id = 'COOP-' . date('Y') . '-' . $branch . '-' . Str::random(5);
        return $unique_id;
    }
    
    public function coop_cooperator_create($data){
        DB::beginTransaction();
        try {
            $cooperator = Cooperator::create([
                'uuid' => Str::uuid(),
                'unique_id' => $data['unique_id'] ?? $this->coop_cooperator_generate_unique_id($data['branch_id'] ?? null),
                'user_id' => $data['user_id'] ?? auth('api')->id(), 
                'status_date' => date('Y-m-d H:i:s'),
                'status' => 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);

            //$cooperator->user_id = $data['user_id'] ?? $user->id; 
            $cooperator_account = (isset($data['account']) && (!is_null($data['account']))) ? $this->coop_saving_account_create() :$this->coop_saving_account_create([
                'account_type_id' => $data['account_type_id'] ?? null,
                'cooperator_id' => $cooperator->uuid,
                'balance' => 0.00,
                'fixed' =>  0.00,
                'unconfirmed' => 0.00,
                'name' => 'Saving Contributions',
                'target' => null,
            ]);

            return $cooperator;
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        
    }

    public function coop_cooperator_deactivate($id){
        DB::beginTransaction();
        try {
            $cooperator = Cooperator::where('uuid', $id)->firstOrFail();

            //Get all active loan accounts for the cooperator
            $cooperator_loan_sum = LoanAccount::where('cooperator_id', '=', $id)->where('status', '1')->sum('balance');
            //->sum(DB::raw('balance - adminCommision'))->get();

            //Get all active saving accounts for the cooperator
            $cooperator_saving_sum = SavingAccount::where('cooperator_id', '=', $id)->where('status', '1')->sum('balance');
            //Get all active shares for the cooperator
            $cooperator_shares_sum = Share::where('cooperator_id', '=', $id)->where('status', '1')->sum('balance');
            
            // If Total Loan Amount > Total Saving Amount, then do not deactivate the cooperator
            if ($cooperator_loan_sum > ($cooperator_saving_sum + $cooperator_shares_sum)) {
                //Return an error message stating the user needs to pay the loan amount off first
                return 'You need to pay off your loan before deactivating your account.';
            } 
            elseif ($cooperator_loan_sum == ($cooperator_saving_sum + $cooperator_shares_sum)) {
                //No payout is created
            } 
            elseif ($cooperator_loan_sum < ($cooperator_saving_sum + $cooperator_shares_sum)) {
                //Create a Payout request for the cooperator
                Payout::create([
                   'uuid' => Str::uuid(),
                   'cooperator_id' => $id,
                   'amount' => ($cooperator_saving_sum + $cooperator_shares_sum) - $cooperator_loan_sum,
                   'status' => 1,
                ]);
            }
            
            //Deactivate User also if the SAAS model is one off
            $user = User::where('id', $cooperator->user_id)->firstOrFail();
            $this->ums_user_remove_role($user->id, 'cooperator');
            $this->ums_user_remove_role($user->id, 'Cooperator Admin');
            

            //Deactivate the cooperator
            $cooperator->status = 0;
            $cooperator->deleted_by = auth('api')->id() ?? Auth::id();
            $cooperator->deleted_at = date('Y-m-d H:i:s');
            $cooperator->save();

            DB::commit();    
            $this->log_user_activity('Coopearive Account Updated'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $id, false);
            
            //Send a mail informing the user that his cooperator rights have been revoked and the possible payout he can get 
            
            return $cooperator;
        } catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Coopearive Account Updated'/*, 'Created a new account with ID: ' . $account->uuid, auth('api')->id()*/, $id, false);
            return $e->getMessage();
        }
        
    }


    public function coop_cooperator_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = Cooperator::where('status', '1')->orderBy('created_at', 'DESC');
            break;
            case 'inactive':
                $query = Cooperator::where('status', '0')->orderBy('created_at', 'DESC');
            break;
            case 'all':
                $query = Cooperator::orderBy('created_at', 'DESC');
            break;
            default:
                $query = Cooperator::orderBy('created_at', 'DESC');
            break;
        }

        $query = $detailed ? $query->with(['user']) : $query->select('id', 'user_id');

        $query = $paginated ? $query->paginate(52) : $query->get();

        return $query;
    }

    public function coop_cooperator_get_by_id($type, $id, $viewer){}

    public function coop_cooperator_update($data, $id){}

}