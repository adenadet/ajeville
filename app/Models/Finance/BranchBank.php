<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchBank extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_branch_banks';
    protected $fillable = array('bank_id', 'branch_id', 'account_name', 'account_number', 'purpose', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function bank(){
        return $this->belongsTo('App\Models\Finance\Bank', 'bank_id', 'id');
    }

    public function branch(){
        return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
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