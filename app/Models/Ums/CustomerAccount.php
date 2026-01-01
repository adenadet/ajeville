<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'customer_accounts';
    protected $fillable = array('user_id', 'bank_id', 'account_name', 'account_number', 'status', 'confirmed_by', 'created_by', 'updated_by', 'deleted_by', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at'); 

    public function customer(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function bank(){
        return $this->belongsTo('App\Models\Finance\AllBanks', 'bank_id', 'id');
    }

}
