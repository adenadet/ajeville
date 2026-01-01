<?php

namespace App\Models\Operations;
use App\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class Department extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'operation_departments';
    protected $fillable = array('name', 'hod_id', 'description', 'ext', 'email', 'deleted_by', 'deleted_at');
	
    public function employees(){
    	return $this->hasMany('App\Models\HRMS\Employee', 'department_id', 'id');
		}
	public function hod(){
		return $this->belongsTo('App\Models\HRMS\Employee', 'hod_id', 'id');
		}
	}