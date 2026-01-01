<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'hrms_user_accounts';
    
    protected $fillable = array('user_id', 'bank_id', 'account_name', 'account_number', 'primary_account', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at');

    public function bank(){
        return $this->belongsTo('App\Models\Finance\Bank', 'bank_id', 'id');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
