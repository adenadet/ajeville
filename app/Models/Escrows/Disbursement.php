<?php

namespace App\Models\Escrows;
use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Structure
{
    use HasFactory;

    const StatusPending = 0;
    const StatusApproved = 1;
    const StatusRejected = 20;
    const StatusPaid = 100;
    protected $primaryKey = 'id';

    protected $table = 'escrow_disbursements';
    protected $fillable = array('transaction_id', 'transaction_mile_stone_id', 'user_id', 'user_account_id', 'amount', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
	
    public function milestone(){
		return $this->belongsTo('App\Models\Escrows\TransactionMileStone', 'transaction_mile_stone_id', 'id');
	}

    public function transaction(){
		return $this->belongsTo('App\Models\Escrows\Transaction', 'transaction_id', 'unique_code');
	}

    public function user(){
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

    public function user_account(){
        return $this->belongsTo('App\Models\Ums\Account', 'user_account_id', 'id');
    }
}
