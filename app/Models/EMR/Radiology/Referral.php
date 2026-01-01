<?php

namespace App\Models\EMR\Radiology;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_radiology_requests';

    protected $fillable = array('request_id', 'date', 'transaction_id', 'source_branch_id', 'destination_branch_id', 'vendor_id', 'outsourced_type', 'outsource_result_file', 'status', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function transaction(){
        return $this->belongsTo('App\Models\Finance\Transaction', 'transaction_id', 'id');
    }

}
