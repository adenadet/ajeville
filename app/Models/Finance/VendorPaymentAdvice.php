<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPaymentAdvice extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_vendor_payment_advices';
    protected $fillable = array('name', 'amount', 'payment_id', 'type_id', 'spec_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function grn_item(){
        return $this->belongsTo('App\Models\ReceivedNoteItem', 'spec_id', 'id');
    }
}
