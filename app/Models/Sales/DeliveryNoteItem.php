<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNoteItem extends Structure
{
    use HasFactory;

    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'sales_delivery_note_items';
    protected $fillable = array('delivery_note_id', 'item_id', 'quantity', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function delivery_note(){
    	return $this->hasMany('App\Models\Sales\DeliveryNote', 'delivery_item_id', 'id');
    }

    public function item(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
