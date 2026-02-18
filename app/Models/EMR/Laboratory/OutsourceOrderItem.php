<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutsourceOrderItem extends Structure
{
    use HasFactory;
    public const StatusDraft = 0;
    public const StatusSent = 10;
    public const StatusAcknowledged = 20;
    public const StatusInProgress = 30;
    public const StatusCompleted = 40;
    public const StatusResultReceived = 50;
    public const StatusCancelled = 100; 
    
    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_outsource_order_items';
    protected $fillable = ['request_item_id', 'outsource_order_id', 'status', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function canceller(){
        return $this->belongsTo('App\Models\User', 'cancelled_by', 'id');
    }

    public function completer(){
        return $this->belongsTo('App\Models\User', 'completed_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'completed_by', 'id');
    }

    public function request(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Request', 'request_id', 'id');
    }

    public function outsourcing_branch(){
        return $this->belongsTo('App\Models\Operation\Branch', 'branch_id', 'id');
    }

    public function receiving_branch(){
        return $this->belongsTo('App\Models\Operation\Branch', 'target_branch_id', 'id');
    }

    public function vendor(){
        return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }

    public function sender(){
        return $this->belongsTo('App\Models\User', 'sent_by', 'id');
    }

    public function receiver(){
        return $this->belongsTo('App\Models\User', 'acknowledged_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
