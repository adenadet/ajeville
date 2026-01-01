<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_admission_requests';
    protected $fillable = array('date', 'visit_id', 'branch_id', 'consultation_id', 'patient_id', 'admission_type_id', 'admitted_date', 'admitted_at', 'discharged_date', 'discharged_at', 'requested_by', 'requested_at', 'requested_remark', 'room_id', 'admitted_by', 'admission_note', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}
