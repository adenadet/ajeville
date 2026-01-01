<?php

namespace App\Models\Escrows;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'escrow_item_types';
    protected $fillable = array('name', 'max_hold_period', 'status', 'main_category_id', 'requires_delivery', 'has_completed', 'created_by', 'updated_by', 'completed_by', 'deleted_by', 'created_at', 'updated_at', 'completed_at', 'deleted_at');
	
    public function transaction(){
		return $this->belongsTo('App\Models\Escrows\Transaction', 'id', 'request_id');
	}
	
	public function hod(){
		return $this->belongsTo('App\Models\User', 'hod_id', 'id');
	}
}
