<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayOutAllocation extends Structure
{
    use HasFactory;

    const StatusDraft = 1;
    const StatusConfirmed = 10;
    const StatusReversed = 100;

    protected $primaryKey = 'id';
    protected $table = 'finance_pay_out_allocations';
    protected $fillable = array('pay_out_id', 'wallet_id', 'expense_id', 'amount', 'created_at', 'updated_at', 'deleted_at');

    public function expense(){
        return $this->belongsTo('App\Models\Finance\Expense', 'expense_id', 'id');
    }

    public function pay_out(){
        return $this->belongsTo('App\Models\Finance\PayOut', 'pay_out_id', 'id');
    }
}
