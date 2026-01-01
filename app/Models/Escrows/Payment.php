<?php

namespace App\Models\Escrows;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'escrow_payments';
    protected $fillable = array('transaction_id', 'unique_code', 'date', 'time_stamped', 'amount', 'channel', 'description', 'proof', 'status', 'confirmed_by', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at');
	
    public function transaction(){
		return $this->belongsTo('App\Models\Escrows\Transaction', 'transaction_id', 'unique_code');
	}
}
