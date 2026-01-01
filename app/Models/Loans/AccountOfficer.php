<?php

namespace App\Models\Loans;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountOfficer extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'loan_accounts_officers';
    protected $fillable = array('staff_id', 'account_id', 'status', 'created_by', 'deleted_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at');

    public function loan(){
        return $this->belongsTo('App\Models\Loans\Account', 'loan_id', 'id');
    }

    public function staff(){
        return $this->belongsTo('App\Models\User', 'staff_id', 'id');
    }
}
