<?php

namespace App\Models\EMR\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInActive = 0;
    protected $primaryKey = 'id';
    protected $table = 'emr_admission_wards';
    protected $fillable = array('name', 'branch_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function beds(){
		return $this->hasMany('App\Models\EMR\Admission\Bed', 'ward_id', 'id');
    }
    public function branch(){
		return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
    }

    public function rooms(){
		return $this->hasMany('App\Models\EMR\Admission\Room', 'ward_id', 'id');
    }
}