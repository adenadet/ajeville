<?php

namespace App\Models\Operations;
use App\Models\Structure;

use Illuminate\Database\Eloquent\Model;

class State extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'operation_states';
    protected $fillable = array('name');

    public function areas(){
        return $this->hasMany('App\Models\Operations\Area', 'state_id', 'id');
        }
	}
