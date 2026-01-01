<?php

namespace App\Models\EMR\Physiotheraphy;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_physio_requests';
    protected $fillable = array('date', 'visit_id', 'branch_id', 'consultation_id', 'patient_id', 'transaction_id', 'quantity', 'item_id', 'status', 'result', 'special', 'sample_collected_by', 'sample_collected_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}
