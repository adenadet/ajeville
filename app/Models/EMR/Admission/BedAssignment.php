<?php

namespace App\Models\EMR\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BedAssignment extends Model
{
    use HasFactory;

    const StatusPending = 0;
    const StatusAssigned = 1;
    const StatusReleased = 2;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_bed_assignments';
    protected $fillable = array('bed_id', 'patient_id', 'admission_id', 'assigned_by', 'assigned_at', 'released_by', 'released_at', 'override', 'override_reason', 'override_approved_by', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function admission(){
        return $this->belongsTo('App\Models\EMR\Admission\Request', 'admission_id', 'id');
    }

    public function assigner(){
        return $this->belongsTo('App\Models\User', 'assigned_by', 'id');
    }
    
    public function bed(){
        return $this->belongsTo('App\Models\EMR\Admission\Bed', 'bed_id', 'id');
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
