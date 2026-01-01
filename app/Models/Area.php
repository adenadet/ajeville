<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'areas';
    protected $fillable = array('name', 'state_id');

    public function state(){
		return $this->belongsTo('App\Models\State', 'state_id', 'id');
	}
}
