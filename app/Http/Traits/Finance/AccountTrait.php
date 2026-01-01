<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Finance\MainTransaction;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Finance\Account;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalLine;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\Report;
use App\Models\Finance\TopUp;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AccountTrait{
    use LogTrait, MainTransactionTrait;
    public function finance_account_balance_alter($id, $amount, $type){
        DB::beginTransaction();

        try{
            $query = Account::findOrFail($id);

            $query->balance = $type == 'Credit' ? bcadd($query->balance, $amount, 2) : bcsub($query->balance, $amount, 2);
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save(); 
        
            DB::commit();
            $this->log_user_activity('Finance Branch Account Balance Altered', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Finance Branch Account Balance Altered', $id, false);

            return $e->getMessage();
        }         
    }
    public function finance_account_create($data){
        DB::beginTransaction();

        try{
            $query = Account::create([
                'bank_id' => $data['bank_id'],
                'branch_id' => $data['branch_id'] ?? request()->cookie('branch_id'),
                'account_number' => $data['account_number'],
                'account_name' => $data['account_name'],
                'status' => $data['status'] ?? Account::StatusActive,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Finance Branch Account Created', $query->id, true);

            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Finance Branch Account Created', null, false);

            return $e->getMessage();
        }
    }

    public function finance_account_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Account::findOrFail($id);

            $query->status = $query->status == Account::StatusActive ? Account::StatusInactive : Account::StatusActive;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save(); 
        
            DB::commit();
            $this->log_user_activity('Finance Branch Account Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Finance Branch Account Deactivated', $id, false);

            return $e->getMessage();
        } 
    }

    public function finance_account_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Account::query();
            break;
            case 'active':
                $query = Account::where('status', '=', 1);
            break;
            case 'branch':
                $query = Account::where('status', '=', $specific);
            break;
            case 'category':
                $query = Account::where('category', '=', $specific);
            break;
            case 'primary':
                $query = Account::where('is_primary', '=', 1);
            break;
        }

        $query = $detailed ? $query->with(['bank']) : $query;

        $query = $paginated? $query->paginate() : $query->get();

        return $query;
    }

    public function finance_account_get_by($type, $id, $detailed){
        $query = Account::where('unique_id', '=', $id)->orWhere('id', '=', $id);

        $query = $detailed ? $query->with([]) : $query;

        return $query;
    }

    public function finance_account_report($data){
        try{
            $report_type = Report::findOrFail($data['report_type_id']);
            switch($report_type->name){
                case 'Summary':
                    
                    //$query = MainTransaction::select()

                break;
                case 'Detailed':
                    $query = $this->finance_main_transaction_branch_account_balance($data['account_id'], $data['start_date'], $data['end_date'], false);
                break;
            } 

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function finance_account_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Account::where('unique_id', '=', $id)->orWhere('id', '=', $id)->first();
            
            $data['updated_by'] = auth('api')->id() ?? Auth::id();

            $query->update([$data]);

            return $query;
        }
        catch(Exception $e){
            
        }
    }

    public function finance_account_branch_accounts_summary($branches){

    }

}