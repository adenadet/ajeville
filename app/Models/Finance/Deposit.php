<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Structure
{
    public const StatusClaimed = 7;
    public const StatusConfirmed = 10;
    public const StatusQueried = 5;
    public const StatusUnconfirmed = 0;
    
    protected $primaryKey = 'id';
    protected $table = 'finance_deposits';
    protected $fillable = array('date', 'bank_id', 'customer_id', 'mode_id', 'amount', 'status', 'collected_by', 'collected_at', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function collector(){
        return $this->belongsTo('App\Models\User', 'collected_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
