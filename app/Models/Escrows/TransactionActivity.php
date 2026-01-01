<?php

namespace App\Models\Escrows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionActivity extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'escrow_transaction_activities';
    protected $fillable = array('transaction_id', 'user_id', 'status', 'subject', 'details', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    protected $hidden = [
        'deleted_by',
        'deleted_at',
    ];

    public function broker(){
        return $this->belongsTo('App\Models\User', 'broker_id', 'id');
    }

    public function buyer(){
        return $this->belongsTo('App\Models\User', 'buyer_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}