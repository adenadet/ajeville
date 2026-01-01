<?php

namespace App\Models\EMR\Appointment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class Appointment extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_appointments';
    protected $fillable = array('unique_id', 'visit_id', 'patient_id', 'service_type_id', 'specialty_id', 'consultant_id', 'plan_id', 'branch_id', 'date', 'time_slot', 'status', 'type', 'remarks',  'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

	public const StatusCancelled = 'cancelled';
	public const StatusCheckedIn = 'checked_in';
	public const StatusCompleted = 'completed';
	public const StatusConfirmed = 'confirmed';
	public const StatusNoShow = 'no_show';
	public const StatusPending = 'pending';

	public const TypePatient = 'patient';
	public const TypeStaff = 'staff';
	public const TypeSystem = 'system';

    public function branch(){
    	return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
	}
    
	public function consultant(){
    	return $this->belongsTo('App\Models\User', 'consultant_id', 'id');
	}

	public function partner(){
		return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
	}

    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
	}

	public function service_type(){
    	return $this->belongsTo('App\Models\EMR\Settings\ServiceType', 'service_type_id', 'id');
	}

	public function specialty(){
    	return $this->belongsTo('App\Models\EMR\Consultation\Specialty', 'specialty_id', 'id');
	}
}
