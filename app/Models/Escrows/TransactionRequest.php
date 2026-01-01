<?php

namespace App\Models\Escrows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionRequest extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'escrow_transaction_requests';
    protected $fillable = array('purpose', 'trans_type_id', 'item_type_id', 'amount', 'buyer_id', 'buyer_email', 'buyer_phone', 'vendor_id', 'description', 'vendor_email', 'vendor_phone', 'image', 'repeating', 'quantity', 'balance', 'status_id', 'created_by', 'updated_by', 'completed_by', 'deleted_by', 'created_at', 'updated_at', 'completed_at', 'deleted_at');
	
    public function transaction(){
		return $this->belongsTo('App\Models\Escrows\Transaction', 'id', 'request_id');
	}
	
	public function hod(){
		return $this->belongsTo('App\Models\User', 'hod_id', 'id');
	}
}
