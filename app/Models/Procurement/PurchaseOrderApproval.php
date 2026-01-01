<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderApproval extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_purchase_order_approvals';
    protected $fillable = array('po_id', 'approved_by', 'remark', 'decision', 'description', 'start_stage', 'end_stage', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    //`po_id`, `approved_by`, `remark`, `decision`, `description`, `start_stage`, `end_stage`, 

    public function approver(){
    	return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function purchase_order(){
        return $this->belongsTo('App\Models\Procurement\PurchaseOrder', 'po_id', 'unique_id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }


}
