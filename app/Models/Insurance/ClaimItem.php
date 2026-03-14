<?php

namespace App\Models\Insurance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimItem extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_claim_items';
    
    protected $fillable = array('claim_id', 'visit_transaction_id', 'visit_transaction_coverage_id', 'agreed_price', 'covered_amount', 'patient_portion', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function claim(){
        return $this->belongsTo('App\Models\Insurance\Claim', 'claim_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}