<?php

namespace App\Http\Traits\Finance;

use App\Models\Finance\BranchBank;
use App\Models\Finance\PaymentMode;
use App\Models\Finance\Bank;
use App\Models\Finance\Income;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\Payment;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Insurance\PlanBranch;
use App\Models\Finance\TopUp;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;
use App\Models\Operations\Branch as OperationBranch;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SettingTrait{
    /*
    -----------------------------------------------------------------------------------
    Finance All Banks Basic Functions
    -----------------------------------------------------------------------------------
    */
    
    public function finance_setting_all_banks_create($data){
        DB::beginTransaction();
        try {
            $query = Bank::create([
                'bank_name'         => $data['bank_name'], 
                'status'            => $data['status'] ?? 1,
            ]);

            DB::commit();
            $this->log_user_activity('All Bank Create', null, false);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('All Bank Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_all_banks_deactivate($id){
        DB::beginTransaction();
        try {
            $query = Bank::findOrFail($id);
            
            $query->status = $query->status == 1 ? 0 : 1;
            $query->save();
            DB::commit();
            $this->log_user_activity('All Bank Deactivate', $id, true);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('All Bank Deactivate', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_all_banks_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Bank::query();

        switch($type){
            case 'active':
                $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query->where('status', '=', 0);
            break;
        }

        $query->orderBy('bank_name', 'asc');
        $query = $paginated ?  $query->paginate(10, ['*'], 'page', $page) : $query->get();
        
        return $query;
    }

    public function finance_setting_all_banks_get_by($id, $detailed){
        $query = Bank::where('id', $id);
        return $query->first();
    }

    public function finance_setting_all_banks_update($data, $id){
        DB::beginTransaction();
        try {
            $query = Bank::findOrFail($id);
            $query->bank_name         = $data['bank_name'];
            $query->status            = $data['status'] ?? 1;
            $query->save();
            DB::commit();
            $this->log_user_activity('All Bank Update', $id, true);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('All Bank Update', null, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------
    Finance Branch Bank Accounts Basic Functions
    -----------------------------------------------------------------------------------
    */

    public function finance_setting_branch_account_create($data){
        DB::beginTransaction();
        try {
            $query = BranchBank::create([
                'bank_id'           => $data['bank_id'], 
                'branch_id'         => $data['branch_id'] ?? request()->cookie('current_branch'), 
                'account_number'    => $data['account_number'], 
                'account_name'      => $data['account_name'], 
                'status'            => $data['status'] ?? 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Branch Bank Create', null, false);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Bank Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_branch_account_deactivate($id){
        DB::beginTransaction();
        try {
            $query = BranchBank::findOrFail($id);
            if ($query) {
                $query->updated_by = Auth::id() ?? auth('api')->id();
                $query->status = $query->status == 1 ? 0 : 1;
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Bank Deactivate', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Bank Deactivate', $id, false);
                return response()->json(['message' => 'Branch Bank not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Bank Deactivate', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_branch_account_delete($id){
        DB::beginTransaction();
        try {
            $query = BranchBank::findOrFail($id);
            if ($query) {
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Bank Delete', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Bank Delete', $id, false);
                return response()->json(['message' => 'Branch Bank not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Bank Delete', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_branch_account_get_all($type, $specific, $detailed, $paginated, $page){
        $query = BranchBank::query();

        switch($type){
            case 'all':
                // No additional conditions needed
            break;
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'branch':
                $user = Auth::user() ?? auth('api')->user();
                $branch_id = request()->cookie('current_branch') ?? $user->branch_id;
                $query->where('branch_id', $specific ?? $branch_id);
            break;
        }

        $query = $detailed ? $query->with(['bank', 'branch', 'creator', 'updater']) : $query->select('id', 'bank_id', 'account_name', 'account_number', 'balance')->with(['bank']);
        $query->orderBy('account_name', 'asc');
        $query = $paginated ?  $query->paginate(10, ['*'], 'page', $page) : $query->get();
        
        return $query;
    }

    public function finance_setting_branch_account_get_by($id, $detailed){
        $query = BranchBank::where('id', $id);
        if ($detailed) {
            $query->with(['bank', 'branch']);
        }
        return $query->first();
    }

    public function finance_setting_branch_account_update($data, $id){
        DB::beginTransaction();
        try {
            $query = BranchBank::find($id);
            if ($query) {
                $query->bank_id           = $data['bank_id'];
                $query->branch_id         = $data['branch_id'] ?? request()->cookie('current_branch'); 
                $query->account_number    = $data['account_number']; 
                $query->account_name      = $data['account_name']; 
                $query->status            = $data['status'] ?? 1;
                $query->updated_by        = Auth::id() ?? auth('api')->id();
                
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Bank Update', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Bank Update', $id, false);
                return response()->json(['message' => 'Branch Bank not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Bank Update', null, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------
    Finance Branch Bank Accounts Basic Functions
    -----------------------------------------------------------------------------------
    */
    

    public function finance_setting_branch_price_list_create($data){
        DB::beginTransaction();
        try {
            $quest = BranchPlanPriceList::where([
                'branch_id'         => $data['branch_id'] ?? request()->cookie('current_branch'), 
                'price_list_id'     => $data['price_list_id'], 
            ])->first();
            if ($quest){
                DB::rollback();
                $this->log_user_activity('Branch Bank Create', null, false);
                return 'Duplicate found';
            }
            $query = BranchPlanPriceList::updateOrCreate([
                'branch_id'         => $data['branch_id'] ?? request()->cookie('current_branch'), 
                'price_list_id'     => $data['price_list_id'], 
            ],[
                'plan_id'           => $data['plan_id'] ?? null, 
                'status'            => $data['status'] ?? 1,
                'description'       => $data['description'] ?? null,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Branch Price List Create', null, false);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Price List Create', null, false);
            return $e->getMessage();
        }
    }

    
    public function finance_setting_price_list_deactivate($id){
        DB::beginTransaction();
        try {
            $query = BranchPlanPriceList::findOrFail($id);
            if ($query) {
                $query->updated_by = Auth::id() ?? auth('api')->id();
                $query->status = $query->status == 1 ? 0 : 1;
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Price List Deactivate', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Price List Deactivate', $id, false);
                return response()->json(['message' => 'Branch Price List not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Price List Deactivate', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_price_list_delete($id){
        DB::beginTransaction();
        try {
            $query = BranchPlanPriceList::findOrFail($id);
            if ($query) {
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Price List Delete', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Price List Delete', $id, false);
                return response()->json(['message' => 'Branch Price List not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Price List Delete', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_price_list_get_all($type, $specific, $detailed, $paginated, $page){
        $query = BranchPlanPriceList::query();

        switch($type){
            case 'all':
                // No additional conditions needed
            break;
            case 'branch':
                $user = Auth::user() ?? auth('api')->user();
                $branch_id = request()->cookie('current_branch') ?? $user->branch_id;
                $query->where('branch_id', $specific ?? $branch_id);
                break;
        }

        $query = $detailed ? $query->with(['bank', 'branch']) : $query;
        $query->orderBy('account_name', 'asc');
        $query = $paginated ?  $query->paginate(10, ['*'], 'page', $page) : $query->get();
        
        return $query;
    }

    public function finance_setting_price_list_get_by($id, $detailed){
        $query = BranchPlanPriceList::where('id', $id);
        if ($detailed) {
            $query->with(['bank', 'branch']);
        }
        return $query->first();
    }

    public function finance_setting_price_list_update($data, $id){
        DB::beginTransaction();
        try {
            $quest = BranchPlanPriceList::where([
                'branch_id'         => $data['branch_id'] ?? request()->cookie('current_branch'), 
                'price_list_id'     => $data['price_list_id'], 
            ])->where('id', '!=', $id)->first();
            
            if ($quest){
                DB::rollback();
                $this->log_user_activity('Branch Price List Create', null, false);
                return 'Duplicate found';
            }
            
            $query = BranchPlanPriceList::findOrFail($id);
            if ($query) {
                $query->branch_id         = $data['branch_id'] ?? request()->cookie('current_branch'); 
                $query->price_list_id     = $data['price_list_id'];
                $query->plan_id           = $data['plan_id'] ?? null; 
                $query->status            = $data['status'] ?? 1;
                $query->description       = $data['description'] ?? null;
                $query->updated_by        = Auth::id() ?? auth('api')->id();
                
                $query->save();
                DB::commit();
                $this->log_user_activity('Branch Price List Update', $id, true);
                return $query;
            } 
            else {
                DB::rollback();
                $this->log_user_activity('Branch Price List Update', $id, false);
                return response()->json(['message' => 'Branch Price List not found'], 404);
            }
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Branch Price List Update', null, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------
    Finance Payment Mode Basic Functions
    ----------------------------------------------------
    */

    public function finance_setting_payment_mode_create($data){
        DB::beginTransaction();
        try {
            $query = PaymentMode::create([
                'name'          => $data['name'], 
                'description'   => $data['description'] ?? null, 
                'status'        => $data['status'] ?? 1,
                'created_by'    => Auth::id() ?? auth('api')->id(),
                'updated_by'    => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Finance Payment Mode Create', null, false);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Finance Payment Mode Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_setting_payment_mode_deactivate($id){
        DB::beginTransaction();
        try {
            $query = PaymentMode::findOrFail($id);
            
            $query->status = !$query->status;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
        
            DB::commit();
            $this->log_user_activity('Finance Payment Mode Deactivate', null, false);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Finance Payment Mode Deactivate', null, false);
            return $e->getMessage();
        }      
    }

    public function finance_setting_payment_mode_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = PaymentMode::query();
                break;
            case 'active':
                $query = PaymentMode::where('status', 1);
                break;
            case 'inactive':
                $query = PaymentMode::where('status', 0);
                break;
            default:
                $query = PaymentMode::query();
        }

        $query = $detailed ? $query->with(['creator', 'updater']) : $query;
        $query->orderBy('name', 'asc');
        $query = $paginated ?  $query->paginate(10, ['*'], 'page', $page) : $query->get();
        return $query;
    }

    public function finance_setting_payment_mode_get_by($id, $detailed){
        $query = PaymentMode::where('id', $id);
        if ($detailed) {
            $query->with(['creator', 'updater']);
        }
        return $query->first();
    }

    public function finance_setting_payment_mode_update($data, $id){
        DB::beginTransaction();
        try {
            $query = PaymentMode::findOrFail($id);
            
            $query->name          = $data['name'];
            $query->description   = $data['description'] ?? null;
            $query->status        = $data['status'] ?? 1;
            $query->updated_by    = Auth::id() ?? auth('api')->id();
            
            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Payment Mode Update', $id, true);
            return $query;
        } 
        catch (Exception $e) {
            DB::rollback();
            $this->log_user_activity('Finance Payment Mode Update', null, false);
            return $e->getMessage();
        }
    }
}