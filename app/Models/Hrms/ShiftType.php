<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftType extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_shift_types';
    protected $fillable = array('name', 'shift_id', 'days_of_week', 'created_at', 'updated_at', 'deleted_at');

    public function employees(){
        return $this->hasMany('App\Models\Hrms\Employee', 'shift_type_id', 'id');
    }

    public function shift(){
        return $this->belongsTo('App\Models\Hrms\Shift', 'shift_id', 'id');
    }
    protected $casts = [
        'days_of_week'  => 'json',
        'end_time'      => 'time',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];
}