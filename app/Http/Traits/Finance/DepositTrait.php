<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\TopUp;
use App\Models\Insurance\PlanBranch;
use App\Models\Inventory\Item;
use App\Models\Operations\Branch as OperationBranch;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DepositTrait{

    use ItemTrait, LogTrait;

    public function finance_deposit_create($data){
        DB::beginTransaction();
        try{
            $query = TopUp::create([
                'amount' => $data['amount'], 
                'bank_id' => $data['bank_id'] ?? null,
                'channel' => $data['channel'],
                'collected_by' => $data['collected_by'] ?? (Auth::id() ?? auth('api')->id()),
                'collected_at' => $data['collected_at'] ?? date('Y-m-d H:i:s'),
                'date' => date('Y-m-d'), 
                'mode_id' => $data['mode_id'],
                'patient_id' => $data['patient_id'],
                'status' => 1,
                'trans_type' => 'Credit', 
                'visit_id' => $data['visit_id'] ?? null,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $patient = Patient::find($data['patient_id']);
            $patient->balance = $patient->balance + $data['amount'];
            $patient->save();

            $this->log_user_activity('Deposit Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Deposit Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_deposit_report($data){
        $patient = Patient::find($data['patient_id']);
        $transactions = TopUp::where('patient_id', $data['patient_id'])->whereBetween('created_at', [$data['start_date'], $data['end_date']])->get();
        // Get opening balance for the start date
        $previous_transactions = TopUp::where('patient_id')->where('created_at', '<', $data['start_date'])->get();
        $openingBalance = $previous_transactions->sum(function ($transaction) {
            return $transaction->trans_type == 'Credit' ? $transaction->amount : -$transaction->amount;
        });
        $openingBalance = $patient->balance - $openingBalance;

        // Get transactions between the start and end dates
        $transactions = TopUp::where('patient_id')->whereBetween('created_at', [$data['start_date'], $data['end_date']])->get();

        // Get closing balance
        $closingBalance = $openingBalance + $transactions->sum(function ($transaction) {
            return $transaction->trans_type == 'Credit' ? $transaction->amount : -$transaction->amount;
        });

        $data = [
            'closing_balance' => $closingBalance,
            'opening_balance' => $openingBalance,
            'transactions' => $transactions,
        ];

        return $data;
    }

    public function finance_deposit_withdrawal($data){
        DB::beginTransaction();
        try{
            $query = TopUp::create([
                'amount' => $data['amount'], 
                'bank_id' => $data['bank_id'] ?? null,
                'channel' => $data['channel'],
                'date' => date('Y-m-d'), 
                'mode_id' => $data['mode_id'],
                'patient_id' => $data['patient_id'],
                'status' => 1,
                'visit_id' => $data['visit_id'] ?? null,
                'details' => $data['details'] ?? null,
                'collected_by' => $data['collected_by'] ?? (Auth::id() ?? auth('api')->id()),
                'collected_at' => $data['collected_at'] ?? date('Y-m-d H:i:s'),
                'trans_type' => 'Debit', 
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $patient = Patient::find($data['patient_id']);
            $patient->balance -= $data['amount'];
            $patient->save();

            $this->log_user_activity('Deposit Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Deposit Create', null, false);
            return $e->getMessage();
        }
    }

}