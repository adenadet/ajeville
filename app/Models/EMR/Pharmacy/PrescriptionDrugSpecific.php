<?php

namespace App\Models\EMR\Pharmacy;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDrugSpecific extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_prescription_specific_drug_confirmations';
    protected $fillable = array('prescription_id', 'drug_id', 'specific_drug_id', 'drug_name', 'detail', 'dose', 'duration', 'frequency', 'form',  'route_id', 'quantity', 'start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at');
}
