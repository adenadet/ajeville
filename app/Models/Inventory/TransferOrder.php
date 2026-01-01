<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Structure
{
    const ApprovalStatusPending = 0;
    const ApprovalStatusApproved = 10;
    const ApprovalStatusCancelled = 40;
    const StatusConfirmed = 2;
    protected $primaryKey = 'id';
    protected $table = 'inventory_transfer_requests';
    protected $fillable = array('name', 'description', 'unique_id', 'requesting_store_id', 'issuing_store_id', 'status', 'approval_status', 'approved_by', 'approval_note', 'approved_at', 'accepted_by', 'accepted_at', 'acceptance_note', 'rejected_by', 'rejected_at', 'rejection_note', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accepter(){
        return $this->belongsTo('App\Models\User', 'accepted_by', 'id');
    }

    public function approver(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function issuing_store(){
        return $this->belongsTo('App\Models\Inventory\Store', 'issuing_store_id', 'id');
    }

    public function items(){
        return $this->hasMany('App\Models\Inventory\TransferOrderItem', 'transfer_request_id', 'id');
    }

    public function rejecter(){
        return $this->belongsTo('App\Models\User', 'rejected_by', 'id');
    }

    public function requesting_store(){
        return $this->belongsTo('App\Models\Inventory\Store', 'requesting_store_id', 'id');
    }
}
