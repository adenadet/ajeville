<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_deductions';
    protected $fillable = array('employee_id', 'title', 'amount', 'date', 'type', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

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