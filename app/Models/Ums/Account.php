<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Structure

{
    use HasFactory;

    protected $table = 'user_accounts';
    protected $fillable = array('user_id', 'bank_id', 'account_name', 'account_number', 'primary_account', 'status', 'confirmed_by', 'created_by', 'updated_by', 'deleted_by', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at'); 

    public function bank()
    {
        return $this->belongsTo('App\Models\Finance\AllBank', 'bank_id', 'id');
    }
}
