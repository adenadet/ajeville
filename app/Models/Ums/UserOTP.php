<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOTP extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'ums_user_otps';
    protected $fillable = array( 'type', 'code', 'status', 'user_id', 'requested_by', 'request_channel', 'created_at', 'updated_at', 'deleted_at');
    
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
