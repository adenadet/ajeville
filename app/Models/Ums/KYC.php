<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYC extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'customer_kyc_details';
    protected $fillable = array('user_id', 'id_type_id', 'id_file', 'utility_type_id', 'utility_file', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
