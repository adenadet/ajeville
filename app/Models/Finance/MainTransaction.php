<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainTransaction extends Structure
{
    use HasFactory;

    const StatusNotPaid = 0;
    const StatusPaid = 1;
    const StatusCompleted = 10;
    const StatusConfirmed = 40;
    const StatusQueried = 70;
    const StatusRejected = 100;

    protected $primaryKey = 'id';
    protected $table = 'finance_main_transactions';
    protected $fillable = array('date', 'payment_due_date', 'customer_id', 'vendor_id', 'staff_id', 'classification_id', 'trans_type', 'transactionable_type', 'transactionable_id', 'unique_id', 'amount', 'paid', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function classification(){
        return $this->belongsTo('App\Models\Finance\ExpenseType', 'classification_id', 'id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function transactionable(){
        return $this->morphTo();
    }

    public function plan(){
        return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\User', 'staff_id', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }

}
