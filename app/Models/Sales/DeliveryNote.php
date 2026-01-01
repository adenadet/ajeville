<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Structure
{
    use HasFactory;

    public const StatusCreated = 1;
    public const StatusAssigned = 2;
    public const StatusEnroute = 3;
    public const StatusDelivered = 10;
    public const StatusQueried = 40;

    protected $primaryKey = 'id';
    protected $table = 'sales_delivery_notes';
    protected $fillable = array('uuid', 'so_id', 'delivery_by', 'delivery_remark', 'delivery_at', 'received_by', 'received_remark', 'received_at', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function delivery_person(){
    	return $this->belongsTo('App\Models\User', 'delivery_by', 'id');
    }

    public function order(){
    	return $this->belongsTo('App\Models\Sales\Order', 'so_id', 'id');
    }

    public function delivery_items(){
    	return $this->hasMany('App\Models\Sales\DeliveryNoteItem', 'delivery_note_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
