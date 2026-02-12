<?php

namespace App\Models\EMR\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreAdmissionCheck extends Model
{
    use HasFactory;

    protected $table = 'emr_admission_pre_admission_checks';

    protected $fillable = ['admission_request_id', 'code', 'name', 'meta', 'result', 'notes', 'checked_by', 'checked_at', 'created_by', 'updated_by', 'deleted_by',  'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'meta' => 'array',
    ];
    public function admission_request()
    {
        return $this->belongsTo('App\Models\EMR\Admission\Request', 'admission_request_id', 'id');
    }
}
