<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostOp extends Structure
{
    use HasFactory;

    const ClearanceStatusCleared = 100;
    const ClearanceStatusICU = 50;
    const ClearanceStatusWard = 40;
    const ClearanceStatusReturn = 10;

    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_case_post_operations';
    protected $fillable = array('case_id', 'monitored_by', 'start_time', 'end_time', 'aldrete_score', 'pain_score', 'complications', 'vital_stable', 'airway_patency', 'nausea', 'clearance_status', 'remarks', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}
