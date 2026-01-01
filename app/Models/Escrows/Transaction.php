<?php

namespace App\Models\Escrows;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Structure
{
    use HasFactory;

    const StatusAwaitingConfirmationBuyer = 1110;
    const StatusAwaitingConfirmationSeller = 1101;
    const StatusAwaitingConfirmationBroker = 1001;
    const StatusAcceptedPaymentAwaiting = 1111;
    const StatusPaidAwaitingDelivery = 2100;
    const StatusOngoing = 3000;
    const StatusDeliveredAwaitingBuyer = 4101;
    const StatusDeliveredAwaitingSeller = 401;
    const StatusDeliveredAwaitingBroker = 4010;
    const StatusCompleted = 5000;
    const StatusDisputed = 6000;
    const StatusDisputedByBroker = 6100;
    const StatusDisputedByBuyer = 6010;
    const StatusDisputedBySeller = 6001;
    const StatusDisputeResolved = 7000;
    const StatusCancelled = 0000;
    const StatusCancelledByBroker = 0100;
    const StatusCancelledByBuyer = 0010;
    const StatusCancelledBySeller = 0001;
    const StatusCleared = 8000;
    const StatusPending = 1000;
    const StatusRejected = 9000;
    const StatusRejectedByBroker = 9100;
    const StatusRejectedByBuyer = 9010;
    const StatusRejectedBySeller = 9001;
    //const StatusRejected = 10;
    
    protected $primaryKey = 'id';
    protected $table = 'escrow_transactions';
    protected $fillable = array('amount', 'broker_id', 'buyer_id', 'confirmation_code', 'contract', 'date', 'item_details', 'inspection_period', 'item_type_id', 'unique_code', 'product_id', 'request_id', 'seller_id', 'status', 'title', 'created_by', 'updated_by', 'completed_by', 'deleted_by', 'created_at', 'updated_at', 'completed_at', 'deleted_at');

    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    public function activities(){
        return $this->hasMany('App\Models\Escrows\TransactionActivity', 'transaction_id', 'id');
    }

    public function broker(){
        return $this->belongsTo('App\Models\User', 'broker_id', 'id');
    }

    public function buyer(){
        return $this->belongsTo('App\Models\User', 'buyer_id', 'id');
    }

    public function item_type(){
        return $this->belongsTo('App\Models\Escrows\ItemType', 'item_type_id', 'id');
    }

    public function files(){
		return $this->hasMany('App\Models\Escrows\TransactionFile', 'transaction_id', 'id');
	}

    public function payment(){
        return $this->belongsTo('App\Models\Finance\Payment', 'unique_id', 'transaction_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Escrows\Product', 'product_id', 'id');
    }

    public function seller(){
        return $this->belongsTo('App\Models\User', 'seller_id', 'id');
    }
}
