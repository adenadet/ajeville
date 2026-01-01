<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClockIn extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'hrms_employee_clock_ins';
    protected $fillable = array('employee_id', 'clock_in_time', 'source', 'created_at', 'updated_at', 'deleted_at');
    protected $casts = [
        'start_time'    => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

}
