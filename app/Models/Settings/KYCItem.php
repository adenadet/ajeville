<?php

namespace App\Models\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KYCItem extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'setting_kyc_items';
    protected $fillable = array('name', 'expires', 'file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
		return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}
	
	public function updater(){
		return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
	
}
