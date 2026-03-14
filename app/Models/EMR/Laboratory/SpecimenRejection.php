<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecimenRejection extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_specimen_rejections';

    protected $fillable = ['specimen_id', 'reason', 'remarks', 'rejected_by', 'rejected_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function specimen(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Specimen', 'specimen_id', 'id');
    }
}
