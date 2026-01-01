<?php

namespace App\Services\Finance;

use App\Models\Finance\Expense;
use App\Models\Finance\Income;
use App\Models\Finance\MainTransaction;
use App\Models\Payment;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MainService
{
    public function finance_generateRandomString($length = 10){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function finance_setting_generate_unique_id($type){
        $code = $this->finance_generateRandomString(12);
        switch($type){
            case 'expense':
                $prefix = 'EXP';
                $query = Expense::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_setting_generate_unique_id('expense');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'income':
                $prefix = 'INC';
                $query = Income::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_setting_generate_unique_id('income');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'payment':
                $prefix = 'PYT';
                $query = Payment::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_setting_generate_unique_id('payment');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'transaction':
                $prefix = 'TRN';
                $query = MainTransaction::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_setting_generate_unique_id('transaction');
                }
                else{
                    return $prefix.'-'.$code;
                }
               
        }
    }

    public function createTransaction($data){
        DB::beginTransaction();
        try{
            // All Transactions are of 2 types Debit or Credit
            $main_transaction = MainTransaction::create([
                'date' => $data['date'] ?? date('Y-m-d'),
                'payment_due_date' => $data['payment_due_date'] ?? date('Y-m-d'),
                'customer_id' => $data['customer_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'staff_id' => $data['staff_id'] ?? null,
                'trans_type' => $data['trans_type'],
                'transactionable_type' => $data['transactionable_type'],
                'transactionable_id' => $data['transactionable_id'],
                'unique_id' => $this->finance_setting_generate_unique_id('transaction'),
                'amount' => $data['amount'],
                'paid' => $data['paid'] ?? 0.00,
                'payable' => $data['payable'] ?? $data['amount'],
                'status' => $data['status'] ?? 0,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            return $main_transaction;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}