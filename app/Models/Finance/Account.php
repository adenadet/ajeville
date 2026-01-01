<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'finance_branch_banks';
    protected $fillable = array('bank_id', 'branch_id', 'account_name', 'account_number', 'balance', 'purpose', 'is_primary', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function bank(){
        return $this->belongsTo('App\Models\Finance\Bank', 'bank_id', 'id');
    }
}
