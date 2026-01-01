<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionNew extends Structure
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'finance_transactions_new';
    protected $fillable = array('unique_id', 'date', 'transaction_type', 'classification', 'customer_id', 'reference_id', 'amount', 'discount', 'account_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function bank_account(){
        return $this->belongsTo('App\Models\Finance\Account', 'account_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function payments(){
        return $this->hasMany('App\Models\Finance\TransactionPayment', 'transaction_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
