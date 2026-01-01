<?php

namespace App\Models\Finance;

use App\Models\Structure;

class TopUp extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_patient_topups';
    protected $fillable = array('patient_id', 'amount', 'channel', 'date', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}