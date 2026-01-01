<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayOut extends Structure
{
    use HasFactory;

    const StatusDraft = 1;
    const StatusConfirmed = 10;
    const StatusReversed = 100;

    protected $primaryKey = 'id';
    protected $table = 'finance_pay_outs';
    protected $fillable = array('unique_id', 'expense_id', 'date', 'amount', 'vendor_id', 'staff_id', 'customer_id', 'account_id', 'description', 'status', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
        return $this->belongsTo('App\Models\Finance\Account', 'account_id', 'id');
    }

    public function allocations(){
        return $this->hasMany('App\Models\Finance\PayOutAllocation', 'pay_out_id', 'id');
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

    public function staff(){
        return $this->belongsTo('App\Models\User', 'staff_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }
}
