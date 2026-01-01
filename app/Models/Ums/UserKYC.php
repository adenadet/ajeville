<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserKYC extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'user_kyc_items';
    protected $fillable = array('user_id', 'item_id', 'identification', 'file', 'expiry_date', 'valid', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function kyc_item(){
        return $this->belongsTo('App\Models\Settings\KYCItem', 'item_id', 'id');
    }
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
