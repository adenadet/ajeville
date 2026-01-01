<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryStructure extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'hrms_salary_structures';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function components(){
        return $this->hasMany('App\Models\Hrms\SalaryStructureComponent', 'salary_structure_id', 'id');
    }
    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
