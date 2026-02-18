<?php

namespace App\Models\EMR\Patient;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_patient_ledgers';
    protected $fillable = array('patient_id', 'visit_id', 'visit_transaction_id', 'visit_payment_id', 'type', 'referenceable_type', 'referenceable_id', 'amount', 'direction', 'balance_after', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    
    public function visit(){
    	return $this->belongsTo('App\Models\EMR\Visit', 'plan_id', 'id');
	}

    public function visit_payment(){
    	return $this->belongsTo('App\Models\EMR\VisitPayment', 'visit_payment_id', 'id');
	}

    public function visit_transaction(){
    	return $this->belongsTo('App\Models\EMR\VisitTransaction', 'visit_transaction_id', 'id');
	}
}
