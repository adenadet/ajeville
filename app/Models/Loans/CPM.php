<?php

namespace App\Models\Loans;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPM extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'loan_account_cpms';
    protected $fillable = array('loan_id', 'detail', 'hbd_id', 'hbd_date', 'ceo_id', 'ceo_at', 'chairman_id', 'chairman_at', 'rm_id', 'rm_at', 'ao_id', 'ao_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account_officer(){
        return $this->belongsTo('App\Models\User', 'ao_id', 'id');
    }

    public function business_developer(){
        return $this->belongsTo('App\Models\User', 'hbd_id', 'id');
    }

    public function ceo(){
        return $this->belongsTo('App\Models\User', 'ceo_id', 'id');
    }

    public function chairman(){
        return $this->belongsTo('App\Models\User', 'chairman_id', 'id');
    }

    public function risk_manager(){
        return $this->belongsTo('App\Models\User', 'rm_id', 'id');
    }
}
